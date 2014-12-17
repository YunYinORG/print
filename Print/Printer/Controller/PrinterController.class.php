<?php

namespace Printer\Controller;
use Think\Controller;
class PrinterController extends Controller {


    public function index(){
        //Display profile and table to make a change
        if(session('?pri_id'))
        {
            $Printer = M('Printer');
            $data = $Printer->where("id=".session('pri_id'))->find();
            $this->data = $data;
            $this->display();
        }
        else
        {
            echo("Unauth");
        }
    }
    
    public function change(){
    //Not supported to change password
        if(session('?pri_id'))
        {
            $Printer = M('Printer');
            if($Printer->create()) 
            {
                $result = $Printer->save();
                if($result)
                {
                    echo("Success");
                }
                else
                {
                    $this->error('Unable to write');
                }
            }
            else
            {
                $this->error($Printer->getError());
            }
        }
        else
        {
            echo("Unauth");
        }
    }
    
    
    public function logout()
    {
        delete_token(cookie('token'));        
        session(null);
        cookie(null);
    }
    
    
    //Still in plan
    /*
    public function detail(){
        //ditail of file?
        $this->display();
    }*/
    
    
    public function file(){
        if(session('pri_id'))
        {
        
            //list of file,unproceeded and proceeded
            $Notification = M();
            $files = $Notification->query("SELECT * FROM file INNER JOIN notification WHERE file.id=notification.fil_id AND file.status!=3 AND file.pri_id=".session('pri_id'));
            echo("<h1>For each file displayed here</h1><br><p>You can change its status by clicking those links,but you can't see the change</p><br><p>Instead, it change in User File List</p><br><p>Once you click Paid,it may disappear after reload the page</p>");
            foreach($files as $file)
            {
                var_dump($file);
                // $this->assign('data',M('file')->getById($file['fil_id']));
                echo $this->fetch(T('Home@File/index'));
                echo("<br><a href='".U('Printer/Printer/download?id='.$file['fil_id'])."'>Downloaded</a><br>");
                echo("<a href='".U('Printer/Printer/printed?id='.$file['fil_id'])."'>Printed</a><br>");
                echo("<a href='".U('Printer/Printer/paid?id='.$file['fil_id'])."'>Paid</a><br><br><br>");
            }
        }
        else
        {
            echo("Unauth");
        }
    }
    
//Status change methods
    public function download($id){
        $File = M('File');
        $result = $File->where("id=".$id)->setField('status',1);//File downloaded
        $Notification = M();
        $result = $Notification->query("UPDATE notification SET content=1 WHERE fil_id=".$id);
    }
    public function printed($id){
        $File = M('File');
        $result = $File->where("id=".$id)->setField('status',2);//File printed
        $Notification = M();
        $result = $Notification->query("UPDATE notification SET content=2 WHERE fil_id=".$id);
    }
    
    public function paid($id){
        $File = M('File');
        $result = $File->where("id=".$id)->setField('status',3);//File paid
        $Notification = M();
        $result = $Notification->query("DELETE FROM notification WHERE fil_id=".$id);
        var_dump($result);
    }
    
    
//Not available now
 
    public function signup(){
        if(session('?pri_id'))
        {
            var_dump($_COOKIE);
        }
        else
        {
            if(cookie('?token'))
            {

                $info = auth_token(cookie('token'));
                if($info)
                {      
                    session('pri_id',$info['id']);//Needed when file upload
                    var_dump($_COOKIE);
                }
                else
                {
                    $this->display();//Fake token
                }
            }
            else
            {
                $this->display();//First time to sign up or in?
            }
        }
    }

    
    
    public function signin(){
        if(session('?pri_id'))
        {
            var_dump($_COOKIE);
            	    echo("<a href='".U('Printer/Printer/index')."'>Change password without recomfirmation</a><br>");
	    echo("<a href='".U('Printer/Printer/logout')."'>Logout</a><br>");	    
	    echo("<a href='".U('Printer/Printer/get')."'>Processing file list</a><br>");	    

        }
        else
        {
            if(cookie('?token'))
            {

                $info = auth_token(cookie('token'));
                if($info)
                {      
                    session('pri_id',$info['id']);//Needed when file upload
                    var_dump($_COOKIE);
                    	    echo("<a href='".U('Printer/Printer/index')."'>Change password without recomfirmation</a><br>");
	    echo("<a href='".U('Printer/Printer/logout')."'>Logout</a><br>");	    
	    echo("<a href='".U('Printer/Printer/get')."'>Processing file list</a><br>");	    

                }
                else
                {
                    var_dump($info);
//                    $this->display();//Fake token
                }
            }
            else
            {
                $this->display();//First time to sign up or in?
            }
        }
    }


//Not available now

    public function add(){
        $Printer = D('Printer');
        
        $data['account'] = I('post.account');
        $data['password'] = encode(I('post.password'),I('post.account'));
        $data['name'] = I('post.name');
        $data['address'] = I('post.address');
        $data['phone'] = I('post.phone');
        $data['qq'] = I('post.qq');
                
        if($Printer->create()) 
        {
            $result = $Printer->add($data);
            if($result) 
            {                
                session('pri_id',$result);
                $token = update_token($result,2);
                cookie('token',$token,3600);
                var_dump($_COOKIE);
            }
            else
            {
                $this->error('Can not insert to database');
            }
        }
        else
        {
            $this->error('Can not create model');
        }
    }
    
    
    public function auth(){
        $Printer = D('Printer');
            $account = I('post.account');
            $password = encode(I('post.password'),$account);
//            var_dump($password);
            $result = $Printer->where("account={$account}")->find();
            if($result["password"]==$password) 
            {
                session('pri_id',$Printer->id);
                $token = update_token($Printer->id,2);
//                var_dump($token);
                cookie('token',$token,3600);
                var_dump($_COOKIE);
                	    echo("<a href='".U('Printer/Printer/index')."'>Change password without recomfirmation</a><br>");
	    echo("<a href='".U('Printer/Printer/logout')."'>Logout</a><br>");	    
	    echo("<a href='".U('Printer/Printer/get')."'>Processing file list</a><br>");	    

            }
            else
            {
                var_dump($result);
            }
    }
            
}

?>
