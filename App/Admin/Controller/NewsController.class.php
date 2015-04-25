<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/2/14
 * Time: 16:20
 */

namespace Admin\Controller;
use Admin\Util\Category;
use Org\Util\Ueditor;
use Think\Page;

/*
 *
 * pic---文章缩略图，以第一张图片制作
 * 图片上传路径Upload/news/image/{uid}/{yyyy}{mm}{dd}/
 *
 * create table aunet_news(id int unsigned not null primary key auto_increme
 * nt,title varchar(30) not null default '',content text,time int(10) unsigned not
 * null default 0,cid int unsigned not null,del tinyint(1) unsigned not null defaul
 * t 0,pic text not null default '',src text not null default '')ENGINE=MyISAM default charset=utf8;
 */


/*
 * 新闻与标签多对多关联数据库
 * create table aunet_news_attr(nid int unsigned not null,aid int unsigned n
 * ot null,index nid(nid),index aid(aid));
 */
class NewsController extends CommonController{


    //新闻列表
    public function news_index(){

        $count=D('NewsRelation')->getNewsCount();
        $this->count=$count;
        $Page=new Page($count,5);
        $this->news=D('NewsRelation')->where(array('del'=>0))->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->page=$Page->show();
        $this->display('news_index');
    }


    //添加新闻界面
    public function addnews(){
        $cate=M('cate')->order('sort')->select();
        $this->cate=Category::unlimitedForLevel($cate);
        $this->attr=M('attr')->select();
        $this->display();
    }

    public function ueditor(){
        $ueditor=new Ueditor();
        echo $ueditor->output();
    }

