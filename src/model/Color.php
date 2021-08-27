<?
class Color extends Model{
	public $id;
	public $descr;
	public $idEmpresa;

	public function __construct($data, $prefix=null, $separator="."){
		if(isset($data)){
			$this->unserialize($data, $prefix, $separator);
		}else{
			$this->unserialize(array());
		}
	}

	public function unserialize($data, $prefix=null, $separator="."){
		$success = true;
		try{
			$this->id = $this->val("id", $data, $prefix, $separator);
			$this->descr = $this->val("descr", $data, $prefix, $separator);
			$this->idEmpresa = $this->val("idEmpresa", $data, $prefix, $separator);
		}catch(Exception $e){
			$success = false;
		}
		return $success;
	}

}

?>