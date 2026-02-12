<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-16 10:50:14
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-28 19:14:48
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Websocket;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

use App\Facades\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacade;

/**
 * @see \App\Facades\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacade
 */
class PhoneSocketFacadeService
{
   public function test()
   {
       echo "PhoneSocketFacadeService test";
   }

	//定义消息类型码和处理函数的映射关系
	//心跳
	const MSG_TYPE_PING = 10;
	//登录
	const MSG_TYPE_LOGIN = 20;
	//聊天
	const MSG_TYPE_CHAT = 30;
	//业务
	const MSG_TYPE_NOTICE = 40;
	//服务端主动发起
	const MSG_TYPE_SERVER = 50;

	protected $handlers = [];

	//websocket server 对象sss
	private $server;

	protected $port;

	//反向映射
	protected $fdToUserId = [];

	//认证用户i
	protected $onlineFds = [];

	public function __construct()
	{
		$this->handlers = [
            self::MSG_TYPE_PING => 'handlePing',
            self::MSG_TYPE_LOGIN => 'handleLogin',
            self::MSG_TYPE_CHAT => 'handleChat',
            self::MSG_TYPE_NOTICE => 'handleNotice',
            self::MSG_TYPE_SERVER => 'handleService'
        ];
	}

	/**
	* 初始化
	*/
	public function init()
	{
		$this->port = config('custom.websocket_port');
		echo "端口号:{$this->port}\r\n";
		
		$this->server = new Server('0.0.0.0', $this->port);
		//监听WebSocket连接打开事件
		$this->server->on('Open', function (Server $webSocketServer, Request $request)
		{
			//p($frame->fd);
			echo "新客户端连接成功！fd: {$request->fd}\n";

			$message = ['code'=>0,'type'=>'connect','msg'=>'websocket server connected'];
			
			$webSocketServer->push($request->fd, \json_encode($message));
		});

		//监听WebSocket消息事件
		$this->server->on('Message', function (Server $webSocketServer, Frame $frame)
		{
				echo "收到来自 fd:{$frame->fd} 的原始消息: {$frame->data}\n";

				// 前端统一用 Json.stringify() 后端解析为数组
				$data = \json_decode($frame->data,true);

				//Log::debug(['$data'=>$data]);

				if (json_last_error() !== JSON_ERROR_NONE) {
					$errorMessage = ['code'=>10000,'type' => 'error','msg'=>'无效的消息格式'];
					$webSocketServer->push($frame->fd, \json_encode($errorMessage));
					return;
				}

				if(!isset($data['type'])){
					$errorMessage = ['code'=>11000,'type' => 'error','msg'=>'消息格式错误'];
					$webSocketServer->push($frame->fd, \json_encode($errorMessage));
					return;

				}

				['type' => $type] = $data+ [];

				//根据类型码查找并执行对应的处理函数
				if (isset($this->handlers[$type])) {
					$handlerFunction = $this->handlers[$type];
					// 调用处理函数，并传递所需参数
					 $this->$handlerFunction($webSocketServer, $frame, $data);
				} else {
					// 处理未知的消息类型
					echo "未知的消息类型码: {$type} 来自 fd:{$frame->fd}\n";
					$errorMessage = ['code'=>15000,'type' => 'error','msg'=>'未知的消息码'];
					$webSocketServer->push($frame->fd, json_encode($errorMessage));
				}
		});

		//监听WebSocket连接关闭事件
		$this->server->on('Close', function (Server $webSocketServer,int  $fd)
		{
			echo "客户端 fd:{$fd} 已断开连接\n";

			// 如果这个 fd 曾经登录过
			if (isset($this->fdToUserId[$fd])) {

				$userId = $this->fdToUserId[$fd];
				// 从本地反向映射中删除
				unset($this->fdToUserId[$fd]);
				// 删除认证用户
				unset($this->onlineFds[$fd]);
				PhoneSocketUserFacade::deleteSocketUser($userId);
			}
		});

		

		//监听请求 配合后端主动推送消息
		$this->server->on('Request', function ($request, $response)
		{
			$data = $request->post;

			if(isset($data['send_type']) && !is_null($data['send_type']))
			{
				// $msg = $request->post['message'];

				$message = \json_encode($data);

				if($data['send_type'] == 10)
				{
					// 接收http请求从get获取message参数的值，给用户推送
					// $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
					// 便利维护的认证用户
					//$this->onlineFds 是一个关联数组，其结构是 [fd => true]
					foreach (array_keys($this->onlineFds) as $fd)
					{
						echo "All-Request\n";
						// 需要先判断是否是正确的websocket连接，否则有可能会push失败,留作参考
						/* if ($this->server->isEstablished($fd))
						{
							$this->server->push($fd,  $message);
						} */
						$this->server->push($fd, $message);
					}
				}

				//只对特定发送
				if($data['send_type'] == 20)
				{
					// 增加对 user_id 的检查
					if (!isset($data['user_id'])) {
						Log::warning('主动推送失败：缺少 user_id 参数');
						return;
					}
					$fd = Redis::hget('socket:socket',$data['user_id']);

					//p($fd);

					if($fd)
					{
						// 需要先判断是否是正确的websocket连接，否则有可能会push失败
						if ($this->server->isEstablished($fd))
						{
							$this->server->push($fd, $message);
						}else {
							// 如果 fd 存在但连接已断开，说明 Redis 中的数据与实际连接状态不一致
							// 这是一个很好的机会来清理 Redis
							Log::warning("用户 {$data['user_id']} 的 fd {$fd} 已不存在，正在清理 Redis 记录。");
							Redis::hdel('socket:socket', $data['user_id']);
						}
					}
					else
					{
						Log::debug(['fd'=>"用户 {$data['user_id']} 未登录或已断开连接"]);
					}
				}

			}

		});

		//只有非 WebSocket 连接关闭时才会触发该事件。
		$this->server->on('Disconnect',function(Server $webSocketServer,int $fd)
		{
			echo "客户端 fd:{$fd}链接已关闭\n";
		});

		

		echo "WebSocket 服务器已启动，监听 0.0.0.0:{$this->port}\n";
		$this->server->start();
	}

