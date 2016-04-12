<?php
class Http
{

    public function query_get($url, $params){
        $ch = curl_init();
        $query_url = $url."?".http_build_query($params);
        curl_setopt($ch , CURLOPT_URL, $query_url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch , CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}
