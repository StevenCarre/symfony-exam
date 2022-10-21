<?php

namespace App\Service;

class Common
{
    /**
     * returns an array containing all values of an array in all of its depths, all at the same depth
     * @param string[] $array
     * @return string[]
     */
    public static function boo(array $array): array
    {
        $result = [];
        array_walk_recursive($array, function ($a) use (&$result) {
            $result[] = $a;
        });

        return $result;
    }

    /**
     * returns an array containing all keys and values of first array, and keys 'k' and 'v' of second array
     * @param string[] $array1
     * @param string[] $array2
     * @return string[]
     */
    public static function foo(array $array1, array $array2): array
    {
        return [...$array1, $array2['k'] => $array2['v']];
    }

    /**
     * returns true if none of the keys of first array is a key or a value of second array
     * @param string[] $array1
     * @param string[] $array2
     * @return bool
     */
    public static function bar(array $array1, array $array2): bool
    {
        $r = array_filter(array_keys($array1), fn ($k) => !in_array($k, $array2));

        return count($r) == 0;
    }
}
