<?php
/*
 * @Descripttion: 自定义助手函数
 * @Author: YouHuJun
 * @Date: 2020-02-20 11:25:39
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-08-31 18:11:31
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

 if(!function_exists('p'))
 {
     /**
      * 打印函数
      *
      * 该函数用于格式化输出传入的参数，便于调试和查看数据结构。
      *
      * @param mixed $param 要打印的参数，可以是任意类型。
      * @return void 无返回值。
      */
     function p($param):void
     {
         echo "<pre>";
         print_r($param);
         echo "</pre>";
     }
 }


if(!function_exists('plog'))
{
        /**
     * 自定义日志记录函数
     * 
     * 将数据以JSON格式记录到指定目录的日志文件中，自动按日期和类型分类
     * 
     * @param mixed $data 要记录的数据，可以是任意类型
     * @param string $typeDir 日志分类目录，默认为'common'
     * @param string $logFileName 日志文件名(不含扩展名)，默认为'common'
     * @return int|false 返回写入的字节数，失败时返回false
     */
    function plog($data, $typeDir = 'common',$logFileName = 'common')
    {
        $dir = storage_path('logs/custom'.DIRECTORY_SEPARATOR.$typeDir. DIRECTORY_SEPARATOR.date("Y-m-d") ) . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $dir . $logFileName . ".log";
        $content = date("H:i:s") . " " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\r\n";
        return file_put_contents($file, $content, FILE_APPEND);
    }

}

 if(!function_exists('f'))
 {
     /**
      * 过滤字符串中的标签
      *
      * 此函数用于过滤输入参数中的 HTML 标签。可以处理字符串或数组类型的参数。
      * 
      * @param mixed $param 输入的字符串或数组
      * @param int $type 过滤类型，0 表示转换为 HTML 实体，1 表示去除 HTML 标签
      * @return mixed 返回过滤后的字符串或数组
      */
     function f($param,$type = 0)
     {
         if(is_array($param))
         {
              foreach($param as $key => $value)
              {
                  if(is_array($value))
                  {
                      $value = f($value);
                  }
                  else
                  {
					if(!$type)
					{
						$value = htmlspecialchars($value);
					}
					else
					{
						$value = strip_tags($value);
					}

                  }
              }
         }
         else
         {
            if(!$type)
			{
				$value = htmlspecialchars($value);
			}
			else
			{
				$value = strip_tags($value);
			}
         }
         return $param;
     }
 }

 if(!function_exists('code'))
 {
     /**
      * 处理请求返回的数据
      *
      * @param array $code 配置文件中的代码定义
      * @param array $add 需要手动添加的数据
      * @return array 合并后的结果数组
      *
      * 如果 $code 和 $add 均为 null，返回空数组；
      * 如果 $code 为 null，返回 $add；
      * 如果 $add 为 null，返回 $code；
      * 否则，返回 $code 和 $add 的合并结果。
      */
     function code($code=[],$add=[])
     {
         $resArr = [];
         if(is_null($code)&&is_null($add))
         {
            $resArr = [];
         }
         else if(is_null($code)&&!is_null($add))
         {
            $resArr = $add;
         }
         else if(!is_null($code)&&is_null($add))
         {
            $resArr = $code;
         }
         else
         {
            $resArr = array_merge($code,$add);
         }
        return  $resArr;
     }
 }


if(!function_exists('convertToString'))
{
    /**
     * 将数组或对象数据转换为字符串。
     *
     * 此函数接受一个数组或对象作为输入，并根据输入类型将其转换为字符串。
     * 如果输入是 Eloquent 模型，则使用序列化处理；否则，如果是数组或对象，则使用 JSON 编码。
     *
     * @param mixed $data 要转换的数据，可以是数组、对象或 Eloquent 模型。
     * @return string 转换后的字符串表示。
     */
   
    function convertToString($data)
    {
        if($data instanceof Model || $data instanceof Collection)
        {
            $data = \serialize($data);
        }
        else
        {
            if(\is_array($data)||\is_object($data))
            {
                $data = \json_encode($data);
            }
        }

        return $data;
    }
}

 if(!function_exists('is_serialized'))
{
    /**
     * 检测给定字符串是否为 PHP 序列化格式。
     *
     * 此函数通过检查字符串的格式来判断其是否为序列化数据。
     * 支持的序列化类型包括：数组、对象、字符串、布尔值、整数和浮点数。
     *
     * @param mixed $data 需要检测的字符串数据。
     * @return boolean 如果字符串是序列化的，返回 true；否则返回 false。
     */
    /**
     * 检测字符串是否是序列化的
     *
     * @param [type] $data
     * @return boolean
     */
    function is_serialized( $data )
    {
        $result = 0;
        $data = trim( $data );
        if ( 'N;' == $data )
        $result = 1;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
        $result = 0;
        switch ( $badions[1] )
        {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                $result = 1;
            break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                $result = 1;
            break;
        }
        return $result;
    }
}

/**
 * 请求处理 ++++++++++++++++++++++++++++++++++++++++++
 */

