<?php

declare(strict_types=1);

namespace ialdrich23xx\libasynwebhook\discord;

use ialdrich23xx\libasynwebhook\discord\body\Base;
use ialdrich23xx\libasynwebhook\Loader;
use ialdrich23xx\libasynwebhook\thread\SendWebHookTask;

class WebHook
{
    public function __construct(
        private string $url,
        private Base $body
    ) {}

    public static function make(string $url, Base $body): self
    {
        return new self($url, $body);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getBody(): Base
    {
        return $this->body;
    }

    public function send(): void
    {
        if (Loader::getInstance()->isValidUrl($this->getUrl())) {
            Loader::getInstance()->getThread()->submitTask(new SendWebHookTask($this));
        } else Loader::getInstance()->getPlugin()->getLogger()->error("Url not valid: " . $this->getUrl());
    }
}