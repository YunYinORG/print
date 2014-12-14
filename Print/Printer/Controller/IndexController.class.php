<?php
namespace Printer\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
	    echo('<h1>Print Index Page</h1><br>');
	    echo('<h3>Sorry you are not allow to sign up</h1><br>');
	    echo("<a href='".U('Printer/Printer/signin')."'>Sign in</a><br>");
    }
}
