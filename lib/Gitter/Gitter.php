<?php

namespace Gitter;

/**
 * @method static createRepository(string $path, $bare = null)
 * @method static getRepository(string $path)
 * @method static run($repository, string $command)
 * @method static getVersion()
 * @method static getPath()
 * @method static setPath(string $path)
 */
class Gitter
{
    protected static Client $client;

    public static function __callStatic(string $method, array $arguments)
    {
        if (!isset(self::$client)) {
            self::$client = new Client();
        }

        return call_user_func_array([self::$client, $method], $arguments);
    }

}

