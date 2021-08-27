<?
	class Artefacto extends Model implements IModel{
		public $nombre;
		public $clasificacion;
		public $list;

		public function init(){
			$this->clasificacion = new Clasificacion();
			$this->periodos = array(new Periodo());
		}
	}
?>