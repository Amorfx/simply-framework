<?php

namespace Simply\Core\Compiler;

use Simply\Core\Cache\CacheDirectoryManager;

class HookCompiler
{
    /**
     * @var array<string, array<array<string, string|int>>>
     */
    private array $hooksMapping = array();
    private const FILE_NAME = 'hooks.php';

    protected function getFilePath(): string
    {
        return CacheDirectoryManager::getCachePath(self::FILE_NAME);
    }

    /**
     * @return bool|array<string, array<array<string, string|int>>>
     */
    public function getFromCache(): bool|array
    {
        $fp = $this->getFilePath();
        if (!file_exists($fp)) {
            return false;
        }
        // @codeCoverageIgnoreStart
        return require $fp;
        // @codeCoverageIgnoreEnd
    }

    public function add(string $className, string $hookClass, string $hook, string $function, int $priority = 10, int $numberArguments = 1): void
    {
        /** @phpstan-ignore-next-line  */
        if (!isset($this->hooksMapping[$className]) || !is_array($this->hooksMapping[$className])) {
            $this->hooksMapping[$className] = array();
        }
        $this->hooksMapping[$className][] = array(
            'hook' => $hook,
            'type' => $hookClass,
            'fn' => $function,
            'priority' => $priority,
            'numberArguments' => $numberArguments,
        );
    }

    /**
     * @param string $className
     * @return \int[][]|mixed|\string[][]
     */
    public function getFromClass(string $className)
    {
        $content = $this->getFromCache();
        return $content[$className];
    }

    public function compile(): void
    {
        file_put_contents($this->getFilePath(), '<?php return ' . var_export($this->hooksMapping, true) . ';');
    }
}
