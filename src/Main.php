<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

use Symfony\Component\Console\Application;

final class Main
{
    static function main(): int
    {
        $application = new Application("UpToDocs", "1.0.0");
        $application->add(new RunCommand());
        return $application->run();
    }
}
