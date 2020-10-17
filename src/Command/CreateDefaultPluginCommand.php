<?php

namespace SimplyFramework\Command;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Create a default plugin using Simply Framework
 *
 * @package SimplyFramework\Command
 */
class CreateDefaultPluginCommand extends AbstractWordPressCommand {
    static $commandName = 'simply:create:plugin';
    static $requiredArgs = ['plugin-slug'];
    function execute($args, $assoc_args) {
        $pluginSlug = $assoc_args['plugin-slug'];
        $this->showColorMessage("Start creating new WordPress plugin called " . $pluginSlug, '%g');
        // Create the directory plugin and copy the resources and change value
        $newPluginDir = WP_PLUGIN_DIR . '/' . $pluginSlug;
        $fs = new Filesystem();
        $fs->remove($newPluginDir);
        $fs->mkdir($newPluginDir);
        $fs->mirror(SIMPLY_RESOURCES_DIRECTORY . '/plugin/sample', $newPluginDir);
        $fs->rename($newPluginDir . '/sample.php', $newPluginDir . '/' . $pluginSlug . '.php');
        $this->showColorMessage('Created', '%b');
        $this->confirm('Do you want to activate the plugin ?');
        $this->runCommand('plugin activate ' . $pluginSlug);
    }
}
