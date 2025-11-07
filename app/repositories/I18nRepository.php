<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;

class I18nRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, string>
     */
    public function translationsForLocale(string $locale): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT k.`key`, t.`value`
             FROM i18n_keys k
             JOIN i18n_translations t ON t.key_id = k.id
             WHERE t.locale = :locale'
        );
        $stmt->execute(['locale' => $locale]);

        $translations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $translations[$row['key']] = $row['value'];
        }

        return $translations;
    }

    /**
     * @param array<string, string> $localizedValues
     */
    public function upsert(string $key, ?string $context, array $localizedValues): void
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare('SELECT id FROM i18n_keys WHERE `key` = :key LIMIT 1');
            $stmt->execute(['key' => $key]);
            $keyId = $stmt->fetchColumn();

            if (!$keyId) {
                $insert = $this->pdo->prepare('INSERT INTO i18n_keys (`key`, context) VALUES (:key, :context)');
                $insert->execute([
                    'key' => $key,
                    'context' => $context,
                ]);
                $keyId = $this->pdo->lastInsertId();
            }

            $upsert = $this->pdo->prepare(
                'INSERT INTO i18n_translations (key_id, locale, value) VALUES (:key_id, :locale, :value)
                 ON DUPLICATE KEY UPDATE value = VALUES(value)'
            );

            foreach ($localizedValues as $locale => $value) {
                $upsert->execute([
                    'key_id' => $keyId,
                    'locale' => $locale,
                    'value' => $value,
                ]);
            }

            $this->pdo->commit();
        } catch (PDOException $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }
}
