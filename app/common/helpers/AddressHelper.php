<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/24
 * Time: 上午11:13
 */

namespace app\common\helpers;


use app\common\components\BaseController;
use app\common\models\Address;

class AddressHelper extends BaseController
{

    public static function tplLinkedAddress($names, $data)
    {
        $html = "";
        //省
        $html .= '<select id="sel-provance" name="' . $names[0] . '" onchange="selectCity();" class="select">';
        $html .= '<option value="">请选择省份</option>';
        $html .= '</select>';
        //市
        $html .= '<select id="sel-city" name="' . $names[1] . '" onchange="selectcounty();" class="select">';
        $html .= '<option value="" >请选择城市</option>';
        $html .= '</select>';
        //区
        if(isset($names[3])){
            $html .= '<select id="sel-area" name="' . $names[2] . '" onchange="selectstreet();" class="select">';
        }else{
            $html .= '<select id="sel-provance" name="' . $names[2] . '" class="select">';
        }
        $html .= '<option value="" >请选择区</option>';
        $html .= '</select>';
        //街道
        if (isset($names[3])) {
            $html .= '<select id="sel-street" name="' . $names[3] . '"  class="select">';
            $html .= '<option value="">请选择街道</option>';
            $html .= '</select>';
        }
        return $html;
    }

}