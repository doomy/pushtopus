<?php

namespace Doomy\Pushtopus\Model;

class PushNotification
{
    private string $title;
    private string $message;
    private string $url;
    private string $icon;
    private string $badge;

    public function __construct(string $title, string $message, string $url, string $icon, string $badge)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
        $this->badge = $badge;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getBadge(): string
    {
        return $this->badge;
    }
}