#!/usr/bin/env php
<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use DMT\Ubl\Generate\CodeGenerator;

(new CodeGenerator)->generate(
        realpath(__DIR__ . '/../schema/'),
        realpath(__DIR__.'/../dist/')
);
