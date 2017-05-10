<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/12
 * Time: 下午7:40
 */

namespace app\frontend\modules\refund\controllers;


use app\common\components\ApiController;
use app\frontend\modules\refund\models\RefundApply;

class ListController extends ApiController
{
    public function index(\Request $request)
    {
        $this->validate($request, [
            'pagesize' => 'sometimes|filled|integer',
            'page' => 'sometimes|filled|integer',
        ]);
        $refunds = RefundApply::defaults()->paginate($request->query('pagesize', '20'));
        $refunds = $refunds->filter(function ($refund){
            if(!isset($refund->order)){
                $refund->delete();
            }
            return isset($refund->order);
        });
        $this->successJson('成功', $refunds->toArray());
    }
}