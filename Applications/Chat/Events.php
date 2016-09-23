<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;

class Events
{
   
   /**
    * 有消息时
    * @param int $client_id
    * @param mixed $message
    */
   public static function onMessage($client_id, $message)
   {
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";
        
        // 客户端传递的是json数据
        // $message_data = json_decode($message, true);
        $message_data = json_encode($message, true);

	 
        if(!$message_data)
        {

			echo "run test 8888\n";
            return ;
        }
        
		var_dump($message_data);

		$n = strpos($message,'=M=');//寻找位置
		if ($n) $stroption = substr($message, 0, $n);//删除后面

		echo ($stroption); // 显示发来的请求字符串
		if ($stroption == "Login")
        {

			//echo ("------in login-------\n");
			//处理字符分配到数组
			$res=explode("|",$message);
			//for($i=0;$i<11;$i++)
			 //echo "$res[$i]\n";

			$res[0] = '1';
			$roomid = '"roomid":"'.$res[0].'",';
			$chatid =  '"chatid":"'.$res[1].'",';
			$nick = '"nick":"'.$res[2].'",';

			//echo "\n send nick = ".$nick."\n";

			$sex= '"sex":"'.$res[3].'",';
			$age= '"age":"'.$res[4].'",';
			$qx='"qx":"'.$res[5].'",';
			$ip= '"ip":"'.$res[6].'",';
			$vip='"vip":"'.$res[7].'",';
			$color= '"color":"'.$res[8].'",';
			$cam= '"cam":"'.$res[9].'",';
			$state= '"state":"'.$res[10].'",';
			$mood= '"mood":"'.$res[11].'"';

			$login_info = '{'.$roomid.$chatid.$nick.$sex.$age.$qx.$ip.$vip.$color.$cam.$state.$mood.'}';
			
			/**
			$login_info = array('roomid'=>$res[0], 
				'chatid'=>$res[1],
				'nick'=>$res[2],
				'sex'=>$res[3],
				'age'=>$res[4],
				'qx'=>$res[5],
				'ip'=>$res[6],
				'vip'=>$res[7],
				'color'=>$res[8],
				'cam'=>$res[9],
				'state'=>$res[10],
				'mood'=>$res[11]
			);
			*/

			$login_info_send = json_decode($login_info, true);
			
			//打包成登陆信息
			$loginstr = '{"stat":"OK","type":"Ulogin","Ulogin":'.$login_info.'}';
			
			$message_data = $loginstr;

			 $message_data = json_decode($message_data, true);

			//var_dump($message_data);

		    //var_dump($message_data['type']);
		
	   }


	   if ($stroption == "SendMsg")
	   {

			//echo ("------in SendMsg-------\n");
			//处理字符分配到数组
			$res=explode("|",$message);
			//for($i=0;$i<11;$i++)
			 //echo "$res[$i]\n";

			$gl_chatidstr = '"ChatId":"'.$client_id.'",';

			$res[0] = 'ALL';
			$ToChatId = '"ToChatId":"'.$res[0].'",'; 
			$IsPersonal = '"IsPersonal":"'.$res[1].'",';
			$Style =  '"Style":"'.$res[2].'",';
			$Txt = '"Txt":"'.$res[3].'"';

			$send_info = '{'.$gl_chatidstr.$ToChatId.$IsPersonal.$Style.$Txt.'}';

			$sendmsg_send_info = json_decode($send_info, true);

			echo "\n";
			$message_data = '{"stat":"OK","type":"UMsg","UMsg":{'.$gl_chatidstr.$ToChatId.$IsPersonal.$Style.$Txt.'}}';

			$message_data = json_decode($message_data, true);
		
	   }



        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            // 客户端回应服务端的心跳
            case 'ping':
				echo "ping\n";
                return;
            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'Ulogin':

				echo "--------------------------- Ulogin -------------------\n";
                // 判断是否有房间号
                if(!isset($message_data['Ulogin']['roomid']))
                {
                    throw new \Exception("\$message_data['roomid'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }



				// 把房间号昵称放到session中
				/*
                $room_id = $message_data['Ulogin']['roomid'];
                $client_name = htmlspecialchars($message_data['Ulogin']['nick']);
                $_SESSION['room_id'] = $room_id;
                $_SESSION['client_name'] = $client_name;
                
                // 存储到当前房间的客户端列表
                $all_clients = self::addClientToRoom($room_id, $client_id, $client_name);
                
                // 整理客户端列表以便显示
                $client_list = self::formatClientsData($all_clients);
                
                // 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx} 
                // $new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'client_name'=>htmlspecialchars($client_name), 'client_list'=>$client_list, 'time'=>date('Y-m-d H:i:s'));
                $client_id_array = array_keys($all_clients);
                Gateway::sendToAll(WebSocket::encode(json_encode($new_message)), $client_id_array);

				
                return;

                */
                
                // 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx} 
                //$new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'client_name'=>htmlspecialchars($client_name), 'time'=>date('Y-m-d H:i:s'));
				
				//发送登陆信息
				$new_message = array('stat'=>'OK','type'=>$message_data['type'], 'Ulogin'=>$login_info_send );
         

                Gateway::sendToCurrentClient(json_encode($new_message));
				echo "----------------- Login ----------------\n";
				var_dump($new_message);
				echo "---------------------------------\n";


				//发送在线显示用户列表信息
				$list_onlineusr =array('client_id'=>$client_id, 'client_name'=>$login_info_send);
				$new_message_listuser = array('stat'=>'OK','type'=>'UonlineUser', 'roomListUser'=>array($list_onlineusr) );


                Gateway::sendToCurrentClient(json_encode($new_message_listuser));
				echo "--------------- UonlineUser ------------------\n";
				var_dump($new_message_listuser);
				echo "---------------------------------\n";
				

                return;
			
			
			case 'UMsg':


                //echo "\n";
				//$message = '{"stat":"OK","type":"UMsg","UMsg":{"ChatId":"0x423A87E","ToChatId":"ALL","IsPersonal":"false","Style":"font-weight:;font-style:;text-decoration:;color:rgb(0,0,0);font-family:;font-size:12pt","Txt":"fcb960c4_+_ok"}}';
			    //echo $message;

				$new_message = array('stat'=>'OK','type'=>'UMsg', 'UMsg'=>$sendmsg_send_info );
	
			    //Gateway::sendToCurrentClient($message);
				Gateway::sendToCurrentClient(json_encode($new_message));
				echo "--------------- UMsg ------------------\n";
				var_dump($new_message);
				echo "---------------------------------\n";

				return;

                
            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                }
                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];
                
                // 私聊
                if($message_data['to_client_id'] != 'all')
                {
                    $new_message = array(
                        'type'=>'say',
                        'from_client_id'=>$client_id, 
                        'from_client_name' =>$client_name,
                        'to_client_id'=>$message_data['to_client_id'],
                        'content'=>"<b>对你说: </b>".nl2br(htmlspecialchars($message_data['content'])),
                        'time'=>date('Y-m-d H:i:s'),
                    );
                    Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
                    $new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
                    return Gateway::sendToCurrentClient(json_encode($new_message));
                }
                
                $new_message = array(
                    'type'=>'say', 
                    'from_client_id'=>$client_id,
                    'from_client_name' =>$client_name,
                    'to_client_id'=>'all',
                    'content'=>nl2br(htmlspecialchars($message_data['content'])),
                    'time'=>date('Y-m-d H:i:s'),
                );
                return Gateway::sendToGroup($room_id ,json_encode($new_message));
        }
   }
   
   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id)
   {
       // debug
       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
       
       // 从房间的客户端列表中删除
       if(isset($_SESSION['room_id']))
       {
           $room_id = $_SESSION['room_id'];
           $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
           Gateway::sendToGroup($room_id, json_encode($new_message));
       }
   }
  
}
