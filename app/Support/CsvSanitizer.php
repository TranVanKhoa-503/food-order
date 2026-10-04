<?php

namespace App\Support;

class CsvSanitizer
{
    /**
     * Dangerous formula triggers in spreadsheet applications (Excel, Calc, Sheets).
     *
     * @var array<int, string>
     */
    protected const DANGEROUS_PREFIXES = ['=', '+', '-', '@', "\t", "\r"];

    /**
     * Sanitize a cell value to prevent CSV Formula Injection (CWE-1236).
     *
     * Values that begin with dangerous formula triggers (even preceded by
     * whitespace or control characters) are prepended with a single quote (').
     */
    public static function sanitize(mixed $value): mixed
    {
        if ($value === null || is_int($value) || is_float($value) || is_bool($value)) {
            return $value;
        }

        $stringValue = (string) $value;

        if ($stringValue === '') {
            return '';
        }

        // Strip leading whitespace and ASCII control characters to check the effective leading char
        $trimmed = ltrim($stringValue, " \t\n\r\0\x0B");

        if ($trimmed !== '') {
            $firstChar = mb_substr($trimmed, 0, 1, 'UTF-8');

            if (in_array($firstChar, self::DANGEROUS_PREFIXES, true)) {
                return "'".$stringValue;
            }
        }

        return $stringValue;
    }

    /**
     * Sanitize an entire CSV row of cells.
     *
     * @param  array<int|string, mixed>  $row
     * @return array<int|string, mixed>
     */
    public static function sanitizeRow(array $row): array
    {
        return array_map([self::class, 'sanitize'], $row);
    }
}
