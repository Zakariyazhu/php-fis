<?php

class userDao {

    const TABLENAME = 'fkls_wx_user';
    
    public function __construct() {
    }

    public function test() {
        global $apiDb;
        $sql = "select * from ent_student where ID = '10000002'";
        $res = $apiDb->query_array($sql, $apiDb->slave());
        return $res;
    }

    public function addUser($uid, $openid) {
        global $webDb;
        $tablename = self::TABLENAME;
        //$sql = "insert into {$tablename}(uid, openid) values ('{$uid}', '{$openid}')";
        $sql = "update {$tablename} set uid = '{$uid}' where openid = '{$openid}'";
        $linkId = $webDb->master();
        $webDb->query($sql,$linkId);
        $insertId = $webDb->insert_id($linkId);
        $webDb->close($webDb->master());
        return $insertId;
    }
    
    public function addOpenid($openid){
        global $webDb;
        $tablename = self::TABLENAME;
        $sql = "insert into {$tablename}(openid) values ('{$openid}')";
        $linkId = $webDb->master();
        $webDb->query($sql,$linkId);
        $insertId = $webDb->insert_id($linkId);
        $webDb->close($webDb->master());
        return $insertId;        
    }
    
    public function hasUser($uid){
        global $webDb;
        $tablename = self::TABLENAME;
        $sql = "select openid from {$tablename} where uid = '{$uid}'";
        $res = $webDb->query_one($sql, $webDb->slave());
        return $res['openid'] ? true : false;
    }
    
    public function hasUserByOpenid($openid){
        global $webDb;
        $tablename = self::TABLENAME;
        $sql = "select uid from {$tablename} where openid = '{$openid}'";
        $res = $webDb->query_one($sql, $webDb->slave());
        return $res['openid'] ? true : false;        
    }

}
