<?php
// ===================================================================
// | FileName:      /Print/Admin/VerifyController.class.php
// ===================================================================
// | Discription：   VerifyController 内网验证服务控制器
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - index()
 * - VerifyRegister()
 * - addVerify()
 * Classes list:
 * - VerifyController extends Controller
 */
namespace Admin\Controller;
use Think\Controller;

class VerifyController extends Controller {

    /**
     *内网机器得到待查询账号和密码处
     */
    public function index(){
        $this->redirect('/Admin');
    }

    public function clear(){
        if(I('post.key')==C('VERIFY_KEY')){
            S(array('prefix'=>'verify','expire'=>30));
            S('verify_array', null);
            $this->show('OK');
        } else{
            $this->show('ERROR:WRONG KEY');
        }
    }
    /**
     *内网机器得到待查询账号和密码处
     */
    public function pull(){
        if($_SERVER['HTTPS'] == 'on' && I('post.key')==C('VERIFY_KEY')){
            S(array('prefix'=>'verify','expire'=>30));
            $verify_array = S('verify_array');
            if(!$verify_array) $verify_array = array();
            $s = '';
            foreach ($verify_array as $stu_num) {
                $s = $s.$stu_num.':'.S('verify_'.$stu_num).';';
            }
            if($s){
                $this->show('OK:'.$s);
            } else{
                $this->show('ERROR:NO DATA');
            }
        } else{
            $this->show('ERROR:WRONG KEY');
        }
    }

    /**
     *内网机器返回查询结果
     */
    public function push() {
        if($_SERVER['HTTPS'] == 'on' && I('post.key')==C('VERIFY_KEY')){
            S(array('prefix'=>'verify','expire'=>30));
            $verify_array = S('verify_array');
            $stu_num = I('post.stu_num');
            $name = I('post.name');
            $offset = array_search($stu_num, $verify_array);
            if($offset!==false){
                if($name=='WRONG PASSWORD' || $name=='BAD NETWORK')
                    $name = '0';
                S('verify_'.$stu_num, $name);
                array_splice($verify_array, $offset, 1);
                S('verify_array', $verify_array);
                $this->show('OK.');
            } else{
                $this->show('ERROR:NOT IN LIST');
            }
        } else{
            $this->show('ERROR:WRONG KEY');
        }
    }

}