	private function validateAuth(Server $webSocketServer, Frame $frame, array $data): bool
	{
		if(!isset($data['token'])){
			$errorMessage = ['code'=>12000,'type' => 'error','msg'=>'缺少授权'];
			$webSocketServer->push($frame->fd, \json_encode($errorMessage));
			return false;
		}

		if(!isset($data['user_id'])){
			$errorMessage = ['code'=>14000,'type' => 'error','msg'=>'缺少用户'];
			$webSocketServer->push($frame->fd, \json_encode($errorMessage));
			return false;
		}

		return true;
	}

	//验证登录
	private function validateLogin(Server $webSocketServer, Frame $frame, array $data): bool{

		if(!isset($data['token'])){
			$errorMessage = ['code'=>12000,'type' => 'error','msg'=>'缺少授权'];
			$webSocketServer->push($frame->fd, \json_encode($errorMessage));
			return false;
		}

		if(!isset($data['user_id'])){
			$errorMessage = ['code'=>14000,'type' => 'error','msg'=>'缺少用户'];
			$webSocketServer->push($frame->fd, \json_encode($errorMessage));
			return false;
		}
		
		if (!PhoneSocketUserFacade::checkSocketUserIsLogin($data['user_id'], $data['token'])) {
		    $errorMessage = ['code'=>60030,'type' => 'error','msg'=>'授权无效或已过期'];
		    $webSocketServer->push($frame->fd, \json_encode($errorMessage));
		    return false;
		}

		return true;
	}

	/**
	 * 心跳
	 */
	protected function handlePing(Server $webSocketServer, Frame $frame, array $data): void
	{
		echo "收到来自 fd:{$frame->fd} 的心跳包\n";
		$response = ['type' => 'ping']; // 响应类型可以还是字符串，也可以是数字
		$webSocketServer->push($frame->fd, json_encode($response));
		echo "已向 fd:{$frame->fd} 发送心跳响应\n";
	}