if(!function_exists('httpGet'))
{
   /**
    * 发起 CURL 的 GET 请求
    *
    * @param string $url 请求地址
    * @return mixed 请求的结果
    *
    * @throws Exception 如果请求失败
    */
   function httpGet($url)
   {
       $curl = curl_init();
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
       curl_setopt($curl, CURLOPT_TIMEOUT, 10 );
       curl_setopt($curl, CURLOPT_URL, $url );
       $res = curl_exec($curl);
       curl_close($curl);
       return $res;
   }
}
if(!function_exists('httpPost'))
{
      /**
       * 发起 CURL 的 POST 请求
       *
       * @param string $url 请求的 URL 地址
       * @param array $headers 请求头数组，默认为空
	   * $headers = ['X-TOKEN:'.$this->token,'X-VERSION:1.1.3','Content-Type:application/json','charset=utf-8'];
       * @param mixed $data 请求的数据，默认为 null
       * @return mixed 返回请求结果
       */
    function httpPost($url, $headers=[],$data=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        //设置请求头
        if(is_array($headers) && count($headers) > 0)
        {
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}
/**
 * 请求处理结束--------------------------------------------------

 */

/**
 * 协助处理数组++++++++++++++++++++++++++++
 */

if (!function_exists('array_level')) {
    /**
     * 计算给定数组的维度。
     *
     * 此函数通过调用 total 函数来计算数组的层级，并返回数组的最大维度。
     *
     * @param Array $arr 需要计算的数组
     * @return int 返回数组的维度
     */
    function array_level(array $arr)
    {
        $al = [0];
        total($arr, $al);
        return max($al);
    }
}
if (!function_exists('total')) {
    /**
     * 计算给定数组的维度。
     *
     * 此函数递归地遍历数组，并统计其维度。每当遇到一个数组时，维度计数器增加。
     *
     * @param array $arr 需要计算维度的数组。
     * @param array &$al 用于存储每一层的维度信息的引用数组。
     * @param int $level 当前的维度层级，默认为0。
     * @return void 此函数没有返回值。
     */
    function total($arr, &$al, $level = 0)
    {
        if (is_array($arr)) {

            $level++;

            $al[] = $level;

            foreach ($arr as $v) {
                total($v, $al, $level);
            }
        }
    }
}

if (!function_exists('toArray'))
{
    /**
     * 将给定的数组转换为包含其元素的数组。
     *
     * @param array $array 输入的数组
     * @return array 转换后的数组
     */
   
    function toArray($array)
    {
        if(is_array($array))
        {
            foreach($array as $k => &$v)
            {
                $v =  array($v);
            }
        }

        return $array;
    }
}

/**
 * 协助处理数组结束--------------------------
 */


/**
 * 处理时间开始
 */

if(!function_exists('showTime'))
{
    /**
     * 将时间戳转换为字符串格式
     *
     * @param int $time 时间戳
     * @param bool $bool 是否返回完整的日期时间（包含时分秒）
     * @return string 格式化后的日期字符串
     */

    function showTime($time,$bool = false)
    {
       if($bool)
       {
           return date('Y-m-d H:i:s',$time);
       }
       else
       {
           return date('Y-m-d',$time);
       }

    }
}

/**
 * 处理时间结束
 */

if(!function_exists('em'))
{
    /**
     * 处理捕获的异常并根据需要记录日志或发送通知
     *
     * @param \Throwable $e 捕获的异常对象
     * @param boolean $log 是否将异常信息写入日志，默认为 false
     * @param boolean $notification 是否发送异常通知，默认为 false
     * @return string 返回格式化的异常信息
     */
    
    function em(\Throwable $e,$log = false,$notification = false)
    {
        $time = date('Y-m-d H:i:s',time());
        $msg = "时间:{$time}";
        $msg .= "\r\n异常消息：".($e->message?$e->message:$e->getMessage());
        $msg .= "\r\n错误码:".$e->getCode();
        $msg .= "\r\n文件：".$e->getFile();
        $msg .= "\r\n行号：".$e->getLine();
        $msg .= "\r\n参数:".json_encode(['params' => request()->all()]);

        if($log)
        {
            \file_put_contents(\storage_path()."/logs/exception.log","{$msg}\r\n",\FILE_APPEND);
        }

        if($notification)
        {
            if(env('YouHuJun_Publish'))
            {
                 Notification::route('mail', config('mail.exception.email'))
                 ->notify(new \App\Notifications\LaravelFastApi\V1\SendExceptionMessageNoification($msg));
            }
        }

        return $msg;
    }
}

if(!function_exists('checkId'))
{
    /**
     * 检查给定的值是否为有效的 ID。
     *
     * 该函数使用正则表达式验证输入的 $id 是否仅由数字组成。
     * 如果验证失败，则返回 0。
     *
     * @param mixed $id 要检查的值
     * @return int 返回有效的 ID 或 0
     */
   
    function checkId($id)
    {
        $partten = "/^[0-9]+$/";

        $regexResult = \preg_match($partten,$id);

        if(!$regexResult)
        {
            $id = 0;
        }

        return $id;
    }
}


if(!function_exists('price'))
 {
      /**
       * 格式化价格为带两位小数的字符串
       *
       * @param float|string $price 需要格式化的价格
       *
       * @return string 格式化后的价格字符串
       */
     
     function price($price)
     {
        return number_format($price,'2','.','');
     }
 }
