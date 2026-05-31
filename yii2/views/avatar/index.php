<?php

declare(strict_types=1);

/** @var yii\web\View $this */

$img = static fn (string $name): string => Yii::getAlias("@web/img/avatar/{$name}");
$file = static fn (string $name): string => Yii::getAlias("@web/files/{$name}");

$benefits = [
    [
        'title' => 'Объединение участников и компаний из разных стран',
        'text' => 'Возможность объединения российских и зарубежных компаний и граждан.',
        'icon' => 'globe',
    ],
    [
        'title' => 'Кооперативные участки без регистрации',
        'text' => 'Потребительское общество может расширить присутствие через кооперативные участки без регистрации в местных налоговых органах.',
        'icon' => 'home',
    ],
    [
        'title' => 'Внутренний обмен вместо купли-продажи',
        'text' => 'Члены потребкооперации внутри системы производят обмен товарами и услугами в некоммерческом правовом поле.',
        'icon' => 'exchange',
    ],
    [
        'title' => 'Защищённая форма собственности',
        'text' => 'Одна из самых защищённых форм коллективного взаимодействия и собственности.',
        'icon' => 'shield',
    ],
    [
        'title' => 'Защита паевых взносов',
        'text' => 'На паевые взносы пайщиков не распространяются исковые требования со стороны третьих лиц.',
        'icon' => 'lock',
    ],
    [
        'title' => 'Субсидиарная ответственность в размере паевого взноса пайщика',
        'text' => 'Ответственность пайщика ограничивается размером внесённого паевого взноса.',
        'icon' => 'scale',
    ],
    [
        'title' => 'Оптимальные поставки по выгодным условиям',
        'text' => 'Кооператив может централизовать поставки товаров и услуг для участников по оптимальным ценам.',
        'icon' => 'cart',
    ],
    [
        'title' => 'Налоговые преимущества',
        'text' => 'Вступительные, целевые и паевые взносы не подлежат налогообложению.',
        'icon' => 'percent',
    ],
];

$steps = [
    ['num' => '01', 'title' => 'Скачать документы', 'text' => 'Загрузите комплект документов для вступления.', 'icon' => 'download', 'link' => null],
    ['num' => '02', 'title' => 'Подписать документы', 'text' => 'Заполните и подпишите все необходимые формы.', 'icon' => 'pen', 'link' => null],
    [
        'num' => '03',
        'title' => 'Отправить документы на почту:',
        'text' => 'Avatarsmega@gmail.com',
        'icon' => 'send',
        'link' => 'mailto:Avatarsmega@gmail.com',
    ],
    ['num' => '04', 'title' => 'Оплатить вступительный взнос', 'text' => 'Внесите вступительный взнос согласно договору.', 'icon' => 'card', 'link' => null],
    ['num' => '05', 'title' => 'Получить доступ к программам сообщества AVATARS', 'text' => 'Добро пожаловать в экосистему AVATARS.', 'icon' => 'star', 'link' => null],
];

$documents = [
    ['num' => '01', 'title' => 'Устав ПОВО', 'file' => 'ustav_povo.docx', 'accent' => 'purple', 'icon' => 'scales'],
    ['num' => '02', 'title' => 'Анкета Заявителя Физ. лицо', 'file' => 'anketa_zayav_fiz.doc', 'accent' => 'blue', 'icon' => 'user'],
    ['num' => '03', 'title' => 'Анкета Заявителя Юр. лицо', 'file' => 'anketa_zayav_yur.doc', 'accent' => 'cyan', 'icon' => 'briefcase'],
    ['num' => '04', 'title' => 'Договор', 'file' => 'dogovor_oferta.doc', 'accent' => 'green', 'icon' => 'handshake'],
    ['num' => '05', 'title' => 'Договор паевого взноса', 'file' => 'dogovor_paevogo_vznosa.doc', 'accent' => 'orange', 'icon' => 'percent-doc'],
];
?>
<header class="avatar-header">
    <div class="avatar-container avatar-header__inner">
        <a class="avatar-logo" href="#top" aria-label="AVATARS COMMUNITY">
            <img class="avatar-logo__img" src="<?= $img('logo.png') ?>" alt="" width="40" height="40" decoding="async">
            <span class="avatar-logo__text">AVATARS <small>COMMUNITY</small></span>
        </a>
        <nav class="avatar-nav" aria-label="Навигация">
            <a href="#benefits">Преимущества</a>
            <a href="#steps">Участие</a>
            <a href="#documents">Документы</a>
            <a href="#contact">Контакты</a>
        </nav>
        <a class="avatar-btn avatar-btn--outline avatar-btn--sm" href="#documents">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Скачать
        </a>
    </div>
</header>

