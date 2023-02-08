<?php

/**
 * @suppress PhanPluginCanUseParamType, PhanPluginCanUseReturnType
 * @param int $length
 * @return string
 */
function randomAlphaNumericString($length)
{
    $chars = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    $out = '';
    while (strlen($out) < $length) {
        $out .= $chars[rand(0, 61)];
    }
    return $out;
}
