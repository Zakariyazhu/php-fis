<?php
error_reporting(E_ERROR);
require_once (dirname ( __FILE__ ) . '/config/config.inc.php');
//网站维护
if(!DEBUG){
	//include_once(PHP_TPL."/common/wh.html");
	exit;
}

require_once (PHP_ROOT . "/common/common.inc.php");
$module = empty ($_INPUT ['module'] ) ? (empty ( $_INPUT ['m'] ) ? 'index' : $_INPUT ['m']) : $_INPUT ['module'];
$act = empty ($_INPUT ['act'] ) ? (empty ( $_INPUT ['a'] ) ? 'index' : $_INPUT ['a']) : $_INPUT ['act'];

$app = new Application ( $module, $act );
$app->run ();
