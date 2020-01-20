<?php
namespace app\test\controller;
use app\test\model\Cate;
use think\Controller;
use think\Session;

class Test extends Controller
{
	public $cate;
    public $cts;
	public function __construct()
	{
        if(!session('?user')){
            $this->redirect('login/index');
        }
		$this->cate=new Cate();
        $this->cts=db('cate')->select();
	}

    public function index()
    {
        $cat=$this->sel_data();
        return view('index',compact(['cat']));
    }

    public function add()
    {
    	if(!request()->isPost()){
            $ct=db('cate')->order('id asc')->select();
            $res=$this->sort($ct);
    		return view('test/add',compact(['res']));
    	}
    	else{
    		$res=input('post.');
            
    		$rs=$this->cate->insert($res);
    		if($rs){
                $this->success('添加成功！','index');
            }
            else{
                $this->error('添加失败！');
            }
    	}
    }

    public function del()
    {
        $id=(int)input('id');
        $cates=db('cate')->select();
        $ids=$this->sort($cates,$id);
        $idlst[]=$id;
        foreach($ids as $val){
            $idlst[]=$val['id'];
        }
    	$res=db('cate')->delete($idlst);
        if($res)
            $this->success('删除成功！','index');
        else
            $this->error('删除失败！');
    }

    public function edit()
    {
        if(!request()->isPost()){
            $id=input('get.');
            $row=db('cate')->where('id',$id['id'])->find();
            $ct=$this->cts;
        	return view('edit',compact(['row','ct']));
        }
        else{
            $res=input('post.');
            $rs=db('cate')->where(['id'=>$res['id']])->update($res);
            if($rs or $rs===0)
                $this->success('更新成功！',url('index'));
            else
                $this->error('更新失败！');
        }
    }

    public function sel_data()
    {
        $data=db('cate')->order('id asc')->select();
        // dump($data);
        $res=$this->sort($data);
        // pp($res);
        return $res;
    }

    public function sort($data,$pid=0,$level=0)
    {
        static $arr=array();
        foreach($data as $key=>$val){
            if($val['fcate']===$pid){
                $val['level']=$level;
                $arr[]=$val;
                $this->sort($data,(int)($val['id']),$level+1);
            }
        }
        return $arr;
    }
}