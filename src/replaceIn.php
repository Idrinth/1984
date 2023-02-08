<?php

// @phan-file-suppress PhanPluginCanUseParamType,PhanPluginCanUsePHP71Void,PhanPluginCanUseReturnType

/**
 * @param string $file
 * @param string|string[] $search
 * @param string|string[] $replace
 * @return void
 */
function replaceIn($file, $search, $replace)
{
    $lastModified = filemtime($file) ?: 0;
    $lastAccessed = fileatime($file) ?: 0;
    file_put_contents($file, str_replace($search, $replace, file_get_contents($file)?: ''));
    if ($lastAccessed + $lastModified > 0) {
        touch($file, $lastAccessed, $lastModified);
    }
}

/**
 * @param string $file
 * @param string $data
 * @param bool $append
 * @return void
 */
function putIn($file, $data, $append = false)
{
    $lastModified = filemtime($file) ?: 0;
    $lastAccessed = fileatime($file) ?: 0;
    file_put_contents($file, $data, $append ? FILE_APPEND : 0);
    if ($lastAccessed + $lastModified > 0) {
        touch($file, $lastAccessed, $lastModified);
    }
}

/**
 * @param string $file
 * @return string
 */
function getOut($file)
{
    if (!is_file($file)) {
        return '';
    }
    $lastModified = filemtime($file) ?: 0;
    $lastAccessed = fileatime($file) ?: 0;
    $data = file_get_contents($file) ?: '';
    touch($file, $lastAccessed, $lastModified);
    return $data;
}