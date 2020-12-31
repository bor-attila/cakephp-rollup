<?php
declare(strict_types=1);

namespace CakephpRollup\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use CakephpRollup\Plugin;

/**
 * Init command.
 */
class InitCommand extends Command
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
        $directories = [
            'scss' => [
                'path' => 'scss',
                'touch' => 'style.scss',
            ],
            'css' => [
                'path' => 'css',
                'touch' => '.gitkeep',
            ],
            'plugins' => [
                'path' => 'plugins',
                'touch' => '.gitkeep',
            ],
            'js' => [
                'path' => 'js',
                'touch' => false,
            ],
            'js/src' => [
                'path' => 'js' . DS . 'src',
                'touch' => 'main.app.js',
            ],
            'js/src/components' => [
                'path' => 'js' . DS . 'src' . DS . 'components',
                'touch' => '.gitkeep',
            ],
            'js/src/mixins' => [
                'path' => 'js' . DS . 'src' . DS . 'mixins',
                'touch' => '.gitkeep',
            ],
            'js/src/static' => [
                'path' => 'js' . DS . 'src' . DS . 'static',
                'touch' => 'script.js',
            ],
        ];

        $base_directory = WWW_ROOT;
        if ($args->getOption('dir')) {
            $base_directory = WWW_ROOT . $args->getOption('dir') . DS;
        }

        foreach ($directories as $name => $directory) {
            $path = $base_directory . $directory['path'];
            if (file_exists($path)) {
                $io->info("{$name} directory already exists ({$path})");
            } else {
                if (mkdir($path, 0755, true)) {
                    $io->info("{$name} directory successfully created ({$path})");
                    if ($directory['touch']) {
                        touch($path . DS . $directory['touch']);
                    }
                } else {
                    $io->error("Creating {$name} directory has failed. Initialization halted.");
                }
            }
        }
        $plugin_webroot = (new Plugin())->getPath() . 'webroot' . DS;
        $files = [
            'rollup.config.js', 'babel.config.json'
        ];

        foreach ($files as $file) {
            if (file_exists(WWW_ROOT . $file)) {
                $io->info("The {$file} already exists");
            } else {
                copy($plugin_webroot . $file, WWW_ROOT . $file);
                $io->info($file . ' created');
            }
        }

        return self::CODE_SUCCESS;
    }
}
