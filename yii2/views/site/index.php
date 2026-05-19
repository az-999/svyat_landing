<?php

declare(strict_types=1);

/** @var yii\web\View $this */

$resumeBody = file_get_contents(Yii::getAlias('@app/data/resume-body.html'));
?>
<div class="cv-wrap">
    <p class="cv-updated">Резюме обновлено 18 мая 2026</p>

    <header class="cv-hero">
        <div class="cv-hero__inner">
            <span class="cv-hero__badge">Портфолио · Backend · AI</span>
            <h1 class="cv-hero__name">Святослав Архангельский</h1>
            <p class="cv-hero__role">AI инженер · Бизнес-аналитик · Технический директор · Программист</p>
            <div class="cv-hero__meta">
                <span>45 лет · Сочи · удалённо</span>
                <a href="tel:+79384547545">+7 (938) 454-75-45</a>
                <a href="mailto:dram1008@yandex.ru">dram1008@yandex.ru</a>
            </div>
            <p class="cv-hero__salary"><span>300 000</span> ₽ на руки</p>
        </div>
    </header>

    <article class="cv-content">
        <?= $resumeBody ?>
    </article>
</div>
