# Outgoing mail queue

Password reset emails (and anything else the public pages send) no longer go
out over SMTP inside the web request. The message is rendered and written to
the `mail_queue` table, and a cron job running **every two minutes** delivers
it.

That is what fixes the two symptoms this replaced:

* online — *"We could not send the email right now, so your password was left
  unchanged"*, which appeared whenever the mail server was slow, unreachable,
  or rejecting the login;
* locally — *"Local test mode: no mail server is configured, so the email was
  saved instead of sent"*, which happened on every localhost request whether or
  not a mail server was reachable.

---

## The SMTP credentials

The original failure was that the mailbox rejected the configured password
(`535 Incorrect authentication data`) — that, not the code, is what produced
*"We could not send the email right now"*. The mailbox password was reset on
2026-08-10 and `application/config/email.php` now carries the working one:

```
$ php index.php mailqueue check
Host: mail.depedddo-mis.com:465 (ssl), user ddorecruitmentsystem@depedddo-mis.com
AUTH     235 Authentication succeeded

OK - the mail server accepted these credentials.
```

Run that check any time delivery looks wrong — it separates "the credentials
are wrong" from "the message is bad", which is otherwise slow to work out.

Worth knowing for next time: `depedddo-mis.com` (mail, `198.23.58.128`,
supremepanel39) and `depedddo-mis.online` (the site, `147.93.78.140`,
Hostinger) are on **different hosting accounts**, and `depedddo-mis.online` has
no MX record of its own. The mailbox lives on the older account's cPanel, so
that is where the password gets reset.

When it next needs rotating, prefer the environment over editing the file —
every setting is overridable, so no code change is needed:

```
SRMS_SMTP_HOST   SRMS_SMTP_USER   SRMS_SMTP_PASS
SRMS_SMTP_PORT   SRMS_SMTP_CRYPTO
```

If mail ever does start failing, **nothing queued in the meantime is lost**.
Fix the credentials, confirm with `check`, then `php index.php mailqueue retry`
puts everything that gave up back in the queue for the next cron run.

---

## Setting it up on the server

**1. Create the table** (once per database):

```bash
mysql -u USER -p DATABASE < mail_queue.sql
```

**2. Add the cron job.** In Hostinger hPanel → Advanced → Cron Jobs, every 2
minutes:

```
*/2 * * * * /usr/bin/php /home/USER/public_html/index.php mailqueue run >/dev/null 2>&1
```

Adjust the PHP binary and path to match the account. Check the path with
`which php`; some hosts want `/usr/local/bin/php` or a version-specific binary
such as `/opt/alt/php82/usr/bin/php`.

**If the host has no CLI cron**, the same run can be triggered over HTTP.
Set a secret first:

```
export SRMS_CRON_TOKEN='some-long-random-string'
```

then point a web cron / uptime pinger at:

```
https://depedddo-mis.online/mailqueue/run?token=some-long-random-string
```

With no token set, HTTP triggering is refused outright, so the endpoint cannot
be left accidentally open.

**3. Confirm it works:**

```bash
MAILQUEUE_TO=you@example.com php index.php mailqueue test
php index.php mailqueue status
```

---

## Commands

All are CLI-only except `run`.

| Command | What it does |
|---|---|
| `php index.php mailqueue run` | Send everything that is due. This is what cron calls. |
| `php index.php mailqueue status` | Counts per status plus the last 15 messages. |
| `php index.php mailqueue check` | Log in to the mail server and hang up — separates "credentials are wrong" from "message is bad". |
| `MAILQUEUE_TO=you@example.com php index.php mailqueue test` | Queue a test message. |
| `php index.php mailqueue bounces` | Read delivery failures back out of the sending mailbox over IMAP and record them against the messages they belong to. |
| `php index.php mailqueue retry [id]` | Put failed messages back in the queue. |

### `sent` does not mean delivered

`sent` means **our own mail server accepted the message for relay** — that is
all any SMTP client can ever know at the moment of sending. Whether the
recipient's server accepts it is decided afterwards, and comes back minutes or
hours later as a bounce addressed to
`ddorecruitmentsystem@depedddo-mis.com`, where nobody was looking.

That is why a message can sit in the queue marked `sent` having never been
delivered. `mailqueue bounces` closes that gap: it reads those bounces and
re-marks the messages as `bounced`, with the reason. Run it whenever mail is
"sent" but not arriving. It only ever reads the mailbox — nothing is deleted,
and messages are not even marked as read.

Two rejections seen in practice, which need completely different responses:

| Reason | Meaning |
|---|---|
| `550 5.4.1 Recipient address rejected: Access denied` (from `*.mail.protection.outlook.com`) | DepEd's Microsoft 365 tenant refused us. Either the mailbox does not exist, or the tenant blocks outside senders. Needs DepEd ICT, not a code change. |
| `Domain … has exceeded the max defers and failures per hour` | **Our own** server discarded it — see above. Nothing to do with the address. |

`test` takes the address from an environment variable because CodeIgniter
builds its URI from the command line arguments and its `permitted_uri_chars`
filter rejects the `@` before the controller is reached.
`mailqueue test you%40example.com` works too.

---

## How a password reset now flows

1. Someone submits the forgot-password form.
2. The email is rendered and `INSERT`ed into `mail_queue`, carrying the new
   password hash in its `payload` column. **The account is not touched yet.**
