<?php

namespace Datto\JsonRpc\Exception;

use Datto\JsonRpc\Exceptions\Exception;

class NotSupported extends Exception
{
    public function __construct()
    {
        parent::__construct('Not supported.', -32001);
    }
}

