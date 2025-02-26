<?php

namespace App\Utils;

class Util
{
    /**
     * Array to string for query
     */
    static public function arrayToString(array $params): string
    {
        return array_reduce($params, function ($carry, &$item) {
            if (is_array($item)) {
                foreach ($item as &$value) {
                    if (gettype($value) == 'string') {
                        $value = "'$value'";
                    }
                }
                $item = "(" . implode(',', $item) . ")";
            } elseif (gettype($item) == 'string') {
                $item = "'$item'";
            }
            $carry .= ($carry ? ',' : '') . $item;
            return $carry;
        }) ?? '';
    }

    /**
     * Check string for date
     */
    static public function isDate(string $str): bool
    {
        return !empty(trim($str)) && is_numeric(strtotime($str));
    }
}