<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Créditos';

$this->registerCss("
    body {
        font-family: Arial, sans-serif;
        font-size: 16px;
        line-height: 1.5;
        color: #333;
    }

    .page-header { margin-top: 30px; margin-bottom: 20px; }
    .uxorcistas-logo {
        display: block;
        width: 100%;
        max-width: 720px;
        height: auto;
        margin: 24px auto;
    }
    .custom-size-UFC {
        display: block;
        width: 100px;
        height: auto;
        margin: 0 auto;
    }
    .brand-strip { margin: 40px 0; }

    footer.credits-footer {
        margin-bottom: 40px;
        color: #666;
    }
");
?>

<div class="site-credits container">
    <div class="text-center page-header">
        <h1><?= Html::encode('Presença +') ?></h1>
        <h3 class="text-muted"><?= Html::encode('Sobre o aplicativo') ?></h3>
    </div>

    <div class="text-center">
        <p>O aplicativo Presença + foi criado por um grupo de estudantes chamado <strong>Uxorcistas</strong>.</p>
        <?= Html::img('@web/uploads/uxorcistas.png', [
            'class' => 'uxorcistas-logo',
            'alt' => 'Uxorcistas',
        ]) ?>
    </div>

    <div class="text-center brand-strip">
        <?= Html::img(
            'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Brasao4_vertical_cor_300dpi.png/500px-Brasao4_vertical_cor_300dpi.png',
            ['class' => 'custom-size-UFC', 'alt' => 'UFC']
        ) ?>
    </div>

    <footer class="credits-footer text-center">
        <p>© 2026 Universidade Federal do Ceará</p>
        <p>Todos os direitos reservados</p>
    </footer>
</div>