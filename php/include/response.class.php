<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Response{
    
    static function json($input){
        echo json_encode($input);
        exit;
    }
    
}

