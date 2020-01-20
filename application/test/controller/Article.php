<?php
namespace app\test\controller;
use think\Controller;
use think\Session;

class Article extends Controller
{
	public $arc;
	protected $beforeActionList=[
		'upload'=>['only'=>'add'],
		'delthumb'=>['only'=>'edit'],
		'rmpic_a'=>['only'=>'del_art']
	];

    public function index()
    {
    	$arcs=db('article')->alias('a')->field('a.*,b.cate')->join('cate b','a.cateid=b.id')->order('id asc')->select();
    	return view('',compact(['arcs']));
    }

    protected function upload()
    {
    	if(!empty(request()->file('thumb'))){
	    	$file=request()->file('thumb');
	    	$info=$file->move(ROOT_PATH.'public'.DS.'uploads');
	    	if($info){
	    		$this->arc['thumb']=$info->getSaveName();
	    	}
    	}
    }

    public function add()
    {
    	if(!request()->isPost()){
	    	$ct=db('cate')->order('id asc')->select();
	        $res=$this->sort($ct);
			return view('',compact(['res']));
		}
		else{
			$brr=array_merge($this->arc,input('post.'));
			$res=db('article')->insert($brr);
			if($res)
				$this->success('添加成功！',url('index'));
			else
				$this->error('添加失败！');
		}
    }

    public function delthumb()
    {
    	if(request()->ispost()){
	    	$file = request()->file('thumb');
	    	if($file){
	    		$res=input('post.');
		    	$aid=input('aid');
				$pic=db('article')->where('id',$aid)->find();
				$aa=$_SERVER['DOCUMENT_ROOT'].'/uploads'.DS.$pic['thumb'];
				if(file_exists($aa)){
					unlink($aa);
				}
				$this->upload();
			}
		}
    }

    public function edit()
    {
    	if(!request()->isPost()){
	    	$row=db('article')->where('id',input('id'))->find();
	    	$cts=db('cate')->select();
	    	$ct=$this->sort($cts);
	    	return view('',compact(['row','ct']));
    	}
    	else{
    		if(isset($this->arc['thumb'])){
	    		$brr=array_merge($this->arc,input('post.'));
	    	}
	    	else{
	    		$brr=input('post.');
	    	}
	    		$aid=$brr['aid'];
	    		unset($brr['aid']);
	    		$res=db('article')->where('id',$aid)->update($brr);
	    		if($res)
	    			$this->success('修改文章成功!',url('index'));
	    		else
	    			$this->error('修改文章失败!');

    	}
    }

    public function sort($data,$pid=0,$level=0)
    {
    	static $arr=array();
    	foreach($data as $key=>$val){
    		if($val['fcate']===$pid){
    			$val['level']=$level;
    			$arr[]=$val;

    			$this->sort($data,$val['id'],$level+1);
    		}
    	}
    	return $arr;
    }

    public function del_art()
    {
    	$aid=input('id');
    	$rs=db('article')->where('id',$aid)->delete();
    	if($rs)
    		$this->success('删除文章成功！',url('index'));
    	else
    		$this->error('删除文章失败！');
    }

    //////////////////
    // 删除文章前，先删除缩略图 //
    //////////////////
    public function rmpic_a()
    {
    	$aid=input('id');
    	$arc=db('article')->where('id',$aid)->find();
    	if($arc['thumb']){
    		unlink($_SERVER['DOCUMENT_ROOT'].'/uploads'.DS.$arc['thumb']);
    	}
    }
}