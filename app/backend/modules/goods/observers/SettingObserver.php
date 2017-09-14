<?php

namespace app\backend\modules\goods\observers;

use app\backend\modules\goods\models\Discount;
use app\backend\modules\goods\models\Share;
use app\backend\modules\goods\services\DiscountService;
use app\backend\modules\goods\services\Privilege;
use app\backend\modules\goods\services\PrivilegeService;
use app\common\models\AdminOperationLog;
use app\common\models\Goods;
use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:24
 */
class SettingObserver extends \app\common\observers\BaseObserver
{


    public function saving(Model $model)
    {
        $log = new AdminOperationLog();
        $log->table_name = $model->getTable();
        dd($model);
        exit;

        $log->table_id = $model->id;
        //dd($model->getDirty());
        $log->after = $model->getDirty();
        $log->before = collect($model->getDirty())->map(function($value,$key) use ($model){
            return $model->getOriginal($key);
        });
        $log->before_identify = md5(json_encode($model->getOriginal()));
        $log->after_identify = md5(json_encode($model->getAttributes()));
        $log->save();
    }


    public function saved(Model $model)
    {
    }

    public function created(Model $model)
    {
    }

    public function updating(Model $model)
    {

    }

    public function updated(Model $model)
    {
    }

    public function deleted(Model $model)
    {
    }


}