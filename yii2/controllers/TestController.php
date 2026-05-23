<?php

declare(strict_types=1);

namespace app\controllers;

use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex(): string
    {
        $this->view->title = 'Тест окружения';

        return $this->render('index');
    }
}
