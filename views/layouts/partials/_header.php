<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a(
        '<span class="logo-mini">P+</span><span class="logo-lg">'
            . Html::img(Url::to('@web/uploads/presenca-plus-logo.png'), [
                'alt' => 'Presença+',
                'class' => 'brand-logo',
            ])
            . '</span>',
        Yii::$app->homeUrl,
        ['class' => 'logo']
    ) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= Url::to(['/admin/logout']) ?>">
                        <i class='glyphicon glyphicon-log-out'></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
