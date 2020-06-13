<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class RunCommand extends Command
{
    function __construct()
    {
        parent::__construct();
        $this
            ->setName('run')
            ->setDescription('Run each PHP block in a markdown file and return an error when one fails.')
            ->addArgument('markdownFile', InputArgument::REQUIRED, 'Markdown file to run.')
            ->addOption('before', 'b',  InputOption::VALUE_REQUIRED, 'A PHP file to run before each code block. Useful for imports and other setup code.', null)
            ->addOption('after', 'a',  InputOption::VALUE_REQUIRED, 'A PHP file to run after each code block. Useful for cleanup, and for running assertions.', null)
            ->addOption('output', 'o',  InputOption::VALUE_REQUIRED, 'Output format. Available formats: console|github.', 'console')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $beforeFile = $input->getOption('before');
        $afterFile = $input->getOption('after');
        $report = self::report($input->getOption('output'));
        $markdown = $input->getArgument('markdownFile');

        $upToDocs = new UpToDocs($report);
        if($beforeFile) $upToDocs->before($beforeFile);
        if($afterFile) $upToDocs->before($afterFile);

        $result = $upToDocs->run($markdown);

        $report->print();
        return $result ? Command::SUCCESS : Command::FAILURE;
    }

    private static function report(string $format) : Report
    {
       switch($format) {
           case 'console': return new ConsoleReport();
           case 'github': return new GithubReport();
           default: throw new \Exception("Unknown output format.");
       }
    }
}