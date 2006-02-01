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
	}	
	
}


}



?>