    //新增 OR 修改新闻后台处理
    public function addNewsHandle(){
//        dump($_POST);
////        die;
        if(!IS_POST){
            $this->error('页面不存在',U('news_index'));
        }

        //相关数据
        $data=array();
        //缩略图
        if(preg_match_all("/src=([\"|']?)([^\"'>]+\.(gif|jpg|jpeg|bmp|png))\\1/i",$_POST['content'],$match)){
            $data['pic']=$match[2][0];
        }else{
            $data['pic']=' ';
        }
        $str="";
        //将所有图片存入数据库
        foreach($match[2] as $v){
            $str=" ".$v.$str;
        }
        $data['src']=$str;
        if(isset($_POST['aid'])){
            foreach($_POST['aid'] as $v){
                $data['attr'][]=$v;
            }
        }
        $data['title']=I('title');
        $data['content']=$_POST['content'];
        $data['time']=time();
        $data['text']=$_POST['text'];
        if(M('news')->where(array('id'=>I('id')))->find()){
            if(D('NewsRelation')->where(array('id'=>I('id')))->relation(true)->save($data)){
                $this->success('修改成功',U('news_index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $data['cid']=(int)$_POST['cid'];
            if(D('NewsRelation')->relation(true)->add($data)){
                $this->success('添加成功',U('news_index'));
            }else{
                $this->error('添加失败');
            }
        }


    }



    //删除 OR 还原新闻
    public function toTrash(){
        $id=I('id','','intval');
        $type=I('type','','intval');

        $msg=$type?'删除':'还原';
        $update=array('id'=>$id,
            'del'=>$type,
        );
        if(M('news')->save($update)){
            $this->success($msg.'成功',U('news_index'));
        }else{
            $this->error($msg.'失败');
        }


    }

    //编辑原有新闻
    public function edit(){
        $id=I('id','','intval');
        $text=D('NewsRelation')->getNewsById($id);
        $this->attr=M('attr')->select();
        foreach($text as $v){
            $this->text=$v;
        }
        $this->display();
    }


    //回收站页面
    public function trash(){
        $count=D('NewsRelation')->getNewsCount(1);
        $Page=new Page($count,2);
        $this->news=D('NewsRelation')->where(array('del'=>1))->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
//        $this->news=D('NewsRelation')->getNews(1);
        $this->page=$Page->show();
        $this->count=$count;
        $this->display('news_index');
    }


    //删除新闻
    public function delete(){
        $id=I('id','','intval');
        $data=M('news')->where(array('id'=>$id))->getField('src');
        $src=explode(" ",substr($data,1));
        foreach($src as $v=>$k){
            if(file_exists(substr($k,6))){
                unlink(substr($k,6));
            }
        }
        D('NewsRelation')->relation('attr')->where(array('id'=>$id))->delete();

        $this->redirect('news_index');

        //In Sae :
        /*
         * $src=explode(" ",substr($data,1));
           $st=new \SaeStorage();

           foreach($src as $v=>$k){
              $filename=substr($k,strpos($k,"com")+3);
              if($st->fileExists('upload',$filename)){
                  if($st->delete('upload',$filename)&&D('NewsRelation')->relation('attr')->where(array('id'=>$id))->delete()){
                      $this->redirect('news_index');
                  }else{
                      $this->error('删除失败');
                  }
              }else{
                  $this->error('删除失败');
              }

         }*/
    }

    public function deleteAll(){
        $del=array('del'=>1);
        $data=M('news')->where($del)->getField('src');
        $src=explode(" ",substr($data,1));
        foreach($src as $v=>$k){
            if(file_exists(substr($k,6))){
                unlink(substr($k,6));
            }
        }
        D('NewsRelation')->relation('attr')->where($del)->delete();
        $this->redirect('news_index');

        //In Sae:


        /*$src=explode(" ",substr($data,1));
          $st=new \SaeStorage();

          foreach($src as $v=>$k){
              $filename=substr($k,strpos($k,"com")+3);
              if($st->fileExists('upload',$filename)){
                  if(D('NewsRelation')->relation('attr')->where($del)->delete()&&$st->delete('upload',$filename)){
                	  $this->redirect('news_index');
                  }else{
                	  break;
                  }
              }else{
            	  break;
              }

          }
          $this->error('删除失败');
         *
         */


    }


    //新闻预览
    public function text(){
        $id=I('id','','intval');
        $text=D('NewsRelation')->getNewsById($id);
//        dump($text);die;
        foreach($text as $v){
            $this->attr=$v['attr'];
            $this->cate=$v['cate'];
            $this->text=$v['content'];
            $this->title=$v['title'];
            $this->time=date('Y-m-d H:i',$v['time']);
        }
        $this->display();

    }

    //清空未利用的资源
    public function clearCache(){
        $images=get_filetree("./Upload/news/image");   //图片文件遍历
        $pics=M('news')->getField('src');
        $pic=explode(" ",substr($pics,1));
        arsort($images);

        foreach($pic as $v=>$k){
            $result[]=substr($k,6);
        }
        $res=array_diff($images,$result);
//        dump($data);dump($result);dump($res);
        if($res!=null){
            foreach($res as $v){
                if(file_exists($v)){
                    if(unlink($v)){
                        $this->success('清除成功','news_index');
                    }else{
                        $this->error('清除失败','news_index');
                    }
                }else{
                    $this->error('清除失败','news_index');
                }
            }
        }else{
            $this->success('没有缓存','news_index');
        }

    }

    //@Override

    //Change in Sae


    /*public function clearCache(){
        $st=new \SaeStorage();
        $images=$st->getList('upload','news/image');


        //        $images=get_filetree("./Upload/news/image");   //图片文件遍历
        $pics=M('news')->field('src')->select();
        foreach($pics as $s=>$p){
            foreach($p as $k=>$v){
                $pic[]=explode(" ",substr($v,1));
            }

        }
        foreach($pic as $s=>$p){
            foreach($p as $k=>$v){
                if($v!=''){
                    $res[]=substr($v,strpos($v,"com")+4);

                }

            }
        }
        $result=array_diff($images,$res);
        if($result!=null){
            foreach($result as $k=>$v){
                if($st->fileExists('upload',$v)){
                    if($st->delete('upload',$v)){
                        $this->success('清除成功','news_index');
                        break;
                    }else{
                        break;
                    }
                }else{
                    break;
                }

            }
            $this->success('没有缓存','news_index');
        }else{
            $this->success('没有缓存','news_index');
        }


    }*/


} 