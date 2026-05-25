<?php

declare(strict_types=1);

namespace app\controllers;

use yii\web\Controller;

class AvatarController extends Controller
{
    public function actionIndex(): string
    {
        $this->layout = 'avatar';
        $this->view->title = 'AVATARS COMMUNITY — ПОВО';

        return $this->render('index');
    }
}
