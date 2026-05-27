<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-18 22:40:03
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-18 23:18:02
 * @FilePath: \app\Rules\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfigVoiceSaveType.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Rules\LaravelFastApi\V1\Admin\System\SystemConfig;

use Closure;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\Common\RuleException;

class VoiceConfigVoiceSaveType implements DataAwareRule, ValidationRule
{
	/**
     * 所有正在被验证的数据。
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    // ...

    /**
     * 设置正在被验证的数据。
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
		$validated = $this->data;

       if($value == 10){
		if(!isset($validated['voice_path']) || !$validated['voice_path']){
			throw new RuleException('RuleVoiceConfigVoiceSaveTypeError',$attribute);
		}
	   }
	   if($value == 20){
		if(!isset($validated['voice_path']) || !$validated['voice_url']){
			throw new RuleException('RuleVoiceConfigVoiceSaveTypeError',$attribute);
		}
	   }
    }				
}
