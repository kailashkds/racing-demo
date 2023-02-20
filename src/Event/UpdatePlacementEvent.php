<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UpdatePlacementEvent extends Event
{
    private $data;

    public function __construct(int $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
