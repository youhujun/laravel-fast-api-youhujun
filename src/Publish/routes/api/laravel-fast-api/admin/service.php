<?php

/**模板路由 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\Service';

//业务设置
Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
	Route::prefix('admin')->group(function()
	{
		// 后台管理-业务设置-分类管理
		Route::prefix('group')->namespace('Group')->group(function()
		{
			/**
			 * |--|--|--产品分类
			* bind
			* @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\GoodsClassController
			*/
			Route::prefix('goods-class')->controller(GoodsClassController::class)->group(function()
			{
				//添加产品分类
				Route::get('getTreeGoodsClass','getTreeGoodsClass');
				//更新产品分类
				Route::post('addGoodsClass','addGoodsClass');
				//移动产品分类
				Route::post('updateGoodsClass','updateGoodsClass');
				//更新产品分类
				Route::post('moveGoodsClass','moveGoodsClass');
				//删除产品分类
				Route::post('deleteGoodsClass','deleteGoodsClass');

				//查询单个产品分类信息
				Route::any('getSingleGoodsClass','getSingleGoodsClass');

			});

			/**
			 *|--|--|--文章分类
			* bind
			* @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\CategoryController
			*/
			Route::prefix('category')->controller(CategoryController::class)->group(function()
			{
				//获取树形文章分类
				Route::get('getTreeCategory','getTreeCategory');
				//添加文章分类
				Route::post('addCategory','addCategory');
				//更新文章分类
				Route::post('updateCategory','updateCategory');
				//移动树形结构
				Route::post('moveCategory','moveCategory');
				//更新文章分类
				Route::post('deleteCategory','deleteCategory');

			});

			/**
			 *|--|--|--标签管理
			* bind
			* @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\LabelController
			*/
			Route::prefix('label')->controller(LabelController::class)->group(function()
			{
				//获取树形标签分类
				Route::get('getTreeLabel','getTreeLabel');
				//添加标签分类
				Route::post('addLabel','addLabel');
				//更新标签分类
				Route::post('updateLabel','updateLabel');
				//移动树形结构
				Route::post('moveLabel','moveLabel');
				//更新标签分类
				Route::post('deleteLabel','deleteLabel');

			});
		});

		//后台管理-业务设置-级别管理
		Route::prefix('level')->namespace('Level')->group(function()
		{
			/**
			 *|--|--|--级别条件
			* bind
			* @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\level\LevelItemController
			*/
			Route::prefix('level-item')->controller(LevelItemController::class)->group(function()
			{
				//获取级别条件
				Route::post('getLevelItem','getLevelItem');
				//添加级别条件
				Route::post('addLevelItem','addLevelItem');
				//更新级别条件
				Route::post('updateLevelItem','updateLevelItem');
				//删除级别条件
				Route::post('deleteLevelItem','deleteLevelItem');
				//批量删除级别条件
				Route::post('multipleDeleteLevelItem','multipleDeleteLevelItem');

				//|--|--|--|--级别条件选项
				//获取默认级别条件
				Route::get('defaultLevelItem','defaultLevelItem');
				//查找级别条件
				Route::post('findLevelItem','findLevelItem');
			});

			/**
				 *|--|--|--用户级别
				* bind
				* @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\level\UserLevelController
				*/
			Route::prefix('user-level')->controller(UserLevelController::class)->group(function()
			{
				//|--|--|--用户级别选项
				//获取默认用户级别
				Route::get('defaultUserLevel','defaultUserLevel');
				//查找用户级别
				Route::post('findUserLevel','findUserLevel');
				//|--|--|--用户级别
				//获取用户级别
				Route::post('getUserLevel','getUserLevel');
				//添加用户级别
				Route::post('addUserLevel','addUserLevel');
				//更新用户级别
				Route::post('updateUserLevel','updateUserLevel');
				//删除用户级别
				Route::post('deleteUserLevel','deleteUserLevel');
				//批量删除用户级别
				Route::post('multipleDeleteUserLevel','multipleDeleteUserLevel');
				//|--|--|--用户级别配置项
				//添加用户级级别配置项
				Route::post('addUserLevelItemUnion','addUserLevelItemUnion');
				//修改用户级级别配置项
				Route::post('updateUserLevelItemUnion','updateUserLevelItemUnion');
				//删除用户级级别配置项
				Route::post('deleteUserLevelItemUnion','deleteUserLevelItemUnion');

			});
		});
		//后台管理-业务设置-平台配置
		Route::prefix('platform')->group(function()
		{
			Route::namespace('Platform')->group(function()
			{

				//后台管理-业务设置-平台配置-首页轮播
				Route::prefix('banner')->group(function()
				{
					/**
					 * @see  \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform\PhoneBannerController
					 */
					Route::controller(PhoneBannerController::class)->group(function()
					{
						//获取轮播图
						Route::post('getPhoneBanner','getPhoneBanner');
						//添加轮播图
						Route::post('addPhoneBanner','addPhoneBanner');
						//修改轮播图
						Route::post('updatePhoneBanner','updatePhoneBanner');
						//删除轮播图
						Route::post('deletePhoneBanner','deletePhoneBanner');
						// 批量删除轮播图
						Route::post('multipleDeletePhoneBanner','multipleDeletePhoneBanner');

					});

					//轮播图详情
					Route::prefix('info')->group(function()
					{
						Route::namespace('PhoneBanner')->group(function()
						{
							/**
							 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\PhoneBannerDetailsController
							 */
							Route::controller(PhoneBannerDetailsController::class)->group(function()
							{
								//修改轮播图图片
								Route::post('updatePhoneBannerPicture','updatePhoneBannerPicture');
								//修改轮播图跳转
								Route::post('updatePhoneBannerUrl','updatePhoneBannerUrl');
								//修改轮播图排序
								Route::post('updatePhoneBannerSort','updatePhoneBannerSort');
								//修改轮播图备注
								Route::post('updatePhoneBannerBakInfo','updatePhoneBannerBakInfo');
							});
						});
					});
					
					
				});
			});
			
		});
	});
});