	/**
	 * 登录
	 */
	protected function handleLogin(Server $webSocketServer, Frame $frame, array $data): void
	{
		echo "收到来自 fd:{$frame->fd} 的登录请求\n";

		if (!$this->validateAuth($webSocketServer, $frame, $data)) {
			return; // 验证失败，直接返回
		}

		//过滤完成,开始处理业务逻辑
		['type'=>$type,'token'=>$token,'user_id'=>$user_id] = $data+ [];

		// 检查是否已有登录
		$oldFd = Redis::hget('socket:socket', $user_id);

		// 确保 $oldFd 是整数，并且不是当前的连接
		if ($oldFd && (int)$oldFd !== $frame->fd) {
			$oldFd = (int)$oldFd; // 确保是整数

			// 【关键】在推送前，检查旧连接是否真的还存在
			if ($this->server->exist($oldFd)) {
				echo "检测到旧连接 fd={$oldFd} 仍存在，准备将其踢下线...\n";

				// 1. 向旧连接推送“被踢下线”的通知
				$this->server->push($oldFd, json_encode([
					'type' => 'kick_off',
					'reason' => 'New device login',
					'message' => '您的账号在另一台设备登录，已被强制下线。'
				]));

				// 2. 稍作延迟后再关闭连接，确保消息能成功发送
				swoole_timer_after(100, function() use ($oldFd, $user_id) {
					if ($this->server->exist($oldFd)) { // 再次检查，以防万一
						$this->server->close($oldFd);
						echo "已强制关闭旧连接 fd={$oldFd}\n";
					}
				});

			} else {
				echo "旧连接 fd={$oldFd} 已不存在，无需推送通知，直接清理记录。\n";
			}

			// 3. 无论旧连接是否存在，都清理 Redis 和本地的旧映射
			Redis::hdel('socket:user_fds', $user_id);
			if (isset($this->fdToUserId[$oldFd])) {
				unset($this->fdToUserId[$oldFd]);
			}
			if (isset($this->onlineFds[$oldFd])) {
				unset($this->onlineFds[$oldFd]);
			}
		}
		$response = ['code'=>60030,'msg'=>"user_id-{$user_id}:socket用户登录失败",'type'=>'error','error'=>'socketUserLoginError'];

		$checkResult = PhoneSocketUserFacade::checkSocketUserIsLogin($user_id,$token);

		if($checkResult)
		{
			$saveResult =  PhoneSocketUserFacade::saveSocketUser($user_id,$frame->fd);

			if($saveResult)
			{
				Redis::hset('socket:user_fds', $user_id, $frame->fd);
				$this->fdToUserId[$frame->fd] = $user_id;
				$this->onlineFds[$frame->fd] = true;
				$response = ['code'=>0,'type'=>'login','msg'=>"user_id-{$user_id}:socket用户登录成功"];
			}
		}

		$webSocketServer->push($frame->fd, json_encode($response));
	}

	/**
	 * 聊天消息
	 */
	protected function handleChat(Server $webSocketServer, Frame $frame, array $data): void
	{


		echo "收到来自 fd:{$frame->fd} 的聊天消息\n";

		if (!$this->validateLogin($webSocketServer, $frame, $data)) {
			return; // 验证失败，直接返回
		}

		if (!isset($data['content'])) {
			$response = ['code'=>15000,'type' => 'error','msg'=>'聊天内容为空'];
			$webSocketServer->push($frame->fd, json_encode($response));
			return;
		}

		//过滤完成,开始处理业务逻辑
		['type'=>$type,'token'=>$token,'user_id'=>$user_id,'content'=>$content] = $data+ [];

		$response = [
			'code'=>0,
			'type' => 'chat',
			'msg' => '服务器收到了你的消息：' . $data['content']
    	];

		$webSocketServer->push($frame->fd, json_encode($response));
	}

	/**
	 * 业务消息
	 */
	protected function handleNotice(Server $webSocketServer, Frame $frame, array $data):void
	{
		echo "收到来自 fd:{$frame->fd} 的业务消息\n";

		if (!$this->validateLogin($webSocketServer, $frame, $data)) {
			return; // 验证失败，直接返回
		}


		//过滤完成,开始处理业务逻辑
		['type'=>$type,'token'=>$token,'user_id'=>$user_id] = $data+ [];

		$response = [
			'code'=>0,
			'type' => 'notice',
			'msg' => '服务器收到了你的业务消息'
    	];

		$webSocketServer->push($frame->fd, json_encode($response));
	}

   /**
    *
    *
    * @param  [type] $data
		$data = [];
		$data['user_id'] = $user->id;
		//10 所有人 20只对某一个用户 30 对某一些用户
		$data['send_type'] = 10;
		$data['code'] = 0;
		//事件对应操作 10表示存储socket用户登录id
		$data['event'] = 10;
		//返回信息
		$data['msg'] = '主动推送消息'.date("Y-m-d H:i:s");
		PhoneSocketFacade::curl($data);
    */
    public function curl($data)
    {
		$this->port = config('custom.websocket_port');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:{$this->port}");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
		
		if ($result === false) {
			$error = curl_error($curl);
			Log::error("调用 WebSocket 服务器推送失败: {$error}");
		}

		curl_close($curl);
    }

}
