<?php

namespace Home\Model;
use Think\Model;
class FileModel extends Model {

    protected $_validate    =   array(
        array('pri_id','require','Require printer'),
        );
        /*
    protected $_auto    =   array(
        array('upload','time',1,'function'),
        );
        */ 
}
?>

