# ShardHelperFacade 使用手册

## 概述

`ShardHelperFacade` 是游鹄生态系统中处理分库分表操作的核心门面服务，封装了分片路由、查询、缓存等所有底层逻辑，提供简洁易用的 API 接口。

**特点：**
- ✅ 支持单体和微服务架构平滑切换
- ✅ 自动适配 youhu/youhushop/youhujun 不同模块配置
- ✅ 内置 Redis Hash 缓存，提升查询性能
- ✅ 全表遍历支持提前退出优化
- ✅ 缓存自动失效机制

---

## 核心方法

### 1. 分片信息计算

#### `getShardInfo(string $modelClass, string|int $uid): array`

获取完整的分片信息，包括分库名、分表名、分片键和基础表名。

**参数：**
- `$modelClass` - 模型类名（如 `User::class`）
- `$uid` - 分片业务ID（如用户ID）

**返回值：**
```php
[
    'db' => 'youhu_0',          // 分库名
    'table' => 'users_0',        // 分表名
    'shard_key' => 0,           // 分片键
    'base_table' => 'users'      // 基础表名
]
```

**示例：**
```php
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\User;

$userUid = '276406781286953';
$shardInfo = ShardHelperFacade::getShardInfo(User::class, $userUid);

p($shardInfo);
// 输出: ['db' => 'youhu_0', 'table' => 'users_0', 'shard_key' => 0, 'base_table' => 'users']
```

---

### 2. 分表名获取

#### `getTableName(string $modelClass, string|int $uid): string`

仅获取分表名，用于特定场景。

**示例：**
```php
$tableName = ShardHelperFacade::getTableName(User::class, $userUid);
// 返回: 'users_0'
```

---

### 3. 分库名获取

#### `getDbName(string|int $uid): string`

仅获取分库名，用于特定场景。

**示例：**
```php
$dbName = ShardHelperFacade::getDbName($userUid);
// 返回: 'youhu_0'
```

---

### 4. 分片上下文执行

#### `withShardContext(string $modelClass, string|int $uid, callable $callback): mixed`

带分片上下文执行闭包，自动处理 `bindShardBusinessId` 和 `clearBoundShardBusinessId`。

**适用场景：** 需要在特定分片上执行复杂查询或操作。

**示例：**
```php
$result = ShardHelperFacade::withShardContext(User::class, $userUid, function () {
    // 在这里执行分片相关操作
    return User::where('account_status', 1)->first();
});
```

---

### 5. 带分片创建记录

#### `createWithShard(string $modelClass, string|int $uid, array $data): mixed`

在指定分片上创建模型记录，自动处理分片路由。

**参数：**
- `$modelClass` - 模型类名
- `$uid` - 分片业务ID
- `$data` - 要创建的数据数组

**示例：**
```php
$userUid = '276406781286953';

$adminObject = ShardHelperFacade::createWithShard(
    App\Models\LaravelFastApi\V1\Admin\Admin::class,
    $userUid,
    [
        'user_uid' => $userUid,
        'account_name' => 'admin001',
        'account_status' => 1,
        'password' => bcrypt('123456'),
    ]
);

p($adminObject);
```

---

### 6. 全表遍历查询

#### `queryAllShards(string $modelClass, callable $callback, string $targetField = '', array $targets = []): Collection`

遍历所有分表执行查询，支持提前退出优化和 Redis 缓存。

**参数：**
- `$modelClass` - 模型类名
- `$callback` - 查询回调函数，接收查询构造器
- `$targetField` - 提前退出的目标字段（可选）
- `$targets` - 提前退出的目标值数组（可选）

**示例 1：遍历所有表查询**
```php
$users = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1)
              ->select(['user_uid', 'account_name']);
    }
);
```

**示例 2：带提前退出的查询**
```php
$users = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1);
    },
    'account_name',        // 目标字段
    ['develop', 'super']   // 找到这两个用户就停止遍历
);
```

---

### 7. 带缓存的精准查询

#### `queryByShardWithCache(string $modelClass, string|int $businessId, array $conditions = []): mixed`

对指定业务ID的查询进行 Redis 缓存，自动处理缓存读写和失效。

**参数：**
- `$modelClass` - 模型类名
- `$businessId` - 业务ID
- `$conditions` - 查询条件（用于生成缓存key）

**示例：**
```php
$userObject = ShardHelperFacade::queryByShardWithCache(
    User::class,
    $userUid,
    ['account_status' => 1]  // 缓存key的一部分
);
```

---

### 8. 清理缓存

#### `clearCache(string $modelClass, string|int $uid = '', array $conditions = []): void`

手动清理指定模型的缓存。

**示例：**
```php
// 清理指定用户的所有缓存
ShardHelperFacade::clearCache(User::class, $userUid);

// 清理指定用户特定条件的缓存
ShardHelperFacade::clearCache(User::class, $userUid, ['account_status' => 1]);
```

---

### 9. 注册缓存自动失效

#### `registerCacheInvalidation(string $modelClass): void`

