#!/usr/bin/env bash

cd $(dirname $0)/../
mkdir -p schema
cd schema

for version in 2.1; do
  if [ ! -f UBL-$version.zip ]; then
    wget https://docs.oasis-open.org/ubl/os-UBL-$version/UBL-$version.zip -O UBL-$version.zip
  fi

  unzip -o UBL-$version.zip 'xsd/*' 'xsdrt/*'

done
