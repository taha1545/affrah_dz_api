<?php

class Filter
{
    protected static $operation = [
        'eq'  => '=',
        'bt'  => '>',
        'ls'  => '<',
        'btq' => '>=',
        'lsq' => '<=',
        'like'=>'LIKE'
    ];

    public static function Filterquery($query, $table = null)
    {
        $rules = [];

        if ($table == "annonce") {
            // Only include specific mappings
            $query = [
                'nom_an'   => $query['name'] ?? null,
                'tarif_an' => $query['price'] ?? null,
                'type_b'   => $query['type'] ?? null,
                'categorie_an'=>$query['category'] ?? null,
                'ville_an'=>$query['city'] ?? null,
            ];

            // Remove null values
            $query = array_filter($query, function ($value) {
                return $value !== null;
            });
        }

        foreach ($query as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if (isset(self::$operation[$subKey])) {
                        $operator = self::$operation[$subKey];
                        $rules[] = [$key, $operator, trim($subValue, '"')]; // Remove extra quotes
                    } else {
                        $rules[] = [$key, '=', trim($subValue, '"')];
                    }
                }
            } else {
                $rules[] = [$key, '=', trim($value, '"')];
            }
        }

        return $rules;
    }
}
