<?php

function fileCache($dir, $file, $var)
{
    $_dir = $dir;
    if (!is_dir($_dir)) {
        if (!mkdirs($_dir)) {
            return false;
        }
    }
    $file = $_dir . '/' . $file;
    $sh = fopen($file, "w");
    fwrite($sh, $var, strlen($var));
    fclose($sh);
    return true;
}