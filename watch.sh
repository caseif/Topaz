#!/bin/bash

# This script will only compile TypeScript and SASS source files - the distribution tarball will not be generated!

set -e

tsc --watch &
ts_pid=$!

sass --scss --style compressed --watch ./src/styles/src/:./src/styles/built/ &
sass_pid=$!

echo $ts_pid
echo $sass_pid

function finish {
    kill $ts_pid
    kill $sass_pid
}

trap finish EXIT

while true; do sleep 100; done
