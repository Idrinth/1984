<?php

// @phan-suppress-next-line PhanPluginUnsafeEval
eval(openssl_decrypt(
    base64_decode('###DATA###'),
    getenv('LOCAL_CRYPT'),
    getenv('LOCAL_PASS'),
    OPENSSL_RAW_DATA,
    getenv('LOCAL_IV')
));
