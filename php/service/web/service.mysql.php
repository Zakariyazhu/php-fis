<?php
class webDataService{
    function __construct($type){
        global $webDb;
        if(!$webDb){
            require_once(PHP_ROOT."/config/config_web_db.inc.php");
            $webDb = new dbstuff($dbconfig);
        }
    }
    
}