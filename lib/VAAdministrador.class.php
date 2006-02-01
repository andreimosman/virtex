<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAAdministrador extends VirtexAdmin {

	public function VAAdministrador() {
		parent::VirtexAdmin();
	
	
	}
	
	
	public function processa($op=null) {
	
	if($op == "altera"){	
		$this->arquivoTemplate = "administrador_alterarsenha.html";
	} else if ($op == "cadastro"){
		$this->arquivoTemplate = "administrador_cadastro.html";
	}

}
}
?>
