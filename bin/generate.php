#!/usr/bin/env php
<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use DMT\Ubl\Generate\CodeGenerator;

(new CodeGenerator)->generate(
        __DIR__ . '/../schema/xsd/maindoc/',
        __DIR__.'/../dist/'
);
