<?php

//用户二维数组运算

$gl_client_user = array (
	"stat"=> "OK",
	"type"=> "UonlineUser",
	"roomListUser"=> array (
		array( 
		"client_id"=> 5964,
		"client_name"=> array (
			"roomid"=> "1",
			"chatid"=>"x05287A98",
			"nick"=> "\u6e38\u5ba205287A98",
			"sex"=> "0",
			"age"=> "0",
			"qx"=> "0",
			"ip"=> "113.88.73.252",
			"vip"=> "AA6",
			"color"=> "0",
			"cam"=> "0",
			"state"=> "0",
			"mood"=> ""
		)
	),

	array(
		"client_id"=> 5966,
		"client_name"=> array (
			"roomid"=> "1",
			"chatid"=> "1",
			"nick"=> "\u4e00\u53f7\u4ea4\u6613\u7ba1\u7406\u5458",
			"sex"=> "0",
			"age"=> "0",
			"qx"=> "1",
			"ip"=> "113.88.73.252",
			"vip"=> "AA1",
			"color"=> "2",
			"cam"=> "0",
			"state" => "0",
			"mood"=> "bibi\u6b22\u6b22"
		)
	)
  )
);

//新增加的用户
$adduser = array(
		"client_id"=> 5967,
		"client_name"=> array (
			"roomid"=> "1",
			"chatid"=> "1",
			"nick"=> "\u4e00\u53f7\u4ea4\u6613\u7ba1\u7406\u5458",
			"sex"=> "0",
			"age"=> "0",
			"qx"=> "1",
			"ip"=> "113.88.73.252",
			"vip"=> "AA1",
			"color"=> "2",
			"cam"=> "0",
			"state" => "0",
			"mood"=> "bibi\u6b22\u6b22"
		)
);


$adduser_test = array(
		"client_id"=> 5969,
		"client_name"=> array (
			"roomid"=> "1",
			"chatid"=> "1",
			"nick"=> "\u4e00\u53f7\u4ea4\u6613\u7ba1\u7406\u5458",
			"sex"=> "0",
			"age"=> "0",
			"qx"=> "1",
			"ip"=> "113.88.73.252",
			"vip"=> "AA1",
			"color"=> "2",
			"cam"=> "0",
			"state" => "0",
			"mood"=> "bibi\u6b22\u6b22"
		)
);



//增加一个数组
array_push($gl_client_user['roomListUser'], $adduser);
array_push($gl_client_user['roomListUser'], $adduser_test);
var_dump($gl_client_user);

$out_array = json_encode($gl_client_user, true);
echo "--- json_encode ---\n";
var_dump($out_array);

echo "----------------------------------------------------------\n";
//print_r($out_array);

//检查数组是否存在
if (in_array($adduser_test, $gl_client_user['roomListUser']))
{
	echo "in array 键存在！";
}
else
{
	echo "in array 键不存在！";
}


?>