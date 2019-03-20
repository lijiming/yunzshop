<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/10
 * Time: 下午12:37
 */

namespace app\platform\modules\user\controllers;


use app\common\events\UserActionEvent;
use app\platform\controllers\BaseController;
use app\platform\modules\user\models\AdminUser as User;
use app\platform\modules\user\models\AdminUser;
use app\platform\modules\user\models\Role;
use app\platform\modules\user\requests\AdminUserCreateRequest;
use app\platform\modules\user\requests\AdminUserUpdateRequest;
use app\platform\modules\user\models\YzUserProfile;
use app\platform\modules\application\models\UniacidApp;
use app\platform\modules\application\models\AppUser;

class AdminUserController extends BaseController
{
    protected $fields = [
        'name' => '',
        'phone' => '',
        'roles' => [],
    ];

    /**
     * Display a listing of the resource.(显示用户列表.)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $parames = request();
        $users = User::getList($parames);

        if (!$users->isEmpty()) {
            return $this->successJson('成功', $users);
        } else {
            return $this->check(6);
        }
    }

    /**
     * Show the form for creating a new resource And Store a newly created resource in storage.(添加用户)
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \app\common\exceptions\AppException
     */
    public function create()
    {
        $user = request()->user;
        if ($user) {
            $validate = $this->validate($this->rules(), $user, $this->message());
            if ($validate) {
                return $validate;
            }
            return $this->check(User::saveData($user, $user_model = ''));
        }
    }

    /**
     * Show the form for editing the specified resource And Update the specified resource in storage.(修改用户)
     *
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \app\common\exceptions\AppException
     */
    public function edit()
    {
        $uid = request()->uid;
        if (!$uid) {
            return $this->check(5);
        }
        $user = AdminUser::with('hasOneProfile')->find($uid);
        if (!$user) {
            return $this->check(6);
        }
        $data = request()->user;

        if($data) {
            $validate  = $this->validate($this->rules($uid, $user['hasOneProfile']['id']), $data, $this->message());
            if ($validate) {
                return $validate;
            }
            return $this->check(AdminUser::saveData($data, $user));
        }

        if ($user) {
            return $this->successJson('成功', $user);
        } else {
            return $this->check(0);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uid)
    {
        $tag = User::find((int)$uid);
        foreach ($tag->roles as $v) {
            $tag->roles()->detach($v);
        }
        if ($tag && $tag->$uid != 1) {
            $tag->delete();
        } else {
            return redirect()->back()
                ->withErrors("删除失败");
        }

        return redirect()->back()
            ->withSuccess("删除成功");
    }

    public function validate(array $rules, \Request $request = null, array $messages = [], array $customAttributes = [])
    {
        if (!isset($request)) {
            $request = request();
        }
        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            return $this->errorJson('失败', $validator->errors()->all());
        }
    }

    public function rules($u_id, $p_id)
    {
        $rules = [];
        if (request()->path() == "admin/user/create") {
            $rules = [
                'username' => 'required|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\-]{3,30}$/u|unique:yz_admin_users',
                'mobile' => 'required|regex:/^1[34578]\d{9}$/|unique:yz_users_profile',
            ];
        }

        if (request()->path() == "admin/user/edit") {
            $rules = [
                'username' => 'required|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\-]{3,30}$/u|unique:yz_admin_users,username,'.$u_id.',uid',
                'mobile' => 'required|regex:/^1[34578]\d{9}$/|unique:yz_users_profile,mobile,'.$p_id,
            ];
        }

        if (request()->path() != "admin/user/edit") {
            $rules['password'] = 'required';
            $rules['re_password'] = 'same:password';
        }
        return $rules;
    }

    public function message()
    {
        return [
            'username.required' => '用户名不能为空',
            'username.regex' => '用户名格式不正确',
            'username.unique' => '用户名已存在',
            'mobile.required' => '手机号已存在',
            'mobile.regex' => '手机号格式不正确',
            'mobile.unique' => '手机号已存在',
            'password.required' => '密码不能为空',
            're_password.same' => '两次密码不一致',
        ];
    }

    /**
     * 修改状态
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        $uid = request()->uid;
        $status = request()->status;
        if (!$uid || !$status) {
            return $this->check(5);
        }
        $result = AdminUser::where('uid', $uid)->update(['status'=>$status]);
        if ($result) {
            return $this->check(1);
        } else {
            return $this->check(0);
        }
    }

    /**
     * 修改密码
     *
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \app\common\exceptions\AppException
     */
    public function change()
    {
        $uid = request()->uid;
        $data = request()->user;
        if (!$uid || !$data) {
            return $this->check(5);
        }
        if ($data){
            $user = AdminUser::getData($uid);
            if (!$user) {
                return $this->check(6);
            }
            $validate  = $this->validate($this->rules(), $data, $this->message());
            if ($validate) {
                return $validate;
            }
            return $this->check(AdminUser::saveData($data, $user));
        }
    }

    /**
     * 返回 json 信息
     *
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function check($user)
    {
        switch ($user) {
            case 1:
                return $this->successJson('成功', '');
                break;
            case 2:
                return $this->errorJson('原密码错误', '');
                break;
            case 3:
                return $this->errorJson('新密码与原密码一致', '');
                break;
            case 4:
                return $this->errorJson('存储相关信息表失败', '');
                break;
            case 5:
                return $this->errorJson('参数错误', '');
                break;
            case 6:
                return $this->errorJson('未获取到数据', '');
                break;
            default:
                return $this->errorJson('失败', '');
        }
    }

    /**
     * 单个用户平台列表
     */
    public function applicationList()
    {
        $uid = request()->uid;
        $page = request()->page;
        // 如果page小于且等于1 就等于0 (因为offset是从0开始取数据)
        if ($page<=1) {
            $page = 0;
            $offset = ($page)*15;
        } else {
            $offset = ($page-1)*15;
        }
        $lastpage = AppUser::where('uid', $uid)->paginate()->lastpage();
        $user = AdminUser::with(['hasManyAppUser' => function ($query) use ($offset) {
            $query->with('hasOneApp');
            $query->offset($offset)->limit('15');
        }])->where('uid', $uid)->first();

        $user['lastpage'] = $lastpage;

        if (!$user) {
            return $this->errorJson('未获取到该用户', '');
        }
        if ($user->hasManyAppUser->isEmpty()) {
            return $this->errorJson('该用户暂时没有平台', '');
        }

        return $this->successJson('成功', $user);
    }
    /**
     * 店员列表
     */
    public function clerkList()
    {
        $parames = request();
        $user = AdminUser::searchUsers($parames)->with(['hasOneProfile'])->where('type', 3)->paginate();
        foreach ($user as &$item) {
            if ($item['status'] == 2) {
                $item['state'] = '有效';
            } elseif ($item['status'] == 3) {
                $item['state'] = '已禁用';
            }
            $item['create_at'] = $item['created_at']->format('Y年m月d日');
            $item->hasOneAppUser['app_name'] = $item->hasOneAppUser->hasOneApp->name;
        }

        if ($user->isEmpty()) {
            return $this->errorJson('未获取到店员信息', '');
        }

        return $this->successJson('成功', $user);
    }
}

