<?php

global $templateConfig;
$templateConfig = array(
    #通用模板
    'common' => array(
        'template_dir' => '/com/', //模板目录
        'compile_dir' => '/com/compile/', //编译目录                
    ),
    'plugins_dir' => SMARTY_PLUGIN,
    'debugging' => false,
    //'compile_check'     =>,
    //'config_dir'        =>'/config/',//配置文件
    //'cache_dir'         =>,
    'left_delimiter' => "{%", //左边界
    'right_delimiter' => "%}", //右边界
    'compile_check' => 1,// 0 - 不检测，有助于提升性能 1 - 检测
);
