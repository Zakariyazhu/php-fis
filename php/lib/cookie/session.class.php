<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//添加存储路径
class Session{
    
    public function __construct() {
        session_start();
    }
    
    public function set($name, $val){
        $_SESSION[$name] = $val;
    }
    
    public function get($name){
        return $_SESSION[$name];
    }
    
}

