<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\ChatRateLimiter;
use app\models\ChatSession;
use app\models\ChatStartForm;
use app\services\AssistantChatService;
use Throwable;
use Yii;
use yii\captcha\CaptchaAction;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ChatController extends Controller
{
    public $layout = 'chat';

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'start' => ['POST'],
                    'message' => ['POST'],
                    'leave' => ['POST'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'only' => ['message'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
                'height' => 50,
                'minLength' => 4,
                'maxLength' => 5,
            ],
        ];
    }

    public function actionIndex(): Response|string
    {
        if (ChatSession::isAllowed()) {
            return $this->redirect(['room']);
        }

        $model = new ChatStartForm();

        return $this->render('gate', ['model' => $model]);
    }

    public function actionStart(): Response|string
    {
        if (ChatSession::isAllowed()) {
            return $this->redirect(['room']);
        }

        $model = new ChatStartForm();
        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('gate', ['model' => $model]);
        }

        try {
            /** @var AssistantChatService $assistant */
            $assistant = Yii::$app->assistantChat;
            $threadId = $assistant->createThread();
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            $model->addError('turnstileToken', 'Не удалось начать диалог. Попробуйте позже.');

            return $this->render('gate', ['model' => $model]);
        }

        ChatSession::save([
            'allowed' => true,
            'thread_id' => $threadId,
            'started_at' => time(),
            'message_count' => 0,
            'last_message_at' => 0,
        ]);

        return $this->redirect(['room']);
    }

    public function actionRoom(): Response|string
    {
        if (!ChatSession::isAllowed()) {
            return $this->redirect(['index']);
        }

        return $this->render('room');
    }

    public function actionMessage(): array
    {
        if (!ChatSession::isAllowed()) {
            Yii::$app->response->statusCode = 403;

            return ['success' => false, 'error' => 'Сначала пройдите проверку на странице чата.'];
        }

        $message = trim((string) Yii::$app->request->post('message', ''));
        $maxLen = (int) Yii::$app->params['chatMaxMessageLength'];

        if ($message === '') {
            Yii::$app->response->statusCode = 400;

            return ['success' => false, 'error' => 'Пустое сообщение.'];
        }

        if (mb_strlen($message) > $maxLen) {
            Yii::$app->response->statusCode = 400;

            return ['success' => false, 'error' => 'Сообщение слишком длинное (макс. ' . $maxLen . ' символов).'];
        }

        $session = ChatSession::get();
        if ($delayError = $this->checkMessageDelay($session)) {
            Yii::$app->response->statusCode = 429;

            return ['success' => false, 'error' => $delayError];
        }

        $ip = Yii::$app->request->userIP ?? 'unknown';
        /** @var ChatRateLimiter $limiter */
        $limiter = Yii::$app->chatRateLimiter;

        if ($error = $limiter->acquire('chat:ip:' . $ip)) {
            Yii::$app->response->statusCode = 429;

            return ['success' => false, 'error' => $error];
        }

        if ($error = $limiter->acquire('chat:session:' . Yii::$app->session->id)) {
            Yii::$app->response->statusCode = 429;

            return ['success' => false, 'error' => $error];
        }

        $threadId = ChatSession::getThreadId();
        if ($threadId === null) {
            Yii::$app->response->statusCode = 403;

            return ['success' => false, 'error' => 'Сессия чата недействительна.'];
        }

        try {
            /** @var AssistantChatService $assistant */
            $assistant = Yii::$app->assistantChat;
            $reply = $assistant->sendMessage($threadId, $message);
            ChatSession::incrementMessageCount();

            return [
                'success' => true,
                'reply' => $reply,
            ];
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'success' => false,
                'error' => 'Не удалось получить ответ. Попробуйте ещё раз.',
            ];
        }
    }

    public function actionLeave(): Response
    {
        ChatSession::clear();

        return $this->redirect(['index']);
    }

    private function checkMessageDelay(?array $session): ?string
    {
        $minDelay = (int) Yii::$app->params['chatMinDelaySeconds'];
        $now = time();
        $startedAt = (int) ($session['started_at'] ?? $now);
        $lastAt = (int) ($session['last_message_at'] ?? 0);

        if (($session['message_count'] ?? 0) === 0) {
            if ($now - $startedAt < $minDelay) {
                return 'Подождите пару секунд перед первым сообщением.';
            }

            return null;
        }

        if ($lastAt > 0 && $now - $lastAt < 1) {
            return 'Слишком быстро. Подождите секунду.';
        }

        return null;
    }
}
