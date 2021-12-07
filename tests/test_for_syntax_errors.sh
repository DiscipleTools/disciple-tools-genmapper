#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")/../"

if [ "$(php -r 'echo version_compare( phpversion(), "7.0", ">=" ) ? 1 : 0;')" != 1 ] ; then
    php -l ../disciple-tools-plugin-starter-template.php
    exit
fi

found_error=0

while read -d '' filename ; do

    # php -l checks the file for syntax errors
    php -l "$filename" || found_error=1

done < <(find . -path ./vendor -prune -o -name "*.php" -print0)

exit $found_error
