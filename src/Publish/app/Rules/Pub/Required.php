<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-08 17:49:55
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-01 01:13:38
 * @FilePath: \app\Rules\Pub\Required.php
 */

namespace App\Rules\Pub;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\Common\RuleException;

use Illuminate\Support\Facades\Log;

/**
 * 必须有数据
 */
class Required implements ValidationRule
{
   

   /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
	    $result = true;

	    if($value === null){

			$result = false;
		}
		else{
			//判断数值不为空的情况
			if(isset($value) || !empty($value))
			{
				//判断是否是数组
				$checkIsArray = is_array($value);

				if($checkIsArray)
				{
					$result = true;
				}
				else
				{
					//判断字符串不能为空
					$requiredPartten = "/[\S]+/";

					$requiredResult = preg_match($requiredPartten, $value);

					if ($requiredResult)
					{
						$result = true;
					}
				}
			}
		}

        if(!$result)
        {
            $this->message = '该数据不能为空,至少包含一个非空白字符';
            throw new RuleException('RuleRequiredError',$attribute);
        }
    }	
}
