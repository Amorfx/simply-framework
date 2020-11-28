<?php

namespace Simply\Core\Command;

abstract class AbstractWordPressCommand {
    static $commandName;
    static $requiredArgs = array();
    
    public function register() {
        \WP_CLI::add_command($this::$commandName, array($this, '_execute'));
    }

    /**
     * Show log with color
     * use correct color of WP_CLI
     * See https://make.wordpress.org/cli/handbook/internal-api/wp-cli-colorize/
     * @param $message
     * @param string $color
     */
    protected function showColorMessage($message, $color = '%n') {
        \WP_CLI::log(\WP_CLI::colorize($color.$message));
    }

    /**
     * Add question for command
     * @param $question
     * @param array $assoc_args
     */
    protected function confirm($question, $assoc_args = array()) {
        \WP_CLI::confirm($question, $assoc_args);
    }

    protected function runCommand($command, $option = array()) {
        \WP_CLI::runCommand($command, $option);
    }

    /**
     * Verify required args of the command
     * @param $assoc_args
     */
    protected function verifyRequiredArgs($assoc_args) {
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
     * @param $args
     * @param $assoc_args
     */
    public function _execute($args, $assoc_args) {
        // Exit if not have required args
        $this->verifyRequiredArgs($assoc_args);

        // If have all required args
        $this->execute($args, $assoc_args);
    }

    abstract function execute($args, $assoc_args);
}
