@extends('layouts.base')

@section('content')
    <div class="panel panel-default">
        <div class='panel-heading'>
            提现者信息
        </div>
        <div class='panel-body'>
            <div style='height:auto;width:120px;float:left;'>
                <img src='{{tomedia($item->hasOneMember->avatar)}}'
                     style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
            </div>
            <div style='float:left;height:auto;overflow: hidden'>
                <p>
                    <b>昵称:</b>
                    {{$item->hasOneMember->nickname}}
                    <b>姓名:</b>
                    {{$item->hasOneMember->realname}}
                    <b>手机号:</b>
                    {{$item->hasOneMember->mobile}}
                </p>
                <p><b>分销等级:</b> {{$item->hasOneAgent['agent_level']['name']}} (
                    @if($set['level']>=1)一级比例: <span style='color:blue'>{{$item->hasOneAgent['agent_level']['first_level']}}
                        %</span>@endif
                    @if($set['level']>=2)二级比例: <span style='color:blue'>{{$item->hasOneAgent['agent_level']['second_level']}}
                        %</span>@endif
                    @if($set['level']>=3)三级比例: <span style='color:blue'>{{$item->hasOneAgent['agent_level']['third_level']}}
                        %</span>@endif
                    )
                </p>
                <p>
                    <b>累计收入: </b><span style='color:red'>{{$item->hasOneAgent['commission_total']}}</span> 元
                </p>
                <p>
                    <b>提现金额: </b><span style='color:red'>{{$item->amounts}}</span> 元
                <p>
                <p>
                    <b>收入类型: </b>{{$item->type_name}}
                <p>
                <p>
                    <b>提现方式: </b>{{$item->pay_way_name}}
                </p>
                <p>
                    <b>状态: </b>{{$item->status_name}}
                </p>
                <p>
                    <b>申请时间: </b>{{$item->created_at}}
                </p>
                @if($item->audit_at)
                    <p>
                        <b>审核时间: </b>{{$item->audit_at}}
                    </p>
                @endif
                @if($item->pay_at)
                    <p>
                        <b>打款时间: </b>{{$item->pay_at}}
                    </p>
                @endif
                @if($item->arrival_at)
                    <p>
                        <b>到账时间: </b>{{$item->arrival_at}}
                    </p>
                @endif

            </div>
        </div>

        <div class='panel-heading'>
            提现申请订单信息 共计 <span style="color:red; ">{{$order_total}}</span> 个订单 ,
            佣金总计 <span style="color:red; ">{{$item['amounts']}}</span> 元
        </div>

        <div class='panel-body'>
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th>订单号</th>
                    <th>总金额</th>
                    <th>商品金额</th>
                    <th>运费</th>
                    <th>付款方式</th>
                    <th>下单时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($item['type_data']['orders'] as $row)
                    <tr style="background: #eee">
                        <td>{{$row['order_sn']}}</td>
                        <td>{{$row['order_goods_price']}}</td>
                        <td>{{$row['price']}}</td>
                        <td>运费：{{$row['dispatch_price']}}</td>
                        <td>
                            <span class="label label-danger">余额支付</span>
                            {{--<span class="label label-default">后台付款</span>--}}
                            {{--<span class="label label-success">在线支付</span>--}}
                            {{--<span class="label label-danger">支付宝支付</span>--}}
                            {{--<span class="label label-primary">银联支付</span>--}}
                            {{--<span class="label label-primary">货到付款</span>--}}
                        </td>

                        <td>{{$row['created_at']}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <table width="100%">
                                <thead class="navbar-inner">
                                <tr>
                                    <th style='width:60px;'>商品</th>
                                    <th></th>
                                    <th>单价</th>
                                    <th>数量</th>
                                    <th>总价</th>
                                    <th>佣金</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($row['has_many_order_goods'] as $g)
                                    <tr>
                                        <td style='height:60px;'>
                                            <img src="{!! tomedia($g['thumb']) !!}"
                                                 style="width: 50px; height: 50px;border:1px solid #ccc;padding:1px;">
                                        </td>
                                        <td><span>{$g['title']}</span><br/><span>{$g['optionname']}</span>
                                        </td>
                                        {if p('hotel') && $row['order_type']=='3'}
                                        <td>
                                            房价: {php echo $g['price']/$g['total']}<br/>
                                            折扣后 ：{php echo $g['realprice']/$g['total']}<br/>
                                            押金:{php echo $row['depositprice']}
                                        </td>
                                        <td>{$g['total']}</td>
                                        <td>
                                            <strong>
                                                进店日期:{php echo date('Y-m-d',$row['btime'])}<br/>
                                                离店日期:{php echo date('Y-m-d',$row['etime'])}</strong>
                                        </td>
                                        <td>
                                            {else}
                                        <td>:原价: {php echo $g['price']/$g['total']}<br/>折扣后:{php echo
                                            $g['realprice']/$g['total']}
                                        </td>
                                        <td>{$g['total']}</td>
                                        <td><strong>原价:{php echo round($g['price'],2)}<br/>折扣后:{php echo
                                                round($g['realprice'],2)}</strong></td>
                                        <td>
                                            {/if}
                                            {if $this->set['level']>=1 && $row['level']==1}<p>
                                            <div class='input-group'>
                                                <span class='input-group-addon'>一级佣金</span>
                                                <span class='input-group-addon' style='background:#fff;width:80px;'>{$g['commission1']}</span>
                                                <span class='input-group-addon'>状态</span>
        <span class='input-group-addon' style='background:#fff'>
        {if $g['status1']==-1}
        <span class='label label-default'>未通过</span>
        {elseif $g['status1']==1}
        {if $apply['credit20'] <= 0}
        <label class='radio-inline'><input type='radio' class='status1' value='-1' name="status1[{$g['id']}]"/>
            不通过</label>
        {/if}
        <label class='radio-inline'><input type='radio' value='2' name="status1[{$g['id']}]" {if $apply['credit20'] >
            0}checked="checked"{/if} /> 通过</label>

        {elseif $g['status1']==2}
        <span class='label label-success'>通过</span>
        {elseif $g['status1']==3}
        <span class='label label-warning'>已打款</span>
        {/if}
        </span>
                                                <span class='input-group-addon'>备注</span>
                                                <input type='text' class='form-control' name='content1[{$g[' id']}]'
                                                style='width:200px;' value="{$g['content1']}">

                                            </div>
                                            </p>
                                            {/if}

                                            {if $this->set['level']>=2 && $row['level']==2}<p>

                                            <div class='input-group'>
                                                <span class='input-group-addon'>二级佣金</span>
                                                <span class='input-group-addon' style='background:#fff;width:80px;'>{$g['commission2']}</span>
                                                <span class='input-group-addon'>状态</span>
        <span class='input-group-addon' style='background:#fff'>
        {if $g['status2']==-1}
        <span class='label label-default'>未通过</span>
        {elseif $g['status2']==1}
        {if $apply['credit20'] <= 0}
        <label class='radio-inline'><input type='radio' class='status2' value='-1' name="status2[{$g['id']}]"/>
            不通过</label>
        {/if}
        <label class='radio-inline'><input type='radio' value='2' name="status2[{$g['id']}]" {if $apply['credit20'] >
            0}checked="checked"{/if} /> 通过</label>

        {elseif $g['status2']==2}
        <span class='label label-success'>通过</span>
        {elseif $g['status2']==3}
        <span class='label label-warning'>已打款</span>
        {/if}
        </span>
                                                <span class='input-group-addon'>备注</span>
                                                <input type='text' class='form-control' name='content2[{$g[' id']}]'
                                                style='width:200px;' value="{$g['content2']}">
                                            </div>
                                            </p>
                                            {/if}
                                            {if $this->set['level']>=2 && $row['level']==3}<p>

                                            <div class='input-group'>
                                                <span class='input-group-addon'>三级佣金</span>
                                                <span class='input-group-addon' style='background:#fff;width:80px;'>{$g['commission3']}</span>
                                                <span class='input-group-addon'>状态</span>
        <span class='input-group-addon' style='background:#fff'>
        {if $g['status3']==-1}
        <span class='label label-default'>未通过</span>
        {elseif $g['status3']==1}
        {if $apply['credit20'] <= 0}
        <label class='radio-inline'><input type='radio' class='status3' value='-1' name="status3[{$g['id']}]"/>
            不通过</label>
        {/if}
        <label class='radio-inline'><input type='radio' value='2' name="status3[{$g['id']}]" {if $apply['credit20'] >
            0}checked="checked"{/if} /> 通过</label>

        {elseif $g['status3']==2}
        <span class='label label-success'>通过</span>
        {elseif $g['status3']==3}
        <span class='label label-warning'>已打款</span>
        {/if}
        </span>
                                                <span class='input-group-addon'>备注</span>
                                                <input type='text' class='form-control' name='content3[{$g[' id']}]'
                                                style='width:200px;' value="{$g['content3']}">
                                            </div>
                                            </p>
                                            {/if}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        {if $apply['status']==2}
        <div class='panel-heading'>
            打款信息
        </div>
        <div class='panel-body'>
            此次佣金总额: <span style='color:red'>{$totalcommission}</span> 元 {if $apply['credit20'] > 0}已消费<span
                    style='color:red'>{$apply['credit20']}</span> 元{/if} 应该打款：<span style='color:red'>{php echo $totalpay-$apply['credit20']}</span>
            元
        </div>
        {/if}

        {if $apply['status']==3}
        <div class='panel-heading'>
            打款信息
            {if $apply['reason']!=''}
            <span style="color:red"> 失败原因：{$apply['reason']}</span>
            {/if}
        </div>
        <div class='panel-body'>
            此次佣金总额: <span style='color:red'>{$totalcommission}</span> 元 {if $apply['credit20'] > 0}已消费<span
                    style='color:red'>{$apply['credit20']}</span> 元{/if} 实际打款：<span style='color:red'>{php echo $totalpay-$apply['credit20']}</span>
            元
        </div>
        {/if}

    </div>
@endsection