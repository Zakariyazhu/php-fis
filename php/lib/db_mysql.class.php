<?php

/**
  $linkId = master 主数据库 slave 从数据库
  当DB_MASTER_HOST == DB_SLAVE_HOST && DB_MASTER_NAME == DB_SLAVE_NAME时会选择主数据库使用
  使用方法:

  $db = new dbstuff;
  $sql = "select * from sql limit 1";
  $db->query_one($sql,$db->master());//从主数据库读 query() query_array()方法类似
  $db->close($db->master());//关闭连接

  $sql = "insert into sql(no1,no2) value('aa','bb')";
  $db->query($sql,$db->slave());//写从主数据库
  $db->close($db->slave());//关闭连接
 */
if (!defined('IS_SECURE')) {
    exit('Access Denied');
}

class dbstuff {

    var $_masterhost;
    var $_slavehost;
    var $_masterlnk;
    var $_slavelnk;
    var $_samelnk = true;
    var $querynum = 0;
    var $querysql = "";

    function dbstuff($config) {
        $this->_masterhost = $config['master'];
        //$count_slave = count($config['slave']);
        //$random_slave = mt_rand(0,$count_slave - 1);
        //$this->_slavehost  = $config['slave'][$random_slave];
        $this->_slavehost = $config['slave'];
        if (array_diff($this->_masterhost, $this->_slavehost)) {
            $this->_samelnk = false;
        }
    }

    function connect($dbhost, $dbuser, $dbpw, $dbname, $dbport = "3306") {
        $connId = mysql_connect($dbhost . ":" . $dbport, $dbuser, $dbpw, true);
        if (!$connId) {
            $this->halt('Can not connect to MySQL server');
        }
        mysql_query("SET NAMES 'utf8'", $connId);
        mysql_select_db($dbname, $connId);
        return $connId;
    }

    function fetch_array($query, $result_type = MYSQL_ASSOC) {
        return mysql_fetch_array($query, $result_type);
    }

    function query_array($sql, $linkId) {//返回所须的数组
        $result = array();
        $query = $this->query($sql, $linkId);
        while ($row = mysql_fetch_assoc($query)) {
            $result[] = $row;
        }
        return $result;
    }

    function query_one($sql, $linkId) { //返回单条记录
        $query = $this->query($sql, $linkId);
        $result = mysql_fetch_assoc($query);
        return $result ? $result : array();
    }

    function query($sql, $linkId, $tag = '') { //tag=1强行不报错
//		echo $sql."<br>";
        if ('' == $linkId) {
            $this->halt("linkid为空;sql:$sql");
        }
        $t1 = explode(" ", microtime());
        $start = $t1[1] + $t1[0];
        $query = mysql_query($sql, $linkId);
        $t2 = explode(" ", microtime());
        $end = $t2[1] + $t2[0];
        $t = $end - $start;
        $spend = sprintf("%.8f", $t);

        if ('1' != $tag) {
            if (!$query && DB_DEBUG) {   //echo '错误提示：'.mysql_error().'<br>'.$sql.'<br>';
                $this->halt('MySQL Query Error', $sql);
            }
        }

        if (DB_DEBUG) {
            //echo $sql."<br />";
        }
        $this->querysql .= $sql . "\t $t s\n";
        $this->querynum++;
        return $query;
    }

    function affected_rows($linkId) {
        return mysql_affected_rows($linkId);
    }

    function error() {
        return mysql_error();
    }

    function errno() {
        return intval(mysql_errno());
    }

    function result($query, $row) {
        $query = @mysql_result($query, $row);
        return $query;
    }

    function num_rows($query) {
        $query = mysql_num_rows($query);
        return $query;
    }

    function num_fields($query) {
        return mysql_num_fields($query);
    }

    function free_result($query) {
        return mysql_free_result($query);
    }

    function insert_id($linkId) {
        $id = mysql_insert_id($linkId);
        return $id;
    }

    function fetch_row($query) {
        $query = mysql_fetch_row($query);
        return $query;
    }