<main class="avatar-page" id="top">
    <section class="avatar-hero" aria-labelledby="hero-title">
        <div class="avatar-hero__bg" aria-hidden="true">
            <div class="avatar-hero__gradient"></div>
            <div class="avatar-hero__orb avatar-hero__orb--1"></div>
            <div class="avatar-hero__orb avatar-hero__orb--2"></div>
            <div class="avatar-hero__orb avatar-hero__orb--3"></div>
        </div>
        <div class="avatar-container avatar-hero__inner">
            <span class="avatar-badge">COMMUNITY</span>
            <h1 id="hero-title" class="avatar-hero__title">AVATARS COMMUNITY</h1>
            <p class="avatar-hero__subtitle">ПОВО — программа участия</p>
            <div class="avatar-hero__text">
                <p>Целевая программа по улучшению благосостояния, долголетия и совместного развития участников экосистемы AVATARS.</p>
                <p>Станьте частью сообщества, участвуйте в паевых программах и совместных инициативах экосистемы.</p>
            </div>
            <div class="avatar-hero__actions">
                <a class="avatar-btn avatar-btn--primary" href="#documents">
                    Скачать документы
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>
    </section>

    <section class="avatar-section avatar-benefits" id="benefits" aria-labelledby="benefits-title">
        <div class="avatar-container">
            <h2 id="benefits-title" class="avatar-section__title">Преимущества ПОВО</h2>
            <ul class="avatar-benefits__grid">
                <?php foreach ($benefits as $i => $item): ?>
                <li class="avatar-card avatar-card--benefit" data-accent="<?= ($i % 4) + 1 ?>">
                    <div class="avatar-card__icon" aria-hidden="true">
                        <?php include __DIR__ . '/_icons/' . $item['icon'] . '.php'; ?>
                    </div>
                    <h3 class="avatar-card__title"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="avatar-card__text"><?= htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8') ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="avatar-section avatar-steps" id="steps" aria-labelledby="steps-title">
        <div class="avatar-container">
            <h2 id="steps-title" class="avatar-section__title">Как стать участником</h2>
            <ol class="avatar-steps__timeline">
                <?php foreach ($steps as $step): ?>
                <li class="avatar-step">
                    <div class="avatar-step__node">
                        <span class="avatar-step__num"><?= $step['num'] ?></span>
                    </div>
                    <div class="avatar-step__icon" aria-hidden="true">
                        <?php include __DIR__ . '/_icons/' . $step['icon'] . '.php'; ?>
                    </div>
                    <h3 class="avatar-step__title"><?= htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php if ($step['link']): ?>
                    <a class="avatar-step__link" href="<?= htmlspecialchars($step['link'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($step['text'], ENT_QUOTES, 'UTF-8') ?></a>
                    <?php else: ?>
                    <p class="avatar-step__text"><?= htmlspecialchars($step['text'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <section class="avatar-section avatar-docs" id="documents" aria-labelledby="docs-title">
        <div class="avatar-container">
            <h2 id="docs-title" class="avatar-section__title">Документы для скачивания</h2>
            <ul class="avatar-docs__grid">
                <?php foreach ($documents as $doc): ?>
                <li class="avatar-card avatar-card--doc avatar-card--<?= $doc['accent'] ?>">
                    <span class="avatar-card__bg-num" aria-hidden="true"><?= $doc['num'] ?></span>
                    <div class="avatar-card__icon avatar-card__icon--lg" aria-hidden="true">
                        <?php include __DIR__ . '/_icons/' . $doc['icon'] . '.php'; ?>
                    </div>
                    <h3 class="avatar-card__title"><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <a class="avatar-btn avatar-btn--outline avatar-btn--xs" href="<?= $file($doc['file']) ?>" download="<?= htmlspecialchars($doc['file'], ENT_QUOTES, 'UTF-8') ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Скачать
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="avatar-section avatar-contact" id="contact" aria-labelledby="contact-title">
        <div class="avatar-container avatar-contact__inner">
            <div class="avatar-contact__content">
                <h2 id="contact-title" class="avatar-section__title">Мы на связи</h2>
                <p class="avatar-contact__lead">Если у вас есть вопросы — мы на связи.</p>
                <ul class="avatar-contact__list">
                    <li>
                        <span class="avatar-contact__label">Telegram:</span>
                        <a href="https://t.me/NewMillenniumStar" target="_blank" rel="noopener noreferrer">@NewMillenniumStar</a>
                    </li>
                    <li>
                        <span class="avatar-contact__label">Телефон:</span>
                        <a href="tel:+79104018833">+7 910 401 8833</a>
                    </li>
                </ul>
            </div>
            <div class="avatar-contact__visual" aria-hidden="true">
                <div class="avatar-contact__rings">
                    <span class="avatar-contact__ring avatar-contact__ring--1"></span>
                    <span class="avatar-contact__ring avatar-contact__ring--2"></span>
                    <span class="avatar-contact__ring avatar-contact__ring--3"></span>
                    <img class="avatar-logo__img avatar-logo__img--lg" src="<?= $img('logo.png') ?>" alt="" width="120" height="120" decoding="async">
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="avatar-footer">
    <div class="avatar-container">
        <p>© <?= date('Y') ?> AVATARS COMMUNITY — ПОВО</p>
    </div>
</footer>
