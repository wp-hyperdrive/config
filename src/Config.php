<?php

namespace Hyperdrive\Config;

use Hyperdrive\Config\Exceptions\ConstantAlreadyDefinedException;
use Hyperdrive\Config\Exceptions\UndefinedKeyException;

class Config
{
    /**
     * @var array<string, mixed>
     */
    protected static $config = [];

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function define(string $key, $value): void
    {
        self::defined($key) or self::$config[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        if (!array_key_exists($key, self::$config)) {
            $class = self::class;
            throw new UndefinedKeyException("'{$key}' has not been defined. Use `{$class}::define('{$key}, ...)`.");
        }

        return self::$config[$key];
    }

    /**
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void
    {
        unset(self::$config[$key]);
    }

    /**
     * @return void
     */
    public static function apply(): void
    {
        foreach (self::$config as $key => $value) {
            try {
                self::defined($key);
            }
            catch (ConstantAlreadyDefinedException $e) {
                if (constant($key) !== $value) {
                    throw $e;
                }
            }
        }

        foreach (self::$config as $key => $value) {
            defined($key) or define($key, $value);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    protected static function defined(string $key): bool
    {
        if (defined($key)) {
            throw new ConstantAlreadyDefinedException("Could not redefine contant '{$key}. `define('{$key}', ...)` has already occurred elsewhere.");
        }

        return false;
    }
}
