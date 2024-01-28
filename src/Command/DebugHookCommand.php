<?php

namespace Simply\Core\Command;

use ReflectionFunction;
use ReflectionMethod;
use Simply\Core\Dto\Debug\HookDebug;
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
            /** @var HookDebug[] $hookDebug */
            $hookDebug = [];
            foreach ($hooks as $name => $hookData) {
                if (empty($hookData->callbacks)) {
                    continue;
                }

                foreach ($hookData->callbacks as $priority => $callbacks) {
                    foreach ($callbacks as $dataCallback) {
                        $source = '';
                        $functionName = '';
                        $sourceLine = 0;
                        if (is_array($dataCallback['function'])) {
                            $reflector = new ReflectionMethod($dataCallback['function'][0], $dataCallback['function'][1]);
                            $source = $reflector->getFileName();
                            $functionName = $dataCallback['function'][1];
                            $sourceLine = $reflector->getStartLine();
                        } elseif (is_string($dataCallback['function'])) {
                            $reflector = new ReflectionFunction($dataCallback['function']);
                            $source = $reflector->getFileName();
                            $functionName = $dataCallback['function'];
                            $sourceLine = $reflector->getStartLine();
                        }
                        $hookDebug[] = new HookDebug(
                            $name,
                            $source,
                            $sourceLine,
                            $functionName,
                            $priority,
                        );
                    }
                }
            }
            $table = new Table($output);
            $table->setStyle('box');
            $table->setHeaders(['Hook Name', 'Priority', 'method/function name', 'Source']);
            foreach ($hookDebug as $hook) {
                $table->addRow([
                    $hook->name,
                    $hook->priority,
                    $hook->functionName,
                    "<href=file://$hook->source>$hook->source line $hook->sourceLine</>",
                ]);
            }
            $table->render();

            return Command::SUCCESS;
    }
}
