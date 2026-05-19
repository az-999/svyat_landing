<?php

declare(strict_types=1);

namespace app\models;

use app\components\TurnstileValidator;
use Yii;
use yii\base\Model;

class ChatStartForm extends Model
{
    public ?string $verifyCode = null;

    public ?string $turnstileToken = null;

    /** Honeypot — должно оставаться пустым. */
    public ?string $website = null;

    public function rules(): array
    {
        $rules = [
            [['website'], 'validateHoneypot'],
        ];

        if ($this->isTurnstileEnabled()) {
            $rules[] = [['turnstileToken'], 'validateTurnstile'];
        } else {
            $rules[] = [['verifyCode'], 'required'];
            $rules[] = [['verifyCode'], 'captcha', 'captchaAction' => 'chat/captcha'];
        }

        return $rules;
    }

    public function validateHoneypot(string $attribute): void
    {
        if ($this->$attribute !== null && $this->$attribute !== '') {
            $this->addError($attribute, 'Отклонено.');
        }
    }

    public function validateTurnstile(string $attribute): void
    {
        /** @var TurnstileValidator $turnstile */
        $turnstile = Yii::$app->turnstile;
        if (!$turnstile->validate($this->$attribute, Yii::$app->request->userIP)) {
            $this->addError($attribute, 'Подтвердите, что вы не робот.');
        }
    }

    public function isTurnstileEnabled(): bool
    {
        return Yii::$app->turnstile->isEnabled()
            && (Yii::$app->params['turnstileSiteKey'] ?? '') !== '';
    }

    public function attributeLabels(): array
    {
        return [
            'verifyCode' => 'Код с картинки',
        ];
    }
}
