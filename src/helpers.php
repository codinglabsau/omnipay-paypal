<?php

/**
 * @param array $vars
 * @return void
 */
function dd(...$vars)
{
    foreach ($vars as $var) {
        var_dump($var);
    }
    exit;
}
