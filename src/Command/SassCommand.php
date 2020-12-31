<?php
declare(strict_types=1);

namespace CakephpRollup\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * BuildSass command.
 */
class SassCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->addOption('watch', [
                'short' => 'w',
                'help' => 'Turn on watch mode',
                'boolean' => true,
                'default' => false
            ])
            ->addOption('dir', [
                'short' => 'd',
                'help' => 'Sub directory name. Relative to WWW_ROOT',
                'boolean' => false,
                'default' => ''
            ]);
        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $base_directory = $args->getOption('dir');
        if ($base_directory) {
            $sass_directory = WWW_ROOT . $base_directory . DS . 'scss';
            $css_directory = WWW_ROOT . $base_directory . DS . 'css';
        } else {
            $sass_directory = WWW_ROOT . 'scss';
            $css_directory = WWW_ROOT . 'css';
        }

        $webroot = new \DirectoryIterator($sass_directory);
        $files_parameter = '';
        foreach ($webroot as $scss) {
            if (preg_match('/^[a-zA-Z].*\.scss$/', $scss->getFileName())) {
                $filename = $scss->getFileName();
                $files_parameter .=
                    $sass_directory . DS . $filename
                    . ':' .
                    $css_directory . DS . preg_replace('/\.scss$/', '.min.css ', $filename);
            }
        }
        if (!$files_parameter) {

            return self::CODE_SUCCESS;
        }
        if ($args->getOption('watch')) {
            $io->out("sass --no-source-map --style=expanded --update --watch {$files_parameter}");
        } else {
            $io->out("sass --no-source-map --style=compressed {$files_parameter}");
        }

        return self::CODE_SUCCESS;
    }
}
