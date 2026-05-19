<?php

declare(strict_types=1);

/** @var yii\web\View $this */

$img = static fn (string $name): string => Yii::getAlias("@web/img/landing/{$name}");
?>
<main class="landing">
    <section class="landing-hero" aria-labelledby="hero-title">
        <picture class="landing-hero__bg" aria-hidden="true">
            <source media="(max-width: 767px)" srcset="<?= $img('hero-mobile.png') ?>">
            <img
                src="<?= $img('hero-bg.png') ?>"
                alt=""
                width="1440"
                height="800"
                decoding="async"
                fetchpriority="high"
            >
        </picture>
        <p class="landing-brand">
            <span>Калининград</span>
            <span>остров канта</span>
        </p>
        <div class="landing-hero__content">
            <h1 id="hero-title" class="landing-display">Кольцо познания</h1>
            <p class="landing-lead">эксклюзивное ювелирное изделие</p>
            <div class="landing-hero__actions">
                <a class="landing-btn landing-btn--primary" href="#contact">Купить кольцо</a>
                <a class="landing-btn landing-btn--secondary" href="#steps">Участвовать в игре</a>
            </div>
        </div>
    </section>

    <section class="landing-philosophy" aria-label="Философия">
        <img class="landing-philosophy__deco landing-philosophy__deco--left" src="<?= $img('ring-deco.png') ?>" alt="" width="552" height="828" decoding="async">
        <img class="landing-philosophy__deco landing-philosophy__deco--right" src="<?= $img('ring-deco.png') ?>" alt="" width="552" height="828" decoding="async">
        <div class="landing-philosophy__quotes">
            <p class="landing-philosophy__quote">Кольцо символизирует мужество мыслить самостоятельно и искать истину через самопознание</p>
            <p class="landing-philosophy__quote">путь к пониманию мира начинается с понимания себя</p>
        </div>
    </section>

    <section class="landing-sapere" aria-labelledby="sapere-title">
        <div class="landing-sapere__bg" aria-hidden="true"></div>
        <div class="landing-sapere__inner">
            <div class="landing-sapere__scene">
                <img
                    class="landing-sapere__backdrop"
                    src="<?= $img('sapere-frame.png') ?>"
                    alt=""
                    width="780"
                    height="415"
                    decoding="async"
                >
                <div class="landing-sapere__cathedral-wrap">
                    <picture>
                        <source media="(max-width: 767px)" srcset="<?= $img('cathedral-mobile.png') ?>">
                        <img
                            class="landing-sapere__cathedral"
                            src="<?= $img('cathedral-sketch.png') ?>"
                            alt="Кёнигсбергский собор"
                            width="780"
                            height="415"
                            decoding="async"
                        >
                    </picture>
                </div>
                <div class="landing-sapere__frame">
                    <h2 id="sapere-title" class="landing-display landing-display--sm">SAPERE AUDE</h2>
                    <p>Латинская фраза, означающая <strong>«Дерзай знать»</strong> или <strong>«Имей мужество пользоваться собственным разумом»</strong></p>
                    <p>она стала девизом эпохи Просвещения благодаря <strong>Канту</strong>, который использовал её в своей работе <strong>«Что такое просвещение?»</strong>, призывая людей освободиться от неспособности мыслить самостоятельно, используя свой собственный разум, а не руководство других</p>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-features" aria-labelledby="features-title">
        <div class="landing-features__bg" aria-hidden="true"></div>
        <h2 id="features-title" class="landing-features__title landing-display">Уникальность артефакта</h2>
        <ul class="landing-features__grid">
            <li class="landing-features__item landing-features__item--amber">
                <img src="<?= $img('feature-amber.png') ?>" alt="" width="292" height="272" decoding="async">
                <h3 class="landing-display landing-display--xs">янтарь</h3>
                <p>Символ света и жизненной энергии, рожденный Балтийским морем</p>
            </li>
            <li class="landing-features__item landing-features__item--kant">
                <img src="<?= $img('feature-kant.png') ?>" alt="" width="292" height="272" decoding="async">
                <h3 class="landing-display landing-display--xs">философия канта</h3>
                <p>Идея самостоятельного мышления и внутренней свободы</p>
            </li>
            <li class="landing-features__item landing-features__item--geometry">
                <img src="<?= $img('feature-geometry.png') ?>" alt="" width="292" height="272" decoding="async">
                <h3 class="landing-display landing-display--xs">сакральная геометрия</h3>
                <p>Символ гармонии, порядка<br>и связи человека с миром</p>
            </li>
            <li class="landing-features__item landing-features__item--limited">
                <img src="<?= $img('feature-limited.png') ?>" alt="" width="292" height="272" decoding="async">
                <h3 class="landing-display landing-display--xs">лимитированная серия</h3>
                <p>Эксклюзивный коллекционный артефакт</p>
            </li>
        </ul>
    </section>

    <section class="landing-offer" aria-labelledby="offer-title">
        <div class="landing-offer__header">
            <h2 id="offer-title" class="landing-display">Кольцо познания</h2>
            <p class="landing-lead">эксклюзивное ювелирное изделие</p>
        </div>
        <div class="landing-offer__visual">
            <div class="landing-offer__composition">
                <img
                    class="landing-offer__ring"
                    src="<?= $img('ring-product.png') ?>"
                    alt="Кольцо познания"
                    width="666"
                    height="500"
                    decoding="async"
                >
                <aside class="landing-offer__tag-group" aria-label="Специальная цена">
                    <div class="landing-offer__price-tag">
                        <img src="<?= $img('price-tag.png') ?>" alt="" width="524" height="189" decoding="async">
                        <div class="landing-offer__price-text">
                            <span>специальная цена</span>
                            <strong>15 000 ₽</strong>
                        </div>
                    </div>
                    <p class="landing-offer__note">
                        Только для участников, прошедших экскурсию<br>
                        и получивших цифровую награду «Кольцо познания»
                    </p>
                </aside>
            </div>
        </div>
        <p class="landing-offer__full-price"><span>стоимость для всех</span> <strong>75 000 ₽</strong></p>
    </section>

    <section class="landing-steps" id="steps" aria-labelledby="steps-title">
        <div class="landing-steps__bg" aria-hidden="true"></div>
        <h2 id="steps-title" class="landing-steps__title landing-display">путь к особой цене в <span>15 000 ₽</span></h2>
        <ol class="landing-steps__list">
            <li>
                <img src="<?= $img('step-tour.png') ?>" alt="" width="284" height="284" decoding="async">
                <p class="landing-display landing-display--xs">Пройдите экскурсию<br>на острове Канта</p>
            </li>
            <li class="landing-steps__arrow" aria-hidden="true"><img src="<?= $img('arrow.svg') ?>" alt="" width="73" height="399"></li>
            <li>
                <img src="<?= $img('step-reward.png') ?>" alt="" width="284" height="284" decoding="async">
                <p class="landing-display landing-display--xs">Получите цифровую награду<br>«Кольцо познания»</p>
            </li>
            <li class="landing-steps__arrow" aria-hidden="true"><img src="<?= $img('arrow.svg') ?>" alt="" width="73" height="399"></li>
            <li>
                <img src="<?= $img('step-buy.png') ?>" alt="" width="284" height="284" decoding="async">
                <p class="landing-display landing-display--xs">возможность<br>приобрести артефакт<br>по особой цене</p>
            </li>
        </ol>
    </section>

    <section class="landing-contact" id="contact" aria-labelledby="contact-title">
        <div class="landing-contact__bg" aria-hidden="true"></div>
        <div class="landing-contact__inner">
            <h2 id="contact-title" class="landing-display">Свяжитесь с&nbsp;нами</h2>
            <p class="landing-contact__lead">Заполни форму, чтобы оставить заявку на приобретение Кольца Познания.</p>
            <form class="landing-contact__form" method="post" action="#contact">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="landing-contact__fields">
                    <label class="landing-field">
                        <span class="visually-hidden">Имя</span>
                        <input type="text" name="name" placeholder="Как Вас зовут" autocomplete="name">
                    </label>
                    <label class="landing-field">
                        <span class="visually-hidden">Телефон</span>
                        <input type="tel" name="phone" placeholder="+7 (999) 999 99 99" autocomplete="tel">
                    </label>
                    <button type="submit" class="landing-btn landing-btn--primary landing-btn--compact">Позвонить мне</button>
                </div>
                <p class="landing-contact__legal">
                    Нажимая на кнопку «Позвонить мне», вы даёте согласие на обработку
                    <a href="#">персональных&nbsp;данных</a>
                </p>
            </form>
        </div>
    </section>
</main>
