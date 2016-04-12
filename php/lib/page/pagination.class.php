<?php
//分页类
class pagination {

    var $totalpage;
    var $current;
    var $vhead;
    var $pagelinkurl = '';

    /*
     * @param key string 分页区别标记
     * @param int size 每页记录数
     * @param int groupsize 每组页码数
     * @param int current 当前页码
     */

    public function pagination($totalpage = 0, $current = 1, $pagelinkurl = '') {
        $this->totalpage = $totalpage;
        $this->current = $current;
        if ($pagelinkurl) {
            $this->pagelinkurl = $pagelinkurl;
        }
    }

    /*
     * 设置链接头信息
     * @param string remove 要移除的变量名成
     */

    public function _setLinkhead() {
        parse_str($_SERVER['QUERY_STRING'], $query);
        foreach ($query as $k => $v) {
            if (empty($v) === true && $v != 0) {
                unset($query[$k]);
            }
        }

        if (isset($query['pageno']))
            unset($query['pageno']);

        foreach ($query as $k => $v) {
            if (is_array($v)) {
                $v = implode(',', $v);
                $tmp[] = $k . '=-100,' . urlencode($v);
            } else {
                $tmp[] = $k . '=' . urlencode($v);
            }
        }
        $vhead = '';
        $this->vhead != '' && $vhead = $this->vhead . '&';
        $tmp[] = 'pageno' . '=';
        $urlstr = strtolower($_SERVER['PHP_SELF']) . '?' . $vhead . implode('&', $tmp);

        return $urlstr;
    }

    //显示页面链接
    public function PageLinks() {
        if ($this->totalpage <= 1)
            return '';
        $linkhead = $this->_setLinkhead();
        $this->vhead = $linkhead;
        $current = $this->current;
        $totalpage = $this->totalpage;
        $separator = '';
        $content = '';


        $prev = "<a title='上一页' href='" . $this->buildLink($current - 1) . "' class='wenzi'>上一页</a>";

        $next = "<a title='下一页' href='" . $this->buildLink($current + 1) . "' class='wenzi'>下一页</a>";

        if ($current > 1) {
            $content .= $prev;
        }

        for ($i = 1; $i <= $totalpage; $i++) {
            if ($i == $current) {
                $content .= "<a title='第" . $i . "页' href='" . $this->buildLink($i) . "' class='p_sel'>" . $i . " </a>";
            } else {
                if ($current - $i >= 4 && $i != 1) {
                    $content .="<span>...</span>";
                    $i = $current - 3;
                } else {
                    if ($i >= $current + 5 && $i != $totalpage) {
                        $content .="<span>...</span>\n";
                        $i = $totalpage;
                    }
                    $content .= "<a title='第" . $i . "页' href='" . $this->buildLink($i) . "'>" . $i . " </a> ";
                }
            }
        }

        if ($current < $totalpage) {
            $content .= $next;
        }

        $content .= ' 转到 <input type="text" class="listpageNum" id="listpageNum" name="listpageNum"/> 页 <input type="button" value="确定" class="listPageSubmit" style="cursor:pointer;"/>';

        return $content;
    }

    public function PageLinks_admincp() {
        if ($this->totalpage <= 1)
            return '';
        $linkhead = $this->_setLinkhead();
        $this->vhead = $linkhead;
        $current = $this->current;
        $totalpage = $this->totalpage;
        $separator = '';
        $content = '';

        //$content .="<span id='syslog_first' class='first ui-corner-tl ui-corner-bl fg-button ui-button ui-state-default ui-state-disabled'>First</span>";
        $prev = "<span id='syslog_previous' class='previous fg-button ui-button ui-state-default'><a title='上一页' href='" . $this->buildLink($current - 1) . "'>上一页 </a> </span>";

        $next = "<span id='syslog_next' class='next fg-button ui-button ui-state-default'><a title='下一页' href='" . $this->buildLink($current + 1) . "'>下一页</a> </span>";

        if ($current > 1) {
            $content .= $prev;
        }
        //$content .='<span>';
        for ($i = 1; $i <= $totalpage; $i++) {
            if ($i == $current) {
                $content .= "<span><span class='fg-button ui-button ui-state-default ui-state-disabled'><a title='第" . $i . "页' href='" . $this->buildLink($i) . "' class='p_sel'>" . $i . " </a> </span></span>";
            } else {
                if ($current - $i >= 4 && $i != 1) {
                    $content .="<span>...</span>";
                    $i = $current - 3;
                } else {
                    if ($i >= $current + 5 && $i != $totalpage) {
                        $content .="<span>...</span>\n";
                        $i = $totalpage;
                    }
                    $content .= "<span class='fg-button ui-button'><a title='第" . $i . "页' href='" . $this->buildLink($i) . "'>" . $i . " </a> </span>";
                }
            }
        }
        if ($current < $totalpage) {
            $content .= $next;
        }

        return $content;
    }

    public function buildLink($num) {
        if ($this->pagelinkurl) {
            return str_replace('$p', $num, $this->pagelinkurl);
        } else {
            return $this->vhead . $num;
        }
    }

}
