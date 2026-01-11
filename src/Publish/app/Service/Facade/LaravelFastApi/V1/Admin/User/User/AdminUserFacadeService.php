<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 14:58:02
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 22:44:46
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
//必用
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Service\Facade\Traits\V1\QueryService;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Events\Common\V1\User\User\CommonUserRegisterEvent;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserCollection;
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;
use App\Contract\LaravelFastApi\V1\Common\User\AddUserHandlerContract;

/**
 * @see \App\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserFacade
 */
class AdminUserFacadeService
{
    use QueryService;
    public function test()
    {
        echo "AdminUserFacadeService test";
    }

    protected static $sort = [
        '1' => ['created_time','asc'],
        '2' => ['created_time','desc'],
    ];

    protected static $searchItem = [
        'phone',
        'account_name',
        'nick_name',
        'real_name',
        'id_number'
    ];


    /**
     * 导出表格数据
     *
     * @param [type] $userList
     * @return void
     */
    protected function exportData($userList)
    {
        $cloumn = [['账号','手机号','昵称','姓名','身份证号','性别','生日','说明','注册时间']];

        $data = [];

        foreach ($userList as $key => $value) {
            $list = [];

            $list[] = isset($value->account_name) ?? $value->account_name;
            $list[] = isset($value->phone) ?? $value->phone;
            $list[] = isset($value->userInfo->nick_name) ?? $value->userInfo->nick_name ;
            $list[] = isset($value->userInfo->real_name) ?? $value->userInfo->real_name ;
            $list[] = isset($value->userInfo->id_number) ?? $value->userInfo->id_number ;

            if (isset($value->userInfo->sex)) {
                $list[] = $value->userInfo->sex == 1 ? '男' : '女';
            } else {
                $list[] = '未知';
            }

            $list[] = isset($value->userInfo->solar_birthday_at) ?? $value->userInfo->solar_birthday_at;
            $list[] = isset($value->userInfo->introduction) ?? $value->userInfo->introduction;
            $list[] = isset($value->created_at) ?? $value->created_at;

            $data[] =  $list;
        }

        $title = "用户表";

        ExcelFacade::exportExcelData($cloumn, $data, $title, 1);

        return $title;
    }

