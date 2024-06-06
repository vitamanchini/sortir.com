<?php

namespace App\EventListener;

use Symfony\Contracts\EventDispatcher\Event;

class PlaceDetailsListener extends Event
{
    public const NAME = 'show.place';

}