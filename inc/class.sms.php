<?php
class QuanmSmsSDK {
	/* 泉鸣短信接口类(Hi[at]hechaocheng[dot]com), 构建于2022-09-20 */
	private $appId		= "";	// 泉明开放平台openId
	private $apiKey		= "";	// 泉明开放平台apiKey
	private $url		= "://dev.quanmwl.com/v1/sms";	// 请求地址
	private $data		= array();	// POST数据
	private $stateCode	= array(	// 回调信息
		200 => '短信发送成功',
        201 => '表单信息或接口信息有误',
        202 => '信息重复',
        203 => '服务器异常，请稍后重试',
        204 => '找不到数据',
        205 => '本次请求不安全',
        206 => '接口版本过低',
        207 => '余额不足',
        208 => '验签失败',
        209 => '功能被禁用',
        210 => '账户被禁用',
        211 => '参数过长',
        212 => '权限不足',
        213 => '参数调用状态异常',
        214 => '版本过高',
        215 => '内容受限',
        216 => '内容违规',
        '???' => '严重未知错误，请联系服务提供商'
	);
	
	// 构析初始化时可传入 appId、apiKey
	public function __construct($appId = "", $apiKey = "", $ssl = true){
		$this->set($appId, $apiKey, $ssl);
		return $this;
	}

	public function __destruct(){

	}

	// 短信内容,需传入 短信模板ID & 短信内容
	public function sms($templateId = "0", $message = ""){
		$this->data = array(
			"openID"		=> $this->appId,
			"model_args"	=> json_encode($message),
			"model_id"		=> $templateId
		);
		return $this;
	}

	// 执行发送动作, 需传入目标号码
	public function send($tel = ""){
		if(!preg_match("/^\d+$/is", $this->appId) || strlen($this->appId) < 1)				return $this->msg("openId");
		if(!preg_match("/^[0-9a-f]+$/is", $this->apiKey) || strlen($this->apiKey) != 32)	return $this->msg("apiKey");
		if(!preg_match("/^\d+$/is", $tel) || strlen($tel) != 11)							return $this->msg("mobile number");
		if(!function_exists("curl_init"))													return $this->msg("cURL");
		
		$this->data["tel"]	= $tel;
		$this->data["sign"]	= md5(
			$this->appId .
			$this->apiKey .
			$tel .
			$this->data["model_id"] .
			$this->data["model_args"]
		);

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        $response = curl_exec($ch);
		$error = curl_error($ch);
        curl_close($ch);

        $response = json_decode($response, true);
		return array_key_exists($response["state"], $this->stateCode) ?
			array(
				"code" => $response["state"],
				"tel" => $tel,
				"msg" => $response["mess"],
				"err" => ""
			) :
			array(
				"code" => "-1",
				"tel" => $tel,
				"msg" => "请求结果错误，请联系开发人员核实",
				"err" => $error
			);
	}

	// 消息输出
	public function msg($msg = ""){
	    return sprintf("Please check and confirm that the <strong>%s</strong> is correct.", $msg);
	}

	// 设置openid、apikey
	public function set($appId = "", $apiKey = "", $ssl = true){
		$this->url = preg_replace("/^(https|http)/is", "", $this->url);
		if(gettype($appId) === "string" && gettype($apiKey) === "string"){
			$this->appId	= $appId;
			$this->apiKey	= $apiKey;
			$this->url		= ($ssl == false ? "http" : "https") . $this->url;
		}elseif(gettype($appId) === "array"){
			$this->appId	= isset($appId["appid"]) ? $appId["appid"] : "";
			$this->apiKey	= isset($appId["apikey"]) ? $appId["apikey"] : "";
			$this->url		= ((isset($appId["ssl"]) ? $appId["ssl"] : true) == false ? "http" : "https") . $this->url;
		}else exit($this->msg("parameter"));
		return $this;
	}
}
?>