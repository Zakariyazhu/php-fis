<?php

require_once(PHP_LIB . "/wechat/Auth.php");
require_once(PHP_MODEL . "/user/user.dao.php");

class login extends page {

    private $userDao;

    function __construct() {
        parent::__construct();
        $this->userDao = new userDao();
        $this->router();
    }

    function router() {
        $do = $this->input('do') ? $this->input('do') : "index";
        $this->$do();
    }

    private function index() {
        $auth = new Auth(APP_ID, APP_SECRET, $this->input);
        $userData = $auth->authorize(null, 'snsapi_base');
        if (isset($userData['openid']) && $userData['openid']) {
            $this->cookie->set('openid', $userData['openid']);
            $hasUser = $this->userDao->hasUserByOpenid($userData['openid']);
            if (!$hasUser) {
                $this->userDao->addOpenid($userData['openid']);
            }
            header("Location: /user/login/page");
        } else {
            abort(404);
        }
        exit;
    }

    private function page() {
        //登录首页
        echo $this->cookie->get('openid');
        echo '登录绑定页面';
        exit;
    }

    private function check() {
        $param['phone'] = $this->input('phone');
        $password = $this->input('password');
        $param['password'] = md5($password);
        $param['pushID'] = '23213213ewqeqeq321312321';
        $res = $this->httpClient->post('/v3/student/user/login', $param);
        if ($res['status'] == Code::SUCCESS) {
            $this->input['uid'] = $uid = $res['data']['userInfo']['userID'];
            $token = $res['data']['token'];
            //$auth = new Auth(APP_ID, APP_SECRET, $this->input);
            //$t = $auth->authorize(null, 'snsapi_base');
            //$userData = json_decode($t, true);
            $openid = $this->cookie->get('openid');
            $openid = $openid ? $openid : 'oRIZTszeEEjABDv_hXDr6b08_7eA';
            $userData = [
                'openid' => $openid,
                'uid' => $uid,
                'token' => $token,
            ];
            $this->cookie->set('userinfo', $userData);
            $hasUser = $this->userDao->hasUser($userData['uid']);
            if (!$hasUser) {
                if ($userData['openid']) {
                    $id = $this->userDao->addUser($userData['uid'], $userData['openid']);
                    if ($id) {
                        $this->message->set(Code::SUCCESS);
                        return Response::json($this->message->get());
                    } else {
                        $this->message->set(Code::SYSTEM_ERROR);
                        return Response::json($this->message->get());
                    }
                } else {
                    $this->message->set(Code::UNKNOW_ERROR);
                    return Response::json($this->message->get());
                }
            } else {
                $this->message->set(Code::SUCCESS);
                return Response::json($this->message->get());
            }
        }
        $this->message->set(Code::LOGIN_FAILED);
        return Response::json($this->message->get());
    }

    private function success() {
        //登录成功页面
        echo '查看账户页面!';
        exit;
    }

}
