<?

require_once("MWebApp.class.php");

class VirtexAdmin extends MWebApp {


	public function VirtexAdmin() {
	   parent::MWebApp("etc/virtex.ini",'template/default');
	
	}
	
	
	public function processa($op=null) {
	   // Não faz nada por hora.
	}


}



?>
