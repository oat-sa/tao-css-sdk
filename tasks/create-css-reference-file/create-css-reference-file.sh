#!/bin/sh

CWD=`dirname $0`

echo "title CSS SDK: Generate CSS reference file"

HAS_PHP=`which php`

if [ "${HAS_PHP}" = "" ]; then
{
    echo "Please install the PHP runtime (ie. php5-cli package)"
    exit 1
}
fi

php "${CWD}/create-css-reference-file.php"

exit 0
