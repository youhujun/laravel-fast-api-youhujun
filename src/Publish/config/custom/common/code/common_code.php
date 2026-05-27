<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-20 23:39:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-07 13:43:03
 * @FilePath: \youhu-laravel-api-12\config\custom\common\code\common_code.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

$totalCodeArray = [
    'ServiceBusyError' => ['code' => 10000, 'msg' => '服务繁忙,请稍后再试!','error' => 'ServiceBusyError'],
    'IndexNotFoundError' => ['code' => 10000, 'msg' => '映射标识不存在!','error' => 'IndexNotFoundError'],
    'IndexMapCodeNotFoundError' => ['code' => 10000, 'msg' => '映射标识码不存在!','error' => 'IndexMapCodeNotFoundError'],

    'ApiAuthError' => ['code' => 10000, 'msg' => 'Api验签失败!','error' => 'ApiAuthError'],
    'GetAccessTokenError' => ['code' => 10000, 'msg' => '获取临时凭证失败!','error' => 'GetAccessTokenError'],
    'BindAppidByMSError' => ['code' => 10000, 'msg' => '微服务绑定AppId失败!','error' => 'BindAppidByMSError'],
    'AuthTokenError' => ['code' => 10000, 'msg' => '凭证校验失败!','error' => 'AuthTokenError'],
    'BusinessIdEmptyError' => ['code' => 10000, 'msg' => '业务id不能为空!','error' => 'BusinessIdEmptyError'],
    'ThisUserHasNoDefaultAlbumError' => ['code' => 10000, 'msg' => '用户没有默认相册!','error' => 'ThisUserHasNoDefaultAlbumError'],
];


$logCodeArray = [
    'EventDataLogError' => ['code' => 10000, 'msg' => '事件数据格式错误!','error' => 'EventDataLogError'],
    'EventDataLogEmptyError' => ['code' => 10000, 'msg' => '事件数据为空!','error' => 'EventDataLogEmptyError'],
    'EventServiceFlagError' => ['code' => 10000, 'msg' => '事件数标识错误!','error' => 'EventServiceFlagError'],
    'YouHuAuthServiceDataError' => ['code' => 10000, 'msg' => '授权解密数据不存在!','error' => 'YouHuAuthServiceDataError'],
];


$authCodeArray = [
    'AuthServiceFlagError' => ['code' => 10000, 'msg' => '请求标识错误!','error' => 'AuthServiceFlagError'],
    'ServiceFlagEmptyError' => ['code' => 10000, 'msg' => '请求标识缺失!','error' => 'ServiceFlagEmptyError'],
    'AccessTokenEmptyError' => ['code' => 10000, 'msg' => '临时凭证失效!','error' => 'AccessTokenEmptyError'],
    'YouHuAuthServiceEmptyError' => ['code' => 10000, 'msg' => '秘钥和凭证数据不存在!','error' => 'YouHuAuthServiceEmptyError'],
    'AuthTimeError' => ['code' => 10000, 'msg' => '请求超时!','error' => 'AuthTimeError'],
    'AuthNonceError' => ['code' => 10000, 'msg' => '请求重复!','error' => 'AuthNonceError'],
    'AuthAccessTokenError' => ['code' => 10000, 'msg' => '凭证验证失败!','error' => 'AuthAccessTokenError'],
    'VerifySignError' => ['code' => 10000, 'msg' => '验签失败!','error' => 'VerifySignError'],
];

$youhuAuthCodeArray = [
    'AddYouHuAuthServiceError' => ['code' => 10000, 'msg' => '验签失败!','error' => 'AddYouHuAuthServiceError'],
];

$userCodeArray = [
    'AddSyncUserMapError' => ['code' => 10000, 'msg' => '同步用户映射表失败!','error' => 'AddSyncUserMapError'],
];

$queryCodeArray = [
    'QueryModelMapFlagEmptyError' => ['code' => 10000, 'msg' => '查询模型映射数据不存在!','error' => 'AddSyncUserMapError'],
    'GetShardInfoByNonBizIdError' => ['code' => 10000, 'msg' => '精准查询失败!','error' => 'GetShardInfoByNonBizIdError'],
    'GetBizIdsByFuzzySearch' => ['code' => 10000, 'msg' => '模糊查询失败!','error' => 'GetBizIdsByFuzzySearch'],
];

$esCodeArray = [
    'EsQueryError' => ['code' => 10000, 'msg' => 'ES查询错误!','error' => 'EsQueryError'],
    'EsCountError' => ['code' => 10000, 'msg' => 'ES统计错误!','error' => 'EsCountError'],
    'EsIndexNameEmptyError' => ['code' => 10000, 'msg' => '索引名称为空!','error' => 'EsIndexNameEmptyError'],
    'EsHostConfigEmptyError' => ['code' => 10000, 'msg' => 'es的host配置为空!','error' => 'EsHostConfigEmptyError'],
    'EsInitError' => ['code' => 10000, 'msg' => 'es初始化失败!','error' => 'EsInitError'],
    'EsGetError' => ['code' => 10000, 'msg' => 'get查询失败!','error' => 'EsGetError'],
    'EsCountError' => ['code' => 10000, 'msg' => 'es统计查询失败!','error' => 'EsCountError'],
    ''

];

$errorCodeArray = array_merge(
    $totalCodeArray,
    $logCodeArray,
    $authCodeArray,
    $youhuAuthCodeArray,
    $userCodeArray,
    $queryCodeArray,
    $esCodeArray
);

return $errorCodeArray;
