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
    
    
    
    /*
    public function detail(){
        //ditail of file?
        $this->display();
    }*/
    
    
    public function get(){
        if(session('?pri_id'))
        {
        
            //list of file,unproceeded and proceeded
            $Notification = M();
            $files = $Notification->query("SELECT * FROM file INNER JOIN notification WHERE file.id=notification.fil_id AND file.printed=0 AND file.pri_id=".session('pri_id'));
           /*
            foreach($files as $file)
            {
               // var_dump($file);
                $file['fil_id'];
                $File = M('File');
                $detail
            }
            */
            $this->data = $files;
            /*
            $this->data['download']=U('Printer/download?id='.$files['fil_id']);
            $this->data['printed']=U('Printer/printed?id='.$files['fil_id']);
            $this->data['paid']=U('Printer/paid?id='.$files['fil_id']);
            */
            $this->display();
        }
        else
        {
            echo("Unauth");
        }
    }
    
//Status change methods
    public function download($fil_id){
        $File = M('File');
        $result = $File->where("id=".$fil_id)->setField('printed',1);//File downloaded
        $Notification = M();
        $result = $Notification->query("UPDATE notification SET content=1 WHERE fil_id=".$fil_id);
    }
    public function printed($fil_id){
        $File = M('File');
        $result = $File->where("id=".$fil_id)->setField('printed',2);//File printed
        $Notification = M();
        $result = $Notification->query("UPDATE notification SET content=2 WHERE fil_id=".$fil_id);
    }
    
    public function paid($fil_id){
        $File = M('File');
        $result = $File->where("id=".$fil_id)->setField('printed',3);//File paid
        $Notification = M();
        $result = $Notification->query("DELETE FROM notification WHERE fil_id=".$fil_id);
        var_dump($result);
    }
    
    
    
    public function signup(){
        if(session('?pri_id'))
        {
            print_r($_COOKIE);
        //    $this->success('Should not be here');
        }
        else
        {
            if(cookie('?token'))
            {

                $info = auth_token(cookie('token'));
                if($info)
                {      
                    session('pri_id',$info['id']);//Needed when file upload
                    print_r($_COOKIE);
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
            print_r($_COOKIE);
        }
        else
        {
            if(cookie('?token'))
            {

                $info = auth_token(cookie('token'));
                if($info)
                {      
                    session('pri_id',$info['id']);//Needed when file upload
                    print_r($_COOKIE);
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

    
    public function add(){
        $Printer = D('Printer');
        
        $data['account'] = I('post.account');
        $data['password'] = I('post.password','','md5');
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
                print_r($_COOKIE);
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
            $password = I('post.password','','md5')
            $result = $Printer->where("account={$account}")->find();
            if($result["password"]==$password) 
            {
                session('use_id',$Printer->id);
                $token = update_token($Printer->id,1);
                cookie('token',$token,3600);
                print_r($_COOKIE);
            }
            else
            {
                var_dump($result);
            }
    }
            
}

?>
