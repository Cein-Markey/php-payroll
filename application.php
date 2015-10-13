#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Acme\Console\Command\PayrollCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new PayrollCommand());
$application->run();