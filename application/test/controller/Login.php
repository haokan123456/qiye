<?php
namespace app\test\controller;
use app\test\model\Cate;
use think\Controller;

class Login extends controller
{
    public function index()
    {
        return view();
    }

    //////////
    // 添加用户 //
    //////////
    public function add()
    {
        if(request()->isPost()){
            $user=input('post.');
            $res=db('admin')->where($user)->find();
            if(!empty($res)){
                $this->error("用户已存在！");
            }
            else{
                $user['password']=md5($user['password']);
                $rs=db('admin')->insert($user);
                $this->success('添加成功！',url('lst'));
            }
        }
        else{
            return view();
        }
    }

    // 用户列表
    public function lst()
    {
        $user=db('admin')->paginate(5);

        return view('lst',compact(['user']));
    }

    //////////
    // 用户编辑 //
    //////////
    public function edit()
    {
        $id=input('get.id');
        if(!request()->ispost()){
            $user=db('admin')->where(['id'=>$id])->find();
            return view('edit',compact(['user']));
        }
        else{
            $user=input('post.');
            if(!empty($user['password'])){
                $user['password']=md5($user['password']);
            }
            else{
                unset($user['password']);
            }

            $res=db('admin')->where(['id'=>$user['id']])->update($user);
            if($res or $res===0){
                $this->success('更新完成！',url('login/lst'));
            }
            else{
                $this->error('更新失败！');
            }
        }
    }

    ///////////
    // 删除管理员 //
    ///////////
    public function del()
    {
        $id=input('get.id');
        $res=db('admin')->where(['id'=>$id])->delete();
        if($res)
            $this->success('删除成功！',url('lst'));
        else
            $this->error('删除失败！');
    }

    ////////
    // 登录 //
    ////////
    public function denglu()
    {
        $user=input('post.');
        $user['password']=md5($user['password']);
        $res=db('admin')->where($user)->find();
        if(!empty($res)){
            session('user',$user['name']);
            session('uid',$res['id']);
            $this->success('登录成功！','test/index');
        }
        else{
            $this->error('登录失败！');
        }
    }

    ////////
    // 退出 //
    ////////
    public function logout()
    {
        session(null);
        $this->redirect('index');
    }

    //////////
    // 找回密码 //
    //////////
    public function getpwd()
    {
        if(!request()->ispost()){
            $uid=session('uid');
            $user=db('admin')->where('id',$uid)->find();
            return view('getpwd',compact(['user']));
        }
        else{
            $uid=session('uid');
            $pwd=input('post.');
            if($pwd['password']!="" and $pwd['password']===$pwd['password2']){
                unset($pwd['password2']);
                $pwd['password']=md5($pwd['password']);
                $res=db('admin')->where('id',$uid)->update($pwd);
                $this->success('密码修改成功！','lst');
            }
            elseif($pwd['password']!=$pwd['password2']){
                $this->error('两次密码不一致，密码修改失败!');
            }
        }
    }
}