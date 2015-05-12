<?php
function getName($number, $pwd) 
{
    $TRY_TIMES = 10;
    S(array('prefix'=>'verify','expire'=>3*$TRY_TIMES));

    $verify_array = S('verify_array');
    if(!$verify_array)
        $verify_array = array();
    if(!in_array($number, $verify_array)){
        array_push($verify_array, $number);
    } else{
        return false;
    }
    S('verify_'.$number, $pwd);
    S('verify_array', $verify_array);

    $i=0;
    while ($i++ < $TRY_TIMES && S('verify_'.$number) == $pwd){
        sleep(1);
    }
    $name = S('verify_'.$number);
    S(array('prefix'=>''));
    if($i < $TRY_TIMES && $name != '0'){
        return $name;
    } else{
        return false;
    }
}