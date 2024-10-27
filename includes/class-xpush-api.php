<?php

class xPush_API_Class
{

    private $api_key;

    public function __construct() {

        $this->api_key  = get_option('xpush_api_key');

    }

    public function xpush_check_api_status($key){

        $params = array(
            'key' => $key,
            'action' => 'get_sites'
        );
        $gets = http_build_query($params);
        $answer = json_decode($this->xpush_send_api_query($gets));
        if (is_object($answer) && empty($answer->error)){
            $check = 'valid';
        }
        else {
            $check = $answer->error;
        }

        return $check;
    }

    public function xpush_get_api_options($name){

        $params = array(
            'key' => $this->api_key,
            'action' => $this->xpush_get_api_method($name)
        );
        $gets = http_build_query($params);

        $answer = json_decode($this->xpush_send_api_query($gets));

        if (is_object($answer)) {
            if (empty($answer->error)){
                $option = $this->xpush_get_api_result($name, $answer);
            }
            else {
                $option = false;
            }
        }
        else {
            $option = 'error send cUrl';
        }

        return $option;
    }

    protected function xpush_get_api_method($name){

        $method = '';
        switch ($name){
            case 'xpush_sites' : $method = 'get_sites';  break;
            case 'xpush_langs' : $method = 'get_langs'; break;
            case 'xpush_regions' : $method = 'get_regions'; break;
            case 'xpush_tags' : $method = 'get_tags'; break;
        }
        return $method;
    }

    protected function xpush_get_api_result($name, $answer){

        $result = '';
        switch ($name){
            case 'xpush_sites' : $result = $answer->sites;  break;
            case 'xpush_langs' : $result = $answer; break;
            case 'xpush_regions' : $result = $answer; break;
            case 'xpush_tags' : $result= $answer->tags; break;
        }
        return $result;
    }

    public function xpush_send_api_push($params){

        $params['key'] = $this->api_key;
        $params['action'] = 'add_send';
        $params['button1'] = '';
        $params['button2'] = '';
        $params['active'] = 1;
        $params['check'] = 0;

        $params_query = $params['params_query'];

        unset($params['params_query']);

        $params = http_build_query($params);

        $gets = $params.$params_query;

        $errors = '';

        $answer = json_decode($this->xpush_send_api_query($gets));

        if (is_object($answer)) {
            if (empty($answer->error)){

                return $answer->subs;
            }
            else {
                if (is_array($answer->error)) {
                    foreach ($answer->error as $error) {
                        $errors .= $error.',';
                    }
                    if ($errors{strlen($errors)-1} == ',') {
                        $errors = substr($errors,0,-1);
                    }
                    $errors = str_replace(',',', ',$errors);
                    return $errors;
                }
                else {
                    return $answer->error;
                }
            }
        }
        else {
            $answer->error = 'error send cUrl';
        }
    }

    protected function xpush_send_api_query($gets) {
        $url = 'https://3xpush.com/api/?'. $gets;
        global $wp_version;
        $WP_Http_Curl = new WP_Http_Curl();
        $response = $WP_Http_Curl->request($url, array(
            'method'      => 'GET',
            'redirection' => 0,
            'blocking'    => true,
            'user-agent' => 'WordPress/' . $wp_version. '; ' . get_bloginfo('url'),
            'headers'   => array(
                'Accept-Encoding' => ''
            ),
            'compress'    => false,
            'decompress'  => false,
            'sslverify'   => false,
            'stream'      => false,
            'filename' => false
        ));
        if (is_array($response) && ! is_wp_error($response)) {
            return $response['body'];
        }
        else {
            return '{"error":"error send cUrl"}';
        }
    }

}
?>