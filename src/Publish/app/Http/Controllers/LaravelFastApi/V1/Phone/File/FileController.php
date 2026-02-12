<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-11 09:02:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-11 09:40:52
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\File\FileController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;

use App\Facades\LaravelFastApi\V1\Phone\File\PhonePictureFacade;

class FileController extends Controller
{
	 /**
     * 获取默认选项
     */
    public function singleUploadPicture(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
           $result = code(\config('phone_code.SinglePictureNoUploadError'));

            if($request->hasFile('picture'))
            {
                $picture = $request->file('picture');

                // PhonePictureFacade::test(); die;
                //p($picture);die;
                $result = PhonePictureFacade::singleUploadPicture($user, $picture);
            }
        }

        return $result;
    }


}
