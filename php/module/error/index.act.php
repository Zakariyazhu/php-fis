<?php
class index extends page {
    
    
    function __construct() {
        parent::__construct();
        $this->notfound();
    }

    private function notfound() {
        echo 'error';exit;
        parent::view('error');
        $userinfo = get_userinfo($_COOKIE['uid']);
        $this->assign("register_time", $userinfo['register_time']);
        $this->assign("role_id", $userinfo['role_id']);
        $this->assign("name", $_COOKIE['name']);
        $this->assign("ip_address", getip());
        $this->display('right.html');
    }

}
