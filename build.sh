#!/bin/bash

tar_output="build/topaz_dist.tar.xz"

echo "Starting build..."

# exit on error
set -e

# compile TypeScript sources
echo "Compiling TypeScript sources..."
tsc

# compile SCSS sources
echo "Compiling SCSS sources..."
sass --scss --style compressed --update ./src/styles/src/:./src/styles/built/

# create final tarball
echo "Creating final tarball..."
mkdir -p "build"
pushd . > /dev/null
cd "./src/"
tar -cJf "../$tar_output" * --exclude="scripts/src" --exclude="styles/src"
popd > /dev/null

echo "Done!"
echo "An archive has been created at $tar_output"
