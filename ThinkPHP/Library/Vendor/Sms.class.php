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
namespace Vendor;
class Sms
{
    private $_supporter       = '';
    private $_header          = false;
    
    public function __construct($supporter        = '') 
    {
        $this->_supporter = $supporter;
        $softVersion      = "2014-06-30";
        $baseUrl          = "https://api.ucpaas.com/";
        date_default_timezone_set('Asia/Shanghai');
        $accountSid    = C('SMS_ACCOUNT');
        $token         = C('SMS_TOKEN');
        $timestamp     = date("YmdHis");
        $sig           = strtoupper(md5($accountSid . $token . $timestamp));
        $this->_url    = $baseUrl . $softVersion . '/Accounts/' . $accountSid . '/Messages/templateSMS?sig=' . $sig;       
        $auth          = trim(base64_encode($accountSid . ":" . $timestamp));
        $this->_header = array('Content-Type:application/json;charset=utf-8', 'Authorization:' . $auth,);
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
    public function sendSms($toPhone, $msg, $templateId = '') 
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
