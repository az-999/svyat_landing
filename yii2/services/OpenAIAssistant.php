<?php

namespace app\services;

use common\models\Company;
use common\models\OpenaiChat;
use cs\services\VarDumper;
use OpenAI;
use OpenAI\Client;
use OpenAI\Exceptions\ErrorException;
use Exception;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use common\models\OpenaiUsage;
use yii\helpers\Url;

class OpenAIAssistant
{
    public Client $client;
    private string $assistantId;
    public string $threadId;


    public function __construct(string $apiKey, string $assistantId)
    {
        $f = new OpenAI\Factory();

        $client = $f->withApiKey($apiKey)
        ->withOrganization(null)
        ->withProject(null)
        ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
        ->make();

        $this->client = $client;
        $this->assistantId = $assistantId;
        $this->threadId = '';
    }

    /**
     * Создает новый тред для диалога
     */
    public function createThread(): string
    {
        try {
            $response = $this->client->threads()->create([]);
            $this->threadId = $response->id;
            return $this->threadId;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка создания треда: " . $e->getMessage());
        }
    }

    /**
     * Использует существующий тред
     */
    public function setThread(string $threadId): void
    {
        $this->threadId = $threadId;
    }

    /**
     * Отправляет сообщение ассистенту
     */
    public function sendMessage(string $message, array $attachments = []): object
    {
        if (empty($this->threadId)) {
            $this->createThread();
        }

        try {
            // 1. Отправляем сообщение пользователя в тред
            $messageData = [
                'role'    => 'user',
                'content' => $message,
            ];

            if (!empty($attachments)) {
                $messageData['attachments'] = $attachments;
            }

            $this->client->threads()->messages()->create($this->threadId, $messageData);

            // 2. Запускаем ассистента
            $run = $this->client->threads()->runs()->create(
                $this->threadId,
                [
                    'assistant_id' => $this->assistantId,
                ]
            );

            // 3. Ожидаем завершения выполнения (polling)
            $runResult = $this->waitForRunCompletion($run->id);

            // 4. Сохраняем данные об использовании
            //$this->saveUsageData($run->id, $runResult);

            // 5. Получаем ответ
            return $runResult;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка отправки сообщения: " . $e->getMessage());
        }
    }

    /**
     * Ожидает завершения выполнения run
     */
    public function waitForRunCompletion(string $runId, int $maxAttempts = 30, int $delay = 1000): object
    {
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            try {
                $run = $this->client->threads()->runs()->retrieve(
                    threadId: $this->threadId,
                    runId: $runId
                );

                // Проверяем статус выполнения
                if (in_array($run->status, ['completed', 'failed', 'cancelled', 'expired'])) {
                    return $run;
                }

                // Если требуется действие (например, вызов функции) - выходим
                if ($run->status === 'requires_action') {
                    return $run;
                }

                // Ждем перед следующей проверкой
                usleep($delay * 1000);
                $attempts++;

            } catch (ErrorException $e) {
                throw new Exception("Ошибка проверки статуса run: " . $e->getMessage());
            }
        }

