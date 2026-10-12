<?php

namespace Simply\Core\Debug;

use Simply\Core\Hook\LazyHookListener;

/**
 * Resolve a WordPress hook callback to its function or method, without instantiating lazy services
 */
final class CallbackReflector
{
    public static function reflect(mixed $callback): ?\ReflectionFunctionAbstract
    {
        try {
            return match (true) {
                $callback instanceof LazyHookListener => new \ReflectionMethod($callback->class, $callback->method),
                $callback instanceof \Closure => new \ReflectionFunction($callback),
                is_array($callback) && count($callback) === 2 => new \ReflectionMethod($callback[0], $callback[1]),
                is_string($callback) && str_contains($callback, '::') => new \ReflectionMethod(...explode('::', $callback, 2)),
                is_string($callback) => new \ReflectionFunction($callback),
                is_object($callback) && method_exists($callback, '__invoke') => new \ReflectionMethod($callback, '__invoke'),
                default => null,
            };
        } catch (\ReflectionException) {
            return null;
        }
    }

    /**
     * "Class:method" for a method, the function name otherwise
     */
    public static function name(mixed $callback): string
    {
        $reflection = self::reflect($callback);
        if ($reflection instanceof \ReflectionMethod) {
            return $reflection->getDeclaringClass()->getName() . ':' . $reflection->getName();
        }

        return $reflection?->getName() ?? '';
    }
}
