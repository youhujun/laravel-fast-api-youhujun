<?php

$phoneCodeArray = [
	
];

$shopCodeArray = [
	'OpenShopApplyError'=> [ 'code' => 10000, 'msg' => '用户申请开店失败','error'=>'OpenShopApplyError' ],
	'UserHasOpenShopSuccessError'=> [ 'code' => 10000, 'msg' => '用户已经申请开店成功错误','error'=>'UserHasOpenShopSuccessError' ],
	'UserHasOpenShopApplyError'=> [ 'code' => 10000, 'msg' => '用户正在申请开店错误','error'=>'UserHasOpenShopApplyError' ],
	'AddUserApplyShopError'=> [ 'code' => 10000, 'msg' => '添加用户申请开店记录失败!','error'=>'AddUserApplyShopError' ],
];

$userCodeArray = [
    'AddUserSystemParentError' => [ 'code' => 10000, 'msg' => '系统自动分配用户上级失败','error'=>' AddUserSystemParentError' ],

	'AddUserSystemSourceError' => [ 'code' => 10000, 'msg' => '系统自动倒序分配用户上级失败','error'=>' AddUserSystemSourceError' ],
];

$totalCodeArray = array_merge($phoneCodeArray,$userCodeArray,$shopCodeArray);

return $totalCodeArray;