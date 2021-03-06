<?php
/**
 * Created by PhpStorm.
 * User:  barry
 * Email: 530027054@qq.com
 * Date:  2019/3/20
 * Time:  15:31
 */

namespace app\home\controller;


use app\home\model\ChargeModel;
use app\home\model\ChargeValidate;
use think\Db;
use think\Request;

class Charge extends Base
{
    protected $Charge;
    protected $ChargeValidate;


    public function __construct()
    {
        parent::__construct();
        $this->Charge = new ChargeModel();
        $this->ChargeValidate = new ChargeValidate();
    }

    public function index()
    {
        return 'admin/charge/index';
    }

    public function charge()
    {

        if (isset($_POST['membership_id'])) {
            $rec = $_POST;
        } else {
            $request_data = file_get_contents('php://input');
            $rec = json_decode($request_data, true);
        }
        $res = $this->ChargeValidate->check($rec, '', 'charge');

        if ($res) {
            $rec['create_time'] = time();
            $result = $this->Charge->insert($rec);
            if ($result) {
                $result1 = Db::table('membership')->where('membership_id', '=', $rec['membership_id'])
                    ->setInc('balance', $rec['charge_account']);
                return $this->SuccessReturn('success');
            } else {
                return $this->ErrorReturn($this->Charge->getError());
            }
        } else {
            return $this->ErrorReturn($this->ChargeValidate->getError());
        }

    }

    public function lists()
    {
        if (isset($_POST['membership_id'])) {
            $rec = $_POST;
        } else {
            $request_data = file_get_contents('php://input');
            $rec = json_decode($request_data, true);
        }
        $res = $this->ChargeValidate->check($rec, '', 'lists');
        if ($res) {

            $result = Db::table('charge_list')->order('create_time desc')->where('membership_id', '=', $rec['membership_id'])->page($rec['page_num'], $rec['page_size'])->select();
            $count = count(Db::table('charge_list')->where('membership_id', '=', $rec['membership_id'])->select());
            if ($result) {
                $data['count'] = $count;
                $data['rows'] = $result;
                return $this->SuccessReturn('success', $data);
            } else {
                return $this->SuccessReturn('success', (object)[
                    'count' => 0,
                    'rows' => []
                ]);
            }

        } else {
            return $this->ErrorReturn($this->ChargeValidate->getError());
        }
    }

    public function detail()
    {
        $rec = $_GET;
        $res = $this->ChargeValidate->check($rec, '', 'detail');

        if ($res) {
            $result = Db::table('charge')->where('id', '=', $rec['id'])->find();
            if ($result) {
                return $this->SuccessReturn('success', $result);
            } else {
                return $this->ErrorReturn('获取失败');
            }
        }
    }


}