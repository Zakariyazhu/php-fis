<?php
class apiDataService{
    function __construct($type){
        global $apiDb;
        if(!$apiDb){
            require_once(PHP_ROOT."/config/config_api_db.inc.php");
            $apiDb = new dbstuff($dbconfig);
        }
    }
    
}