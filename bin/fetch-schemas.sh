#!/usr/bin/env bash

cd $(dirname $0)/../
mkdir -p schema

for version in 2.1 2.2 2.3 2.4 2.5 2.6; do
  wget https://docs.oasis-open.org/ubl/os-UBL-$version/UBL-$version.zip -O schema/UBL-$version.zip
done
