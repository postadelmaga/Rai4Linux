<?php

date_default_timezone_set('Europe/Rome');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//        set_error_handler(self::DEFAULT_ERROR_HANDLER, E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));

Vue::register('original_include_path', get_include_path());

/**
 * Set include path
 */
$paths = array();
$paths[] = BP . DS . 'app' . DS . 'code';
$paths[] = BP . DS . 'lib';

$appPath = implode(PS, $paths);
set_include_path($appPath . PS . Vue::registry('original_include_path'));

//include_once "Core/functions.php";
include_once "Varien/Autoload.php";

Varien_Autoload::register();

/**
 * Main App hub class
 *
 * @author      Appnto Core Team <core@Appntocommerce.com>
 */
final class Vue
{
    const LOG_DIR = "log";

    /**
     * Registry collection
     *
     * @var array
     */
    static private $_registry = array();

    /**
     * Application root absolute path
     *
     * @var string
     */
    static private $_appRoot;

    /**
     * Application model
     *
     * @var Mage_Core_Model_App
     */
    static private $_app;

    /**
     * Config Model
     *
     * @var Mage_Core_Model_Config
     */
    static private $_config;

    /**
     * Event Collection Object
     *
     * @var Varien_Event_Collection
     */
    static private $_events;

    /**
     * Object cache instance
     *
     * @var Varien_Object_Cache
     */
    static private $_objects;

    /**
     * Set all my static data to defaults
     *
     */
    public static function reset()
    {
        self::$_registry = array();
        self::$_appRoot = null;
        self::$_app = null;
        self::$_config = null;
        self::$_events = null;
        self::$_objects = null;
        // do not reset $headersSentThrowsException
    }

    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     * @param bool $graceful
     * @throws App_Core_Exception
     */
    public static function register($key, $value, $graceful = false)
    {
        if (isset(self::$_registry[$key])) {
            if ($graceful) {
                return;
            }
            self::throwException('App registry key "' . $key . '" already exists');
        }
        self::$_registry[$key] = $value;
    }

    /**
     * Unregister a variable from register by key
     *
     * @param string $key
     */
    public static function unregister($key)
    {
        if (isset(self::$_registry[$key])) {
            if (is_object(self::$_registry[$key]) && (method_exists(self::$_registry[$key], '__destruct'))) {
                self::$_registry[$key]->__destruct();
            }
            unset(self::$_registry[$key]);
        }
    }

    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     */
    public static function registry($key)
    {
        if (isset(self::$_registry[$key])) {
            return self::$_registry[$key];
        }
        return null;
    }

    /**
     * Set application root absolute path
     *
     * @param string $appRoot
     * @throws App_Core_Exception
     */
    public static function setRoot($appRoot = '')
    {
        if (self::$_appRoot) {
            return;
        }

        if ('' === $appRoot) {
            // automagically find application root by dirname of App.php
            $appRoot = dirname(__FILE__);
        }

        $appRoot = realpath($appRoot);

        if (is_dir($appRoot) and is_readable($appRoot)) {
            self::$_appRoot = $appRoot;
        } else {
            self::throwException($appRoot . ' is not a directory or not readable by this user');
        }
    }

    /**
     * Retrieve application root absolute path
     *
     * @return string
     */
    public static function getRoot()
    {
        return self::$_appRoot;
    }

    /**
     * Retrieve Events Collection
     *
     * @return Varien_Event_Collection $collection
     */
    public static function getEvents()
    {
        return self::$_events;
    }

    /**
     * Varien Objects Cache
     *
     * @param string $key optional, if specified will load this key
     * @return Varien_Object_Cache
     */
    public static function objects($key = null)
    {
        if (!self::$_objects) {
            self::$_objects = new Varien_Object_Cache;
        }
        if (is_null($key)) {
            return self::$_objects;
        } else {
            return self::$_objects->load($key);
        }
    }

    public static function run($code = '', $type = 'store', $options = array())
    {
        try {
            self::setRoot();
            self::$_app = new Core_App();

//            if (isset($options['request'])) {
//                self::$_app->setRequest($options['request']);
//            }
//            if (isset($options['response'])) {
//                self::$_app->setResponse($options['response']);
//            }

//            self::$_events = new Varien_Event_Collection();
//            self::_setConfigModel($options);
            self::$_app->run($code);
        } catch (Exception $e) {
            self::printException($e);
        }
    }

    /**
     * App constructor.
     * @param string $code
     * @param string $type
     * @param array $options
     *
     * @return Core_App
     */
    public static function app($code = '', $type = 'store', $options = array())
    {
        if (null === self::$_app) {
            if (!file_exists(Vue::getRoot())) {
                mkdir(Vue::getRoot(), 0777, true);
            }
            if (!file_exists(self::LOG_DIR)) {
                mkdir(self::LOG_DIR, 0777, true);
            }

            self::$_app = new Core_App();
            self::setRoot();
        }
        return self::$_app;
    }

    public static function printException(Exception $e, $extra = '')
    {
        if (self::$_isDeveloperMode) {
            if (TRUE) {
                print '<pre>';

                if (!empty($extra)) {
                    print $extra . "\n\n";
                }

                print $e->getMessage() . "\n\n";
                print $e->getTraceAsString();
                print '</pre>';
            } else {

                $reportData = array(
                    !empty($extra) ? $extra . "\n\n" : '' . $e->getMessage(),
                    $e->getTraceAsString()
                );

                // retrieve server data
                if (isset($_SERVER)) {
                    if (isset($_SERVER['REQUEST_URI'])) {
                        $reportData['url'] = $_SERVER['REQUEST_URI'];
                    }
                    if (isset($_SERVER['SCRIPT_NAME'])) {
                        $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
                    }
                }

                // attempt to specify store as a skin
                try {
                    $storeCode = self::app()->getStore()->getCode();
                    $reportData['skin'] = $storeCode;
                } catch (Exception $e) {
                }

                require_once(self::getBaseDir() . DS . 'errors' . DS . 'report.php');
            }

            die();
        }
    }
}
