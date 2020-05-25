#!/bin/bash

tsc

sass --scss --style compressed --update ./src/styles/src/:./src/styles/built/
