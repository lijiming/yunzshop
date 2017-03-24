<?php

namespace app\backend\modules\goods\observers;

use app\backend\modules\goods\models\Discount;
use app\backend\modules\goods\models\Share;
use app\backend\modules\goods\services\DiscountService;
use app\backend\modules\goods\services\Privilege;
use app\backend\modules\goods\services\PrivilegeService;
use app\common\models\Goods;
use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午11:24
 */
class GoodsObserver extends \app\common\observers\BaseObserver
{


    public function saving(Model $model)
    {

        $result = $this->pluginObserver('observer.goods', $model, 'validator');
        if (in_array(false, $result)) {
            return false;
        }
    }


    public function saved(Model $model)
    {
        $this->pluginObserver('observer.goods', $model, 'saved');
    }

    public function created(Model $model)
    {
        $this->pluginObserver('observer.goods', $model, 'created');
    }

    public function updating(Model $model)
    {
        $this->pluginObserver('observer.goods', $model, 'updating');

    }

    public function updated(Model $model)
    {
        $this->pluginObserver('observer.goods', $model, 'updated');
    }

    public function deleted(Model $model)
    {
        $this->pluginObserver('observer.goods', $model, 'deleted');
    }


}