<?php
class systemDataService{
    function __construct($type){
        global $systemDb;
        if(!$systemDb){
            require_once(PHP_ROOT."/config/config_web_db.inc.php");
            $systemDb = new dbstuff($dbconfig);
        }
    }
    
}