<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class SocketEvent extends Event
{
    public const NAME = 'session';
    public const SESSION_REMOVE_PUBLISHED_DELETE = "session_published";
    public const SESSION_REMOVE_UPDATED_DELETE = "session_updated";
    public const SESSION_REMOVE_PUBLISHE_UPDATE = "session_publish_update";

    public function __construct(private Request $request)
    {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
