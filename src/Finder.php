<?php

namespace API\Kufar;

abstract class Finder
{
    /**
     * Suggest an array key.
     *
     * @param string  $query
     * @param mixed[] $suggestions
     * @return mixed
     */
    public static function suggestKey(string $query, array $suggestions)
    {
        $query = trim(mb_strtolower($query));

        $map = [];
        foreach ($suggestions as $key => $value) {
            if ($query === $value) {
                return $key;
            }

            similar_text($query, trim(mb_strtolower($value)), $percent);
            $map[$key] = $percent;
        }

        return array_search(max(array_values($map)), $map);
    }

    /**
     * Suggest an array value.
     *
     * @param string  $query
     * @param mixed[] $suggestions
     * @return string
     */
    public static function suggestValue(string $query, array $suggestions)
    {
        $query = trim(mb_strtolower($query));

        $map = [];
        foreach ($suggestions as $key => $value) {
            if ($query === $value) {
                return $value;
            }

            similar_text($query, trim(mb_strtolower($value)), $percent);
            $map[$key] = $percent;
        }

        return $suggestions[array_search(max(array_values($map)), $map)];
    }
}
