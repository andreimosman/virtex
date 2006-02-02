<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAAdministrador extends VirtexAdmin {

	public function VAAdministrador() {
		parent::VirtexAdmin();
	
	
	}
	
	// processa os links
	public function processa($op=null) {
	
	if($op == "altera"){	
		$this->arquivoTemplate = "administrador_alterarsenha.html";
	} else if ($op == "cadastro"){
		$this->arquivoTemplate = "administrador_cadastro.html";
	} else if ($op == "direitos"){
		$this->arquivoTemplate = "administrador_direitos.html";
	} else if ($op == "lista"){
		$this->arquivoTemplate = "administrador_listagem.html";
		}
	  
}
}
?>
