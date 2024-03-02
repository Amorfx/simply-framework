<?php

namespace Simply\Core\Command;

use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Simply\Core\Debug\FilterParams;
use Simply\Core\Debug\HookDebug;
use Simply\Core\Debug\SearchEngine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DebugHookCommand extends Command
{
    protected static $defaultName = 'simply:debug:hook';
    private InputInterface $input;
    private OutputInterface $output;

    protected function configure()
    {
        $this->setDescription('List all hooks registered in the application.');
        $this->addOption('hook_name', null, InputOption::VALUE_OPTIONAL, 'Filter the hooks by name.');
        $this->addOption('directory', null, InputOption::VALUE_OPTIONAL, 'Filter the hooks by directory path.');
        $this->addOption('function_name', null, InputOption::VALUE_OPTIONAL, 'Filter the hooks by function name.');
    }

    /**
     * @throws ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->loadAllWordpressFiles();

        $filter = new FilterParams(
            hookName: $input->getOption('hook_name'),
            directory: $input->getOption('directory'),
            functionName: $input->getOption('function_name')
        );

        $hooks = $this->getAllHooksRegistered($filter);
        $hook_names = array_keys($hooks);
        sort($hook_names);

        $hookDebug = $this->constructHookDebugData($hooks);

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

    private function getAllHooksRegistered(FilterParams $filter): array
    {
        global $wp_filter;
        $searchEngine = new SearchEngine($wp_filter, $filter);
        if (array_key_exists($filter->hookName, $wp_filter)) {
            return [$filter->hookName => $wp_filter[$filter->hookName]];
        }

        if ($filter->hookName) {
            $this->output->writeln("<error>Hook $filter->hookName not found.</error>");
            return [];
        }

        return $searchEngine->search();
    }

    /**
     * @throws ReflectionException
     */
    private function constructHookDebugData(array $hooks): array
    {
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
                    // It's a method of a class
                    if (is_array($dataCallback['function'])) {
                        $reflector = new ReflectionMethod($dataCallback['function'][0], $dataCallback['function'][1]);
                        $source = $reflector->getFileName();
                        $fullClassName = $reflector->getDeclaringClass()->getName();
                        $functionName = $fullClassName . ':' . $dataCallback['function'][1];
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

        return $hookDebug;
    }

    private function loadAllWordpressFiles(): void
    {
        require_once ABSPATH . 'wp-admin/includes/admin.php';
        require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';
        require_once ABSPATH . 'wp-admin/includes/dashboard.php';
        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require ABSPATH . 'wp-admin/includes/theme-install.php';
        require ABSPATH . 'wp-admin/includes/update-core.php';
        require ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }
}
