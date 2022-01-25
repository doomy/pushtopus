<?php
namespace Doomy\Pushtopus\Storage;

use Minishlink\WebPush\Subscription;

interface StorageInterface
{
    public function __construct(array $configuration);

    public function saveSubscription(Subscription $subscription): void;

    /* @returns Subscription[] */
    public function getSubscriptions(): array;

    public function deleteSusbcription(string $endpoint): void;
}