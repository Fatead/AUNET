<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/20
 * Time: 0:26
 */

namespace Home\Controller;


use Think\Controller;

class ActivityDetailController extends Controller {
    public function index(){
        $post=I('id',0,'intval');
//        dump($post);
        $this->forecast=D('forecast')->where(array('id'=>$post))->find();
//        dump($this->forecast);
        layout('News_layout');
        $this->display();
    }

}