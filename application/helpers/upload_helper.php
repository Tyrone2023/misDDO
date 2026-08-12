<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reduce an uploaded file's name to characters that are safe on disk, safe in a
 * URL, and safe to send through the WAF that sits in front of the live site.
 *
 * Scanned education documents routinely arrive named things like
 * "TOR & Diploma (Bachelor's) #2.pdf" or "TOR Ñoño.pdf". CodeIgniter's upload
 * library only turns whitespace into underscores, so every other character used
 * to survive into uploads/regfile - which then broke the viewer URL (an unescaped
 * '#' truncates it) and made the request look like an injection attempt to
 * mod_security, which answers with a bare 403 page.
 *
 * @param  string $filename Original client-supplied name.
 * @return string Name containing only [A-Za-z0-9._-], extension preserved.
 */
if (!function_exists('safe_upload_name')) {
    function safe_upload_name($filename)
    {
        $filename = (string) $filename;

        // Never trust a client-supplied path - keep the basename only.
        $filename = str_replace('\\', '/', $filename);
        $filename = substr(strrchr('/' . $filename, '/'), 1);

        $ext = '';
        if (($dot = strrpos($filename, '.')) !== FALSE) {
            $ext      = strtolower(preg_replace('/[^A-Za-z0-9]/', '', substr($filename, $dot + 1)));
            $filename = substr($filename, 0, $dot);
        }

        // Transliterate what we can (Ñ -> N) before dropping the rest.
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT', $filename);
            if ($converted !== FALSE) {
                $filename = $converted;
            }
        }

        $filename = preg_replace('/[^A-Za-z0-9]+/', '_', $filename);
        $filename = trim($filename, '_');

        if ($filename === '') {
            $filename = 'file';
        }

        // Very long names are their own source of trouble on shared hosting.
        $filename = substr($filename, 0, 80);

        return $ext === '' ? $filename : $filename . '.' . $ext;
    }
}

/**
 * Largest upload this PHP install can actually accept, in bytes.
 *
 * Used to tell the browser what to reject up front, so an oversized scan never
 * leaves the user's machine and they get a readable message instead of the web
 * server's raw 403/413 page.
 *
 * @return int
 */
if (!function_exists('max_upload_bytes')) {
    function max_upload_bytes()
    {
        $to_bytes = function ($value) {
            $value = trim((string) $value);
            if ($value === '') {
                return 0;
            }

            $unit   = strtolower(substr($value, -1));
            $number = (int) $value;

            switch ($unit) {
                case 'g':
                    return $number * 1024 * 1024 * 1024;
                case 'm':
                    return $number * 1024 * 1024;
                case 'k':
                    return $number * 1024;
                default:
                    return $number;
            }
        };

        $limits = array_filter(array(
            $to_bytes(ini_get('upload_max_filesize')),
            $to_bytes(ini_get('post_max_size')),
        ));

        // 0 / unset means "no limit" for that directive, so ignore it.
        return empty($limits) ? 0 : min($limits);
    }
}
