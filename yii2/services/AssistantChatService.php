<?php

declare(strict_types=1);

namespace app\services;

use OpenAI;
use OpenAI\Client;
use OpenAI\Exceptions\ErrorException;
use RuntimeException;
use yii\base\Component;

class AssistantChatService extends Component
{
    public string $apiKey = '';

    public string $assistantId = '';

    private ?Client $client = null;

    public function getClient(): Client
    {
        if ($this->client === null) {
            if ($this->apiKey === '' || $this->assistantId === '') {
                throw new RuntimeException('OpenAI API key or assistant ID is not configured.');
            }

            $this->client = OpenAI::factory()
                ->withApiKey($this->apiKey)
                ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
                ->make();
        }

        return $this->client;
    }

    public function createThread(): string
    {
        try {
            return $this->getClient()->threads()->create([])->id;
        } catch (ErrorException $e) {
            throw new RuntimeException('Не удалось создать диалог: ' . $e->getMessage(), 0, $e);
        }
    }

    public function sendMessage(string $threadId, string $message): string
    {
        $client = $this->getClient();

        try {
            $client->threads()->messages()->create($threadId, [
                'role' => 'user',
                'content' => $message,
            ]);

            $run = $client->threads()->runs()->create($threadId, [
                'assistant_id' => $this->assistantId,
            ]);

            $run = $this->waitForRunCompletion($threadId, $run->id);

            if ($run->status !== 'completed') {
                throw new RuntimeException('Ассистент не смог завершить ответ (статус: ' . $run->status . ').');
            }

            return $this->extractLastAssistantText($threadId);
        } catch (ErrorException $e) {
            throw new RuntimeException('Ошибка OpenAI: ' . $e->getMessage(), 0, $e);
        }
    }

    private function waitForRunCompletion(string $threadId, string $runId, int $maxAttempts = 60): object
    {
        $client = $this->getClient();

        for ($i = 0; $i < $maxAttempts; $i++) {
            $run = $client->threads()->runs()->retrieve($threadId, $runId);

            if (in_array($run->status, ['completed', 'failed', 'cancelled', 'expired'], true)) {
                return $run;
            }

            if ($run->status === 'requires_action') {
                throw new RuntimeException('Ассистент запросил вызов функций — на лендинге это не поддерживается.');
            }

            usleep(500_000);
        }

        throw new RuntimeException('Превышено время ожидания ответа ассистента.');
    }

    private function extractLastAssistantText(string $threadId): string
    {
        $messages = $this->getClient()->threads()->messages()->list($threadId, [
            'limit' => 10,
            'order' => 'desc',
        ]);

        foreach ($messages->data as $message) {
            if ($message->role !== 'assistant') {
                continue;
            }

            $parts = [];
            foreach ($message->content as $content) {
                if ($content->type === 'text') {
                    $parts[] = $content->text->value;
                }
            }

            if ($parts !== []) {
                return trim(implode("\n", $parts));
            }
        }

        return '';
    }
}