    /**
     * 查询用户
     *
     * @param [type] $validated
     * @return void
     */
    /**
     * 获取用户列表
     *
     * 根据验证参数查询用户列表，支持多种筛选条件和分页功能
     *
     * @param array $validated 验证参数数组，包含以下可选字段：
     *   - switch: 用户状态开关
     *   - real_auth_status: 实名认证状态
     *   - findSelectIndex: 搜索类型索引
     *   - find: 搜索关键词
     *   - timeRange: 时间范围数组
     *   - sortType: 排序类型
     *   - isExport: 是否导出标志
     *   - exportType: 导出类型(10:当前页,20:全部)
     *
     * @return mixed 返回用户列表集合，包含分页信息和导出链接
     *   成功时返回UserCollection对象，失败时返回错误码
     *
     * @throws \Exception 可能抛出数据库查询异常
     */
    public function getUser($validated)
    {
        $result = code(config('admin_code.GetUserError'));

        $this->setQueryOptions($validated);

        //先处理关联

        /*  $this->withWhere = ['userInfo','userAvatar'=>function($query){
               $query->orderBy('created_time','desc');
         },'userAvatar.albumPicture','role','userAddress','userAddress.country','userAddress.province','userAddress.region','userApplyRealAuth']; */

        $this->withWhere = ['userInfo','userAvatar' => function ($query) {
            $query->orderBy('created_time', 'desc');
        },'userAvatar.albumPicture','role'];

        $query = User::with($this->withWhere);

        // 判断用户是否禁用
        if (isset($validated['switch'])) {
            $this->where[] = ['switch','=',$validated['switch']];

            $query->where($this->where);
        }

        // 判断实名认证状态
        if (isset($validated['real_auth_status']) && $validated['real_auth_status'] > 0) {
            $withWhere[] = ['real_auth_status','=',$validated['real_auth_status']];

            $query->where($this->where);
        }


        // 搜索用户
        if (isset($validated['findSelectIndex'])) {
            // 搜索用户的手机号和账户名
            if ($validated['findSelectIndex'] < 2) {
                if (isset($validated['find']) && !empty($validated['find'])) {
                    $this->where[] = [self::$searchItem[$validated['findSelectIndex']],'=',$validated['find']];

                    $this->orwhere[] = [self::$searchItem[$validated['findSelectIndex']],'like',"%{$validated['find']}%"];

                    $query->where($this->where)->orWhere($this->orwhere);
                }
            } else {
                // 搜索用户的 姓名 真实姓名 以及身份证号
                if ($validated['findSelectIndex'] == 4) {
                    $validated['find'] = strrev(ucfirst(strrev(trim($validated['find']))));
                }
                if (isset($validated['find']) && !empty($validated['find'])) {
                    $withWhere[] = [self::$searchItem[$validated['findSelectIndex']],'=',$validated['find']];

                    $orWithWhere[] = [self::$searchItem[$validated['findSelectIndex']],'like',"%{$validated['find']}%"];

                    $query->whereHas('userInfo', function (Builder $withQuery) use ($withWhere, $orWithWhere) {
                        $withQuery->where($withWhere)->orWhere($orWithWhere);
                    });
                }
            }
        }

        if (isset($validated['timeRange']) && \count($validated['timeRange']) > 0) {
            $this->whereBetween[] = [\strtotime($validated['timeRange'][0])];
            $this->whereBetween[] = [\strtotime($validated['timeRange'][1])];

            $query->whereBetween('created_time', $this->whereBetween);
        }

        if (isset($validated['sortType'])) {
            $sortType = $validated['sortType'];

            $query->orderBy(self::$sort[$sortType][0], self::$sort[$sortType][1]);
        }

        $download = null;

        //判断是否需要导出数据
        if (isset($validated['isExport']) && $validated['isExport'] == 1) {
            if (isset($validated['exportType'])) {
                $userList = null;

                $title = '';
                //本页数据
                if ($validated['exportType'] == 10) {
                    $userList = $query->offset(($this->page - 1) * $this->perPage)->limit($this->perPage)->get();

                    $title = $this->exportData($userList);
                }

                if ($validated['exportType'] == 20) {
                    $userList = $query->get();

                    $title = $this->exportData($userList);
                }

                $exists = Storage::disk('public')->exists("excel/{$title}.xlsx");

                if ($exists) {
                    $download = asset("storage/excel/{$title}.xlsx");
                }
            }
        }

        //统计实名认证待审核数量

        $userApplayRealAuthNumber = 0;

        $where = [];

        $where[] = ['real_auth_status','=',20];

        $userApplayRealAuthNumber = User::where($where)->count();

        $userList = $query->paginate($this->perPage, $this->columns, $this->pageName, $this->page);

        if (\optional($userList)) {
            $result = new UserCollection($userList, ['code' => 0,'msg' => '获取用户列表成功!'], $download, $userApplayRealAuthNumber);
        }

        return  $result;
    }

    /**
     * 添加用户
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function addUser($validated, $admin)
    {
        $result = code(config('admin_code.AddUserError'));

        ['source_user_id' => $source_user_id,'phone' => $phone,'password' => $password,'nick_name' => $nick_name,'sex' => $sex] = $validated;

        DB::beginTransaction();

        $user = new User();

        $user->phone = $phone;

        $user->password = Hash::make($password);

        //用户级别 默认最低
        $user->level_id = 1;
        //用户默认未认证
        $user->real_auth_status = 10;
        //用户默认是可用的
        $user->switch = 1;
        //创建张账户
        $user->account_name = \bin2hex(\random_bytes(4));
        //创建认证token
        $user->auth_token = Str::random(20);

        //绑定用户推荐id
        $user->source_user_id = $source_user_id;

        $user->userId = Str::uuid()->toString();

        //如果有上级就查找
        if ($source_user_id) {
            //查找父级用户
            $sourceUser = User::find($source_user_id);

            if (!$sourceUser) {
                throw new CommonException('ThisParentDataNotExistsError');
            }

            $user->source_userId = $sourceUser->userId;
        }

        $user->created_at = time();
        $user->created_time = time();

        //保存用户
        $userResult = $user->save();

        // 邀请码
        $user_id = $user->id;

        if (mb_strlen($user_id) < 4) {
            $user->invite_code = str_pad($user_id, 4, '0', STR_PAD_LEFT);
        } else {
            $user->invite_code = $user_id;
        }

        $userResult = $user->save();

        if (!$userResult) {
            throw new CommonException('AddUserError');
        }

        //分别传递用户,管理员,数据,是否开启事务,是否开启推荐分销
        CommonUserRegisterEvent::dispatch(['user' => $user,'admin' => $admin,'validated' => $validated,'isTransation' => 1,'isUserSource' => 1]);

        //契约业务
        $handleParam = ['user' => $user,'admin' => $admin,'validated' => $validated];

        app(AddUserHandlerContract::class)->handle($handleParam);

        $eventResult = CommonEvent::dispatch($admin, $validated, 'AddUser');

        DB::commit();

        $result = code(['code' => 0,'msg' => '添加用户成功!']);
        return $result;
    }


    /**
     * 禁用用户
     *
     * @param [type] $id
     * @param [type] $admin
     * @return void
     */
    public function disableUser($validated, $admin)
    {
        $result = code(config('admin_code.DisableUserError'));

        $checkResult = $this->checkIsSystemUserByUserId($validated['id']);

        if ($checkResult) {
            throw new CommonException('DisableSystemUserError');
        }

        $user = User::find($validated['id']);

        $revision = $user->revision;

        $updateData = ['switch' => $validated['switch'],'updated_time' => time(),'updated_at' => \date('Y-m-d H:i:s', time()),'revision' => $revision + 1];

        $userResult = User::where('id', $validated['id'])->where('revision', $revision)->update($updateData);

        if (!$userResult) {
            throw new CommonException('DisableUserError');
        }

        CommonEvent::dispatch($admin, $validated, 'DisableUser');

        $result = code(['code' => 0,'msg' => '禁用用户成功!']);

        return $result;
    }



