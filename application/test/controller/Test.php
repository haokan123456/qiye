<?php
namespace app\test\controller;
// use think\Request;
use app\test\model\Cate;
use think\Controller;

class Test extends controller
{
	public $cate;
    public $cts;
	public function __construct()
	{
		$this->cate=new Cate();
        $this->cts=db('cate')->select();
	}
    public function index()
    {
        $cat=db('cate')->order('id asc')->select();
        return view('index',compact(['cat']));
    }

    public function add()
    {
    	if(!request()->isPost()){
            $ct=db('cate')->select();
    		return view('add',compact(['ct']));
    	}
    	else{
    		$res=input('post.');

            $cate=explode(',',$res['cate']);
            foreach($cate as $val){
                $row[]=['cate'=>$val,'fcate'=>$res['fcate']];
            }
    		$rs=$this->cate->insertAll($row);
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
        $id=input('get.')['id'];
    	$res=db('cate')->delete($id);
        if($res)
            $this->success('删除成功！','index');
        else
            $this->error('删除失败！');
    }

    public function edit()
    {
        $id=input('get.');
        $row=db('cate')->where('id',$id['id'])->find();
        $ct=$this->cts;
    	return view('edit',compact(['row','ct']));
    }
}