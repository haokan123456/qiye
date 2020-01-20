<?php
namespace app\test\controller;
use app\test\model\Cate;
use think\Controller;
use think\Session;

class Aaa extends Controller
{
    public $cate;
    public $cts;

    protected $beforeActionList=[
        'hehe'=>'index'
    ];

    public function __construct()
    {
        if(!session('?user')){
            $this->redirect('login/index');
        }
        $this->cate=new Cate();
        $this->cts=db('cate')->select();
    }

    protected function hehe(){
        dump("123123<br/>");
    }

    public function index(){
        dump(111111);
    }
}