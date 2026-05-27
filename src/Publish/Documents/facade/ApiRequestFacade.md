# ApiRequestFacade 使用手册

> **版本**: v1  
> **作者**: youhujun & xueer  
> **更新日期**: 2026-03-04

## 📖 概述

`ApiRequestFacade` 是游鹄生态中用于**微服务之间API通信**的安全请求门面服务。它提供了跨服务的加密请求、认证和响应处理功能。

### 核心特点

- ✅ **AES 加密通信**：请求参数使用 AES 加密传输
- ✅ **服务认证管理**：自动获取和管理各服务的认证信息
- ✅ **统一请求封装**：统一的请求头格式和参数加密
- ✅ **日志记录**：自动记录请求和响应日志
- ✅ **多服务支持**：支持 youhu-base、shard-map、youhu、youhushop、xuehu 等服务

### 适用场景

- 微服务架构下的跨服务调用
- 需要加密传输的 API 请求
- 服务间数据同步
- 跨服务的用户认证和授权

## 🔧 配置说明

### 1. 服务 API 地址配置

在配置文件中定义各服务的 API 地址：

```php
// config/shardmap_api_url.php (示例)
return [
    'SyncMapData' => 'http://youhu-base/api/v1/shardmap/sync',
    'queryOneData' => 'http://youhu-base/api/v1/shardmap/query-one',
    'queryManyData' => 'http://youhu-base/api/v1/shardmap/query-many',
];
```

### 2. AES 加密密钥配置

在配置文件中配置 AES 密钥：

```php
// config/common.php
return [
    'aes' => [
        'key' => env('AES_KEY', 'your-secret-key'),
    ],
];
```

### 3. 支持的服务列表

`ApiRequestFacadeService` 中已配置支持以下服务：

| 服务标识 | 服务代码 | 说明 |
|---------|---------|------|
| `youhu-base` | 10 | 基础服务 |
| `shard-map` | 20 | 分片映射服务 |
| `youhu` | 30 | 游鹄业务服务 |
| `youhushop` | 40 | 游鹄商城服务 |
| `xuehu` | 50 | 雪鹄服务 |

## 📚 方法详解

### decoder

解码并执行 API 请求，返回响应结果。

#### 方法签名

```php
public static function decoder(
    string $userUid, 
    string $url, 
    array $params, 
    string $originServiceFlag = 'youhu-base'
): string
```

#### 参数说明

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| `$userUid` | string | 是 | 用户唯一标识（用于获取服务认证信息） |
| `$url` | string | 是 | 请求的 API 地址 |
| `$params` | array | 是 | 请求参数数组（会被 AES 加密） |
| `$originServiceFlag` | string | 是 | 目标服务标识（如 'youhu-base', 'youhu' 等） |

#### 返回值

返回 API 请求的原始响应结果（JSON 字符串）。

#### 工作流程

```
调用 decoder()
    ↓
1. 根据 userUid 和服务标识获取认证信息
   - 查询对应服务的 YouHuAuthService 记录
   - 获取 secret_key 和 auth_token
    ↓
2. 使用 AES 密钥解密 secret_key
    ↓
3. 生成随机数和时间戳
    ↓
4. 构建请求头
   - X-Auth-Token: 认证令牌
   - X-Service: 服务标识
   - X-Nonce: 随机数
   - X-Timestamp: 时间戳
    ↓
5. 加密请求参数
   - 所有参数值使用解密后的 secret_key 加密
   - user_uid 使用 AES 密钥加密
    ↓
6. 记录请求日志
    ↓
7. 发送 POST 请求
    ↓
8. 记录响应日志
    ↓
返回响应结果
```

#### 请求头格式

```http
X-Auth-Token: {encrypted_auth_token}
X-Service: {originServiceFlag}
X-Nonce: {nonce}
X-Timestamp: {timestamp}
Content-Type: application/json
```

#### 请求参数格式

所有参数（包括 `user_uid`）都会被 AES 加密：

