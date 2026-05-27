# 游鹄生态迁移文件与模型路径映射明细

## 核心组件包路径（所有模型/DTO的字段数据源）
### 1. LaravelFastApi 组件
- 迁移文件根路径：D:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\database\migrations\Create
- 对应模型根路径：app\Models\LaravelFastApi\V1
- 字段规范：所有*_uid字段为unsignedBigInteger类型，default(0)；shard_key锚定业务归属字段（如user_uid/payer_uid）。

### 2. YouHu 核心组件
- 迁移文件根路径：D:\wwwroot\PHP\Components\Laravel\youhujun\youhu\src\database\migrations\Create
- 对应模型根路径：app\Models\YouHu\V1
- 字段规范：同上。

### 3. YouHuShop 商城组件
- 迁移文件根路径：D:\wwwroot\PHP\Components\Laravel\youhujun\youhushop\src\database\migrations\Create
- 对应模型根路径：app\Models\YouHuShop\V1
- 字段规范：同上。

## 预留 DTO 路径（待创建）
- LaravelFastApi DTO：app\DTOs\LaravelFastApi\V1
- YouHu DTO：app\DTOs\YouHu\V1
- YouHuShop DTO：app\DTOs\YouHuShop\V1