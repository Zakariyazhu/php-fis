<?php
//memcache客户端“一致性hash算法”设置
//ini_set('memcache.hash_strategy','consistent'); 
//ini_set('memcache.hash_function','crc32');
ini_set('default_socket_timeout', -1); 
session_cache_limiter('private, must-revalidate');

require_once(PHP_LIB.'/db_mysql.class.php');
require_once(PHP_INCLUDE.'/global.func.php'); //全局函数类
require_once(PHP_SERVICE."/service.php"); //数据库驱动类
require_once(PHP_FREWORK.'/application.class.php');
require_once(PHP_LIB."/sdk/HttpClient.php"); //api请求类
require_once(PHP_LIB."/sdk/Crypt3Des.php"); //api加密解密类
require_once(PHP_EXCEPTION."/code.class.php"); //异常定义类
require_once(PHP_EXCEPTION."/message.class.php"); //异常定义类
require_once(PHP_INCLUDE."/response.class.php"); //异常定义类
require_once(PHP_LIB."/cookie/cookie.class.php");
require_once(PHP_LIB."/cookie/session.class.php");
require_once(PHP_CONFIG."/smarty.inc.php");

$onlineip = getip();
preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
$onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : '127.0.0.1';
unset($onlineipmatches);

if (get_magic_quotes_gpc())
{ 
	//exit("please set magic_quotes_gpc in php.ini to off"); 
}

//防止全局变量被修改
$_INPUT = $_REQUEST;
unset($_GET,$_POST,$_REQUEST);
//${$db_handle_name} = new dbstuff($dbconfig);

//全局配置
$_CACHE = array();
/*
 //redis proxy
$redis_proxy = new Redis();
$redis_proxy->connect($redis_host['host'],$redis_host['port']);
$redis_proxy->select(0);
*/

//db init
service::init("web","mysql");
service::init("api","mysql");
