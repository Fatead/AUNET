<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/28
 * Time: 19:30
 */

namespace Home\Controller;


use Think\Controller;

class WeiZaiController extends Controller{
    public function index(){
        layout('weizailayout');
        $this->display();
    }
    public function bumen(){
        layout('weizailayout');
        $this->display();
    }
    public function shetuan(){
        layout('weizailayout');
        $this->display();
    }
    public function heci(){
        layout('weizailayout');
        $this->display();
    }
}