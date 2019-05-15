<?php

if (!function_exists('dd')) {
    /**
     * @param array $vars
     * @return void
     */
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die(1);
    }
}
