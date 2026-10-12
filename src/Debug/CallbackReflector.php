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
     * "Class:method" for a method, the function name otherwise.
     * A lazy listener of a service whose id is not its class also shows the id, to tell services of the same class apart.
     */
    public static function name(mixed $callback): string
    {
        $reflection = self::reflect($callback);
        if ($reflection instanceof \ReflectionMethod) {
            $name = $reflection->getDeclaringClass()->getName() . ':' . $reflection->getName();
            if ($callback instanceof LazyHookListener && $callback->serviceId !== $callback->class) {
                $name .= ' (' . $callback->serviceId . ')';
            }

            return $name;
        }

        return $reflection?->getName() ?? '';
    }
}
