<?php

use Doomy\Pushtopus\Configuration;
use Doomy\Pushtopus\Model\PushNotification;
use Doomy\Pushtopus\Pushtopus;
use Doomy\Pushtopus\Storage\SQLite;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

$config = Configuration::getFromJSONFile(__DIR__ . '/config.local.json');
$storage = new SQLite($config->getStorageConfiguration());
$webPush = new WebPush($config->getWebPushConfiguration());
$pushtopus = new Pushtopus($storage, $webPush);

$request = json_decode(file_get_contents('php://input'), true);

$action = $request['action'] ?? NULL;

switch ($action) {
    case 'subscribe':
        $subscription = new Subscription($request['endpoint'], $request['key'], $request['token']);
        $pushtopus->subscribe($subscription);
        break;
    case 'unsubscribe':
        $pushtopus->unsubscribe($request['endpoint']);
        break;
    case 'push':
        $notification = new PushNotification(
            $request['title'],
            $request['message'],
            $request['url'],
            $request['icon'] ?? $config->getDefaultIcon(),
            $request['badge'] ?? $config->getDefaultBadge()
        );
        $pushtopus->sendPushNotification($notification);
        break;
}

?>