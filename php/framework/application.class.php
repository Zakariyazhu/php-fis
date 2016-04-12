<?php

require_once(PHP_FREWORK . "/page.class.php");

class Application {

    var $_module = 'index';                           //使用模块
    var $_act = 'index';

    function Application($module = '', $act = '') {
        if (!defined("PHP_FREWORK")) {
            define(PHP_FREWORK, dirname(__FILE__));
        }
        $this->setModule($module);
        $this->setAct($act);
    }

    function setModule($module) {
        if (!defined("PHP_MODULE")) {
            define("PHP_MODULE", PHP_FRWWORK . "/../modules");
        }
        $this->_module = strtolower($module);
    }

    function setAct($act) {
        $this->_act = strtolower($act);
    }

    function getModule() {
        return $this->_module;
    }

    function getAct() {
        return $this->_act;
    }

    function run() {
        $this->initEnv();
        $this->execute();
        exit;
    }

    function initEnv() {
        if (!defined('ENCODE')) {
            header("Content-type: text/html; charset=" . CHINO_DEFAULTENCODE);
            define("ENCODE", CHINO_DEFAULTENCODE);
        } else {
            header("Content-type: text/html; charset=" . ENCODE);
        }
        header("Pragma: no-cache");
    }

    function execute() {
        //var_dump(file_exists(PHP_MODULE."/".$this->_module.'/'.strtolower($this->_act).".act.php"));exit;
        if (file_exists(PHP_MODULE . "/" . $this->_module . '/' . strtolower($this->_act) . ".act.php")) {
            require_once(PHP_MODULE . "/" . $this->_module . '/' . strtolower($this->_act) . ".act.php");
        } else {
            header("Location: /error");exit;
        }
        new $this->_act;
    }

}
