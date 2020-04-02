<?php

use ddmp\app\shop\override\yii\web\Application;
use ddmp\common\enums\SessionKeyEnum;
use ddmp\common\utils\config\Environment;

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../common/config/defines.php';
require __DIR__ . '/../../../common/extend/yii/Yii.php';
require __DIR__ . '/../../../app/shop/config/bootstrap.php';

$config = \ddmp\common\extend\yii\helpers\ArrayHelper::mergeAssoc(
	require __DIR__ . '/../../../common/config/main.php',
	require __DIR__ . '/../../../common/config/local.php',
	require __DIR__ . '/../config/main.php'
);

$app = new Application($config);
Yii::setAlias('@bower', __DIR__ . '/../../../vendor/bower-asset');
Environment::checkSpecialRequestParameter('grafana_activate', SessionKeyEnum::IS_GRAFANA_ACTIVE, '1', [Environment::ENV_DEV, Environment::ENV_STAGE]);
Environment::checkSpecialRequestParameter('tm_replacing_activate', SessionKeyEnum::IS_PARTNER_REPLACING_ACTIVE, '1', [Environment::ENV_DEV, Environment::ENV_STAGE]);
$app->run();