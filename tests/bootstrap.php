<?php

require __DIR__ . '/../vendor/autoload.php'; // composer autoload

/**
 * Define test helpers
 */

function clearRepository()
{
    $files = array_merge(glob(__DIR__ . '/Json/countries/*.json'), glob(__DIR__ . '/Json/regions/*.json'));
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); //delete file
        }
    }
}
