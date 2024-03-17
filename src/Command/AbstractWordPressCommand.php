<?php

namespace Simply\Core\Command;

use WP_CLI;

abstract class AbstractWordPressCommand
{
    public static string $commandName;
    /**
     * @var array<string> $requiredArgs
     */
    public static array $requiredArgs = array();

    public function register(): void
    {
        WP_CLI::add_command($this::$commandName, array($this, '_execute'));
    }

    /**
     * Show log with color
     * use correct color of WP_CLI
     * See https://make.wordpress.org/cli/handbook/internal-api/wp-cli-colorize/
     */
    protected function showColorMessage(string $message, string $color = '%n'): void
    {
        WP_CLI::log(WP_CLI::colorize($color.$message));
    }

    /**
     * Add question for command
     * @phpstan-ignore-next-line
     */
    protected function confirm(string $question, array $assoc_args = []): void
    {
        WP_CLI::confirm($question, $assoc_args);
    }

    /** @phpstan-ignore-next-line */
    protected function runCommand(string $command, array $option = []): void
    {
        WP_CLI::runCommand($command, $option);
    }

    /**
     * Verify required args of the command
     * @phpstan-ignore-next-line
     */
    protected function verifyRequiredArgs(array $assoc_args): void
    {
        $missedArgs = array();
        if (is_array($this::$requiredArgs) && ! empty($this::$requiredArgs)) {
            foreach ($this::$requiredArgs as $anArg) {
                if (!array_key_exists($anArg, $assoc_args)) {
                    $missedArgs[] = $anArg;
                }
            }
        }
        if (!empty($missedArgs)) {
            $this->showColorMessage('The arg(s) ' . implode(',', $missedArgs) . ' missed.', '%r');
            exit();
        }
    }

    /**
     * Main execute function for all commands
     * @phpstan-ignore-next-line
     */
    public function _execute($args, $assoc_args): void
    {
        // Exit if not have required args
        $this->verifyRequiredArgs($assoc_args);

        // If have all required args
        $this->execute($args, $assoc_args);
    }

    /** @phpstan-ignore-next-line */
    abstract public function execute(array $args, array $assoc_args);
}
