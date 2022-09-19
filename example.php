<?php
/* 泉鸣短信接口测试(Hi[at]hechaocheng[dot]com), 构建于2022-09-20 */
date_Default_TimeZone_set("PRC");
header("Content-type: text/html;charset=utf-8");

if($_SERVER["REQUEST_METHOD"] === "POST"){
    require "inc/class.sms.php";
	$telNum = isset($_POST["numbler"]) ? $_POST["numbler"] : "";
	$cfg = array(
		"openid"	=> "74",	// http://dev.quanmwl.com/console
		"apikey"	=> "3745dad31b259cb7b0a0e3727bc8e642"	// http://dev.quanmwl.com/ability_sms
	);

	// 实例化
	//$run = new QuanmSmsSDK();

	// 实例化, 并以字符形式传入openid、apikey
	$run = new QuanmSmsSDK($cfg["openid"], $cfg["apikey"], true);

	// 实例化, 并以数组形式传入openid、apikey
	//$run = new QuanmSmsSDK(array(
	//	"appid" => $cfg["openid"],
	//	"apikey" => $cfg["apikey"],
	//	"ssl" => true
	//));

	// 以字符或数组设置或重置openid、apikey
	//$run->set($cfg["openid"], $cfg["apikey"], true);
	//$run->set(array(
	//	"appid" => $cfg["openid"],
	//	"apikey" => $cfg["apikey"],
	//	"ssl" => true
	//));

	//$res = $run->sms("0", array("code" => "[模拟用]"))->send($telNum);
	$res = $run->sms(
		"0",	// 短信模板ID
		array(
			"code" => rand(100000, 999999) ."[测试用]"	// 随机验证码
		)
	)->send($telNum);	// 发送目标

	$msg = isset($res) && is_array($res) ? "<strong>{$res['tel']}</strong> [{$res['code']}] {$res['msg']}" : $res;
}
?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>短信测试</title>
		<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="applicable-device" content="pc,mobile" />
		<link rel="icon" type="image/x-icon" href="favicon.ico" />
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1" />
		<meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1, maximum-scale=1, user-scalable=no" />
		<style type="text/css">
		form{margin:1em 0;}
		input{outline:none}
		fieldset{margin-top:1em;color:#777;border:1px solid #bbb;font-size:12px}
		</style>
	</head>
	<body>
		<form method="post">
		    <p><?php echo isset($msg) ? $msg : "请输入长度为11位的中国号码";?></p>
			<input type="text" name="numbler" id="numbler" autocomplete="off" x-webkit-speech="false" spellcheck="false" onmouseOver="this.focus();" autofocus="autofocus" onchange="this.value=this.value.replace(/[^\d]+/is, '');" placeholder="请输入长度为11位的中国号码" />
			<input type="submit" value="发送" />
		</form>
		<fieldset>
			<legend>说明</legend>
			可重用实例化的类<br />可重置已实例化的openid、apikey<br />灵活使用短信模板ID
		</fieldset>
	</body>
</html>