# PHP mime_content_type() Compatibility Fix

## Problem
The error `Call to undefined function PhpOffice\PhpSpreadsheet\Worksheet\mime_content_type()` occurs because the `mime_content_type()` function was deprecated in PHP 5.3.0 and removed entirely in later versions. This function is not available on many production servers, causing the Brigada export feature to fail online while working locally.

## Solution
I've implemented a fallback mechanism that tries multiple methods to detect MIME types, in order of preference:

1. **mime_content_type()** - If available (legacy support)
2. **finfo_file()** - The recommended modern method (available in most PHP installations)
3. **File extension mapping** - Ultimate fallback for basic image type detection

## Files Modified

### 1. `/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Drawing.php`
- Added `getMimeType()` helper method that handles the fallback logic
- Updated `isImage()` method to use the new helper instead of direct `mime_content_type()` call
- This fixes the primary error in the Brigada export

### 2. `/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Html.php`
- Added `getMimeType()` helper method to the Html class
- Updated image embedding code to use the new helper method
- Prevents errors when embedding images in HTML exports

### 3. `/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/Csv.php`
- Added `getMimeType()` helper method to the Csv class
- Updated file type detection to use the new helper method
- Ensures CSV file validation works on all servers

## How It Works

The new `getMimeType()` methods follow this logic:

```
1. Check if mime_content_type() exists → Use it
2. Check if finfo_file() exists → Use it (RECOMMENDED)
3. Otherwise → Map file extension to MIME type
```

### Supported Image Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- BMP (.bmp)
- TIFF (.tiff, .tif)
- SVG (.svg)
- WebP (.webp)
- ICO (.ico)

### Supported Document Formats (CSV Reader)
- CSV (.csv, .tsv)
- Text (.txt)

## Verification

To verify the fix works:

1. **Enable finfo extension** (Recommended - usually enabled by default):
   - Most modern PHP installations have `php-fileinfo` enabled
   - Check: `php -m | grep fileinfo`

2. **Test the Brigada export** with an image in the report:
   - Go to the Brigada module
   - Generate a report with logo images
   - Export to Excel - should work without errors

3. **Check error logs**:
   - No more "Call to undefined function mime_content_type()" errors

## Requirements

- **Minimum**: PHP 5.3.0+ (which removed mime_content_type())
- **Recommended**: PHP 7.2+ with fileinfo extension enabled

## Additional Notes

- The `finfo` extension is part of the standard PHP library and is usually enabled by default
- If a file extension is not recognized, the code defaults to a safe MIME type
- All modifications maintain backward compatibility with local environments
- The fix handles both file-based and URL-based images

## Rollback

If you need to rollback, the changes only affect three vendor files. Simply restore them from the original phpspreadsheet package.
