<?php

namespace De\Idrinth\Project1984;

final class SSL
{
    public const CRYPT = 'blowfish';

    public static function encrypt(string $data, string $pass, string $iv): string
    {
        return openssl_encrypt($data, self::CRYPT, $pass, 0, $iv);
    }
    public static function str2hex(string $string): string
    {
        $out = '';
        foreach (str_split($string) as $char) {
            $out .= sprintf("%02X", ord($char));
        }
        return $out;
    }
}
