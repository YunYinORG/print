<?php

// ===================================================================
// | FileName:      /Print/Admin/Common/function.php
// ===================================================================
// | Admin 后台管理端公用函数库
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - admin_id()
 * Classes list:
 */

/**
 *admin_id()
 *验证admin权限
 *如果未登录使用cookie自动登陆并更新session
 *@param $redirect_url 重定向url,空不跳转
 *@return int 是否具备admin权限
 */
function admin_id($redirect_url = null) 
{
    $id = session('admin_id');
    if ($id) 
    {
        return $id;
    } else
    {
        $token = I('cookie.token', null, C('REGEX_TOKEN'));
        if ($token) 
        {
            $info = auth_token($token);
            if ($info['type'] == C('ADMIN')) 
            {
                session('admin_id', $info['id']);
                return $info['id'];
            }
        }
    }
    
    if ($redirect_url) 
    {
        redirect($redirect_url);
    } else
    {
        return 0;
    }
}