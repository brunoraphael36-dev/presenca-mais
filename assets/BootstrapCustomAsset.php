<?php


namespace app\assets;

use yii\web\AssetBundle;

class BootstrapCustomAsset extends AssetBundle
{
    public $sourcePath = '@webroot/src/bootstrap';
    public $css = [
        'css/bootstrap.min.css',
    ];
}