<?php

namespace Doomy\Pushtopus;


use Doomy\Pushtopus\Model\PushNotification;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Doomy\Pushtopus\Storage\StorageInterface;

class Pushtopus
{
    private StorageInterface $storage;
    private WebPush $webPush;

    public function __construct(StorageInterface $storage, WebPush $webPush)
    {
        $this->storage = $storage;
        $this->webPush = $webPush;
    }

    public function subscribe(Subscription $subscription): void
    {
        $this->storage->saveSubscription($subscription);
    }

    public function unsubscribe(string $endpoint): void
    {
        $this->storage->deleteSusbcription($endpoint);
    }

    public function sendPushNotification(PushNotification $notification): void
    {
        /* @var Subscription[] */
        $subscriptions = $this->storage->getSubscriptions();
        foreach ($subscriptions as $subscription) {
            $this->webPush->sendOneNotification($subscription, $this->getPayload($subscription, $notification));
        }
    }

   private function getPayload(Subscription $subscription, PushNotification $notification): string
   {
        return sprintf(
            '{"title":"%s","msg": "%s","icon":"%s","badge":"%s","url":"%s"}',
            $notification->getTitle(),
            $notification->getMessage(),
            $notification->getIcon(),
            $notification->getBadge(),
            $notification->getUrl()
        );
   }
}