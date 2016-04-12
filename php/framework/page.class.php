<?php
//use FklsWx\Lib\sdk\HttpClient;
require_once(PHP_CONFIG."/config_api_params.inc.php");
require_once(PHP_SMARTY . '/Smarty.class.php');
require_once(SMARTY_PLUGIN . '/FISResource.class.php');

class page {

    public $input;
    public $httpClient;
    public $cookie;
    public $smarty;
    public $message;

    function __construct() {
        global $_INPUT, $apiConfig;
        $this->input = &$_INPUT;
        $this->httpClient = new HttpClient($apiConfig);
        $this->cookie = new Cookies('entwx');
        $this->message = new Message();
    }

    /**
     * @param unknown_type $var
     * @param unknown_type $type
     * @param unknown_type $securet
     * @return unknown
     */
    public function input($var, $type = 'string', $html = true, $securet = true) {
        if (isset($this->input[$var]) && !is_null($this->input[$var])) {
            if ($type == 'int') {
                return intval($this->input[$var]);
            } else {
                if ($html == true) {
                    if ($securet == true) {
                        //todo 去掉<script,iframe,标签>
                        $search = array("'<script[^>]*?>.*?(</script>)?'si", // 去掉 <script>
                            "'<iframe[^>]*?>.*?(</iframe>)?'si", // 去掉 <iframe>
                            "'<style[^>]*?>.*?(</style>)?'si");  // 去掉 <style>
                        $this->input[$var] = @preg_replace($search, "", $this->input[$var]);
                    }
                } else {
                    $this->input[$var] = strip_tags($this->input[$var]);
                }
                return $this->input[$var];
            }
        } else {
            return false;
        }
    }

    /**
     * 实例化模板引擎smarty
     * @return void
     */
    public function view($tplDir = 'common') {
        global $templateConfig;
        $this->smarty = &new Smarty();
        $this->smarty->setPluginsDir(array(
            $templateConfig['plugins_dir']
        ));
        $this->smarty->setConfigDir(PHP_TPL . '/'.$tplDir.'/data/smarty/config'); //插件map-json目录
        if (!isset($templateConfig[$tplDir])) {
            $this->smarty->setTemplateDir(PHP_TPL . '/' . $tplDir . '/template/'); //模板存放目录
            $this->smarty->setCompileDir(PHP_TPL . '/' . $tplDir . '/compile/'); //编译目录
        } else {
            $this->smarty->setTemplateDir(PHP_TPL . $templateConfig[$tplDir]['template_dir']); //模板存放目录
            $this->smarty->setCompileDir(PHP_TPL . $templateConfig[$tplDir]['compile_dir']); //编译目录
        }
        $this->smarty->left_delimiter = $templateConfig['left_delimiter']; //左定界符
        $this->smarty->right_delimiter = $templateConfig['right_delimiter']; //右定界符
        $this->smarty->caching = false;
        $this->smarty->cache_lifetime = 0;
        $this->smarty->debugging = $templateConfig['debugging'];
    }

    /**
     * 重写Smarty  display方法
     * @param string  $tpl 模版名称
     * @return void
     */
    public function display($tpl) {
        $this->smarty->display($tpl);
    }

    /**
     * 重写Smarty  assign方法
     * @param string  $tpl 模版名称
     * @return void
     */
    public function assign($key, $ags = '') {
        $this->smarty->assign($key, $ags);
    }
    
    public function clear_cache($tpl){
        $this->smarty->clear_cache($tpl);
    }
    
    public function __call($m, $args){
        echo "{$m} mothod not exists!";
        exit;
    }

}
