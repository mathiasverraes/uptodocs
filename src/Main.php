<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

final class Main
{
    static function main(): void
    {
        $application = new Application("UpToDocs", "1.0.0");
        $application->add(new RunCommand());
        $application->run();

    }
}
