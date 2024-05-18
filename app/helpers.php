<?php

if (! function_exists('make_slug')) {
    /**
     * Create a slug (Supports arabic)
     *
     * @param  $value  string the string to be converted
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
     * @param  $array<mixed>,$search  the string to be searched
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

if (! function_exists('pt2px')) {
    /**
     * Convert pt to px
     *
     * @param  $points,$dpi  the dpi
     * @return string
     */
    function pt2px($points, $dpi = 96)
    {
        return $points * $dpi / 72;
    }
}

if (! function_exists('component_exists')) {
    function component_exists($class)
    {
        $manifest = app(\Livewire\LivewireComponentsFinder::class)->getManifest();

        return (bool) array_search($class, $manifest);
    }
}
function split_name($name)
{
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim(preg_replace('#'.preg_quote($last_name, '#').'#', '', $name));

    return [$first_name, $last_name];
}

function clear_cgi_cache()
{
    $nginx_cache_path = config('app.nginx_path').'/cache';
    array_map('unlink', glob($nginx_cache_path.'/*/*/*'));
}