        throw new Exception("Превышено время ожидания выполнения");
    }

    /**
     * Выдает статус выполнения run
     */
    public function getRunStatus(string $runId): object
    {
        return $this->client->threads()->runs()->retrieve(
            threadId: $this->threadId,
            runId: $runId
        );
    }

    /**
     * Получает ответ из треда
     */
    public function getResponse(): array
    {
        try {
            $messages = $this->client->threads()->messages()->list($this->threadId, [
                'limit' => 10,
                'order' => 'desc',
            ]);

            $response = [
                'text' => '',
                'images' => [],
                'files' => [],
            ];

            // Ищем последнее сообщение ассистента
            foreach ($messages->data as $message) {
                if ($message->role === 'assistant') {
                    foreach ($message->content as $content) {
                        if ($content->type === 'text') {
                            $response['text'] = $content->text->value;
                        } elseif ($content->type === 'image_file') {
                            $response['images'][] = $content->image_file->file_id;
                        }
                    }
                    break;
                }
            }

            return $response;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка получения ответа: " . $e->getMessage());
        }
    }

    /**
     * Получает историю сообщений треда
     */
    public function getMessageHistory(int $limit = 20): array {
        if (empty($this->threadId)) {
            return [];
        }

        try {
            $messages = $this->client->threads()->messages()->list($this->threadId, [
                'limit' => $limit,
                'order' => 'asc',
            ]);

            $history = [];
            foreach ($messages->data as $message) {
                foreach ($message->content as $content) {
                    if ($content->type === 'text') {
                        $history[] = [
                            'role' => $message->role,
                            'content' => $content->text->value,
                            'timestamp' => $message->created_at,
                        ];
                    }
                }
            }

            return $history;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка получения истории: " . $e->getMessage());
        }
    }

    /**
     * Удаляет тред
     */
    public function deleteThread(): bool {
        if (empty($this->threadId)) {
            return false;
        }

        try {
            $this->client->threads()->delete($this->threadId);
            $this->threadId = '';
            return true;
        } catch (ErrorException $e) {
            throw new Exception("Ошибка удаления треда: " . $e->getMessage());
        }
    }

    /**
     * Получает информацию об ассистенте
     */
    public function getAssistantInfo(): array {
        try {
            $assistant = $this->client->assistants()->retrieve($this->assistantId);

            return [
                'id'           => $assistant->id,
                'name'         => $assistant->name,
                'model'        => $assistant->model,
                'instructions' => $assistant->instructions,
                'tools'        => $assistant->tools,
            ];

        } catch (ErrorException $e) {
            throw new Exception("Ошибка получения информации об ассистенте: " . $e->getMessage());
        }
    }

    function handleAssistantRun(ThreadRunResponse $run, $context): object
    {
        $client = $this->client;
        $threadId = $this->threadId;

        $requiredAction = $run->requiredAction;

        if ($requiredAction->type !== 'submit_tool_outputs') {
            throw new Exception('Unexpected required action type');
        }

        $toolOutputs = [];

        foreach ($requiredAction->submitToolOutputs->toolCalls as $toolCall) {
            $toolCallId = $toolCall->id;
            $functionName = $toolCall->function->name;
            $rawArgs = $toolCall->function->arguments;

            // Декодируем аргументы
            $args = json_decode($rawArgs, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in function arguments');
            }

            // Обработка функции connect_to_operator
            if ($functionName === 'connect_to_operator') {
                $output = $this->connect_to_operator($args, $context);

                $toolOutputs[] = [
                    'tool_call_id' => $toolCallId,
                    'output'       => $output,
                ];
            } else {
                // Неизвестная функция — возвращаем ошибку
                $toolOutputs[] = [
                    'tool_call_id' => $toolCallId,
                    'output' => json_encode([
                        'success' => false,
                        'message' => "Функция '$functionName' не поддерживается.",
                    ]),
                ];
            }
        }

        // 🔁 Отправляем результаты обратно в OpenAI
        $runFunction = $client->threads()->runs()->submitToolOutputs($threadId, $run->id, [
            'tool_outputs' => $toolOutputs,
        ]);

        return $this->waitForRunCompletion($runFunction->id);
    }

    /**
     * Соединяет пользователя с оператором: отправляет сообщение оператору, и ставит чат в режим ожидания ответа
     *
     * @param $args
     * @param array $context
     * [
     *  'chat'       => $chat // \common\models\OpenaiChat
     *  'chatDriver' => $this->chatDriver, // \common\services\chatDriver\driverInterface
     * ]
     * @return false|string
     */
    public function connect_to_operator($args, $context)
    {
        $operatorId = \avatar\controllers\CabinetOwnerSupportBotController::$operator_id;

        // Перевожу чат в режим работы с оператором
        /** @var \common\models\OpenaiChat $chat */
        $chat = $context['chat'];
        $chat->status = OpenaiChat::STATUS_OPERATOR;
        $chat->save();

        /** @var Company $company */
        $company = $context['company'];

        // 🔁 Отправляем запрос на подключение к оператору
        /** @var \common\services\chatDriver\driverInterface $chatDriver */
        $chatDriver = $context['chatDriver'];
        $chatDriver->sendMessage(join("\n", [
            'Вас вызывает на поддержку пользователь в чат',
            'Пользователь: ' . $chat['name_first'] . ' ' . $chat['name_last'] . ' ' . $chat['tg_id'],
            'Чат: ' . Url::to('/cabinet-owner-support-bot/chat-item?id='.$chat['id'].'&company_id=' . $company['id'], true),
        ]), $operatorId);

        $output = json_encode([
            'success' => true,
            'message' => "Я позвала оператора",
        ]);

        return $output;
    }

    public function order_bananas($args)
    {
        return json_encode([
            'success' => true,
            'message' => "Вы успешно заказали бананы! Спасибо за заказ.",
        ]);
    }

    /**
     * Сохраняет данные об использовании токенов
     *
     * @param string $runId
     * @param object $runResult
     * @return void
     */
    private function saveUsageData($runId, $runResult)
    {
        try {
            // Проверяем, есть ли данные об использовании в результате
            if (isset($runResult->usage)) {
                $usage = $runResult->usage;
                
                // Создаем или обновляем запись об использовании
                OpenaiUsage::updateByRunId(
                    $runId,
                    $usage->promptTokens ?? 0,
                    $usage->completionTokens ?? 0,
                    $usage->totalTokens ?? 0
                );
            } else {
                // Если данных об использовании нет, создаем запись с нулевыми значениями
                // Это поможет отслеживать все запросы, даже если OpenAI не вернул данные об использовании
                $existingUsage = OpenaiUsage::findOne(['run_id' => $runId]);
                
                if (!$existingUsage) {
                    OpenaiUsage::create(
                        $this->assistantId,
                        $this->threadId,
                        $runId,
                        0,
                        0,
                        0
                    );
                }
            }
        } catch (Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            error_log("Ошибка сохранения данных об использовании: " . $e->getMessage());
        }
    }

    /**
     * Получает статистику использования для текущего ассистента
     *
     * @param int|null $fromTimestamp
     * @param int|null $toTimestamp
     * @return array
     */
    public function getUsageStats($fromTimestamp = null, $toTimestamp = null)
    {
        return OpenaiUsage::getUsageStats($this->assistantId, $fromTimestamp, $toTimestamp);
    }

    /**
     * Получает последние записи об использовании
     *
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getRecentUsage($limit = 10)
    {
        return OpenaiUsage::getRecentUsage($this->assistantId, $limit);
    }

    /**
     * Создает нового ассистента
     *
     * @param string $name Имя ассистента
     * @param string $instructions Инструкции для ассистента
     * @param string $model Модель для использования (по умолчанию gpt-4o)
     * @param array $tools Массив инструментов для ассистента
     * @param array $fileIds Массив ID файлов для загрузки
     * @return \OpenAI\Responses\Assistants\AssistantResponse Информация о созданном ассистенте
     * @throws Exception
     */
    public function createAssistant(
        string $name,
        string $instructions,
        string $model = 'gpt-4o',
        array $tools = [],
        array $fileIds = []
    ) {
        try {
            $assistantData = [
                'name' => $name,
                'instructions' => $instructions,
                'model' => $model,
            ];

            // Добавляем инструменты если они указаны
            if (!empty($tools)) {
                $assistantData['tools'] = $tools;
            }

            // Добавляем файлы если они указаны
            if (!empty($fileIds)) {
                $assistantData['file_ids'] = $fileIds;
            }

            $assistant = $this->client->assistants()->create($assistantData);

            return $assistant;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка создания ассистента: " . $e->getMessage());
        }
    }

    /**
     * Обновляет существующего ассистента
     *
     * @param string $assistantId ID ассистента для обновления
     * @param array $updates Массив обновлений
     * @return \OpenAI\Responses\Assistants\AssistantResponse Обновленная информация об ассистенте
     * @throws Exception
     */
    public function updateAssistant(string $assistantId, array $updates)
    {
        try {
            $assistant = $this->client->assistants()->modify($assistantId, $updates);

            return $assistant;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка обновления ассистента: " . $e->getMessage());
        }
    }

    /**
     * Удаляет ассистента
     *
     * @param string $assistantId ID ассистента для удаления
     * @return bool Результат удаления
     * @throws Exception
     */
    public function deleteAssistant(string $assistantId): bool
    {
        try {
            $this->client->assistants()->delete($assistantId);
            return true;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка удаления ассистента: " . $e->getMessage());
        }
    }

    /**
     * Получает список всех ассистентов
     *
     * @param int $limit Лимит количества ассистентов
     * @param string $order Порядок сортировки (asc/desc)
     * @return \OpenAI\Responses\Assistants\AssistantResponse[] Список ассистентов
     * @throws Exception
     */
    public function listAssistants(int $limit = 20, string $order = 'desc'): array
    {
        try {
            $assistants = $this->client->assistants()->list([
                'limit' => $limit,
                'order' => $order,
            ]);

            $result = [];
            foreach ($assistants->data as $assistant) {
                $result[] = $assistant;
            }

            return $result;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка получения списка ассистентов: " . $e->getMessage());
        }
    }

    /**
     * Получает информацию о конкретном ассистенте по ID
     *
     * @param string $assistantId ID ассистента
     * @return \OpenAI\Responses\Assistants\AssistantResponse Информация об ассистенте
     * @throws Exception
     */
    public function getAssistantById(string $assistantId)
    {
        try {
            $assistant = $this->client->assistants()->retrieve($assistantId);

            return $assistant;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка получения ассистента: " . $e->getMessage());
        }
    }

    /**
     * Получает список файлов в векторном хранилище
     *
     * @param string $vectorStoreId ID векторного хранилища
     * @param int $limit Лимит количества файлов
     * @return array Список файлов
     * @throws Exception
     */
    public function getVectorStoreFiles(string $vectorStoreId, int $limit = 100): array
    {
        try {
            $files = $this->client->vectorStores()->files()->list($vectorStoreId, [
                'limit' => $limit,
            ]);

            $result = [];
            foreach ($files->data as $file) {
                $result[] = [
                    'id'                => $file->id,
                    'object'            => $file->object,
                    'created_at'        => $file->created_at,
                    'vector_store_id'   => $file->vector_store_id,
                    'status'            => $file->status,
                    'chunking_strategy' => $file->chunking_strategy ?? null,
                ];
            }

            return $result;

        } catch (ErrorException $e) {
            throw new Exception("Ошибка получения списка файлов: " . $e->getMessage());
        }
    }

    /**
     * Загружает файл в OpenAI
     *
     * @param string $filePath Путь к файлу на диске
     * @param string $purpose Назначение файла (по умолчанию 'assistants')
     * @return string ID загруженного файла
     * @throws Exception
     */
    public function uploadFile(string $filePath, string $purpose = 'assistants'): string
    {
        try {
            if (!file_exists($filePath)) {
                throw new Exception("Файл не найден: {$filePath}");
            }

            // Получаем имя файла из пути
            $fileName = basename($filePath);
            
            // Открываем файл для чтения
            $fileResource = fopen($filePath, 'r');
            if ($fileResource === false) {
                throw new Exception("Не удалось открыть файл: {$filePath}");
            }

            try {
                $file = $this->client->files()->upload([
                    'purpose' => $purpose,
                    'file' => $fileResource,
                    'filename' => $fileName,
                ]);

                return $file->id;
            } finally {
                // Закрываем файл
                fclose($fileResource);
            }

        } catch (ErrorException $e) {
            throw new Exception("Ошибка загрузки файла: " . $e->getMessage());
        }
    }

    /**
     * Добавляет файл в векторное хранилище
     *
     * @param string $vectorStoreId ID векторного хранилища
     * @param string $fileId ID файла в OpenAI
     * @return array Информация о добавленном файле
     * @throws Exception
     */
    public function addFileToVectorStore(string $vectorStoreId, string $fileId): array
    {
        try {
            $result = $this->client->vectorStores()->files()->create($vectorStoreId, [
                'file_id' => $fileId,
            ]);

            return [
                'id'                => $result->id,
                'object'            => $result->object,
                'created_at'        => $result->created_at,
                'vector_store_id'   => $result->vector_store_id,
                'status'            => $result->status,
            ];

        } catch (ErrorException $e) {
            throw new Exception("Ошибка добавления файла в векторное хранилище: " . $e->getMessage());
        }
    }
}