    /**
     * 批量禁用用户
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function multipleDisableUser($validated, $admin)
    {
        $result = code(config('admin_code.MultipleDisableUserError'));

        if (isset($validated['selectId']) && count($validated['selectId'])) {
            $checkResult = $this->checkIsSystemUserByUserIdArray($validated['selectId']);

            if ($checkResult) {
                throw new CommonException('MultipleDisableSystemUserError');
            }

            $updateData = ['switch' => $validated['switch'],'updated_time' => time(),'updated_at' => \date('Y-m-d H:i:s', time())];

            $disableResult = User::whereIn('id', $validated['selectId'])->update($updateData);

            if (!$disableResult) {
                throw new CommonException('MultipleDisableUserError');
            }

            CommonEvent::dispatch($admin, $validated, 'MultipleDisableUser');

            $result = code(['code' => 0,'msg' => '批量禁用用户成功!']);
        }

        return $result;
    }

    /**
     * 删除用户
     *
     * @param [type] $id
     * @param [type] $admin
     * @return void
     */
    public function deleteUser($validated, $admin)
    {
        $result = code(config('admin_code.DeleteUserError'));

        $checkResult = $this->checkIsSystemUserByUserId($validated['id']);

        if ($checkResult) {
            throw new CommonException('DeleteSystemUserError');
        }

        $user = User::find($validated['id']);

        $user->deleted_at = date('Y-m-d H:i:s', time());

        $userResult = $user->save();

        if (!$userResult) {
            throw new CommonException('DeleteUserError');
        }

        CommonEvent::dispatch($admin, $validated, 'DeleteUser');

        $result = code(['code' => 0,'msg' => '删除用户成功!']);

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function multipleDeleteUser($validated, $admin)
    {
        $result = code(config('admin_code.MultipleDeleteUserError'));

        if (isset($validated['selectId']) && count($validated['selectId'])) {
            $checkResult = $this->checkIsSystemUserByUserIdArray($validated['selectId']);

            if ($checkResult) {
                throw new CommonException('MultipleDeleteSystemUserError');
            }

            $deleteResult = User::whereIn('id', $validated['selectId'])->delete();

            if (!$deleteResult) {
                throw new CommonException('MultipleDeleteUserError');
            }

            CommonEvent::dispatch($admin, $validated, 'MultipleDeleteUser');

            $result = code(['code' => 0,'msg' => '批量删除用户成功!']);
        }

        return $result;
    }

    /**
     * 通过用户id检测是否是系统用户
     *
     * @param  [type] $user_id
     */
    protected function checkIsSystemUserByUserId($user_id = 0)
    {
        $checkResult = 0;

        if ($user_id == 1 || $user_id == 2 || $user_id == 3 || $user_id == 4) {
            $checkResult = 1;
        }

        return $checkResult;
    }

    /**
     * 通过用户id数组检测是否含有系统和用户
     *
     * @param  array $user_id_array
     */
    protected function checkIsSystemUserByUserIdArray($user_id_array = [])
    {
        $checkResult = 0;

        if (in_array(1, $user_id_array) || in_array(2, $user_id_array) || in_array(3, $user_id_array) || in_array(4, $user_id_array)) {
            $checkResult = 1;
        }

        return $checkResult;
    }
}