3. The request makes one short best-effort delivery attempt
   (`mail_queue_inline`, 5 second timeout). If the mail server is healthy the
   email arrives in a second or two.
4. If that attempt does not work, the row goes straight back to `pending` with
   **no failed attempt recorded**, and the page says *"A temporary password is
   on its way… your current password keeps working until the new one reaches
   you."* — not an error.
5. Cron picks it up within two minutes and sends it, holding one authenticated
   SMTP connection open across the whole batch.
6. **Only once the mail server has accepted the message** is the new password
   hash written to `users`, and the message body — which contains the temporary
   password in the clear — is discarded from the queue.

Step 6 is the important one. It preserves the property the original code had:
an account is never left with a password that only the server knows because
the email could not be delivered.

Failures are retried after 2 min, 10 min, 30 min, then hourly, up to 5
attempts, after which the message is marked `failed` and left for someone to
look at. Successfully sent rows are pruned after 30 days.

An address the mail server **refuses outright** — a 5xx reply to `RCPT TO`,
meaning the mailbox does not exist or the domain is not one it can deliver to —
is marked `failed` on the first attempt and shown as `REJECTED`. Retrying it
would change nothing and would only bury the real problem under four more
identical failures. A rejected *login* is not treated this way: those keep
their attempts, so once the credentials are fixed the backlog goes out on its
own without anyone running `retry`.

### ⚠️ "Message discarded" — the hourly failure quota

**This is the one that breaks delivery to addresses that are perfectly fine.**

```
Domain depedddo-mis.com has exceeded the max defers and failures
per hour (5/5 (100%)) allowed. Message discarded.
```

cPanel's Exim allows the domain **5 failed or deferred deliveries per hour**.
Once that is used up, it **throws away everything else the domain tries to
send for the rest of the hour** — including mail to good addresses.

That is the cascade behind "it works for Gmail sometimes":

1. A handful of bad addresses fail — a `.edu.ph` with no mailbox, a
   `deped.gov.ph` that refuses us, a Gmail deferral.
2. Five of those in an hour uses up the quota.
3. Every message after that is discarded. Valid Gmail addresses included.

`php index.php mailqueue bounces` labels these `THROTTLED` rather than
`PERMANENT`, because the address is not the problem and dropping it from your
list would be exactly the wrong response.

**To fix it:**

* Raise **Max defers and failures per hour** for the domain — WHM → Tweak
  Settings (needs root, or ask the host). The default of 5 is far too low for
  a system that sends password resets to hundreds of applicants.
* **Reduce the failures feeding it.** Every dead address burns quota that
  working recipients need. The queue now fails permanently-rejected addresses
  on the first attempt instead of retrying them five times, which alone cuts
  the burn rate substantially.

### "No Such User Here" for a domain on the same server

Watch for recipient domains that have **no MX record** but whose A record
points at `198.23.58.128` — the same Exim server that hosts
`depedddo-mis.com`. Exim treats those as local domains and looks for a local
mailbox, so mail to them fails with `550 No Such User Here` unless that exact
mailbox exists on that server. `candortci.edu.ph` is one of these.

To check an address without sending anything:

```bash
AUTH=$(printf '\0USER\0PASS' | base64)
printf 'EHLO localhost\r\nAUTH PLAIN %s\r\nMAIL FROM:<USER>\r\nRCPT TO:<the-address>\r\nQUIT\r\n' "$AUTH" \
  | openssl s_client -connect mail.depedddo-mis.com:465 -crlf -quiet 2>/dev/null | grep -E '^(235|250 |5..)'
```

`250 OK` after `RCPT TO` means the address is deliverable; `550` means it is
not, and no amount of retrying will change that.

Overlapping runs are safe — a run that is still going when the next one starts
keeps the rows it claimed, and the new run works on the rest.

---

## Settings

`application/config/mail_queue.php`:

| Setting | Default | Meaning |
|---|---|---|
| `mail_queue_batch` | 25 | Messages per cron run. |
| `mail_queue_max_attempts` | 5 | Tries before a message is marked `failed`. |
| `mail_queue_backoff` | 120, 600, 1800, 3600 | Seconds between retries. |
| `mail_queue_lock_ttl` | 900 | When a stuck `sending` row is reclaimed. |
| `mail_queue_retention_days` | 30 | How long sent rows are kept. |
| `mail_queue_inline` | TRUE | Try to deliver immediately as well as via cron. |
| `mail_queue_inline_timeout` | 5 | Seconds that attempt may take. |
| `mail_queue_cron_token` | *(empty)* | Secret for HTTP triggering; empty disables it. |

SMTP credentials live in `application/config/email.php`.

---

## Local development

Local now sends for real — same code path as production, no "local test mode".

To send through a different mailbox locally, copy
`application/config/email_local.php.example` to `email_local.php` and fill it
in. That file is git-ignored, and it is read both by web requests and by the
CLI worker so the queue and the sender always agree on which mail server they
are using.

If there is no SMTP host configured *at all*, the message is written to
`application/logs/dev_emails/` and a preview link is shown — but that is now
only a fallback for a machine with nothing configured, not something that
happens just because the request came from localhost.

The local cron job installed on this machine:

```
*/2 * * * * /Applications/XAMPP/bin/php /Applications/XAMPP/xamppfiles/htdocs/misDDO/index.php mailqueue run >/dev/null 2>>/Applications/XAMPP/xamppfiles/htdocs/misDDO/application/logs/mailqueue-cron.log
```
