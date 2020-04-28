#!/bin/bash

tsc

sass --scss --style compressed --update ./styles/src/:./styles/built/
