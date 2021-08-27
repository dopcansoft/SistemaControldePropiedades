<?
class Properties{
	public $data;
	
	function __construct($pathFile){
		if(isset($pathFile)){
			$sourceXml = array();
			if(is_array($pathFile)&&count($pathFile)>0){
				foreach($pathFile as $file){
					$sourceXml[] = simplexml_load_file($file);
				}	
			}else{
				$sourceXml[] = simplexml_load_file($pathFile);
			}

			$this->data = array();
			foreach($sourceXml as $xml){
				foreach($xml->entry as $nodo){
					$this->data[(string)$nodo->attributes()["key"]] = (string)trim($nodo);
				}
			}
		}	
	}

	function prop($prop){
		return isset($this->data[$prop])?$this->data[$prop]:"";
	}

	function getProp($property){
		return isset($this->data[$property])?$this->data[$property]:"";
	}

	function getAllProp(){
		return $this->data;
	}
}
?>