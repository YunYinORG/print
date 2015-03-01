<?php
/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - _connection()
 * - sendSms()
 * Classes list:
 * - Sms
 */
namespace Common\Common;
class Sms
{
    private $_url             = '';
    private $_supporter       = '';
    private $_header          = false;
    
    public function __construct($supporter        = '') 
    {
        $this->_supporter = $supporter ? : C('SMS_SUPPORTER');
        if ($this->_supporter == 'ucpaas') 
        {
            $softVersion      = "2014-06-30";
            $baseUrl          = "https://api.ucpaas.com/";
            date_default_timezone_set('Asia/Shanghai');
            $accountSid    = C('SMS_ACCOUNT');
            $token         = C('SMS_TOKEN');
            $timestamp     = date("YmdHis");
            
            //验证参数,URL后必须带有sig参数，sig= MD5（账户Id + 账户授权令牌 + 时间戳，共32位）(注:转成大写)
            $sig           = strtoupper(md5($accountSid . $token . $timestamp));
            $this->_url    = $baseUrl . $softVersion . '/Accounts/' . $accountSid . '/Messages/templateSMS?sig=' . $sig;
            
            //包头验证信息,使用Base64编码（账户Id:时间戳）
            $auth          = trim(base64_encode($accountSid . ":" . $timestamp));
            $this->_header = array('Content-Type:application/json;charset=utf-8', 'Authorization:' . $auth,);
        } 
        else
        {
            $this->_url    = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit';
            $this->_header=array('Content-Type: application/x-www-form-urlencoded',);
        }
    }
    
    /**
    *连接服务器回尝试curl和file_get_contents两种方法
     * @param $data  post数据
     * @param $type
     * @param $method post或get
     * @return mixed|string
     */
    private function _connection($data, $type          = 'json', $method        = 'post') 
    {
        if ($type == 'json') 
        {
            $mine          = 'application/json';
        } 
        else
        {
            $mine          = 'application/xml';
        }
        if (function_exists("curl_init")) 
        {
            $ch            = curl_init($this->_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_header);
            if ($method == 'post') 
            {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        } 
        else
        {
            $opts    = array();
            $opts['http']         = array();
            $headers = array("method"         => strtoupper($method),);
            $headers[]         = 'Accept:' . $mine;
            $headers['header']         = $this->_header ? : array();
            if (!empty($data)) 
            {
                $headers['header'][]         = 'Content-Length:' . strlen($data);
                $headers['content']         = $data;
            }
            $opts['http']         = $headers;
            $result  = file_get_contents($this->_url, false, stream_context_create($opts));
        }
        return $result;
    }
    
    /*发送短信
    **/
    public function sendSms($toPhone, $msg, $templateId = 2173) 
    {
        if ($this->_supporter == 'huyi') 
        {
            $account    = 'account=' . C('SMS_ACCOUNT') . '&password=' . C('SMS_TOKEN');
            $data       = $account . '&mobile=' . $toPhone . '&content=' . $msg;
            $return_str = $this->_connection($data, 'xml');
            $xml        = simplexml_load_string($return_str);
 
            return intval($xml->code) == 2;
        } 
        else
        {
            $body_json  = array(
                'templateSMS'            => array(
                    'appId'            => C('SMS_APPID'), 
                    'templateId'            => $templateId, 
                    'to'            => $toPhone, 
                    'param'            => $msg));
 
            $data       = json_encode($body_json);
            $return_str = $this->_connection($data, 'json');
            $result     = json_decode($return_str);
 
            return isset($result->resp->respCode) ? ($result->resp->respCode == 0) : false;
        }
    }
}
