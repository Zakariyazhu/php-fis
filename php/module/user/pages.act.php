<?php

class pages extends page {

    private $userinfo;
    private $uid;
    private $token;
    private $display;
    
    const STUDENTINFO = '/v3/student/user/studentinfo';
    const BILLINGLIST = '/v3/student/billing/listv4';
    const PACKETLIST = '/packet/list';
    const COUPONLIST = '/v3/student/coupon/list';
    const ORDERLIST = '/v3/student/user/orderlist';

    function __construct() {
        parent::__construct();
        $this->router();
    }

    function router() {
        $do = $this->input('do') ? $this->input('do') : "index";
        $this->userinfo = $this->cookie->get('userinfo');
        $this->uid = $this->userinfo['uid'];
        $this->token = $this->userinfo['token'];
        $this->display = $this->input('display');
        if (!$this->uid) {
            exit('获取用户信息失败!');
        }
        $this->$do();
    }

    private function index() {
        $param['userid'] = $this->uid;
        $param['token'] = $this->token;
        $param['productID'] = STUDENT_IOS;
        $res = $this->httpClient->get(self::STUDENTINFO, $param);
        if ($res['status'] == Code::SUCCESS) {
            $studentInfo = $res['data']['studentInfo'];
            $balance = $res['data']['amount'];
            $couponnum = $res['data']['couponCount'];
            $ordernum = $res['data']['orderCount'];

            $params['userid'] = $this->uid;
            $params['token'] = $this->token;
            $params['p'] = 1;
            $billingRes = $this->httpClient->get(self::BILLINGLIST, $params);
            $billingList = [];
            if ($billingRes['status'] == Code::SUCCESS) {
                $billingList = $billingRes['data']['billingList'];
            }
            if ($this->display == 'json') {
                $retData = ['status' => Code::SUCCESS, 'message' => '', 'data' => ['studentInfo' => $studentInfo, 'balance' => $balance, 'couponnum' => $couponnum, 'ordernum' => $ordernum, 'billingList' => $billingList]];
                return Response::json($retData);
            } else {
                //模板渲染
            }
        } else {
            if ($this->display == 'json') {
                $this->message->set(Code::SYSTEM_ERROR);
                $retData = ['status' => Code::SYSTEM_ERROR, 'message' => $this->message->get()];
                return Response::json($retData);
            } else {
                exit('系统错误!');
            }
        }
        exit;
    }

    //账户余额
    private function balance() {
        $page = $this->input('page') ? $this->input('page') : 1;
        $param['userid'] = $this->uid;
        $param['token'] = $this->token;
        $param['p'] = $page;
        $res = $this->httpClient->get(self::BILLINGLIST, $param);
        if ($res['status'] == Code::SUCCESS) {
            $accountAmount = $res['data']['accountAmount'];
            $billingList = $res['data']['billingList'];
            if ($this->display == 'json') {
                $retData = ['status' => Code::SUCCESS, 'message' => '', 'data' => ['accountAmount' => $accountAmount, 'billingList' => $billingList]];
                return Response::json($retData);
            } else {
                
            }
        } else {
            if ($this->display == 'json') {
                $this->message->set(Code::SYSTEM_ERROR);
                $retData = ['status' => Code::SYSTEM_ERROR, 'message' => $this->message->get()];
                return Response::json($retData);
            } else {
                exit('系统错误!');
            }
        }
        exit;
    }

    //红包
    private function packet() {
        $this->httpClient->updateHost();
        $page = $this->input('page') ? $this->input('page') : 1;
        $param['userid'] = $this->uid;
        $param['token'] = $this->token;
        $param['page'] = $page;
        $param['year'] = intval(date('Y'));
        $param['type'] = 2;
        $res = $this->httpClient->get(self::PACKETLIST, $param);
        if ($res['status'] == Code::SUCCESS) {
            $hasMore = $res['data']['hasMore'] ? 1 : 0;
            $packets = $res['data']['packets'];
            if ($this->display == 'json') {
                $retData = ['status' => Code::SUCCESS, 'message' => '', 'data' => ['hasMore' => $hasMore, 'packets' => $packets]];
                return Response::json($retData);
            } else {
                
            }
        } else {
            if ($this->display == 'json') {
                $this->message->set(Code::SYSTEM_ERROR);
                $retData = ['status' => Code::SYSTEM_ERROR, 'message' => $this->message->get()];
                return Response::json($retData);
            } else {
                exit('系统错误!');
            }
        }
        exit;
    }

    //优惠券
    private function coupon() {
        $param['userid'] = $this->uid;
        $param['token'] = $this->token;
        $res = $this->httpClient->get(self::COUPONLIST, $param);
        if ($res['status'] == Code::SUCCESS) {
            if ($this->display == 'json') {
                $retData = ['status' => Code::SUCCESS, 'message' => '', 'data' => ['couponList' => $res['data']['couponList']]];
                return Response::json($retData);
            } else {
                
            }
        } else {
            if ($this->display == 'json') {
                $this->message->set(Code::SYSTEM_ERROR);
                $retData = ['status' => Code::SYSTEM_ERROR, 'message' => $this->message->get()];
                return Response::json($retData);
            } else {
                exit('系统错误!');
            }
        }
        exit;
    }

    //我的订单
    private function order() {
        $page = $this->input('page') ? $this->input('page') : 1;
        $param['userid'] = $this->uid;
        $param['token'] = $this->token;
        $param['p'] = $page;
        $res = $this->httpClient->get(self::ORDERLIST, $param);
        if ($res['status'] == Code::SUCCESS) {
            if ($this->display == 'json') {
                $retData = ['status' => Code::SUCCESS, 'message' => '', 'data' => ['orderList' => $res['data']['orderList']]];
                return Response::json($retData);
            } else {
                
            }
        } else {
            if ($this->display == 'json') {
                $this->message->set(Code::SYSTEM_ERROR);
                $retData = ['status' => Code::SYSTEM_ERROR, 'message' => $this->message->get()];
                return Response::json($retData);
            } else {
                exit('系统错误!');
            }
        }
        exit;
    }

}
