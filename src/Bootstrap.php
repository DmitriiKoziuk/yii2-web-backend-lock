<?php
namespace DmitriiKoziuk\yii2WebBackendLock;

use Yii;
use yii\helpers\Url;
use yii\web\Application as WebApp;
use yii\web\Controller;
use yii\base\BootstrapInterface;
use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApp) {
            /** @var ConfigService $configService */
            $configService = Yii::$container->get(ConfigService::class);
            $backendAppId = $configService->getValue(
                ConfigManagerModule::GENERAL_CONFIG_NAME,
                'backendAppId'
            );
            if ($backendAppId == $app->id) {
                $app->on(Controller::EVENT_BEFORE_ACTION, function () use ($app) {
                    if (
                        $app->getUser()->isGuest &&
                        $app->getRequest()->url !== Url::to($app->getUser()->loginUrl)
                    ) {
                        $app->getResponse()->redirect($app->getUser()->loginUrl)->send();
                        exit;
                    }
                });
            }
        }
    }
}