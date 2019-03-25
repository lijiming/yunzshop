<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/27
 * Time: 10:53
 */

namespace app\platform\modules\system\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;

class SiteController extends BaseController
{
    public function index()
    {
        $set_data = request()->setdata;
        $copyright = SystemSetting::settingLoad('remote', 'system_copyright');

        if ($set_data) {
            $site = SystemSetting::settingSave($set_data, 'copyright', 'system_copyright');
            if ($site) {
                return $this->successJson('成功', '');
            } else {
                return $this->errorJson('失败', '');
            }
        }

        if ($copyright) {
            return $this->successJson('成功', $copyright);
        } else {
            return $this->errorJson('没有检测到数据', '');
        }
    }
}