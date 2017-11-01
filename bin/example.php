<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\Application as ZendApplication;
use Zf3SymfonyConsole\Controller\Plugin\ConsoleParams;
use Application\Controller\IndexController;

$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge(
    $appConfig, require __DIR__ . '/../config/development.config.php'
    );
}
$zendApplication = ZendApplication::init($appConfig);
$serviceManager = $zendApplication->getServiceManager();

(new Application('Index Application'))
->register('index')
->addArgument('value', InputArgument::REQUIRED, 'Parameter value')
->setCode(function(InputInterface $input, OutputInterface $output) use ($serviceManager) {

    $serviceManager->get('ControllerPluginManager')
    ->setService('params', new ConsoleParams($input));

    $controller = $serviceManager->get('ControllerManager')->get(IndexController::class);

    echo "Running IndexController::indexAction with value='" . $controller->params('value') . "'" . PHP_EOL;
    $controller->indexAction();
})
->getApplication()
->setDefaultCommand('echo', true)
->run();
