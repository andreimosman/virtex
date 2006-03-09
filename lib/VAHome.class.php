<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAHome extends VirtexAdmin {

	public function VAHome() {
		parent::VirtexAdmin();
	
		$adm = $this->admLogin->obtemAdmin();
		$this->tpl->atribui("admin",$adm);	



		$this->arquivoTemplate = "home.html";
	
	
	}
	
	
	public function processa($op=null) {
	
		if( $op == "cad" ) {
		   
		   
		   
		   
		   
		   
		   $this->arquivoTemplate = "arquivoTal.html";
		   
		}
	
	
	
	
	}

public function __destruct() {
      	parent::__destruct();
}

}



?>
