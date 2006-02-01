<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAClientes extends VirtexAdmin {

	public function VAClientes() {
		parent::VirtexAdmin();
	
	
	}
	
	
	public function processa($op=null) {
	
	if($op == "pesquisa"){	
		$this->arquivoTemplate = "search_clientes.html";
	} else if ($op == "cadastro"){
		$this->arquivoTemplate = "cadastro_clientes.html";
	} else if ($op == "eliminar"){
		$this->arquivoTemplate = "eliminar_cliente.html";
	}	
	
}


}



?>
