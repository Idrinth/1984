<?php

// @phan-file-suppress PhanPluginShortArray,PhanPluginCanUseReturnType,PhanPluginCanUseParamType

/**
 * @param string[] $hosts
 * @param string $api
 * @param string $protocol
 * @param string $data
 * @param string $key
 * @param string $user
 * @return boolean
 */
function transmit(array $hosts, $api, $protocol, $data, $key, $user)
{
    $host = $hosts[rand(0, count($hosts) - 1)];
    if (extension_loaded('curl')) {
        $c = curl_init();
        curl_setopt_array($c, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_URL => "$protocol://$host/$api.php",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'CONTENT-TYPE: text/plain',
                "ANYTHINGGOES: $key",
                "LOGGEDUSER: $user",
                'LOGTYPE: bash'
            )
        ));
        $result = curl_exec($c);
        curl_close($c);
        return $result;
    }
    return file_get_contents("$protocol://$host/$api.php", false, stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => array(
                'CONTENT-TYPE: text/plain',
                "ANYTHINGGOES: $key",
                "LOGGEDUSER: $user",
                'LOGTYPE: bash',
                'Connection: close'
            ),
            'content' => $data
        ),
    )));
}