```json
{
    "user_uid": "encrypted_user_uid_value",
    "param1": "encrypted_param1_value",
    "param2": "encrypted_param2_value"
}
```

#### 异常处理

| 异常 | 说明 |
|------|------|
| `EventServiceFlagError` | 服务标识为空 |
| `EventServiceFlagNotExistError` | 服务标识不存在 |
| `YouHuAuthServiceEmpptyError` | 未查询到授权数据 |

---

## 💡 使用示例

### 示例1：同步映射表数据到基础服务

```php
use App\Facades\Common\V1\Api\Request\ApiRequestFacade;

class UserRegisterService
{
    /**
     * 用户注册后同步数据到映射表
     */
    public function syncUserToMap(string $userUid, object $userObject)
    {
        // 1. 计算用户分片信息
        $shardInfo = ShardHelperFacade::calc($userUid, 'user');
        
        // 2. 准备同步数据
        $url = config('shardmap_api_url.SyncMapData');
        $params = [
            'shardInfo' => $shardInfo,
            'modelData' => [
                'user_uid' => $userUid,
                'account_name' => $userObject->account_name,
                'phone' => $userObject->phone,
                'email' => $userObject->email,
            ]
        ];
        
        // 3. 发送请求到基础服务
        $response = ApiRequestFacade::decoder(
            $userUid,
            $url,
            $params,
            'youhu-base'  // 目标服务
        );
        
        // 4. 处理响应
        $result = json_decode($response, true);
        if ($result['code'] !== 0) {
            throw new BusinessException('同步映射表失败');
        }
        
        return true;
    }
}
```

### 示例2：查询用户信息（跨服务调用）

```php
use App\Facades\Common\V1\Api\Request\ApiRequestFacade;

class UserService
{
    /**
     * 跨服务查询用户信息
     */
    public function getUserInfoFromAnotherService(string $userUid, string $targetService)
    {
        // 1. 配置 API 地址
        $url = config('user_api_url.getUserInfo');
        
        // 2. 准备查询参数
        $params = [
            'user_uid' => $userUid,
            'need_profile' => true,
            'need_orders' => false,
        ];
        
        // 3. 发送请求到目标服务
        $response = ApiRequestFacade::decoder(
            $userUid,
            $url,
            $params,
            $targetService  // 如 'youhu', 'youhushop' 等
        );
        
        // 4. 解析响应
        $result = json_decode($response, true);
        if ($result['code'] !== 0) {
            throw new BusinessException($result['msg'] ?? '查询失败');
        }
        
        return $result['data'];
    }
}
```

---

## 🔒 安全机制

### 1. AES 加密传输

- 请求参数使用 AES 加密传输
- 使用服务间共享的 secret_key 进行加密解密
- user_uid 使用系统级 AES 密钥加密

### 2. 认证令牌

- 每个服务都有独立的 auth_token
- 令牌存储在 YouHuAuthService 表中
- 通过分片查询获取对应服务的认证信息

### 3. 请求签名

- X-Nonce: 随机数防止重放攻击
- X-Timestamp: 时间戳防止过期请求
- 服务器端可验证请求时效性

### 4. 日志记录

所有请求和响应都会记录到日志：

```
storage/logs/custom/{originServiceFlag}/{Y-m-d}/ApiRequest.log
```

日志格式：

```json
{
    "info": "请求记录日志",
    "url": "http://youhu-base/api/v1/shardmap/sync",
    "headers": ["X-Auth-Token:xxx", "X-Service:youhu-base", ...],
    "params": {"user_uid": "encrypted_value", ...}
}
```

---

## ⚠️ 注意事项

### 1. 服务标识必须存在

使用前确保服务标识已在 `$map` 中配置：

```php
// ✅ 正确：使用已配置的服务标识
ApiRequestFacade::decoder($userUid, $url, $params, 'youhu-base');

// ❌ 错误：服务标识不存在
ApiRequestFacade::decoder($userUid, $url, $params, 'unknown-service');
// 抛出异常: EventServiceFlagNotExistError
```

