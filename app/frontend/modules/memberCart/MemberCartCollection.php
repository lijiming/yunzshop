<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/1
 * Time: 下午5:31
 */

namespace app\frontend\modules\memberCart;

use app\frontend\models\MemberCart;
use Illuminate\Database\Eloquent\Collection;

class MemberCartCollection extends Collection
{
    public function validate()
    {

        $this->unique('goods_id')->each(function (MemberCart $memberCart) {

            if (isset($memberCart->goods->hasOnePrivilege)) {
                // 合并规格商品数量,并校验
                $total = $this->where('goods_id', $memberCart->goods_id)->sum('total');

                $memberCart->goods->hasOnePrivilege->validate($total);
            }
        });
        $this->each(function (Membercart $memberCart) {
            $memberCart->validate();
        });
    }
    public function loadRelations(){
        $this->load(['goods','goods.hasOnePrivilege','goods.hasOneOptions','goods.hasManyGoodsDiscount','goods.hasOneGoodsDispatch','goods.hasOneSale','goodsOption']);
        $this->each(function (MemberCart $memberCart) {
            if(isset($memberCart->goodsOption)){
                $memberCart->goodsOption->setRelation('goods',$memberCart->goods);
            }
        });
        return $this;
    }

}