<?php
/**
 * Created by PhpStorm.
 * User:  barry
 * Email: 530027054@qq.com
 * Date:  2019/3/20
 * Time:  15:31
 */

namespace app\admin\controller;


use app\admin\model\ArticleModel;
use app\admin\model\ArticleValidate;
use think\Db;
use think\Request;

class Article extends Base
{
    protected $Article;
    protected $ArticleValidate;


    public function __construct()
    {
        parent::__construct();
        $this->Article = new ArticleModel();
        $this->ArticleValidate = new ArticleValidate();
    }

    public function index()
    {
        return 'admin/article/index';
    }

    public function create()
    {
//        $rec = $_POST;
        if (isset($_POST['title'])) {
            $rec = $_POST;
        } else {
            $request_data = file_get_contents('php://input');
            $rec = json_decode($request_data, true);
        }
        $res = $this->ArticleValidate->check($rec, '', 'create');
        if ($res) {
            $rec['update_time']=$rec['create_time'];
            $result = $this->Article->isUpdate(false)->save($rec);
            if ($result) {
                return $this->SuccessReturn('success', $rec);
            } else {
                return $this->ErrorReturn($this->Article->getError());
            }
        } else {
            return $this->ErrorReturn($this->ArticleValidate->getError());
        }
    }

    public function update()
    {
//        $rec = $_POST;
        if (isset($_POST['title'])) {
            $rec = $_POST;
        } else {
            $request_data = file_get_contents('php://input');
            $rec = json_decode($request_data, true);
        }
        $res = $this->ArticleValidate->check($rec, '', 'update');
        if ($res) {
            $result = $this->Article->update($rec);
            if ($result) {
                return $this->SuccessReturn();
            } else {
                return $this->ErrorReturn($this->Article->getError());
            }
        } else {
            return $this->ErrorReturn($this->ArticleValidate->getError());
        }
    }

    public function delete()
    {
        $rec = $_GET;
        $res = $this->ArticleValidate->check($rec, '', 'delete');
        if ($res) {
            $result = $this->Article->where('id', '=', $rec['id'])->delete();
            if ($result) {
                return $this->SuccessReturn();
            } else {
                return $this->ErrorReturn($this->Article->getError());
            }
        } else {
            return $this->ErrorReturn($this->ArticleValidate->getError());
        }
    }

    public function lists()
    {
        $rec = $_GET;
        $res = $this->ArticleValidate->check($rec, '', 'lists');
        if ($res) {
            if (isset($rec['type'])) {
                $result = Db::table('article')->where('type', '=', $rec['type'])->order('update_time desc')->page($rec['page_num'], $rec['page_size'])->field('content', true)->select();
                $count = count(Db::table('article')->where('type', '=', $rec['type'])->select());
                $data['count'] = $count;
                $data['rows'] = $result;
                return $this->SuccessReturn('success', $data);

            } else {
                $result = Db::table('article')->order('update_time desc')->page($rec['page_num'], $rec['page_size'])->field('content', true)->select();
                $count = count(Db::table('article')->select());
                if ($result) {
                    $data['count'] = $count;
                    $data['rows'] = $result;
                    return $this->SuccessReturn('success', $data);
                } else {
                    return $this->SuccessReturn('success', (object)[
                        'count'=>0,
                        'rows'=>[]
                    ]);
                }
            }
        } else {
            return $this->ErrorReturn($this->ArticleValidate->getError());
        }
    }

    public function search()
    {
        if (isset($_POST['key'])) {
            $rec = $_POST;
        } else {
            $request_data = file_get_contents('php://input');
            $rec = json_decode($request_data, true);
        }
        $res = $this->ArticleValidate->check($rec, '', 'search');
        if ($res) {
            $count = count(Db::table('article')->where('title', 'like', '%' . $rec['key'] . '%')->where('type', '=', $rec['type'])->order('create_time desc')->field('content', true)->select());
            $result = Db::table('article')->where('title', 'like', '%' . $rec['key'] . '%')->where('type', '=', $rec['type'])->order('create_time desc')->page($rec['page_num'], $rec['page_size'])->field('content', true)->select();
            $ret['count'] = $count;
            $ret['rows'] = $result;

            return $this->SuccessReturn('success', $ret);
        } else {
            return $this->ErrorReturn($this->ArticleValidate->getError());
        }

    }

    public function detail()
    {
        $rec = $_GET;
        $res = $this->ArticleValidate->check($rec, '', 'detail');

        if ($res) {
            $result = Db::table('article')->where('id', '=', $rec['id'])->find();
            if ($result) {
                return $this->SuccessReturn('success', $result);
            } else {
                return $this->ErrorReturn('获取失败');
            }
        }
    }

    public function allList(){
        $result['pictures']['count']=count(Db::table('article')->where('type', '=', '轮播图')->select());
        $result['listOne']['count']=count(Db::table('article')->where('type', '=', '赴加生子福利')->select());
        $result['listTwo']['count']=count(Db::table('article')->where('type', '=', '成功案例')->select());
        $result['listThree']['count']=count(Db::table('article')->where('type', '=', '月子中心')->select());
        $result['listFour']['count']=count(Db::table('article')->where('type', '=', '政策解析')->select());
        $result['listFive']['count']=count(Db::table('article')->where('type', '=', '赴加生子费用')->select());
        $result['listSix']['count']=count(Db::table('article')->where('type', '=', '赴加攻略')->select());
        $result['listSeven']['count']=count(Db::table('article')->where('type', '=', '赴加签证')->select());
        $result['listEight']['count']=count(Db::table('article')->where('type', '=', '大温介绍')->select());

        return $this->SuccessReturn('success',$result);
    }

}