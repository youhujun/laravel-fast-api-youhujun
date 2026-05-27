---
name: youhu-migration-sources
description: 该Skill用于游鹄生态Laravel项目的模型生成/校验、DTO字段定义场景，当用户提及LaravelFastApi/YouHu/YouHuShop的迁移文件/模型/DTO相关操作时触发，提供组件包迁移文件与模型路径映射规则。
---

# 游鹄生态迁移文件使用指令

## 核心操作指令
1. To generate migration files for YouHu ecosystem components, use the migration template in assets/migration_template.stub, and adjust sharding logic (sharded/non-sharded) according to table requirements.
2. To generate models for YouHu ecosystem components, load the path mapping reference first, then extract field definitions from migration files in the corresponding path, and generate models to the specified model directory.
3. To validate model fields against migration files, load the path mapping reference, compare the field type/default value/comment of the model with the migration file, and report inconsistencies.
4. To define DTO fields, load the path mapping reference, sync the field rules (e.g., *_uid as unsignedBigInteger with default 0) from migration files to DTO definitions.

## 资源引用说明
- 详细的路径映射规则：references/path_mapping.md
- 创建数据库迁移生成模板：assets/migration_template.stub
- 修改数据库迁移生成模板：assets/migration_update_template.stub
- 模型生成模板：assets/model_template.stub
- DTO生成模板：assets/dto_template.stub