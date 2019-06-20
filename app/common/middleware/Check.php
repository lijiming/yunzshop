<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/12
 * Time: 下午5:42
 */

namespace app\common\middleware;


use app\common\traits\JsonTrait;
use app\common\facades\Setting;

class Check
{
    use JsonTrait;

    public function handle($request, \Closure $next)
    {
        $this->checkRegister();

        return $next($request);
    }

    /**
     * 检测是否注册
     */
    private function checkRegister()
    {
        $key = Setting::getNotUniacid('shop.key')['key'];
        $secret = Setting::getNotUniacid('shop.key')['secret'];

        if ((!$key || !$secret) && (request()->path() != 'admin/index' && !strpos(request()->path(), 'siteRegister'))) {
            $this->errorJson('', [
                'status' => -5
            ])->send();
            exit;
        }
    }
}