<?
class App{
	public $config;
	public $langDefault;
	public $langSeparator;

	function __construct($path){
		$this->config = new Properties($path);
		$this->langDefault = $this->config->prop("system.lang.default");		
		$this->langSeparator = $this->config->prop("system.lang.file.separator");	
	}

	function getRequest(){
		return $this->config->getProp("request.params.method")=="POST"?$_POST:$_GET;
	}

	function getLangDefaultFile($lang=null, $settings){
		$file = "";
		$file = $settings->prop("system.translate.path").(isset($lang)?$lang:$settings->prop("system.lang.default")).DIRECTORY_SEPARATOR.$settings->prop("system.translate.default");
		return $file;
	}

	function getLangFile($param, $lang=null){
		$directory = $this->config->prop("system.path").$this->config->prop("system.lang.library");
		$file = $this->removeExt(@basename($param));
		$lang = isset($lang)?$lang:$this->langDefault;
		return $directory.$lang.DIRECTORY_SEPARATOR.$file.".xml";
	}

	function removeExt($str){
		$str = explode(".php", $str);
		return $str[0];
	}
}
?>