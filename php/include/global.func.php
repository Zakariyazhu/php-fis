<?php

/**
 * 验证字符串长度
 *
 * @param string $string
 * @param int $min
 * @param int $max
 * @return boolean or mixed
 */
function verifyLength($string, $min = 0, $max = 0) {
    $string = iconv("utf-8", "gbk", $string);
    $length = strlen($string);

    if ($length < $min) {
        return "$length < $min";
    }

    if ($max && $length > $max) {
        return "$length > $max";
    }

    return true;
}

/**
 * 验证是否包含脏字符
 *
 * @param string $string 要验证的字符串
 * @param boolean $replace 找到脏字符是否替换
 * @param boolean $badWords 附加脏字符
 */
function verifyBadWords($string, $replace = false, $badWords = '') {
    global $censorWords;
    if ($badWords != "") {
        $censorWords .="," . $badWords;
    }
    $censorexp = '/(' . str_replace(array('\\*', ",", ' '), array('.*', '|', ''), preg_quote(($censorWords = trim($censorWords)), '/')) . ')/i';

    if (@preg_match($censorexp, $string, $matches)) {
        if ($replace) {
            $string = strtr($string, $matches[0], "*");
        } else {
            return false;
        }
    }
    return $string;
}

/*
  功能描述:判断是否搜索引擎
 */

function noRobot() {
    if (!defined('IS_ROBOT')) {
        $kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
        $kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
        if (preg_match("/($kw_browsers)/", $_SERVER['HTTP_USER_AGENT'])) {
            return;
        } elseif (preg_match("/($kw_spiders)/", $_SERVER['HTTP_USER_AGENT'])) {
            exit(header("HTTP/1.1 403 Forbidden"));
        } else {
            return;
        }
    }
}

function getip() {
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    } else
        $onlineip = '';
    return $onlineip;
}

function chkEncode($str) {
    if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
        return "UTF-8";
    } else {
        return "GBK";
    }
}

function cutstr($string, $length, $dot = ' ...') {
    global $charset;

    if (strlen($string) <= $length) {
        return $string;
    }

    $strcut = '';
    if (strtolower($charset) == 'utf-8') {

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length - strlen($dot) - 1; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }

    return $strcut . $dot;
}

function cc_cut($str_cut, $length, $dot = '...') {//中英文混合截取
    $str_cut = mb_strlen($str_cut, 'utf-8') > $length ? mb_convert_encoding(trim(mb_substr($str_cut, 0, $length, 'utf-8')), 'utf-8', 'auto') . $dot : mb_convert_encoding(trim($str_cut), 'utf-8', 'auto');
    return $str_cut;
}

function daddslashes($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = daddslashes($val);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}

function dstripcslashes($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dstripcslashes($val);
        }
    } else {
        $string = stripcslashes($string);
    }

    return $string;
}

function dhtmlspecialchars($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val);
        }
    } else {
        $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
    }
    return $string;
}

