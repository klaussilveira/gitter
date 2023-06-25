<?php

namespace Gitter;

use Gitter\Client as GitterClient;

class Gitter extends GitterClient
{
    protected static Client $client;

    /**
     * @return Client
     */
    public static function repo(): Client
    {
        return new static();
    }
}

