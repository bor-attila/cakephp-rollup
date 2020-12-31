<?php
declare(strict_types=1);

namespace CakephpRollup;

use Cake\Core\BasePlugin;
use Cake\Console\CommandCollection;
use CakephpRollup\Command\InitCommand;
use CakephpRollup\Command\SassCommand;

/**
 * Plugin for CakephpRollup
 */
class Plugin extends BasePlugin
{
    /**
     * Plugin name.
     *
     * @var string
     */
    protected $name = 'CakephpRollup';

    /**
     * Do bootstrapping or not
     *
     * @var bool
     */
    protected $bootstrapEnabled = false;

    /**
     * Load routes or not
     *
     * @var bool
     */
    protected $routesEnabled = false;

    /**
     * @inheritDoc
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands
            ->add('rollup:init', InitCommand::class)
            ->add('rollup:sass', SassCommand::class);
    }
}
