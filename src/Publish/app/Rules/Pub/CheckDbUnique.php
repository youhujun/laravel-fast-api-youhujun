<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-08 06:31:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 06:37:45
 * @FilePath: \youhu-laravel-api-12\app\Rules\Pub\CheckDbUnique.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Rules\Pub;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\Common\RuleException;
use Illuminate\Support\Facades\DB;

class CheckDbUnique implements DataAwareRule, ValidationRule
{
    /**
     * 所有正在被验证的数据。
     *
     * @var array<string, mixed>
     */
    protected $dataArray = [];
    //表名
    protected $tableName;

    //字段
    protected $field;

    //忽略的uid
    protected $ignore_id;

    public function __construct(string $tableName, string $field, ?string $ignore_id = null)
    {
        $this->tableName = $tableName;

        $this->field = $field;

        $this->ignore_id = $ignore_id;
    }

    /**
    * 设置正在被验证的数据。
    *
    * @param  array<string, mixed>  $data
    */
    public function setData(array $dataArray): static
    {
        $this->dataArray = $dataArray;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = true;

        $tableName = $this->tableName;

        $field = $this->field;

        $ignore_id = $this->ignore_id;

		$query = DB::table($tableName)->where($field, $value);

        //如果有忽略的UID，则是更新检查
        if ($ignore_id) {
            // 精确匹配搜索
            $totalNumber = $query->count();
            //超过1个肯定不对
            if ($totalNumber > 1) {
                $result = false;
            }
            //有一个的情况下，需要判断是否是当前的UID
            if ($totalNumber == 1) {
                $dataObject = $query->first();

                
                    if ($dataObject->id != $ignore_id) {
                        $result = false;
                    }
                
            }
        } else {

            $totalNumber = $query->count();

            if ($totalNumber) {
                $result = false;
            }
        }

        if (!$result) {
            throw new RuleException('RuleCheckUniqueError', $attribute);
        }
    }


}
