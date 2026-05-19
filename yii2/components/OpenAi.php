<?php

declare(strict_types=1);

namespace app\components;

use OpenAI;
use OpenAI\Client;
use yii\base\Component;
use yii\base\InvalidConfigException;

class OpenAi extends Component
{
    public string $apiKey = '';

    private ?Client $client = null;

    public function getClient(): Client
    {
        if ($this->client === null) {
            if ($this->apiKey === '') {
                throw new InvalidConfigException('OPENAI_API_KEY is not configured in .env');
            }

            $this->client = OpenAI::client($this->apiKey);
        }

        return $this->client;
    }
}
