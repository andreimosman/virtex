<?

require_once("MWebApp.class.php");

class VirtexRelatorio extends MWebApp {


	public function VirtexRelatorio() {
	   parent::MWebApp("etc/virtex.ini",'template/default');
	
	}
	
	
	public function processa($op=null) {
	   // Não faz nada por hora.
	}


}



?>
