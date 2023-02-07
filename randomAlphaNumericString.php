<?php

function randomAlphaNumericString(int $length)
{
    $chars = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    $out = '';
    while (strlen($out) < $length) {
        shuffle($chars);
        $out .= $chars[rand(0, 61)];
    }
    return $out;
}
