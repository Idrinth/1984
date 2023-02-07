#!/bin/sh

export TARGET_API=###TARGET_API###
export TARGET_PROTOCOL=###TARGET_PROTOCOL###
export TARGET_HOST=###TARGET_HOST###
export TARGET_KEY=###TARGET_KEY###
export LOCAL_CRYPT=###LOCAL_CRYPT###
export LOCAL_IV=###LOCAL_IV###
export LOCAL_PASS=###LOCAL_PASS###

apt-get install php-cli openssl php-curl &>/dev/null &2>/dev/null

php `basename "$0" .sh`.php &>/dev/null &