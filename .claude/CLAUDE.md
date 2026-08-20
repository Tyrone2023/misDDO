# CLAUDE DEVELOPMENT RULES

This workspace is for software development only.

## Stack

PHP, CodeIgniter 3, MySQL/MariaDB, Bootstrap, jQuery, AJAX, DataTables, Select2, HTML, CSS, JavaScript.

## Core Rules

* Preserve the existing project structure and coding style.
* Do not rewrite working code unnecessarily.
* Prefer the smallest safe fix.
* Prioritize the exact reported error.
* Perform root-cause debugging.
* Keep all solutions CodeIgniter 3 compatible.
* Respect existing table names, columns, routes, variables, functions, session keys, and business rules.
* Do not invent missing database fields, functions, routes, files, APIs, or project behavior.
* If something is unknown, state the assumption briefly instead of hallucinating.
* Do not introduce new frameworks or major dependencies unless explicitly requested.
* Keep production compatibility in mind.

## Token-Saving Rule

**Do not display full code by default.**

When fixing code:

* Show only the changed lines or smallest relevant code block.
* Do not repeat unchanged code.
* Do not repeat the code provided by the user.
* Avoid long explanations.
* State the problem and fix briefly.
* Provide full code only when the user explicitly says:

  * `code all`
  * `full code`
  * `complete function`
  * `complete file`

If multiple changes exist, show only the affected sections and identify the filename/function.

## Debugging

Check common causes such as:

* Undefined variables
* Empty URI segments
* Routes/base_url
* SQL syntax
* Wrong joins
* Missing columns/tables
* Duplicate records
* Sessions
* Role restrictions
* AJAX/JSON mismatch
* Select2/DataTables
* Flashdata/redirects
* Validation
* Legacy compatibility
* Print CSS

Respond preferably as:

**Cause:** brief cause
**Fix:** direct fix
**Change:** only modified code

## Database

* Use exact existing table and column names.
* Do not change schema unless requested.
* Check joins, filters, grouping, ordering, NULL handling, and duplicate-prone logic.
* Maintain MySQL/MariaDB compatibility.
* Suggest indexes only when relevant.
* Be careful with UPDATE/DELETE queries.

## Frontend

Default to:

* Light theme
* Modern
* Professional
* Clean
* Responsive
* Bootstrap-compatible

Preserve existing:

* PHP logic
* Form actions
* Input names
* IDs
* JavaScript selectors
* AJAX endpoints
* Loops
* Validation
* Role restrictions

Improve appearance without breaking functionality.

Use subtle professional animations only when useful.

Avoid dark, neon, flashy, or excessive effects unless requested.

## Print Pages

For reports/forms:

* Prioritize A4 compatibility.
* Preserve margins, alignment, borders, tables, and official formatting.
* Do not break print behavior.
* Keep both screen and print layouts usable.

## New Features

* Integrate with the current architecture.
* Reuse existing patterns.
* Do not redesign the whole system.
* Do not invent schema.
* Keep implementation practical and production-safe.

## Main Goal

Help with:

* Debugging
* Fixing
* Coding
* SQL
* AJAX
* Code review
* Optimization
* UI improvements
* Print layouts

Avoid:

* Long tutorials
* Generic theory
* Repeating unchanged code
* Unnecessary rewrites
* Unrequested modernization
* Excessive explanations
## Database Auto-Creation

When a new table or column is required:

* Use the project's existing `ensure_table()` / schema-checking pattern.
* Automatically create missing tables or columns when the relevant page/module loads.
* Creation must be idempotent: safe to run repeatedly.
* If the table/column already exists, do nothing.
* Never drop, recreate, truncate, or reset an existing table on later visits.
* Never delete existing data during schema checking.
* Preserve existing columns, indexes, and records.
* Only add the exact missing table/column/index required.
* Prefer `CREATE TABLE IF NOT EXISTS` and existence checks before `ALTER TABLE`.
* Integrate schema creation into the existing `ensure_table()` mechanism instead of creating a separate migration system unless explicitly requested.
* Any automatically created schema must remain permanently available on future page reloads/visits.

**Rule:** `ensure_table()` may create or extend schema when missing, but must never destroy or recreate existing schema/data.

**Default response style: direct, concise, implementation-focused, and token-efficient.**
