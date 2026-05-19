<?php

declare(strict_types=1);

namespace app\controllers;

use yii\web\Controller;

class LandingController extends Controller
{
    public function actionIndex(): string
    {
        $this->layout = 'landing';
        $this->view->title = 'Кольцо познания — Остров Канта';

        return $this->render('index');
    }
}
