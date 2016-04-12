<?php
//namespace FklsWx\Lib\sdk;

class HttpClient
{

    private $_timeout = 5;

    private $_config;

    public function __construct($_config)
    {
        if($_config){
            $this->_config = $_config;
        }else{
            exit('config error!');
        }
    }

    public function updateHost(){
        unset($this->_config['api_host']);
        $this->_config['api_host'] = $this->_config['packet_api_host'];
    }
    
    /**
     * get 请求
     * 
     * @param unknown $serice            
     * @param unknown $params            
     * @return string
     */
    public function get($serice, $params = array())
    {
        $queryStr = $this->_config['api_queryStr'];
        if (! empty($params)) {
            $queryStrPre = http_build_query($params);
        }
        if(!empty($queryStrPre)){
            $queryStr.='&'.$queryStrPre;
        }
        $sign = md5($queryStr  . $this->_config['API_MD5KEY']); // 生成sign
        $url = $this->_config['api_host'] . $serice . '?' . $queryStr . '&sign=' . $sign; // 拼接url
        $extheaders = [
            'Content-Type: application/json; charset=utf-8',
            'Accept : application/json'
        ];
        return $this->request($url, $params, 'GET', $extheaders);
    }

    /**
     * post 请求
     * array('Content-Type'=>'application/json')
     * 
     * @param unknown $serice            
     * @param unknown $params            
     * @param unknown $extheaders            
     * @param string $multi            
     */
    public function post($serice, $params = array())
    {   
        $queryStr = $this->_config['api_queryStr'];
        $queryStrPre = substr(strstr($serice, '?'), 1);
        if(!empty($queryStrPre)){
            $queryStr.='&'.$queryStrPre;
        }
        $baseurl=strstr($serice, '?',true);
        if($baseurl){
         $serice=$baseurl;
        }
        $paramsjson = json_encode($params);
        $sign = md5($queryStr . $paramsjson . $this->_config['API_MD5KEY']); // 生成sign
        $url = $this->_config['api_host'] . $serice . '?' . $queryStr . '&sign=' . $sign; // 拼接url
        $extheaders = [
            'Content-Type: application/json; charset=utf-8',
            'Accept : application/json'
        ];
        return $this->request($url, $paramsjson, 'POST', $extheaders);
    }

    /**
     * 发起一个HTTP/HTTPS的请求
     *
     * @param $url 接口的URL            
     * @param $params 接口参数
     *            array('content'=>'test', 'format'=>'json');
     * @param $method 请求类型
     *            GET|POST
     * @param $extheaders 扩展的包头信息            
     * @return string
     */
    public function request($url, $params = '', $method = 'GET', $extheaders = array())
    {
        if (! function_exists('curl_init'))
            $this->errExit('Need to open the curl extension');
        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, 'PHP-SDK OAuth2.0');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);
        $headers = (array) $extheaders;
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (! empty($params)) {
                    
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $url);
        if ($headers) {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($ci);
        $data = json_decode($response, true);
        if ($data['isEncrypt'] == 'true') {
            $crypt = new Crypt3Des();
            $crypt->key = $this->_config['API_RES_KEY'];
            $data['data'] = json_decode($crypt->decrypt($data['data']),true);
        }
        curl_close($ci);
        return $data;
    }

    /**
     * 错误提示
     */
    public function errExit($message)
    {
        exit("error:" . $message);
    }
}