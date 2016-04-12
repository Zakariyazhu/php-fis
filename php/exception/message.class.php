<?php

class Message{
    
    private $code;
    private $messageArr = [
        Code::SUCCESS => '操作成功',
        Code::SYSTEM_ERROR => '系统错误',
        Code::UNKNOW_ERROR => '未知错误',
        Code::LOGIN_FAILED => '登录失败',
    ];
    
    public function get(){
        if(isset($this->messageArr[$this->code])){
            return ['status' => $this->code, 'message' => $this->messageArr[$this->code]];
        }else{
            return ['status' => $this->code, 'message' => $this->messageArr[Code::UNKNOW_ERROR]];
        }
    }
    
    public function set($code){
        $this->code = $code;
    }
}