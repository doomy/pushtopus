<?php

use Doomy\Pushtopus\Model\PushNotification;
use Doomy\Pushtopus\Pushtopus;
use Doomy\Pushtopus\Storage\StorageInterface;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class PushtopusTest extends TestCase
{
    private StorageInterface $storage;
    private WebPush $webPush;
    private Pushtopus $pushtopus;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->storage = Mockery::mock(StorageInterface::class);
        $this->webPush = MockerY::mock(WebPush::class);
        $this->pushtopus = new Pushtopus($this->storage, $this->webPush);
        parent::__construct($name, $data, $dataName);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testSubscribe(): void
    {
        $subscription = Mockery::mock(Subscription::class);
        $this->storage->shouldReceive('saveSubscription')->with($subscription)->once();
        $this->pushtopus->subscribe($subscription);
        $this->expectNotToPerformAssertions();
    }

    public function testUnsubscribe(): void
    {
        $this->storage->shouldReceive('deleteSusbcription')->with('mock-endpoint')->once();
        $this->pushtopus->unsubscribe('mock-endpoint');
        $this->expectNotToPerformAssertions();
    }

    public function testSendPushNotification(): void
    {
        [$subscription1, $subscription2] = [Mockery::mock(Subscription::class), Mockery::mock(Subscription::class)];
        $notification = Mockery::mock(PushNotification::class);

        $notification->shouldReceive('getTitle')->withNoArgs()->twice()->andReturn('mock-title');
        $notification->shouldReceive('getMessage')->withNoArgs()->twice()->andReturn('mock-message');
        $notification->shouldReceive('getUrl')->withNoArgs()->twice()->andReturn('mock-url');
        $notification->shouldReceive('getIcon')->withNoArgs()->twice()->andReturn('mock-icon');
        $notification->shouldReceive('getBadge')->withNoArgs()->twice()->andReturn('mock-badge');

        $this->storage->shouldReceive('getSubscriptions')->once()->withNoArgs()->andReturn([$subscription1, $subscription2]);
        $expectedPayload =
            '{"title":"mock-title","msg": "mock-message","icon":"mock-icon","badge":"mock-badge","url":"mock-url"}';
        $this->webPush->shouldReceive('sendOneNotification')->with($subscription1, $expectedPayload)->once();
        $this->webPush->shouldReceive('sendOneNotification')->with($subscription2, $expectedPayload)->once();

        $this->pushtopus->sendPushNotification($notification);

        $this->expectNotToPerformAssertions();
    }
}
