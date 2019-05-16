<?php

namespace API\Kufar;

abstract class Finder
{
    /**
     * Find a value
     * in the array and
     * return found value key.
     *
     * @param string  $value
     * @param mixed[] $values
     * @return mixed
     */
    public static function find(string $value, array $values)
    {
        $value = strtolower($value);
        $shortest = -1;

        foreach ($values as $k => $v) {
            $levenshtein = levenshtein(strtolower($v), $value);

            if (0 === $levenshtein) {
                return $k;
            }

            if ($levenshtein <= $shortest || 0 > $shortest) {
                $closest = $k;
                $shortest = $levenshtein;
            }
        }

        return $closest;
    }
}
