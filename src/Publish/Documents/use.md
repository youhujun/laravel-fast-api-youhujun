# 开发规范

## 全局命名规范
PascalCase（大驼峰）：类名、控制器名、模型名、DTO 名，首字母全大写
camelCase（小驼峰）：变量、方法、普通属性，一眼就知道是内存里的业务对象
snake_case（下划线）：数据库字段、表名、ES 字段，和数据源强绑定
UPPER_SNAKE_CASE（全大写下划线）：常量、配置项

## 1. 命名规范

### 1.1 变量命名

- 模型对象命名：首字母小写最后跟随Object后缀,例如 `$userObject`,`$userInfoObject`
- 集合命名: 首字母小写最后跟随Collection后缀,例如 `$userCollection`,`$userInfoCollection`
- 枚举命名: 首字母小写最后跟随Enum后缀,例如 `$userEnum`,`$userInfoEnum`
- 常量命名: 全部大写,单词间用下划线分隔,例如 `USER_INFO_COLLECTION`,`USER_INFO_ENUM`
- 函数命名: 首字母小写后面大写,例如 `getUserInfo`,`getAdminRole`
- 类命名: 首字母大写,单词间用大写字母分隔,例如 `UserInfo`,`UserInfoCollection`
- 接口命名: 首字母大写,单词间用大写字母分隔,例如 `IUserInfo`,`IUserInfoCollection`
- 配置文件命名: 全部小写,单词间用下划线分隔,例如 `user_info.php`,`user_info_collection.php`
- 数据库字段命名: 全部小写,单词间用下划线分隔,例如 `user_uid`,`user_info_id`

### 1.2 函数命名

- 全局函数: 首字母小写,单词间用下划线分隔,例如 `get_user_info`,`get_admin_role`采用snake_case命名

- 类方法: 首字母小写,单词间用驼峰命名,例如 `getUserInfo`,`getAdminRole`采用camelCase命名

## 批量处理数据

示例代码如下:
重点是使用`cursor()`方法,避免一次性加载所有数据到内存中,导致内存溢出.
注意示例UserInfo的处理,目的是一次查询多个UserInfo,而不是一次查询一个UserInfo.
```php
$startTime = microtime(true);
$total = 0;
$indexName = config('common_es.indices.user.users');

User::queryByAllShard()
->select(['user_uid', 'account_status', 'invite_code', 'real_auth_status', 'level_id','account_name','phone','created_at', 'updated_at','deleted_at'])
->cursor()
->chunk(config('common.chunk_size.es_sync'))
->each(function ($chunk) use (&$total, $indexName) {
	$userCollection = $chunk;
	// 1. 先批量获取这批用户的所有ID
	$userUidArray = $chunk->pluck('user_uid')->toArray();

	// 2. 批量查询UserInfo（这里需要根据实际的UserInfo模型调整）
	// 假设UserInfo也有分片路由，需要按user_uid查询
	$userInfoCollection = UserInfo::queryByAllShard()
		->whereIn('user_uid', $userUidArray)
		->get()
		->keyBy('user_uid');
	$esDataArray = $userCollection->map(function ($userObject) use ($userInfoCollection) {
		$userInfoObject = $userInfoCollection->get($userObject->user_uid);

		return [
			'_id' => $userObject->user_uid,
			'user_uid' => $userObject->user_uid,
			'phone' => $userObject->phone,
			'account_name' => $userObject->account_name,
			'account_status' => $userObject->account_status,
			'invite_code' => $userObject->invite_code,
			'real_auth_status' => $userObject->real_auth_status,
			'level_id' => $userObject->level_id,
			'created_at' => $userObject->created_at?->toDateTimeString(),
			'updated_at' => $userObject->updated_at?->toDateTimeString(),
			'deleted_at' => $userObject->deleted_at?->toDateTimeString(),
			//userInfo
			'id_number' => $userInfoObject->id_number,
			'nick_name' => $userInfoObject->nick_name,
			'real_name' => $userInfoObject->real_name,
			'solar_birthday_at' => $userInfoObject->solar_birthday_at,
			'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
			'sex' => $userInfoObject->sex,
			'introduction' => $userInfoObject->introduction,
			// 'avatar'=>$userInfoObject->avatar,
			// 'ablum_uid'=>$userInfoObject->ablum_uid,
			// 'qrcode'=>$userInfoObject->qrcode,

		];
	})->toArray();

	//p($esDataArray);

	//$result = EsFacade::batchActDoc($indexName, $esData);
	// 统计处理总数
	$total += count($esDataArray);
});

$endTime = microtime(true);
$costTime = round($endTime - $startTime, 2);
```

## DTO说明

1.为了项目的可维护性，我们需要对DTO进行统一管理.项目外接收参数统一用DTO对参数类型进行限定和校验.
2.业务内部参数定义流转,使用业务DTO传递,避免使用数组带来的混乱,提升项目可维护性.
