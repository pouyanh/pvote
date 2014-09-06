<?php
/**
 * Created by IntelliJ IDEA.
 * User: pouyan
 * Date: 8/25/14
 * Time: 3:51 AM
 */

namespace PVote;

use PVote\Plugin\DispatcherEventsManager;
use Pattack\Config;
use Phalcon\Config\Adapter\Json as JsonConfig;
use Phalcon\DI\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Session\Adapter\Files as SessionFileAdapter;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;

class Bootstrap
{
    const DEFAULT_MODE = 'development';

    public function render()
    {
        try {
            return $this->getApplication()->handle()->getContent();
        } catch (\Exception $e) {
            //  TODO: Perform a recursive error tracer + render it in template
            echo "Error occured: " . $e->getMessage();
        }
    }

    protected function getApplication()
    {
        $di = new FactoryDefault();

        $di->set(
            'config',
            function () {
                $config = new Config();
                $config->setVariable('ROOT_PATH', getenv('ROOT_PATH'));
                $config->setVariable('MODE', getenv('MODE') ? getenv('MODE') : self::DEFAULT_MODE);
                $config->merge(new JsonConfig(__DIR__ . '/Config/' . $config->getVariable('MODE') . '.json'));

                return $config;
            }
        );

        $di->set(
            'view',
            function () {
                $view = new View();
                $view->setViewsDir(__DIR__ . '/View');

                $view->registerEngines(
                    [
                        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
                    ]
                );

                return $view;
            }
        );

        $di->set(
            'dispatcher',
            function () use ($di) {
                /** @var EventsManager $eventsManager */
                $eventsManager = $di->getShared('eventsManager');

                $security = new DispatcherEventsManager($di);

                $eventsManager->attach('dispatch', $security);

                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace('PVote\Controller');
                $dispatcher->setEventsManager($eventsManager);

                return $dispatcher;
            }
        );

        $di->set(
            'session',
            function () {
                $session = new SessionFileAdapter();
                $session->start();

                return $session;
            }
        );

        return new Application($di);
    }
}
