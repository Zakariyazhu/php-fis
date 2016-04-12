<?php

class service {

    function __construct() {
        
    }

    function init($name, $type) {
        require_once(PHP_ROOT . "/service/{$name}/service.{$type}.php");
        switch ($name) {
            case 'api':
                return new apiDataService($type);
                break;
            case 'web':
                return new webDataService($type);
                break;
            default:
                return new systemDataService($type);
                break;
        }
    }

    function __destruct() {
        
    }

}
