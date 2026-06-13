<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('rqa_applicant_name')) {
    function rqa_applicant_name($person)
    {
        $lastName = trim((string) ($person->LastName ?? ''));
        $firstName = trim((string) ($person->FirstName ?? ''));
        $middleName = trim((string) ($person->MiddleName ?? ''));
        $nameExtn = trim((string) ($person->NameExtn ?? ''));

        $givenNames = trim(implode(' ', array_filter([$firstName, $middleName], static function ($value) {
            return $value !== '';
        })));

        $name = $lastName;

        if ($givenNames !== '') {
            $name .= ($name !== '' ? ', ' : '') . $givenNames;
        }

        if ($nameExtn !== '') {
            $name .= ($name !== '' ? ' ' : '') . $nameExtn;
        }

        return strtoupper(preg_replace('/\s+/', ' ', trim($name)));
    }
}
