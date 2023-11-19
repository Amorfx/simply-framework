<?php

namespace Simply\Core\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DebugHookCommand extends Command
{
    protected static $defaultName = 'simply:debug:hook';

    protected function configure()
    {
        $this->setDescription('List all hooks registered in the application.');
        $this->addOption('filter', 'f', InputOption::VALUE_OPTIONAL, 'Filter the hooks by name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Load full wordpress : plugin and theme
            global $wp_filter;
            $hooks = $wp_filter;
            $filter = $input->getOption('filter');
            if (array_key_exists($filter, $hooks)) {
                $hooks = [$filter => $hooks[$filter]];
            } elseif ($filter) {
                $hooks = [];
            }
            $hook_names = array_keys($hooks);
            sort($hook_names);
            foreach ($hooks as $hookData) {
                // get source file name of the callback
                $callback = $hookData->callbacks[10];
                $callback = array_shift($callback);
                $callback = $callback['function'];
                $reflector = new \ReflectionMethod($callback[0], $callback[1]);
                $hookData->source = $reflector->getFileName();
                $output->writeln($callback[0]::class);
                $output->writeln($callback[1]);
                $output->writeln($hookData->source);
//                $reflector = new \ReflectionFunction($callback);
//                $hookData->source = $reflector->getFileName();
                dd($hookData->source);
            }
            $table = new Table($output);
            $table->setStyle('box');
            $table->setHeaders(['Hook Name']);
            $table->setRows($hook_names);
            $table->render();

            return Command::SUCCESS;
    }
}
