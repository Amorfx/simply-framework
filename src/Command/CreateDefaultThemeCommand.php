<?php

namespace Simply\Core\Command;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Create a default plugin using Simply Framework
 *
 * @package SimplyFramework\Command
 */
class CreateDefaultThemeCommand extends AbstractWordPressCommand {
    static $commandName = 'simply:create:theme';
    static $requiredArgs = ['theme-slug'];
    public function execute($args, $assoc_args) {
        $themeSlug = $assoc_args['theme-slug'];
        $this->showColorMessage("Start creating new WordPress theme called " . $themeSlug, '%g');
        // Create the directory plugin and copy the resources and change value
        $newThemeDir = WP_CONTENT_DIR . '/themes/' . $themeSlug;
        $fs = new Filesystem();
        $fs->remove($newThemeDir);
        $fs->mkdir($newThemeDir);
        $fs->mirror(SIMPLY_RESOURCES_DIRECTORY . '/theme/sample', $newThemeDir);
        $fs->rename($newThemeDir . '/sample.php', $newThemeDir . '/' . $themeSlug . '.php');
        $this->showColorMessage('Created', '%b');
        $this->confirm('Do you want to activate the theme ?');
        $this->runCommand('theme activate ' . $themeSlug);
    }
}
