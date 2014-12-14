<?php

namespace Printer\Controller;
use Think\Controller;
class PrinterController extends Controller {


    public function index(){
        //profile
        if(session('?account'))
        {
            $Printer = M('Printer');
            $data = $Printer->where("account=".cookie('account'))->find();
            $this->data = $data;
            $this->display();
        }
        else
        {
            echo("Unauth");
        }
    }
    
    public function change(){
        if(session('?account'))
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
        session(null);
        cookie(null);
    }
    
    
    
    /*
    public function detail(){
        //ditail of file?
        $this->display();
    }*/
    
    
    public function get(){
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
        if(session('?account'))
        {
            print_r($_COOKIE);
        //    $this->success('Should not be here');
        }
        else
        {
            $this->display();
        }
    }
    
    public function signin(){
        if(session('?account'))
        {
            print_r($_COOKIE);
        //    $this->success('Should not be here');
        }
        else
        {
            if(cookie('?account')&&cookie('?password'))
            {
                $Printer = M('Printer');
                $account = cookie('account');
                $password = cookie('password');
                $result = $Printer->where("account={$account} and password={$password}")->find();
                if($result) 
                {
                    session('account', $Printer->account);
                    cookie('account',$Printer->account,3600);
                    cookie('password',$Printer->password,3600);
//                $this->success('Successfully sign in');
                    print_r($_COOKIE);
                }
                else
                {
                    $this->display();
                }
            }
            else
            {
                $this->display();
            }
        }
    }

    
    public function add(){
        $Printer = D('Printer');
        
        $account = I('post.account');
        $password = I('post.password');
        if($Printer->create()) 
        {
            $result = $Printer->add();
            if($result) 
            {                
                session('account',$account);
                session('pri_id',$result);
                cookie('account',$account,3600);
                cookie('password',$password,3600);
//                $this->success('Successfully sign in');
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
            $password = I('post.password');
            $result = $Printer->where("account={$account} and password={$password}")->find();
            if($result) 
            {
                session('account',$Printer->account);
                session('pri_id',$Printer->id);
                cookie('account',$Printer->account,3600);
                cookie('password',$Printer->password,3600);
//                $this->success('Successfully sign in');
                print_r($_COOKIE);
            }
            else
            {
                $this->error('Not sign up yet');
            }
    }
            
}

?>
