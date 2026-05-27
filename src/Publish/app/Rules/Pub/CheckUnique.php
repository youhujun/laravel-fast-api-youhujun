<?php

namespace App\Rules\Pub;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\Common\RuleException;
use App\Facades\Common\V1\Es\EsQueryFacade;

class CheckUnique implements DataAwareRule, ValidationRule
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
    protected $ignore_uid;

    //忽略的字段名
    protected $ignore_uid_field;

    //es对应的模块名称
    protected $model_name;

    public function __construct(string $tableName, string $field, ?string $model_name = '', string $ignore_uid_field = 'user_uid', ?string $ignore_uid = null)
    {
        $this->tableName = $tableName;

        $this->field = $field;

        $this->model_name = $model_name;

        $this->ignore_uid_field = $ignore_uid_field;

        $this->ignore_uid = $ignore_uid;
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

        $ignore_uid_field = $this->ignore_uid_field;

        $ignore_uid = $this->ignore_uid;

        $model_name = $this->model_name;

        $indexName = config('common_es.indices.'.$tableName);

        if ($model_name) {
            //es索引
            $indexName = config('common_es.indices.'.$model_name.'.'.$tableName);
        }

        //如果有忽略的UID，则是更新检查
        if ($ignore_uid) {
            // 精确匹配搜索
            $esTotalNumber = $this->getEsSelectTotalNumber($indexName, $field, $value);
            //超过1个肯定不对
            if ($esTotalNumber > 1) {
                $result = false;
            }
            //有一个的情况下，需要判断是否是当前的UID
            if ($esTotalNumber == 1) {
                $esObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where($field, $value)->get()->first();


                if (isset($esObject->{$ignore_uid_field})) {
                    if ($esObject->{$ignore_uid_field} !== $ignore_uid) {
                        $result = false;
                    }
                }
            }
        } else {
            $esTotalNumber = $this->getEsSelectTotalNumber($indexName, $field, $value);

            if ($esTotalNumber) {
                $result = false;
            }
        }

        if (!$result) {
            throw new RuleException('RuleCheckUniqueError', $attribute);
        }
    }

    /**
     * 获取es查询总数
     *
     * @param  string  $indexName
     * @param  array   $queryArray
     * @return integer
     */
    private function getEsSelectTotalNumber(string $indexName, string $field, string $value): int
    {
        $totalNumber = 0;

        $esColelction = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where($field, $value)->limit(1000)->get();

        $totalNumber = $esColelction->count();

        return $totalNumber;
    }
}
