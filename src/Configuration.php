<?php


namespace Doomy\Pushtopus;


class Configuration
{
    private string $vapidPublicKey;
    private string $vapidPrivateKey;
    private string $pushSubject;
    private array $storageConfiguration;
    private string $defaultIcon;
    private string $defaultBadge;

    public function __construct(
        string $vapidPublicKey,
        string $vapidPrivateKey,
        string $pushSubject,
        array $storageConfiguration,
        string $defaultIcon,
        string $defaultBadge
    ) {
        $this->vapidPublicKey = $vapidPublicKey;
        $this->vapidPrivateKey = $vapidPrivateKey;
        $this->pushSubject = $pushSubject;
        $this->storageConfiguration = $storageConfiguration;
        $this->defaultIcon = $defaultIcon;
        $this->defaultBadge = $defaultBadge;
    }

    public static function getFromJSONFile(string $configurationFile): self
    {
        $config = json_decode(file_get_contents($configurationFile), TRUE);
        return new self(
            $config['vapidPublicKey'],
            $config['vapidPrivateKey'],
            $config['pushSubject'],
            $config['storageConfiguration'],
            $config['defaultIcon'],
            $config['defaultBadge']
        );
    }

    public function getVapidPublicKey(): string
    {
        return $this->vapidPublicKey;
    }

    public function getWebPushConfiguration(): array
    {
        return [
            'VAPID' => [
                'subject' => $this->pushSubject,
                'publicKey' => $this->vapidPublicKey,
                'privateKey' => $this->vapidPrivateKey
            ]
        ];
    }

    public function getStorageConfiguration(): array
    {
        return $this->storageConfiguration;
    }

    public function getDefaultIcon(): string
    {
        return $this->defaultIcon;
    }

    public function getDefaultBadge(): string
    {
        return $this->defaultBadge;
    }
}