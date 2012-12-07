<?php

namespace ApplicationTest; //Change this namespace for your test

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap
{

    protected static $serviceManager;
    protected static $config;
    protected static $bootstrap;

    public static function init()
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load
        if (is_readable(__DIR__ . '/TestConfiguration.php')) {
            $testConfig = include __DIR__ . '/TestConfiguration.php';
        } else {
            $testConfig = include __DIR__ . '/TestConfiguration.php.dist';
        }

        $zf2ModulePaths = array();

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath))) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ? : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        chdir(__DIR__);
        $previousDir = '';
        while (!file_exists('config/application.config.php')) {
            $dir = dirname(getcwd());
            if ($previousDir === $dir) {
                throw new RuntimeException(
                   'Unable to locate "config/application.config.php":'
                   . ' is CdliTwoStageSignup in a subdir of your application skeleton?'
                );
            }
            $previousDir = $dir;
            chdir($dir);
        }
        $configuration = include 'config/application.config.php';
        $configuration['module_listener_options']['config_glob_paths'][] = __DIR__ . '/config/{,*.}{global,local}.php';


// Setup service manager
        $serviceManager = new ServiceManager(new ServiceManagerConfig(@$configuration['service_manager'] ? : array()));
        $serviceManager->setService('ApplicationConfig', $configuration);
        $serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');
        $serviceManager->get('ModuleManager')->loadModules();

        $config = $serviceManager->get('Configuration');

        Framework\TestCase::setServiceLocator($serviceManager);
        Framework\TestCase::setOptions(new Framework\TestCaseOptions($config['application-test']));

        static::$serviceManager = $serviceManager;
        static::$config = $configuration;
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getConfig()
    {
        return static::$config;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        } else {
            $zf2Path = getenv('ZF2_PATH') ? : (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        }

        AutoloaderFactory::factory(array(
           'Zend\Loader\StandardAutoloader' => array(
              'autoregister_zf' => true,
              'namespaces' => array(
                 __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
              ),
           ),
        ));
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir)
                return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }

}

Bootstrap::init();