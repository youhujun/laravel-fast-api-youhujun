<?php

namespace App\DTOs\Traits;

/**
 * 游鹄生态DTO通用Trait（兼容单表/多表、微服务复用、低改造成本）
 * 核心原则：只做数据约定，不做数据校验（校验按需在子类加rules方法）
 */
trait BaseDTOTrait
{
    /**
     * 批量赋值（严格匹配字段映射白名单，过滤非法字段）
     * @param array $attributes 待赋值数组
     * @param bool $allowNull 是否允许赋值null（默认false，避免空值污染）
     * @return self
     */
    public function fill(array $attributes, bool $allowNull = false): self
    {
        $allFields = $this->getAllFieldsFromMap();
        $reflection = new \ReflectionClass($this);

        foreach ($attributes as $key => $value) {
            if (in_array($key, $allFields) && property_exists($this, $key)) {
                // 获取属性类型（PHP 8.0+）
                $property = $reflection->getProperty($key);
                $propertyType = $property->getType();

                // 类型转换：处理字符串到其他类型的自动转换
                $convertedValue = $this->convertType($value, $propertyType);

                // 非空保护：避免null覆盖默认值（适配微服务空值传递）
                if ($convertedValue !== null || $allowNull) {
                    $this->{$key} = $convertedValue;
                }
            }
        }
        return $this;
    }

    /**
     * 类型转换：将字符串或其他类型转换为目标类型
     * @param mixed $value 原始值
     * @param \ReflectionType|null $propertyType 属性类型反射
     * @return mixed 转换后的值
     */
    private function convertType($value, $propertyType)
    {
        if (is_null($value) || !$propertyType) {
            return $value;
        }

        // 处理联合类型（PHP 8.0+）如 int|string
        if ($propertyType instanceof \ReflectionUnionType) {
            $types = $propertyType->getTypes();
            // 优先尝试第一个非null类型
            foreach ($types as $type) {
                $typeName = $type->getName();
                if ($typeName === 'null') {
                    continue;
                }
                $converted = $this->convertByTypeName($value, $typeName);
                if ($converted !== null || $typeName === 'string') {
                    return $converted;
                }
            }
            return $value;
        }

        // 处理单个类型
        $typeName = $propertyType->getName();
        return $this->convertByTypeName($value, $typeName);
    }

    /**
     * 根据类型名称进行转换
     * @param mixed $value 原始值
     * @param string $typeName 类型名称
     * @return mixed 转换后的值
     */
    private function convertByTypeName($value, string $typeName)
    {
        switch ($typeName) {
            case 'int':
                return is_numeric($value) ? (int) $value : null;
            case 'float':
                return is_numeric($value) ? (float) $value : null;
            case 'bool':
                return in_array($value, [1, '1', true, 'true'], true);
            case 'array':
                return is_string($value) ? json_decode($value, true) : $value;
            case 'string':
                return is_string($value) ? $value : (string) $value;
            default:
                return $value;
        }
    }

    /**
     * 从数组创建DTO（快捷方法，复用fill逻辑）
     * @param array $data 数据源
     * @param bool $allowNull 是否允许null
     * @return static
     */
    public static function fromArray(array $data, bool $allowNull = false): static
    {
        return (new static())->fill($data, $allowNull);
    }

    /**
     * 从Request创建DTO（按需使用，不强制绑定校验）
     * @param \Illuminate\Http\Request $request 请求实例
     * @param bool $allowNull 是否允许null
     * @return static
     */
    public static function fromRequest(\Illuminate\Http\Request $request, bool $allowNull = false): static
    {
        return self::fromArray($request->all(), $allowNull);
    }

    /**
     * 转数组（适配微服务通信/多表存储，支持字段过滤）
     * @param string|null $table 只返回指定表的字段
     * @param array $except 排除字段（优先级高于table）
     * @return array
     */
    public function toArray(string $table = null, array $except = []): array
    {
        $data = [];
        $fieldMap = $this->getFieldMap();
        $allFields = $this->getAllFieldsFromMap();

        // 1. 筛选要返回的字段（排除优先）
        $fields = array_diff($allFields, $except);
        if ($table && isset($fieldMap[$table])) {
            $fields = array_intersect($fieldMap[$table], $fields);
        }

        // 2. 赋值（过滤空字符串/null，按需）
        foreach ($fields as $key) {
            $value = $this->{$key};
            // 可选：过滤空值（微服务通信减少冗余）
            if ($value !== '' && $value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取单个字段值（简化调用）
     * @param string $key 字段名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return property_exists($this, $key) ? $this->{$key} : $default;
    }

    /**
     * 批量设置字段（适配批量更新场景）
     * @param array $fields 字段数组 ['key1' => 'value1']
     * @return self
     */
    public function set(array $fields): self
    {
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * 提取所有字段名（内部复用）
     */
    private function getAllFieldsFromMap(): array
    {
        $fieldMap = $this->getFieldMap();
        $allFields = [];

        // 兼容单表（['field1','field2']）和多表（['table1'=>['f1']]）格式
        if (isset($fieldMap[0]) && is_string($fieldMap[0])) {
            $allFields = $fieldMap;
        } else {
            foreach ($fieldMap as $tableFields) {
                $allFields = array_merge($allFields, $tableFields);
            }
        }

        return array_unique($allFields);
    }

    /**
     * 【必实现】字段映射（数据约定核心）
     * 格式1（单表）：['user_uid','timestamp','nonce','sign']
     * 格式2（多表）：['youhu_auth' => ['user_uid','auth_token'], 'users' => ['username']]
     * @return array
     */
    abstract public function getFieldMap(): array;

    /**
     * 【可选实现】校验规则（按需复用才加，不强制）
     * 核心：已写好的接口不用加，需要复用的接口才加
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
