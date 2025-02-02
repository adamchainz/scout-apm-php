<?php

declare(strict_types=1);

namespace Scoutapm\Connector;

use Scoutapm\Connector\Exception\FailedToConnect;
use Scoutapm\Connector\Exception\NotConnected;

interface Connector
{
    /** @throws FailedToConnect */
    public function connect() : void;

    public function connected() : bool;

    /** @throws NotConnected */
    public function sendCommand(Command $message) : bool;

    public function shutdown() : void;
}
