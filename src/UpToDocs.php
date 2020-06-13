<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\CommonMarkCoreExtension;
use League\CommonMark\Node\NodeWalkerEvent;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class UpToDocs
{
    const TIMEOUT = 5;
    private DocParser $parser;
    private string $before = "<?php";
    private string $after = "";
    private ?string $workingDir = null;
    private Report $report;

    function __construct(Report $report)
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $this->parser = new DocParser($environment);
        $this->report = $report;
    }

    /**
     * Execute before each code block
     */
    public function before(string $beforeFile): UpToDocs
    {
        $this->before = file_get_contents($beforeFile);
        if (is_null($this->workingDir)) {
            $this->workingDir(dirname($beforeFile));
        }
        return $this;
    }

    /**
     * The working directory to execute the code in
     */
    public function workingDir(string $workingDir): UpToDocs
    {
        $this->workingDir = $workingDir;
        return $this;
    }

    /**
     * Execute after each code block
     */
    public function after(string $afterFile): UpToDocs
    {
        $this->after = self::dropOpeningTag(file_get_contents($afterFile));
        return $this;
    }

    private static function dropOpeningTag(string $phpCode): string
    {
        return str_replace(["<?php", "declare(strict_types=1);"], "", $phpCode);
    }

    /**
     * Run each code block in the markdownFile, output to STDOUT.
     *
     * @return bool True for successfully running all code blocks, false when one of them fails.
     */
    public function run(string $markdownFile): bool
    {
        $input = file_get_contents($markdownFile);
        if (is_null($this->workingDir)) {
            $this->workingDir = dirname($markdownFile);
        }

        $document = $this->parser->parse($input);
        $walker = $document->walker();
        $success = true;
        while ($event = $walker->next()) {
            if (self::isPHPBlock($event)) {
                $node = $event->getNode();

                $codeBlock = $node->getStringContent();
                $code = $this->before . "\n" . self::dropOpeningTag($codeBlock) . "\n" . $this->after;
                $process = new Process(['php'], $this->workingDir, null, $code, self::TIMEOUT);

                try {
                    $process->mustRun();
                    $this->report->success();
                } catch (ProcessFailedException $exception) {
                    $this->report->failure($markdownFile, $node->getStartLine(), $exception->getProcess()->getErrorOutput());
                    $success = false;
                }
            }
        }
        return $success;
    }

    private static function isPHPBlock(NodeWalkerEvent $event): bool
    {
        return $event->getNode() instanceof FencedCode
            && $event->isEntering()
            && $event->getNode()->getInfo() === 'php';
    }
}