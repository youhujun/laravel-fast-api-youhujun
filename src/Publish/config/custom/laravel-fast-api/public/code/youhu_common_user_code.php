<?php

$totalCodeArray = [];

$usercodeArray = [
	 'AddUserSystemParentUnionOneIdError'=>[ 'code'=>1000, 'msg'=>'系统自动分配用户上级数据id错误!','error'=>'AddUserSystemParentUnionOneIdError' ],
];

$errorCodeArray = array_merge(
    $totalCodeArray,
    $usercodeArray
);

return $errorCodeArray;

