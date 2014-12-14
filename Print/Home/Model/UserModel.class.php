<?php

namespace Home\Model;
use Think\Model;

class UserModel extends Model {

    protected $_validate = array(
        array('student_number','require','Require student_number'),
        array('password','require','Require password'),
    );
        /*
    protected $_auto    =   array(
        array('upload','time',1,'function'),
        ); 
        */
}
?>

