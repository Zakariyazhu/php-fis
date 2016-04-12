<?php
include_once 'Http.php';

class Auth
{

    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 输入
     *
     * @var Bag
     */
    protected $input;

    /**
     * 获取上一次的授权信息
     *
     * @var array
     */
    protected $lastPermission;

    /**
     * 已授权用户
     *
     * @var \Overtrue\Wechat\Utils\Bag
     */
    protected $authorizedUser;

    const API_USER           = 'https://api.weixin.qq.com/sns/userinfo';
    const API_TOKEN_GET      = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const API_TOKEN_REFRESH  = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    const API_TOKEN_VALIDATE = 'https://api.weixin.qq.com/sns/auth';
    const API_URL            = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret, $input)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->http      = new Http();
        $this->input     = $input;
    }

    /**
     * 生成outh URL
     *
     * @param string $to
     * @param string $scope
     * @param string $state
     *
     * @return string
     */
    public function url($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        $to !== null || $to = $this->current();

        $params = array(
                   'appid'         => $this->appId,
                   'redirect_uri'  => $to,
                   'response_type' => 'code',
                   'scope'         => $scope,
                   'state'         => $state,
                  );

        return self::API_URL.'?'.http_build_query($params).'#wechat_redirect';
    }

    /**
     * 直接跳转
     *
     * @param string $to
     * @param string $scope
     * @param string $state
     */
    public function redirect($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        header('Location:'.$this->url($to, $scope, $state));

        exit;
    }

    /**
     * 获取已授权用户
     *
     * @return \Overtrue\Wechat\Utils\Bag | null
     */
    public function user()
    {
        /*
        if ($this->authorizedUser
            || !$this->input->get('state')
            || (!$code = $this->input->get('code')) && $this->input->get('state')) {
            return $this->authorizedUser;
        }
        */

        $code = $this->input['code'];
        $permission = json_decode($this->getAccessPermission($code), true);

        if ($permission['scope'] !== 'snsapi_userinfo') {
            $user = ['openid' => $permission['openid']];
        } else {
            $user = $this->getUser($permission['openid'], $permission['access_token']);
        }

        return array_merge($user, $this->input);
    }

    /**
     * 通过授权获取用户
     *
     * @param string $to
     * @param string $state
     * @param string $scope
     *
     * @return Bag | null
     */
    public function authorize($redirect_uri = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        if (!$this->input['state'] && !$this->input['code']) {
            //获取code
            $this->redirect($redirect_uri, $scope, $state);
        }

        return $this->user();
    }

    /**
     * 刷新 access_token
     *
     * @param string $refreshToken
     *
     * @return Bag
     */
    public function refresh($refreshToken)
    {
        $params = array(
                   'appid'         => $this->appId,
                   'grant_type'    => 'refresh_token',
                   'refresh_token' => $refreshToken,
                  );

        $permission = $this->http->query_get(self::API_TOKEN_REFRESH, $params);

        $this->lastPermission = array_merge($this->lastPermission, $permission);
    }

    /**
     * 获取用户信息
     *
     * @param string $openId
     * @param string $accessToken
     *
     * @return array
     */
    public function getUser($openId, $accessToken)
    {
        $queries = array(
                    'access_token' => $accessToken,
                    'openid'       => $openId,
                    'lang'         => 'zh_CN',
                   );

        return json_encode($this->http->query_get($url, $queries), true);
    }

    /**
     * 获取access token
     *
     * @param string $code
     *
     * @return string
     */
    public function getAccessPermission($code)
    {
        $params = array(
                   'appid'      => $this->appId,
                   'secret'     => $this->appSecret,
                   'code'       => $code,
                   'grant_type' => 'authorization_code',
                  );

        return $this->lastPermission = $this->http->query_get(self::API_TOKEN_GET, $params);
    }

    /**
     * 魔术访问
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (isset($this->lastPermission[$property])) {
            return $this->lastPermission[$property];
        }
    }
    
    public function current() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } else {
            $host = $_SERVER['HTTP_HOST'];
        }
        return $protocol . $host . $_SERVER['REQUEST_URI'];
    }
    
    public function curl_get(){
        
    }

}
