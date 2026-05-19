<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Диалог с ассистентом';
$this->registerJsFile('@web/js/chat.js', ['depends' => [\yii\web\YiiAsset::class]]);
?>
<div class="cv-wrap chat-room">
    <div class="chat-room__toolbar">
        <a class="chat-back" href="<?= Yii::$app->homeUrl ?>">&larr; Резюме</a>
        <?= Html::beginForm(['leave'], 'post', ['class' => 'chat-leave-form']) ?>
            <?= Html::submitButton('Завершить чат', ['class' => 'chat-btn chat-btn--ghost']) ?>
        <?= Html::endForm() ?>
    </div>

    <section class="chat-card chat-card--room">
        <div id="chat-messages" class="chat-messages" aria-live="polite">
            <div class="chat-msg chat-msg--assistant">
                <p>Здравствуйте! Спросите о моём опыте, стеке, проектах или условиях работы.</p>
            </div>
        </div>

        <form id="chat-form" class="chat-form" novalidate>
            <textarea
                id="chat-input"
                class="chat-input"
                name="message"
                rows="2"
                maxlength="<?= (int) Yii::$app->params['chatMaxMessageLength'] ?>"
                placeholder="Ваш вопрос…"
                required
            ></textarea>
            <button type="submit" class="chat-btn chat-btn--primary" id="chat-send">Отправить</button>
        </form>
        <p id="chat-error" class="chat-error" hidden></p>
    </section>
</div>

<script>
window.chatConfig = {
    messageUrl: <?= json_encode(Url::to(['message']), JSON_THROW_ON_ERROR) ?>,
    csrfParam: <?= json_encode(Yii::$app->request->csrfParam, JSON_THROW_ON_ERROR) ?>,
    csrfToken: <?= json_encode(Yii::$app->request->csrfToken, JSON_THROW_ON_ERROR) ?>,
};
</script>
