<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAConfiguracao extends VirtexAdmin {

	public function VAConfiguracao() {
		parent::VirtexAdmin();
	
	
	}
	
	
	public function processa($op=null) {
	
	if($op == "pop"){	
		$this->arquivoTemplate = "configuracoes_pops.html";
	} else if ($op == "nas"){
		$this->arquivoTemplate = "configuracoes_nas.html";
	} else if ($op == "monitor"){
		$this->arquivoTemplate = "configuracoes_monitoramento.html";
	} else if ($op == "cadpop"){
		$this->arquivoTemplate = "cadastro_pop.html";
	} else if ($op == "altpop"){
		$this->arquivoTemplate = "alteracao_pop.html";
	} else if ($op == "cadnas"){
		$this->arquivoTemplate = "cadastro_nas.html";
	} else if ($op == "altnas"){
		$this->arquivoTemplate = "alteracao_nas.html";
	} else if ($op == "cadredes"){
		$this->arquivoTemplate = "cadastro_nas_redes.html";
	} else if ($op == "altredes"){
		$this->arquivoTemplate = "alteracao_nas_redes.html";
	} else if ($op == "listaredes"){
		$this->arquivoTemplate = "lista_nas_redes.html";
	} else if ($op == "cadpopok"){
		$this->arquivoTemplate = "confirma_cadastro_pops.html";
	} else if ($op == "altpopok"){
		$this->arquivoTemplate = "confirma_alteracao_pops.html";
	}  else if ($op == "cadnasok"){
		$this->arquivoTemplate = "confirma_cadastro_pops.html";
	} else if ($op == "altnasok"){
		$this->arquivoTemplate = "confirma_alteracao_pops.html";
	} else if ($op == "cadredesok"){
		$this->arquivoTemplate = "confirma_cadastro_redes.html";
	} else if ($op == "altredesok"){
		$this->arquivoTemplate = "lista_nas_redes.html";
	} else if ($op == "voltaredes"){
		$this->arquivoTemplate = "lista_nas_redes.html";
	} 
}


}



?>
