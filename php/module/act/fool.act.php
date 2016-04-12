<?php

class fool extends page {
    
    private $titleArr = ['测试标题1', 
        '测试标题2'];
    private $picArr = [
        'http://pic.pp3.cn/uploads//allimg/111111/105R35607-5.jpg',
        'http://a.hiphotos.baidu.com/zhidao/pic/item/7af40ad162d9f2d3d773b74ca9ec8a136327cc37.jpg'];
    private $questionArr = [
        '测试问题1',
        '测试问题2',
    ];
    private $commentArr = [
        '测试评论1',
        '测试评论2',
    ];
    
    function __construct() {
        parent::__construct();
        $this->router();
    }
    
    function router() {
        $do = $this->input('do') ? $this->input('do') : "index";
        $this->$do();
    }

   private function generateId(){
        $num = count($this->titleArr);
        $randArr = range(0, $num - 1);
        $hasRandArr = $this->cookie->get('fid');
        $diffRandArr = array_diff($randArr, $hasRandArr);
        if (!empty($diffRandArr)) {
            $randKey = array_rand($diffRandArr);
            $randVal = $diffRandArr[$randKey];
        } else {
            $randKey = array_rand($randArr);
            $randVal = $randArr[$randKey];
        }
        return $randVal;
    }
   
    private function index() {
        //默认首页
        //echo '哈哈，你上当了！';
        //exit;
        $tmpRandArr[] = $this->generateId();
        $randTitle = $this->titleArr[$randVal];
        $randPic = $this->picArr[$randVal];
        $randQuestion = $this->questionArr[$randVal];
        $randComment = $this->commentArr[$randVal];
        $this->cookie->set('fid', $tmpRandArr);
        if ($this->display == 'json') {
            $retArr = [
                'title' => $randTitle,
                'pic' => $randPic,
                'question' => $randQuestion,
                'comment' => $randComment,
            ];
            return Response::json($retArr);
        } else {
            //模板渲染
        }
        exit;
    }

    private function generate(){
        $name = $this->input('name');
        $name = $name ? $name : '匿名';
    }
    
    

}
