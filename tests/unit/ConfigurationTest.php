<?php

use Doomy\Pushtopus\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testGetWebPushConfiguration(): void
    {
        $config = new Configuration('mock-public-key', 'mock-private-key', 'mock-subject', [], '', '');
        $expectedConfiguration = [
            'VAPID' => [
                'subject' => 'mock-subject',
                'publicKey' => 'mock-public-key',
                'privateKey' => 'mock-private-key'
            ]
        ];
        $this->assertEquals($expectedConfiguration, $config->getWebPushConfiguration(), 'configuration ok');
    }

}
