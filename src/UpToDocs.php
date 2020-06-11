<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Extension\CommonMarkCoreExtension;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class UpToDocs
{
    const TIMEOUT = 5;
    private DocParser $parser;

    function __construct()
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $this->parser = new DocParser($environment);
    }

    /**
     * Runs the
     */
    public function run(string $markdownFile, string $preludeFile = null): bool
    {
        $input = file_get_contents($markdownFile);

        if (is_null($preludeFile)) {
            $prelude = "<?php\n";
            $workingDir = dirname($markdownFile);
        } else {
            $prelude = file_get_contents($preludeFile);
            $workingDir = dirname($preludeFile);
        }

        $document = $this->parser->parse($input);
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();
            if (
                $node instanceof FencedCode
                && $event->isEntering()
                && $node->getInfo() === 'php') {

                $code = $prelude . "\n" . self::dropOpeningTag($node->getStringContent());
                $process = new Process(['php'], $workingDir, null, $code, self::TIMEOUT);

                try {
                    $process->mustRun();
                } catch (ProcessFailedException $exception) {
                    $location = realpath($markdownFile).":".$node->getStartLine();
                    echo "The following code block in $location failed.\n";
                    if (!is_null($preludeFile)) {
                        echo "(using prelude $preludeFile)\n";
                    }
                    echo $node->getStringContent() . "\n";
                    echo "==================\n";
                    echo $exception->getProcess()->getErrorOutput();
                    return false;
                }
            }
        }

        return true;
    }

    private static function dropOpeningTag(string $phpCode): string
    {
        return str_replace("<?php", "", $phpCode);
    }

}

