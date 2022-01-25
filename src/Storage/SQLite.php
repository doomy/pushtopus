<?php

namespace Doomy\Pushtopus\Storage;

use Doomy\Pushtopus\Storage\StorageInterface;
use Minishlink\WebPush\Subscription;
use PDO;

class SQLite implements StorageInterface
{
    private PDO $pdo;

    public function __construct(array $configuration)
    {
        $databaseExists = file_exists($configuration["sqliteFile"]);

        if (!$databaseExists) {
            touch($configuration['sqliteFile']);
        }

        $this->pdo = new PDO("sqlite:" . $configuration["sqliteFile"]);

        if (!$databaseExists) {
            $this->pdo->exec(
                'CREATE TABLE IF NOT EXISTS subscriptions (
                    `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    `endpoint` TEXT NOT NULL,
                    `publicKey` TEXT NULL,
                    `authToken` TEXT NOT NULL,
                    `encoding` TEXT NULL
                );'
            );
        }
    }

    public function saveSubscription(Subscription $subscription): void
    {
        $sql = "REPLACE INTO subscriptions (endpoint, publicKey, authToken, `encoding`) VALUES (?, ?, ?, ?)";
        $statement = $this->pdo->prepare($sql);
        $eInfo = $this->pdo->errorInfo();
        $statement->execute([
                $subscription->getEndpoint(),
                $subscription->getPublicKey(),
                $subscription->getAuthToken(),
                $subscription->getContentEncoding()
            ]);


        return;
    }

    public function getSubscriptions(): array
    {
        $subscriptions = [];
        $statement = $this->pdo->query('SELECT endpoint, publicKey, authtoken, `encoding` FROM subscriptions');
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $subscriptions[] = new Subscription(
                $row['endpoint'], $row['publicKey'], $row['authToken'], $row['encoding']
            );
        }

        return $subscriptions;
    }

    public function deleteSusbcription(string $endpoint): void
    {
        $sql = "DELETE FROM subscriptions WHERE endpoint = '$endpoint'";
        $this->pdo->exec($sql);
    }
}
