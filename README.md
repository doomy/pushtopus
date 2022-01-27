# Pushtopus

Simple Web Push Notifications PHP + JS integration

## Thanks

Heavily inspired by [mksmith1a/php_push_demo](https://github.com/mksmith1a/php_push_demo), so giving credit where it's due. Thank you!

## Usage

Best way to grasp the basic idea how the integration is used is to go through [example.php](example.php), [example-actions.php](example-actions.php) and [js/example.js](js/example.js) files.

### Prerequisities
- Generate your VAPID keys - you can use [this generator](https://www.stephane-quantin.com/en/tools/generators/vapid-keys)

### Frontend

#### Initialization
[serviceWorker.js](serviceWorker.js) needs to be in your project root. The frontend initialization can be seen in [js/example.js](js/example.js). 

First, the Pushtopus class needs to be initialized:

```js
let pushtopus = new Pushtopus(<app_public_key>, <subscription_update_callback>, <backend_actions_url>);
```

##### app_public_key
Your public VAPID key, which you generated in the step before.

##### subscription_update_callback
If you need anything to happen on your frontend whenever the subscription state changes, use this callback here. The callback receives a single argument which is a state of subscription (true/false).

##### backend_actions_url
Url to your backend script / API endpoint that handles the heavy lifting.

#### Subscribing to notifications
```js
pushtopus.subscribe();
```

#### Unsubscribing from notifications
```js
pushtopus.unsubscribe();
```

#### Pushing a message
```js
pushtopus.push('Sample Pushtopus notification', 'Something requires your attention...', 'https://github.com/doomy/pushtopus');
```

### Backend

For backend usage reference, check [example.php](example.php), [example-actions.php](example-actions.php) files.

#### Configuration
Pushtopus uses a wrapper configuration object that needs to be initialized and injected to the main Pushtopus instance. Currently, there's two ways of initializing the configuration object:

The preferred way is to create the configuration from a predefined JSON configuration file. Copy [config.local.example.json](config.local.example.json) to `config.local.json`, replace placeholder strings with your data, then initialize the config like this:

```php
$config = Configuration::getFromJSONFile(__DIR__ . '/config.local.json');
```

Alternatively, you can create the instance of the Configuration object directly, like so:
```php
$config = new Configuration(
    string <vapidPublicKey>, 
    string <vapidPrivateKey>, 
    string <pushSubject>, 
    array <storageConfiguration>, 
    string <defaultIcon>, 
    string <defaultBadge>
);
```
for the expected data structure, check out the reference JSON config file.

#### Storage
Another dependency to be injected for the Pushtopus instance is Storage. So far, only SQLite integration is implemented, however this can be very easily adapted to whichever storage you're using by simply creating your own class implementing [Doomy\Pushtopus\StorageInterface](src/Storage/StorageInterface.php). Check the interface source for required methods.

```php
$storage = new Doomy\Pushtopus\Storage\SQLite($config->getStorageConfiguration());
```

#### WebPush library
Third party library [minishlink/web-push](https://github.com/minishlink/web-push) doing the heavy lifting.

```php
$webPush = new Minishlink\WebPush\WebPush($config->getWebPushConfiguration());
```

#### Initialization

```php
$pushtopus = new Doomy\Pushtopus($storage, $webPush);
```

#### Actions

##### Adding a subscription
```php
$subscription = new Minishlink\WebPush\Subscription('endpoint', 'key', 'token');
$pushtopus->subscribe($subscription);
```

##### Deleting a subscription
```php
$pushtopus->unsubscribe('subsciprition-endpoint-string');
```

##### Sending a notification
```php
$notification = new Doomy\Pushtopus\Model\PushNotification(
    'title',
    'message',
    'request-url',
    'icon.image' ?? $config->getDefaultIcon(),
    'badge.image' ?? $config->getDefaultBadge()
);
$pushtopus->sendPushNotification($notification);
```