注册模型事件监听器，在数据变更时自动清理缓存。

**示例：**（通常在 `AppServiceProvider` 中注册）
```php
// app/Providers/AppServiceProvider.php

use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\User;

public function boot()
{
    // 注册 User 模型的缓存自动失效
    ShardHelperFacade::registerCacheInvalidation(User::class);
}
```

---

## 完整示例

### 示例 1：用户注册流程

```php
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;

// 1. 创建用户
$userData = [
    'account_name' => 'develop',
    'password' => bcrypt('123456'),
    'account_status' => 1,
];

$userObject = ShardHelperFacade::createWithShard(User::class, $userUid, $userData);

// 2. 创建关联管理员
$adminData = [
    'user_uid' => $userUid,
    'account_name' => 'admin001',
    'account_status' => 1,
    'password' => bcrypt('123456'),
];

$adminObject = ShardHelperFacade::createWithShard(Admin::class, $userUid, $adminData);

p('用户和管理员创建成功');
```

---

### 示例 2：全表查询指定用户

```php
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\User;

// 查询多个指定账户名的用户
$users = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->whereIn('account_name', ['develop', 'super', 'admin'])
              ->where('account_status', 1);
    },
    'account_name',           // 目标字段
    ['develop', 'super']     // 提前退出条件
);

foreach ($users as $userObject) {
    p("用户: {$userObject->account_name}, UID: {$userObject->user_uid}");
}
```

---

### 示例 3：带缓存的查询(废弃)

```php
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\User;

// 第一次查询：走数据库
$userObject = ShardHelperFacade::queryByShardWithCache(User::class, $userUid);

// 第二次查询：走 Redis 缓存（速度快）
$userObject = ShardHelperFacade::queryByShardWithCache(User::class, $userUid);
```

---

## 配置说明

### 环境变量

```env
# 是否开启缓存
YOUHUJUN_SHARD_CACHE_ENABLE=false

# Redis 缓存 DB
YOUHUJUN_SHARD_CACHE_DB=3

# 缓存前缀
YOUHUJUN_SHARD_CACHE_PREFIX=shard_

# 默认缓存过期时间（秒）
YOUHUJUN_SHARD_CACHE_TTL=3600

# 分表数量
YOUHUJUN_SHARD_TABLE_COUNT=10
```

### 配置文件

```php
// config/custom/youhujun/shard.php

return [
    'shard' => [
        'cache' => [
            'enable' => env('YOUHUJUN_SHARD_CACHE_ENABLE', false),
            'db' => env('YOUHUJUN_SHARD_CACHE_DB', 3),
            'prefix' => env('YOUHUJUN_SHARD_CACHE_PREFIX', 'shard_'),
            'ttl' => env('YOUHUJUN_SHARD_CACHE_TTL', 3600),
            'allow_models' => [],  // 空数组表示允许所有模型缓存
            'model_ttl' => [
                // 可以为特定模型设置不同的过期时间
                // User::class => 7200,
            ],
        ],
        'table_count' => env('YOUHUJUN_SHARD_TABLE_COUNT', 10),
    ],
];
```

---

## 最佳实践

### 1. 使用 `createWithShard` 替代手动 bind/clear

**不推荐：**
```php
User::bindShardBusinessId($userUid);
try {
    $userObject = User::create($data);
} finally {
    User::clearBoundShardBusinessId();
}
```

**推荐：**
```php
$userObject = ShardHelperFacade::createWithShard(User::class, $userUid, $data);
```

---

### 2. 全表查询时使用提前退出

```php
// 优化前：遍历所有表
$users = ShardHelperFacade::queryAllShards(User::class, function ($query) {
    $query->where('account_name', 'develop');
});

// 优化后：找到就停止
$users = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_name', 'develop');
    },
    'account_name',
    ['develop']
);
```

---

### 3. 在 AppServiceProvider 中注册缓存自动失效

```php
// app/Providers/AppServiceProvider.php

public function boot()
{
    ShardHelperFacade::registerCacheInvalidation(User::class);
    ShardHelperFacade::registerCacheInvalidation(Admin::class);
    // 其他模型...
}
```

---

## 注意事项

1. **分片业务ID不能为空**：调用 `withShardContext` 或 `createWithShard` 时，`$uid` 参数必须有值。

2. **缓存需要 Redis**：使用缓存功能需要配置好 Redis 连接。

3. **白名单配置**：`allow_models` 为空时允许所有模型缓存，否则只缓存白名单内的模型。

4. **微服务适配**：自动检测 `youhu.is_ms` 和 `youhushop.is_ms` 配置，适配不同模块。

5. **日志记录**：所有分片操作都会记录日志到 `storage/logs/custom/shard/` 目录，方便调试。

---

## 相关文档

- [laravel-fast-api 开发思路](./develop.md)
- [游鹄生态系统介绍](./youhu.md)
- [ShardMapHelperFacade 使用手册](./ShardMapHelperFacade.md) （待补充）

---

**作者：** 游鹄君 & 雪儿
**更新日期：** 2026-03-05
