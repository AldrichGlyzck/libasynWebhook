<?php

declare(strict_types=1);

namespace ialdrich23xx\libasynwebhook\thread;

use InvalidArgumentException;
use JsonException;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use ialdrich23xx\libasynwebhook\discord\WebHook;
use function igbinary_serialize;
use function igbinary_unserialize;
use function is_null;
use function json_encode;
use const JSON_THROW_ON_ERROR;

class SendWebHookTask extends AsyncTask
{
    private string $page;
    private int $timeout;
    private string $args;
    private string $headers;

    /**
     * @throws JsonException
     */
    public function __construct(WebHook $webHook) {
        if (is_null($webHook->getBody())) {
            $this->cancelRun();

            return;
        }

        $this->args = json_encode($webHook->getBody()->jsonSerialize(), JSON_THROW_ON_ERROR);

        $this->page = $webHook->getUrl();
        $this->timeout = 10;

        $serialized_headers = igbinary_serialize(["Content-Type: application/json"]);

        if ($serialized_headers === null) {
            throw new InvalidArgumentException("Headers cannot be serialized");
        }

        $this->headers = $serialized_headers;
    }

    public function getHeaders(): array
    {
        /** @var array $headers */
        $headers = igbinary_unserialize($this->headers);

        return $headers;
    }

    public function onRun(): void
    {
        $this->setResult(Internet::postURL(
            $this->page, $this->args, $this->timeout, $this->getHeaders()
        ));
    }

    public function onCompletion(): void {}
}