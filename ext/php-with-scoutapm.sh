#!/usr/bin/env bash

set -euxo pipefail

cd "$(dirname "$0")/../"

PHP_PATH=$(which php)

WORKING_DIRECTORY=$(pwd)
PHP_API=$($PHP_PATH -i | grep 'PHP API' | sed -e 's/PHP API => //')
THREAD_SAFETY=$($PHP_PATH -i | grep 'Thread Safety' | sed -e 's/Thread Safety => //')

if [ "$THREAD_SAFETY" == "enabled" ]; then
#  ZTS_FLAG="-zts"
  ZTS_FLAG="" # intentional break to check CI fails if ext does not load
else
  ZTS_FLAG=""
fi

EXTPATH="$WORKING_DIRECTORY/ext/scoutapm-$PHP_API$ZTS_FLAG.so"

if [[ ! -f $EXTPATH ]]; then
  echo "Extension was not found for PHP API $PHP_API (at $EXTPATH)"
  exit 1
fi

echo "PHP API version is: $PHP_API"
echo "Thread safety is: $THREAD_SAFETY"
echo "Extension path is: $EXTPATH"

$PHP_PATH -d zend_extension=$EXTPATH -r "if (!extension_loaded('scoutapm')) { echo 'ERROR - Scout APM extension failed to load' . PHP_EOL; } exit(extension_loaded('scoutapm')?0:1);"

$PHP_PATH -d zend_extension=$EXTPATH "$@"
