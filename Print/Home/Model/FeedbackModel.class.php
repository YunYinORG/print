<?php

namespace Home\Model;
use Think\Model;

class FeedbackModel extends Model {

    protected $_validate = array(
        array('email','require','请填写邮箱！'),
        array('phone','require','请填写手机号码！'),
        array('message','require','请填写反馈信息！),
    );
    
    protected $_auto =   array(
    array('time','time',1,'function'),
    );
}
?>
