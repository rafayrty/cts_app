<?php

if (! function_exists('make_slug')) {
    /**
     * Create a slug (Supports arabic)
     *
     * @param $value string the string to be converted
     * @return string
     */
    function make_slug($value)
    {
        $slug = '';
        $stringParts = array_values(array_filter(explode(' ', $value)));
        foreach ($stringParts as $key => $string) {
            $hyphen = ($key < count($stringParts) - 1) ? '-' : '';
            $slug .= $string.$hyphen;
        }

        return $slug;
    }
}

if (! function_exists('search_key')) {
    /**
     * Create a slug (Supports arabic)
     *
     * @param $array<mixed>,$search the string to be searched
     * @return string
     */
    function search_key($array, $search)
    {
        $key = null;
        foreach ($array as $key => $value) {
            if ($value['name'] == $search) {
                return $key;
            }
        }
    }
}
