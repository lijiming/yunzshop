<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/21
 * Time: 下午2:01
 */
use Illuminate\Database\Seeder;

class YzMenuUpgradeSeeder extends Seeder
{
    protected $table = 'yz_menu';

    public function run()
    {
        $item = \Illuminate\Support\Facades\DB::table($this->table)->where('item', 'system_update')->first();
        if ($item) {
            echo "system_update 已经有数据了跳过\n";
            return;
        }
        $data = [
            'name' => '系统升级',
            'item' => 'system_update',
            'url' => 'update.index',
            'url_params' => '',
            'permit' => 1,
            'menu' => 1,
            'icon' => 'fa-arrow-circle-up',
            'parent_id' => 1,
            'sort' => 0,
            'status' => 1,
            'created_at' => time(),
        ];
        \Illuminate\Support\Facades\DB::table($this->table)->insert($data);
    }

}