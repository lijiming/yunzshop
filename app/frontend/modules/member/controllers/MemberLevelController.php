<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/8
 * Time: 上午11:54
 */


namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\frontend\modules\member\models\MemberLevel;
use app\common\services\goods\LeaseToyGoods;
use Yunshop\LeaseToy\models\LevelRightsModel;
use app\frontend\modules\member\models\MemberModel;

class MemberLevelController extends ApiController
{

    protected $settinglevel;
    
    public function __construct()
    {
        parent::__construct();
        //会员等级的升级的规则
        $this->settinglevel = \Setting::get('shop.member');
    }


    /**
     * 等级信息
     * @return json 
     */
    public function index()
    {
        //会员等级的升级的规则
        $this->settinglevel = \Setting::get('shop.member');

        if (!$this->settinglevel) {
            return $this->errorJson('未进行等级设置');
        }

        if ($this->settinglevel['level_type'] != 2) {
            return $this->errorJson('.....');
        }

        //升级条件判断
        if ($this->settinglevel['level_type'] == 2) {
            $data =  MemberLevel::getLevelGoods();
            $bool = LeaseToyGoods::whetherEnabled();
            //商品图片处理
            foreach ($data as &$value) {
                $value['rent_free'] = 0;
                $value['deposit_free'] = 0;
                if ($bool) {
                    $levelRights = LevelRightsModel::getRights($value['id']);
                    if ($levelRights) {
                        $value['rent_free'] = $levelRights->rent_free;
                        $value['deposit_free'] = $levelRights->deposit_free;
                    }
                }
                $value['goods']['thumb'] = replace_yunshop(yz_tomedia($value['goods']['thumb']));
            }
        } else {
            $data = MemberLevel::getLevelData($this->settinglevel['level_type']);
        }

        //会员信息
        $uid = \Yunshop::app()->getMemberId();
        $member_info = $this->getUserInfo($uid);
        if (!empty($member_info)) {
            $member_info = $member_info->toArray();

            if (!empty($member_info['yz_member']['level'])) {
                $memberData['level_id'] =  $member_info['yz_member']['level']['id'];
                $memberData['level_name'] =  $member_info['yz_member']['level']['level_name'];
                $levelRights = LevelRightsModel::getRights($member_info['yz_member']['level']['id']);

                $memberData['rights'] = [
                    'discount' => $member_info['yz_member']['level']['discount'] ? $member_info['yz_member']['level']['discount'] : 0,
                    'freight_reduction' => $member_info['yz_member']['level']['freight_reduction'] ? $member_info['yz_member']['level']['discount'] : 0,
                    'rent_free' => $levelRights->rent_free ? $levelRights->rent_free : 0,
                    'deposit_free' => $levelRights->deposit_free ? $levelRights->deposit_free : 0,
                ];
            } else {
                $memberData['level_id'] =  0;
                $memberData['level_name'] =   $this->settinglevel['level_name'] ?  $this->settinglevel['level_name'] : '普通会员';
            }

            $memberData['nickname'] =  $member_info['nickname'];
            $memberData['avatar'] = $member_info['avatar'];
            $memberData['validity'] = $member_info['yz_member']['validity'];
        }

        $shopSet = \Setting::get('shop.shop');
        $shopContact = \Setting::get('shop.contact');
        $levelData = [
            'member_data' => $memberData,
            'level_type' => $this->settinglevel['level_type'],
            'data' => $data,
            'cservice' => $shopSet['cservice'],
            'shop_description' => html_entity_decode($shopContact['description']),
        ];

        return $this->successJson('ok',$levelData);
    }

    /**
     * 会员升级详情
     * @return [json] [detail]
     */
    public function upgradeDetail()
    {
        $id = intval(\YunShop::request()->id);

        if (!$id) {
            return $this->errorJson('参数无效');
        }

        if ($this->settinglevel['level_type'] != 2) {
            return $this->errorJson('.....');
        }

        if ($this->settinglevel['level_type'] == 2) {
            $detail = MemberLevel::uniacid()
                    ->with(['goods' => function($query) {
                        return $query->select('id', 'title', 'thumb', 'price');
                    }])->find($id);
            $bool = LeaseToyGoods::whetherEnabled();
            $detail->rent_free = 0;
            $detail->deposit_free = 0;
            if ($bool) {
                $levelRights = LevelRightsModel::getRights($id);
                if ($levelRights) {
                    $detail->rent_free = $levelRights->rent_free;
                    $detail->deposit_free = $levelRights->deposit_free;
                }
            }


            $detail->goods->thumb = replace_yunshop(yz_tomedia($detail->goods->thumb));
            $detail->interests_rules = html_entity_decode($detail->interests_rules);
        } else {
            $detail = MemberLevel::uniacid()->find($id);
            $detail->interests_rules = html_entity_decode($detail->interests_rules);
        }

        $detail->level_type = $this->settinglevel['level_type'];

        return $this->successJson('leveldetail', $detail);
    }

    public function getUserInfo($member_id)
    {
        return MemberModel::select(['*'])
            ->uniacid()
            ->where('uid', $member_id)
            ->whereHas('yzMember', function($query) use($member_id) {
                $query->where('member_id', $member_id)->whereNull('deleted_at');
            })
            ->with(['yzMember' => function ($query) {
                    return $query->select(['*'])->where('is_black', 0)
                    ->with(['level' => function ($query2) {
                        return $query2->select(['id', 'level_name', 'discount', 'freight_reduction']);
                    }]);
            }])->first();
    }

    public function isOpen()
    {

        $info['is_open'] = 0;

        if (LeaseToyGoods::whetherEnabled()) {
           if ($this->settinglevel['display_page'] && ($this->settinglevel['level_type'] == 2))
           {
                $info['is_open'] = 1;
           }
        }

        return $this->successJson('是否开启', $info);

    }
}

