<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Verraes\UpToDocs\RunCommand;

$application = new Application("UpToDocs", "1.0.0");
$application->add(new RunCommand());
$application->run();