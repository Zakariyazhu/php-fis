<?php
/**
 * @名称 config.inc.php
 * @功能 config
 * @author <zhukai05@baidu.com>
 * @version v1.0
 */

define('DEBUG', true);//开发中
define("DB_DEBUG",true);//mysql debug
define('IS_SECURE', true);

define('IS_SYNC',true);//Synchronize controller

define('PRJ_ROOT', dirname(dirname(dirname(__FILE__))));

define('WWW_ROOT', PRJ_ROOT . '/app');
define("PHP_ROOT", PRJ_ROOT . "/php");
//define("PHP_SHELL",PRJ_ROOT."/shell");
define("PHP_MODEL",PHP_ROOT."/model/dao");
define("PHP_FREWORK",PHP_ROOT."/framework");
define("PHP_LIB", PHP_ROOT . "/lib");
define("PHP_INCLUDE", PHP_ROOT . "/include");
define("PHP_MODULE", PHP_ROOT . "/module");
define("PHP_TPL", WWW_ROOT . "/template");
define("UPLOAD_ROOT",PRJ_ROOT . "/app/upload");
define("PHP_EXT",PHP_ROOT."/ext");
define("PHP_ENGINE",PHP_ROOT."/engine");
define('PHP_CONFIG', PHP_ROOT. '/config');
define("PHP_SERVICE",PHP_ROOT."/service");
define('PHP_EXCEPTION', PHP_ROOT."/exception");
define("CHINO_DEFAULTENCODE",'utf-8');

//db prefix
define("TABLE_PRE","cloud_");
define("CLOUD_VERSION","201404042121");
define("CLOUD_COMPRESS",1);

define("PHP_SMARTY",PHP_ROOT."/smarty");
define('SMARTY_PLUGIN', PHP_SMARTY.'/baidu_plugin');

define('APP_ID', 'wx1b8d85af328e420b');
define('MCH_ID', '1271078001');
define('MCH_KEY', '098f6bcd4621d373cade4e832627b4f6');
define('APP_SECRET', '94ebea231abd890faf0545483fc4b801');

//产品线
define('TEACHER_ANDROID', 100);
define('TEACHER_IOS', 101);
define('STUDENT_ANDROID', 102);
define('STUDENT_IOS', 103);