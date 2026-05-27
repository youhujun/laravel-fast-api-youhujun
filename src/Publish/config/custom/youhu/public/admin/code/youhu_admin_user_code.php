<?php

$adminCodeArray = [];

$userCodeArray = [

	'AddUserSystemParentError' => [ 'code' => 10000, 'msg' => '系统自动分配用户上级失败','error'=>' AddUserSystemParentError' ],

	'AddUserSystemSourceError' => [ 'code' => 10000, 'msg' => '系统自动倒序分配用户上级失败','error'=>' AddUserSystemSourceError' ],
];

$totalCodeArray = array_merge($adminCodeArray,$userCodeArray);

return $totalCodeArray;