<?php

class Filter {

    protected static $operation = [
        'eq'  => '=',
        'bt'  => '>',
        'ls'  => '<',
        'btq' => '>=',
        'lsq' => '<=',
    ];

    public static function Filterquery($query) {
        $rules = [];

        foreach ($query as $key => $value) {

    
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if (isset(self::$operation[$subKey])) {
                        $operator = self::$operation[$subKey];
                        $rules[] = [$key, $operator, $subValue];
                    } else {
                        $rules[] = [$key, '=', $subValue];
                    }
                }
            } else {
                if (isset(self::$operation[$key])) {
                    $operator = self::$operation[$key];
                    $rules[] = [$key, $operator, $value];
                } else {
                    $rules[] = [$key, '=', $value];
                }
            }
        }

        return $rules;
    }
}