function random($length, $numeric = 0) {
    mt_srand((double) microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

//编码转换
function siconv($str, $out_charset, $in_charset = '') {
    global $_SC;

    $in_charset = empty($in_charset) ? strtoupper($_SC['charset']) : strtoupper($in_charset);
    $out_charset = strtoupper($out_charset);
    if ($in_charset != $out_charset) {
        if (function_exists('iconv') && (@$outstr = iconv("$in_charset//IGNORE", "$out_charset//IGNORE", $str))) {
            return $outstr;
        } elseif (function_exists('mb_convert_encoding') && (@$outstr = mb_convert_encoding($str, $out_charset, $in_charset))) {
            return $outstr;
        }
    }
    return $str; //转换失败
}

function getTimeBeforeNow($timeline) {
    $currtime = time();
    $timediff = $currtime - $timeline;
    if ($timediff == 0) {
        return '现在';
    } elseif ($timediff > 0 && $timediff < 60) {
        return $timediff . '秒前';
    } elseif ($timediff >= 60 && $timediff < 3600) {
        return (floor($timediff / 60)) . '分钟前';
    } elseif ($timediff >= 3600 && $timediff <= 86400) {
        return (floor($timediff / 3600)) . '小时前';
    } else if ($timediff > 86400 && $timediff < 3 * 86400) {
        return (floor($timediff / 86400)) . '天前';
    } else {
        return date('m月d日', $timeline);
    }
}

/**
 * @name 过滤html危险字符
 * @version 1.0
 *
 * @param string|array $var 要过滤的变量,可以是数组
 * @param string $type 变量类型
 * @param boolean $html 是否html安全
 * @param boolean $securet
 * @return mix $return
 */
function strip_html($var, $type = 'string', $html = true, $securet = true) {
    if (is_array($var)) {
        foreach ($var as $k => $v) {
            $return[$k] = strip_html($v, $type, $html, $securet);
        }
    } else {
        if ($type == 'int') {
            $return = intval($var);
        } else {
            if ($html == true) {
                if ($securet == true) {
                    // 去掉<script,iframe,标签>
                    $search = array("'<script[^>]*?>.*?(</script>)?'si", // 去掉 <script>
                        "'<iframe[^>]*?>.*?(</iframe>)?'si", // 去掉 <iframe>	
                        "'<style[^>]*?>.*?(</style>)?'si");  // 去掉 <style>	
                    $return = @preg_replace($search, "", $var);
                }
            } else {
                $return = strip_tags($var);
            }
        }
    }
    return $return;
}

function strlen_utf8($str) {
    $temp = strtoupper(mb_detect_encoding($str));
    if ($temp != 'UTF-8') {
        $str = iconv($temp, 'UTF-8', $str);
    }
    $i = 0;
    $count = 0;
    $len = strlen($str);
    while ($i < $len) {
        $chr = ord($str[$i]);
        $count++;
        $i++;
        if ($i >= $len)
            break;
        if ($chr & 0x80) {
            $chr <<= 1;
            $count++;
            while ($chr & 0x80) {
                $i++;
                $chr <<= 1;
            }
        }
    }
    return ceil($count / 2);
}

function spiltSpell($spell, $length) {
    $sm = array("b", "p", "m", "f", "d", "t", "n", "l", "g", "k", "h", "j", "q", "x", "zh", "ch", "sh", "r", "z", "c", "s", "y", "w");
    $ym = array("i", "u", "ü", "a", "ia", "ua", "o", "uo", "e", "ie", "üe", "ai", "uai", "ei", "er", "uei", "ao", "iao", "ou", "iou",
        "an", "ian", "uan", "üan", "en", "in", "uen", "ün", "ang", "iang", "uang", "eng", "ing", "ueng", "ong", "iong");
    //var_dump(count($sm),count($ym));exit;
    if (in_array($spell, $ym))
        return array("spell_x" => "", "spell_y" => $spell);
    $res = array();
    $res["spell_x"] = iconv_substr($spell, 0, $length);
    $res["spell_y"] = iconv_substr($spell, $length);
    if (in_array($res["spell_x"], $sm)) {
        return $res;
    } else {
        return spiltSpell($spell, 1);
    }
}

//时间格式转换
function time_format($input_time) {
    $str = "";
    $time = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $diff_time = $input_time - $time;
    while ($diff_time > 0) {
        if ($diff_time >= 24 * 3600) {
            $day_num = ceil($diff_time / (24 * 3600));
            $str .= $day_num;
            $diff_time -= $day_num * 24 * 3600;
            break;
        }/* elseif ($diff_time >= 3600){
          $hour_num = floor($diff_time / 3600);
          $str .= $hour_num."小时";
          $diff_time -= $hour_num * 3600;
          }elseif ($diff_time >= 60){
          $minute_num = floor($diff_time / 60);
          $str .= $minute_num."分钟";
          $diff_time -= $minute_num * 60;
          }else{
          $str .= $diff_time."秒";
          $diff_time -= $diff_time;
          } */ else {
            $str = "1";
            break;
        }
    }
    return $str;
}

function runtime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

function fastdfs_upload($picAddr) {
    $tracker = fastdfs_tracker_get_connection();
    if (!fastdfs_active_test($tracker)) {
        //error_log("errno: " . fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
        exit(1);
    }
    $storage = fastdfs_tracker_query_storage_store();
    if (!$storage) {
        //error_log("errno: " . fastdfs_get_last_error_no() . ", error info: " . fastdfs_get_last_error_info());
        exit(1);
    }
    $extname = 'gif';
    $file_info = fastdfs_storage_upload_by_filename("$picAddr", $extname, array(), null, $tracker, $storage);
    return $file_info['filename'];
}

function my_scandir($path) {
    $file_arr = array();
    if ($path) {
        $current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
        while (($file = readdir($current_dir)) !== false) {    //readdir()返回打开目录句柄中的一个条目
            $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
            if ($file == '.' || $file == '..') {
                continue;
            } else if (is_dir($sub_dir)) {    //如果是目录,进行递归
                //echo 'Directory ' . $file . ':<br>';
                my_scandir($sub_dir);
            } else {    //如果是文件,直接输出
                array_push($file_arr, $file);
                //echo 'File in Directory ' . $path . ': ' . $file . '<br>';
            }
        }
    } else {
        return false;
    }
}

function delete_varnish_cache($url, $host) {
    $err_str = "";
    $err_no = "";
    $fp = fsockopen(VARNISH_IP, 80, $err_no, $err_str, 10);
    if (!$fp) {
        return false;
    } else {
        $out = "ban.url $url HTTP/1.1\r\n";
        $out .= "Host:{$host}\r\n";
        $out .= "Connection: close\r\n\r\n";
        fputs($fp, $out);
        $out = fgets($fp, 4096);
        fclose($fp);
        return true;
    }
}

function abort($code) {
    switch ($code) {
        case 404:
            header('Location: /error');
            break;
        default:
            header('Location: /error');
    }
    exit;
}
