<?php
namespace DmitriiKoziuk\yii2WebBackendLock;

use Yii;
use yii\helpers\Url;
use yii\web\Application as WebApp;
use yii\web\Controller;
use yii\base\BootstrapInterface;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApp) {
            $backendAppId = $app->params['backendAppId'] ?? 'app-backend';
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