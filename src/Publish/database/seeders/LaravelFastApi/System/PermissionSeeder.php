<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-22 14:35:41
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-19 14:35:04
 * @Fileroute_path: \database\seeders\LaravelFastApi\System\PermissionSeeder.php
 */


//后台带单管理
namespace Database\Seeders\LaravelFastApi\System;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->runOneDeepData();
        $this->runTwoDeepData();
        $this->runThreeDeepData();

    }

	//一级类目
	protected function runOneDeepData()
	{
		 //一级类目
        $oneDeepData = [
            //|--10
            ['created_time'=>time(),'id'=>10,'parent_id'=>0,'deep'=>1,'sort'=>100,'type'=>20,'switch'=>1,'route_path'=>'/config','component'=>'Layout','route_name'=>'config','permission_tag'=>'','meta_title'=>'系统设置','meta_icon'=>'el-icon-Setting','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
            //|--20
            ['created_time'=>time(),'id'=>20,'parent_id'=>0,'deep'=>1,'sort'=>100,'type'=>20,'switch'=>1,'route_path'=>'/businesses','component'=>'Layout','route_name'=>'businesses','permission_tag'=>'','meta_title'=>'业务设置','meta_icon'=>'client','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
            //|--30
            ['created_time'=>time(),'id'=>30,'parent_id'=>0,'deep'=>1,'sort'=>100,'type'=>20,'switch'=>1,'route_path'=>'/users','component'=>'Layout','route_name'=>'users','permission_tag'=>'','meta_title'=>'用户管理','meta_icon'=>'el-icon-UserFilled','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
            //|--40
            ['created_time'=>time(),'id'=>40,'parent_id'=>0,'deep'=>1,'sort'=>100,'type'=>20,'switch'=>1,'route_path'=>'/article','component'=>'Layout','route_name'=>'article','permission_tag'=>'','meta_title'=>'文章管理','meta_icon'=>'document','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
            //|--50
            ['created_time'=>time(),'id'=>50,'parent_id'=>0,'deep'=>1,'sort'=>100,'type'=>20,'switch'=>1,'route_path'=>'/picture','component'=>'Layout','route_name'=>'picture','permission_tag'=>'','meta_title'=>'图片空间','meta_icon'=>'el-icon-picture','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
            //|--60
            ['created_time'=>time(),'id'=>60,'parent_id'=>0,'deep'=>1,'sort'=>100,'type'=>20,'switch'=>1,'route_path'=>'/log','component'=>'Layout','route_name'=>'log','permission_tag'=>'','meta_title'=>'日志管理','meta_icon'=>'el-icon-Edit','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
        ];

		DB::table('permission')->insert($oneDeepData);
	}

	//二级类目
	protected function runTwoDeepData()
	{
		 //二级类目
        $twoDeepData = [

            //|--10 系统设置
			 	//|--|--700
                ['created_time'=>time(),'id'=>700,'parent_id'=>10,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'permission','component'=>'system/permission/index','route_name'=>'Permission','permission_tag'=>'','meta_title'=>'菜单管理','meta_icon'=>'menu','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--800
                ['created_time'=>time(),'id'=>800,'parent_id'=>10,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'platformConfig','component'=>'platformConfig','route_name'=>'platformConfig','permission_tag'=>'','meta_title'=>'平台配置','meta_icon'=>'monitor','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--900
                ['created_time'=>time(),'id'=>900,'parent_id'=>10,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'system','component'=>'system','route_name'=>'system','permission_tag'=>'','meta_title'=>'系统配置','meta_icon'=>'system','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--1000
                ['created_time'=>time(),'id'=>1000,'parent_id'=>10,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'role','component'=>'system/role/index','route_name'=>'Role','permission_tag'=>'','meta_title'=>'角色管理','meta_icon'=>'role','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
				//|--|--1100
                ['created_time'=>time(),'id'=>1100,'parent_id'=>10,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'region','component'=>'system/region/index','route_name'=>'region','permission_tag'=>'','meta_title'=>'地区管理','meta_icon'=>'el-icon-location','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
				//|--|--1200
                ['created_time'=>time(),'id'=>1200,'parent_id'=>10,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'bank','component'=>'system/bank/index','route_name'=>'bank','permission_tag'=>'','meta_title'=>'银行管理','meta_icon'=>'el-icon-Postcard','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],

            //--20 业务设置
                //|--|--1300
                ['created_time'=>time(),'id'=>1300,'parent_id'=>20,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'common','component'=>'common','route_name'=>'common','permission_tag'=>'','meta_title'=>'通用设置','meta_icon'=>'el-icon-setting','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
				 //|--|--1400
                ['created_time'=>time(),'id'=>1400,'parent_id'=>20,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'group','component'=>'group','route_name'=>'group','permission_tag'=>'','meta_title'=>'分类管理','meta_icon'=>'tree','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--1500
                ['created_time'=>time(),'id'=>1500,'parent_id'=>20,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'level','component'=>'level','route_name'=>'level','permission_tag'=>'','meta_title'=>'级别管理','meta_icon'=>'el-icon-Flag','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
				//|--|--1600
                ['created_time'=>time(),'id'=>1600,'parent_id'=>20,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'servicePlatform','component'=>'servicePlatform','route_name'=>'servicePlatform','permission_tag'=>'','meta_title'=>'业务平台','meta_icon'=>'el-icon-Monitor','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],

            //|--30 用户管理
                //|--|--1700
                ['created_time'=>time(),'id'=>1700,'parent_id'=>30,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'admin','component'=>'user/admin/adminList/index','route_name'=>'userAdmin','permission_tag'=>'','meta_title'=>'管理员管理','meta_icon'=>'el-icon-Avatar','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--1800
                ['created_time'=>time(),'id'=>1800,'parent_id'=>30,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'user','component'=>'user','route_name'=>'user','permission_tag'=>'','meta_title'=>'用户管理','meta_icon'=>'el-icon-user','meta_affix'=>0,'always_show'=>1,'hidden'=>0,'meta_no_cache'=>0],
				
			//|--50 图片空间
                //|--|--1900
                ['created_time'=>time(),'id'=>1900,'parent_id'=>50,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'album','component'=>'picture/album/albumList/index','route_name'=>'album','permission_tag'=>'','meta_title'=>'我的相册','meta_icon'=>'el-icon-collection','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],	
           
            //|--40 文章管理
                //|--|--2000
                ['created_time'=>time(),'id'=>2000,'parent_id'=>40,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'notice','component'=>'article/notice/index','route_name'=>'notice','permission_tag'=>'','meta_title'=>'公告管理','meta_icon'=>'el-icon-postcard','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--2100
                ['created_time'=>time(),'id'=>2100,'parent_id'=>40,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'systemArticle','component'=>'article/system/index','route_name'=>'systemArticle','permission_tag'=>'','meta_title'=>'系统文章','meta_icon'=>'el-icon-document-copy','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
           
            //|--60 日志管理
                //|--|--2200
                ['created_time'=>time(),'id'=>2200,'parent_id'=>60,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'loginLog','component'=>'loginLog','route_name'=>'LoginLog','permission_tag'=>'','meta_title'=>'登录日志','meta_icon'=>'el-icon-ElementPlus','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--2300
                ['created_time'=>time(),'id'=>2300,'parent_id'=>60,'deep'=>2,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'eventLog','component'=>'eventLog','route_name'=>'eventLog','permission_tag'=>'','meta_title'=>'事件日志','meta_icon'=>'el-icon-view','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
        ];

		DB::table('permission')->insert($twoDeepData);
	}

	//三级类目
	protected function runThreeDeepData()
	{
		//三级类目
        $threeDeepData = [
			//|--10 系统设置
			//|--|--800 平台配置
                //|--|--|--24000
                ['created_time'=>time(),'id'=>24000,'parent_id'=>800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'cacheConfig','component'=>'system/platform/cacheConfig/index','route_name'=>'cacheConfig','permission_tag'=>'','meta_title'=>'缓存设置','meta_icon'=>'el-icon-Box','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],

			//|--|--900 系统配置
                //|--|--|--25000
                ['created_time'=>time(),'id'=>25000,'parent_id'=>900,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'systemConfig','component'=>'system/system/systemConfig/index','route_name'=>'systemConfig','permission_tag'=>'','meta_title'=>'配置参数','meta_icon'=>'cascader','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--26000
                ['created_time'=>time(),'id'=>26000,'parent_id'=>900,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'voiceConfig','component'=>'system/system/voiceConfig/index','route_name'=>'voiceConfig','permission_tag'=>'','meta_title'=>'提示配置','meta_icon'=>'el-icon-bell','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
				//|--|--|--27000
                ['created_time'=>time(),'id'=>27000,'parent_id'=>900,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'otherPaltform','component'=>'system/system/otherPlatform/index','route_name'=>'otherPaltform','permission_tag'=>'','meta_title'=>'三方平台','meta_icon'=>'api','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
				//|--|--|--28000
                ['created_time'=>time(),'id'=>28000,'parent_id'=>900,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'wechatPlatform','component'=>'system/system/otherPlatform/wechat/index','route_name'=>'wechatPlatform','permission_tag'=>'','meta_title'=>'微信平台','meta_icon'=>'wechat','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>0],
				//|--|--|--29000
                ['created_time'=>time(),'id'=>29000,'parent_id'=>900,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'douyinPlatform','component'=>'system/system/otherPlatform/douyin/index','route_name'=>'douyinPlatform','permission_tag'=>'','meta_title'=>'抖音平台','meta_icon'=>'el-icon-Headset','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>0],
			
			//|--|--1300 通用设置
                //|--|--|--30000
                ['created_time'=>time(),'id'=>30000,'parent_id'=>1300,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'gather','component'=>'business/common/collection/index','route_name'=>'gather','permission_tag'=>'','meta_title'=>'集合设置','meta_icon'=>'el-icon-BrushFilled','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
           
            //|--|--1400 分类管理
                //|--|--|--31000
                ['created_time'=>time(),'id'=>31000,'parent_id'=>1400,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'goodsClass','component'=>'business/group/goodsClass/index','route_name'=>'goodsClass','permission_tag'=>'','meta_title'=>'产品分类','meta_icon'=>'el-icon-present','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--32000
                ['created_time'=>time(),'id'=>32000,'parent_id'=>1400,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'category','component'=>'business/group/category/index','route_name'=>'category','permission_tag'=>'','meta_title'=>'文章分类','meta_icon'=>'dict','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--33000
                ['created_time'=>time(),'id'=>33000,'parent_id'=>1400,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'label','component'=>'business/group/label/index','route_name'=>'label','permission_tag'=>'','meta_title'=>'标签管理','meta_icon'=>'el-icon-CollectionTag','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
				
            //|--|--1500 级别管理
                //|--|--|--34000                      
                ['created_time'=>time(),'id'=>34000,'parent_id'=>1500,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'levelItem','component'=>'business/level/levelItem/index','route_name'=>'levelItem','permission_tag'=>'','meta_title'=>'级别条件','meta_icon'=>'size','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--35000
                ['created_time'=>time(),'id'=>35000,'parent_id'=>1500,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userLevel','component'=>'business/level/userLevel/index','route_name'=>'userLevel','permission_tag'=>'','meta_title'=>'用户级别','meta_icon'=>'el-icon-UserFilled','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],

        	//|--|--1600 业务平台
				//|--|--|--36000
                ['created_time'=>time(),'id'=>36000,'parent_id'=>1600,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'phoneIndexBanner','component'=>'business/platform/banner/index','route_name'=>'phoneIndexBanner','permission_tag'=>'','meta_title'=>'首页轮播','meta_icon'=>'el-icon-Star','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],

            //|--|--1800 用户管理
                //|--|--|--37000
    			['created_time'=>time(),'id'=>37000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userList','component'=>'user/user/userList/index','route_name'=>'userList','permission_tag'=>'','meta_title'=>'用户列表','meta_icon'=>'table','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--38000
				['created_time'=>time(),'id'=>38000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userCheck','component'=>'user/user/userAuth/index','route_name'=>'userCheck','permission_tag'=>'','meta_title'=>'等待认证','meta_icon'=>'captcha','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--39000
				['created_time'=>time(),'id'=>39000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'editUser','component'=>'user/user/editUser/index','route_name'=>'editUser','permission_tag'=>'','meta_title'=>'编辑用户','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],
				//|--|--|--40000
				['created_time'=>time(),'id'=>40000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userAddress','component'=>'user/user/userAddress/index','route_name'=>'userAddress','permission_tag'=>'','meta_title'=>'用户地址','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],
				//|--|--|--41000
				['created_time'=>time(),'id'=>41000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userBank','component'=>'user/user/userBank/index','route_name'=>'userBank','permission_tag'=>'','meta_title'=>'用户银行','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],
				//|--|--|--42000
				['created_time'=>time(),'id'=>42000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userIdCard','component'=>'user/user/userIdCard/index','route_name'=>'userIdCard','permission_tag'=>'','meta_title'=>'用户身份证','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],
				//|--|--|--43000
				['created_time'=>time(),'id'=>43000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userTeam','component'=>'user/user/userTeam/index','route_name'=>'userTeam','permission_tag'=>'','meta_title'=>'用户团队','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],
				//|--|--|--44000
				['created_time'=>time(),'id'=>44000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userAccountLog','component'=>'user/user/userAccountLog/index','route_name'=>'userAccountLog','permission_tag'=>'','meta_title'=>'账户日志','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],
				//|--|--|--45000
				['created_time'=>time(),'id'=>45000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userOrder','component'=>'user/user/userOrder/index','route_name'=>'userOrder','permission_tag'=>'','meta_title'=>'用户订单','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],
				//|--|--|--46000
				['created_time'=>time(),'id'=>46000,'parent_id'=>1800,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'userArticle','component'=>'user/user/userArticle/index','route_name'=>'userArticle','permission_tag'=>'','meta_title'=>'用户文章','meta_icon'=>'edit','meta_affix'=>0,'always_show'=>0,'hidden'=>1,'meta_no_cache'=>1],

			
            //|--|--2200 登录日志
                //|--|--|--91000
                ['created_time'=>time(),'id'=>91000,'parent_id'=>2200,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'phoneLogin','component'=>'log/login/phoneLogin/index','route_name'=>'phoneLogin','permission_tag'=>'','meta_title'=>'手机登录','meta_icon'=>'el-icon-phone','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--92000
                ['created_time'=>time(),'id'=>92000,'parent_id'=>2200,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'adminLogin','component'=>'log/login/adminLogin/index','route_name'=>'adminLogin','permission_tag'=>'','meta_title'=>'后台登录','meta_icon'=>'el-icon-Paperclip','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
            //|--|--2300 事件日志
                //|--|--|--93000
                ['created_time'=>time(),'id'=>93000,'parent_id'=>2300,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'phoneEvent','component'=>'log/event/phoneEvent/index','route_name'=>'phoneEvent','permission_tag'=>'','meta_title'=>'手机事件','meta_icon'=>'el-icon-phone','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
                //|--|--|--94000
                ['created_time'=>time(),'id'=>94000,'parent_id'=>2300,'deep'=>3,'sort'=>100,'type'=>10,'switch'=>1,'route_path'=>'adminEvent','component'=>'log/event/adminEvent/index','route_name'=>'adminEvent','permission_tag'=>'','meta_title'=>'后台事件','meta_icon'=>'el-icon-Paperclip','meta_affix'=>0,'always_show'=>0,'hidden'=>0,'meta_no_cache'=>0],
        ];

		DB::table('permission')->insert($threeDeepData);
	}
}
