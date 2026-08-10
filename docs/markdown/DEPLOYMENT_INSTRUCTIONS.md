# Deployment Instructions for mime_content_type() Fix

## What You Need to Know

Your Brigada export feature is failing online because the server doesn't have the `mime_content_type()` PHP function. This is common on modern servers where the function was removed in PHP 5.3+.

## The Fix Applied

I've modified **3 vendor files** from phpspreadsheet to use modern MIME type detection methods:

1. `vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Drawing.php`
2. `vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Html.php`
3. `vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/Csv.php`

Each file now includes a smart fallback mechanism:
- Tries `mime_content_type()` first (if available)
- Falls back to `finfo_file()` (recommended modern method)
- Uses file extension mapping as last resort

## How to Deploy

### Option 1: Direct File Upload (Recommended)
Upload these 3 modified files to your online server:
```
/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Drawing.php
/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Html.php
/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/Csv.php
```

### Option 2: Using Git/Version Control
```bash
git add vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Drawing.php
git add vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Html.php
git add vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/Csv.php
git commit -m "Fix: Add mime_content_type() fallback for modern PHP versions"
git push origin main
```

### Option 3: Server Configuration (Alternative)
If you prefer not to modify vendor files, you can enable the deprecated function on your server:
- Contact your hosting provider to enable `mime_content_type()` in php.ini
- Or ask them to ensure `php-fileinfo` extension is enabled (preferred)

## Testing After Deployment

1. Log in to your MIS system
2. Go to **Brigada module**
3. Generate a report that includes images/logos
4. Try to **Export to Excel**
5. The export should now work without errors

## Troubleshooting

### If export still fails:
1. Check your server's error logs for the specific error
2. Verify `php-fileinfo` extension is enabled: `php -m | grep fileinfo`
3. Contact hosting support to enable the extension if missing

### If you see different MIME type errors:
1. Verify the uploaded files are correct
2. Check file permissions (should be 644)
3. Clear any application cache if present

## Server Requirements

- **PHP Version**: 5.3.0 or higher (5.6+ recommended)
- **Extensions**: `fileinfo` (usually enabled by default)
- **Disk Space**: Minimal (3 small files, ~10KB total)

## Why This Works

Modern PHP uses `finfo_file()` to detect MIME types. It's part of the standard PHP library and works on virtually all servers. Our fallback code:

1. Checks for `mime_content_type()` for compatibility with older systems
2. Uses `finfo_file()` as the primary method (safe, modern)
3. Falls back to file extension mapping for edge cases

## Support

If you have any issues after deployment:
1. Check `MIME_TYPE_FIX.md` for technical details
2. Review your server's PHP error logs
3. Contact your hosting provider about PHP extension availability

---
**Deployment Date**: May 30, 2026
**Affected Modules**: Brigada (Report Export)
**Priority**: High (Fixes broken export functionality)
