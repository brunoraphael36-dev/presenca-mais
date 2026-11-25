<?php

use app\components\filters\AccessRule;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \app\models\User|null $user */
$user = Yii::$app->user->identity;

$menuItems = [
    [
        'label' => 'Presença',
        'icon' => 'fa-list-alt',
        'url' => ['/frequencia/default/index'],
        'permission' => 'presenca.access',
    ],
    [
        'label' => 'Grupos',
        'icon' => 'fa-users',
        'url' => ['/grupo/default/index'],
        'permission' => 'grupo.access',
    ],
    [
        'label' => 'Participantes',
        'icon' => 'fa-user',
        'url' => ['/participante/default/index'],
        'permission' => 'participante.access',
    ],
    [
        'label' => 'Sobre',
        'icon'  => 'fa-info-circle',
        'url'   => ['/frequencia/default/creditos'],
    ],
];

?>

<aside class="main-sidebar">
    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="" class="img-circle" alt="" />
            </div>
            <div class="pull-left info"></div>
        </div>

        <ul class="sidebar-menu tree" data-widget="tree">
            <?php foreach ($menuItems as $item): ?>
                <?php
                $canSee = $user && (!empty($item['permission']) ? $user->hasPermission($item['permission']) : true);
                if (!$canSee) {
                    continue;
                }?>
                <li>
                    <a href="<?= Url::to($item['url']) ?>">
                        <i class="fa <?= $item['icon'] ?>"></i> <span><?= $item['label'] ?></span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</aside>