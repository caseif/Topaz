#!/bin/bash

tsc --watch &
ts_pid=$!

sass --scss --style compressed --watch ./styles/src/:./styles/built/ &
sass_pid=$!

echo $ts_pid
echo $sass_pid

function finish {
    kill $ts_pid
    kill $sass_pid
}

trap finish EXIT

while true; do sleep 100; done
