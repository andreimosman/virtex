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
	} 
	
}


}



?>
