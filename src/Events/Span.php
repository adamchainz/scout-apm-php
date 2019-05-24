<?php

namespace Scoutapm\Events;

use Scoutapm\Helper\Timer;

class Span extends Event implements \JsonSerializable
{
    private $requestId;

    private $parentId;

    private $name;

    private $timer;

    public function __construct(\Scoutapm\Agent $agent, string $name, $override = null)
    {
        parent::__construct($agent);

        $this->name = $name;

        $this->timer = new Timer();
        $this->timer->start();
    }

    public function stop()
    {
        $this->timer->stop();
    }

    public function setRequestId(string $requestId)
    {
        $this->requestId = $requestId;
    }

    public function setParentId(string $parentId)
    {
        $this->parentId = $parentId;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getStartTime()
    {
        return $this->timer->getStart();
    }

    public function getStopTime()
    {
        return $this->timer->getStop();
    }

    public function jsonSerialize()
    {
        $commands = [];
        $commands[] = ['StartSpan' => [
            'request_id' => $this->requestId,
            'span_id' => $this->id,
            'parent_id' => $this->parentId,
            'operation' => $this->name,
            'timestamp' => $this->getStartTime(),
        ]];

        $commands[] = ['StopSpan' => [
            'request_id' => $this->requestId,
            'span_id' => $this->id,
            'timestamp' => $this->getStopTime(),
        ]];

        return $commands;
    }
}
