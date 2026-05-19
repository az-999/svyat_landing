<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\ChatStartForm $model */

use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Чат с ассистентом';
$turnstileSiteKey = Yii::$app->params['turnstileSiteKey'];
$useTurnstile = $model->isTurnstileEnabled() && $turnstileSiteKey !== '';

if ($useTurnstile) {
    $this->registerJsFile(
        'https://challenges.cloudflare.com/turnstile/v0/api.js',
        ['async' => true, 'defer' => true],
    );
}
?>
<div class="cv-wrap chat-gate">
    <a class="chat-back" href="<?= Yii::$app->homeUrl ?>">&larr; Резюме</a>

    <header class="cv-hero chat-gate__hero">
        <div class="cv-hero__inner">
            <span class="cv-hero__badge">AI · Ассистент</span>
            <h1 class="cv-hero__name">Задать вопрос</h1>
            <p class="cv-hero__role">Чат по опыту, навыкам и проектам. Ответы формирует AI на основе резюме.</p>
        </div>
    </header>

    <section class="chat-card">
        <?php $form = ActiveForm::begin([
            'id' => 'chat-start-form',
            'action' => ['start'],
            'options' => ['class' => 'chat-start-form'],
        ]) ?>

        <div class="chat-honeypot" aria-hidden="true">
            <?= $form->field($model, 'website')->textInput(['tabindex' => -1, 'autocomplete' => 'off'])->label(false) ?>
        </div>

        <?php if ($useTurnstile): ?>
            <div class="cf-turnstile" data-sitekey="<?= Html::encode($turnstileSiteKey) ?>"></div>
            <?= $form->field($model, 'turnstileToken')
                ->hiddenInput(['id' => 'turnstile-token'])
                ->label(false) ?>
        <?php else: ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                'template' => '<div class="chat-captcha-row">{image} {input}</div>',
                'options' => ['class' => 'form-control chat-captcha-input'],
            ]) ?>
        <?php endif ?>

        <?= Html::submitButton('Начать диалог', ['class' => 'chat-btn chat-btn--primary']) ?>

        <?php ActiveForm::end() ?>
    </section>
</div>
<?php
if ($useTurnstile) {
    $this->registerJs(<<<'JS'
document.getElementById('chat-start-form').addEventListener('submit', function (e) {
    const input = document.querySelector('[name="cf-turnstile-response"]');
    const hidden = document.getElementById('turnstile-token');
    if (input && hidden) {
        hidden.value = input.value;
    }
    if (!hidden || !hidden.value) {
        e.preventDefault();
        alert('Подтвердите, что вы не робот.');
    }
});
JS);
}
