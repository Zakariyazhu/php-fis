<?php
require_once(PHP_SMARTY . '/Smarty.class.php');

class index extends page {
    
    private $display;
    function __construct() {
        parent::__construct();
        $this->router();
    }
    
    function router() {
        $do = $this->input('do') ? $this->input('do') : "index";
        $this->display = $this->input('display');
        $this->$do();
    }

    private function index() {
        //默认首页
        //echo '哈哈，你上当了！';
        //exit;
        parent::view('test');
        $this->assign('test', '123');
        $this->display('index.html');
    }
    
    private function test(){
        if($this->display == 'json'){
        $retData = ['status' => Code::SUCCESS, 'message' => '', 'data' => ['a' => ['a'=>1,'b'=>2], 'b' => 2]];
            return Response::json($retData);
        }else{
                    parent::view('mfkls');
        $this->assign('data', range(1,100));
        $this->display('mfkls/page/appShare/appShopShare.tpl');  
        }      
    }

}
