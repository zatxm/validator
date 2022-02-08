<?php

namespace Zatxm\Validation;

use ArrayAccess;
use Closure;

class Support
{
    /**
     * Return array specific item.
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function arrGet($array, $key, $default = null)
    {
        if (!static::arrAccessible($array)) {
            return static::value($default);
        }
        if (is_null($key)) {
            return $array;
        }
        if (static::arrKeyExists($array, $key)) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if (static::arrAccessible($array) && static::arrKeyExists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return static::value($default);
            }
        }
        return $array;
    }

    /**
     * Check input is array accessable.
     * @param  mixed $value
     * @return bool
     */
    public static function arrAccessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Check array key exists.
     * @param  array  $array
     * @param  string $key
     * @return bool
     */
    public static function arrKeyExists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     */
    public static function arrDot($array, $prepend = '')
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::arrDot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }

    /**
     * Get the first element of an array. Useful for method chaining.
     * @param  array $array
     * @return mixed
     */
    public static function arrHead($array)
    {
        return reset($array);
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     * @param  \ArrayAccess|array $array
     * @param  string             $key
     * @return bool
     */
    public static function arrHas($array, $key)
    {
        if (!$array) {
            return false;
        }
        if (is_null($key)) {
            return false;
        }
        if (static::arrKeyExists($array, $key)) {
            return true;
        }
        foreach (explode('.', $key) as $segment) {
            if (static::arrAccessible($array) && static::arrKeyExists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Filter the array using the given callback.
     * @param  array    $array
     * @param  callable $callback
     * @return array
     */
    public static function arrWhere($array, callable $callback)
    {
        $filtered = [];
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     * If no key is given to the method, the entire array will be replaced.
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function arrSet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
        return $array;
    }

    /**
     * Convert a string to snake case.
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
    public static function strSnake($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }
        return $value;
    }

    /**
     * Determine if a given string contains a given substring.
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function strContains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string starts with a given substring.
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function strStartsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     * @param  string       $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function strEndsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === static::strSubstr($haystack, -static::strLen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the length of the given string.
     * @param  string $value
     * @return int
     */
    public static function strLen($value)
    {
        return mb_strlen($value);
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     * @param  string   $string
     * @param  int      $start
     * @param  int|null $length
     * @return string
     */
    public static function strSubstr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Determine if a given string matches a given pattern.
     * @param  string $pattern
     * @param  string $value
     * @return bool
     */
    public static function strIs($pattern, $value)
    {
        if ($pattern == $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');
        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = '#^' . str_replace('\*', '.*', $pattern) . '\z#u';

        return (bool)preg_match($pattern, $value);
    }

    /**
     * Convert a value to studly caps case.
     * @param  string  $value
     * @return string
     */
    public static function strStudly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * Return the default value of the given value.
     * @param  mixed $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * Set an item on an array or object using dot notation.
     * @param  mixed        $target
     * @param  string|array $key
     * @param  mixed        $value
     * @param  bool         $overwrite
     * @return mixed
     */
    public static function dataSet(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (!static::arrAccessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    static::dataSet($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (static::arrAccessible($target)) {
            if ($segments) {
                if (!static::arrKeyExists($target, $segment)) {
                    $target[$segment] = [];
                }
                static::dataSet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !static::arrKeyExists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                static::dataSet($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                static::dataSet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}