    function fetch_fields($query) {
        return mysql_fetch_field($query);
    }

    function version() {
        return mysql_get_server_info();
    }

    function close($linkId) {
        return mysql_close($linkId);
    }

    function halt($message = '', $sql = '') {
        echo mysql_error() . "$sql<br/>system error, please try again later!";
        @error_log($sql . "\n" . mysql_error(), 3, "/tmp/mysql.err");
        exit(); //等待设计模板页
    }

    function addslashes($string) {
        return $this->escape_string($string);
    }

    function escape_string($string) {
        if (get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }
        return mysql_real_escape_string($string);
    }

    //主数据库
    function master() {
        if (!$this->_masterlnk) {
            $c = $this->_masterhost;
            $connId = $this->connect($c['host'], $c['user'], $c['pass'], $c['dbname'], $c['dbport']);
            $this->_masterlnk = $connId;
            if ($this->_samelnk) {
                if (!$this->_slavelnk) {
                    $this->_slavelnk = &$this->_masterlnk;
                }
            }
        }
        return $this->_masterlnk;
    }

    //从数据库
    function slave() {
        if (!$this->_slavelnk) {
            $c = $this->_slavehost;
            $connId = $this->connect($c['host'], $c['user'], $c['pass'], $c['dbname'], $c['dbport']);
            $this->_slavelnk = $connId;
            if ($this->_samelnk) {
                if (!$this->_masterlnk) {
                    $this->_masterlnk = &$this->_slavelnk;
                }
            }
        }
        return $this->_slavelnk;
    }

    /**
     * 插入数据
     * @param String $tableName
     * @param array $arr 字段数组
     * @param Source $linkId
     * @return bool
     */
    public function insert_sql($tableName, $arr, $linkId) {
        if (empty($tableName) || empty($arr)) {
            return false;
        }

        $dba = $this->compile_db_insert_string($arr);
        $sql = "INSERT INTO " . $tableName . " ({$dba['FIELD_NAMES']}) VALUES ({$dba['FIELD_VALUES']})";
        //error_log($sql."\r\n",3,'./insert.log');
        $this->query($sql, $linkId);
        $insertId = $this->insert_id($linkId);
        return $insertId ? $insertId : false;
    }

    /**
     * 修改数据
     * @param Sting $tableName 表名
     * @param Array $arr 修改数据
     * @param String $where 条件
     * @param Source $linkId  
     * @return bool
     */
    public function update_sql($tableName, $arr, $where = '', $linkId) {
        $dba = $this->compile_db_update_string($arr);

        $query = "UPDATE " . $tableName . " SET $dba";

        if ($where) {
            $query .= " WHERE " . $where;
        }
        //error_log(date('Y-m-d H:i:s').'==>'.$query."---------".$_COOKIE['uid']."\r\n",3,'./logs/update.log');
        $this->query($query, $linkId);
        $error = $this->errno();
        //error_log(date('Y-m-d H:i:s').'==>'.$error."---------".$_COOKIE['uid']."\r\n",3,'./logs/update.log');
        return empty($error) ? true : false;
    }

    /**
     * 组合insert 数据
     * @param Array $data
     * @return String
     */
    private function compile_db_insert_string($data) {
        $field_names = "";
        $field_values = "";

        foreach ($data as $k => $v) {
            $field_names .= "$k,";
            $field_values .= "'$v',";
        }

        $field_names = preg_replace("/,$/", "", $field_names);
        $field_values = preg_replace("/,$/", "", $field_values);

        return array('FIELD_NAMES' => $field_names,
            'FIELD_VALUES' => $field_values,
        );
    }

    /**
     * 组合update 数据
     * @param Array $data
     * @return String
     */
    private function compile_db_update_string($data) {
        $return_string = "";

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $return_string .= $k . "=" . $v['0'] . ",";
            } else {
                $return_string .= $k . "='" . $v . "',";
            }
        }

        $return_string = preg_replace("/,$/", "", $return_string);
        return $return_string;
    }

}
