<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RunCommand extends Command
{
    function __construct()
    {
        parent::__construct();
        $this
            ->setName('run')
            ->setDescription('Run each PHP block in a markdown file and return an error when one fails.')
            ->addArgument('markdownFile', InputArgument::REQUIRED, 'Markdown file to run')
            ->addArgument('preludeFile', InputArgument::OPTIONAL, 'A PHP file to run before each code block. Useful for imports and other setup code.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $markdown = $input->getArgument('markdownFile');
        $prelude = $input->getArgument('preludeFile');
        $upToDocs = new UpToDocs();
        return $upToDocs->run($markdown, $prelude) ? Command::SUCCESS : Command::FAILURE;
    }
}