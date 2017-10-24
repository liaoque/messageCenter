<?php

class  Session{

    private $session_id=null;

    public function __construct(){

        if(!$_COOKIE['session_id']){
            $this->session_id=session_id();
           setcookie('session_id', $this->session_id,time()+3600*24,'/');
        }else{
            $this->session_id=$_COOKIE['session_id'];
        }
    }
    public function getID(){
        return $this->session_id;
    }
}