### 2. 必须配置认证信息

目标服务的 YouHuAuthService 表中必须有对应的认证记录：

```php
// 系统会自动查询 YouHuAuthService 表
// 如果找不到认证信息，会抛出异常
// 异常: YouHuAuthServiceEmpptyError
```

### 3. 参数必须是数组

`$params` 参数必须是数组类型：

```php
// ✅ 正确
$params = ['user_uid' => $uid, 'data' => $data];

// ❌ 错误
$params = 'invalid_data';  // 类型错误
```

### 4. 响应需要手动解析

`decoder()` 返回的是 JSON 字符串，需要手动解析：

```php
// ✅ 正确：解析 JSON
$response = ApiRequestFacade::decoder(...);
$result = json_decode($response, true);

// ❌ 错误：直接作为数组使用
$response = ApiRequestFacade::decoder(...);
$data = $response['data'];  // 错误：$response 是字符串，不是数组
```

---

## 🎯 最佳实践

### 1. 封装服务调用

建议将每个服务的 API 调用封装成独立的方法：

```php
class BaseServiceApiClient
{
    protected function request(string $url, array $params, string $userUid): array
    {
        $response = ApiRequestFacade::decoder(
            $userUid,
            $url,
            $params,
            'youhu-base'
        );
        
        $result = json_decode($response, true);
        if ($result['code'] !== 0) {
            throw new BusinessException($result['msg'] ?? '请求失败');
        }
        
        return $result['data'];
    }
    
    public function syncMapData(array $shardInfo, array $modelData, string $userUid): bool
    {
        $url = config('shardmap_api_url.SyncMapData');
        $params = compact('shardInfo', 'modelData');
        
        $this->request($url, $params, $userUid);
        
        return true;
    }
}
```

### 2. 统一异常处理

在业务层统一处理异常：

```php
try {
    $response = ApiRequestFacade::decoder(...);
    $result = json_decode($response, true);
    
    if ($result['code'] !== 0) {
        throw new BusinessException($result['msg']);
    }
    
    return $result['data'];
    
} catch (CommonException $e) {
    plog([
        'error' => $e->getMessage(),
        'service' => $originServiceFlag,
        'url' => $url
    ], $originServiceFlag, 'ApiRequestError');
    
    throw new BusinessException('服务请求失败');
}
```

### 3. 监控日志

建议定期检查 API 请求日志，及时发现异常：

```bash
# 查看 youhu-base 服务的请求日志
tail -f storage/logs/custom/youhu-base/$(date +%Y-%m-%d)/ApiRequest.log
```

---

## 📊 与其他门面的关系

```
业务层代码
    ↓
ApiRequestFacade (微服务请求门面)
    ↓
ShardHelperFacade / ShardMapHelperFacade (获取认证信息)
    ↓
httpPost (发送 HTTP 请求)
```

---

## 💬 总结

`ApiRequestFacade` 是游鹄生态中**微服务架构下跨服务通信的核心组件**，其核心价值在于：

1. ✅ **安全通信**：AES 加密 + 认证令牌 + 请求签名
2. ✅ **统一封装**：统一的请求格式和参数加密
3. ✅ **自动认证**：自动获取和管理服务认证信息
4. ✅ **日志追踪**：完整的请求和响应日志记录
5. ✅ **多服务支持**：支持多个业务服务的统一调用

在实际开发中，**建议为每个服务封装专门的 API 客户端类**，以获得更好的代码组织和可维护性。

---

## 🔗 相关文档

- [ShardHelperFacade 使用手册](./ShardHelperFacade.md) - 分库分表核心门面
- [ShardMapHelperFacade 使用手册](./ShardMapHelperFacade.md) - 映射表分片门面
- [ModelMapRouterFacade 使用手册](./ModelMapRouterFacade.md) - 模型路由门面

---

**© 2026 youhujun & xueer. All rights reserved.**
