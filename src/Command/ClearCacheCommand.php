<?php

namespace SimplyFramework\Command;

use Symfony\Component\Filesystem\Filesystem;

class ClearCacheCommand extends AbstractWordPressCommand {
    function execute($args, $assoc_args) {
        $fs = new Filesystem();
    }
}
