<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VARelatorio extends VirtexAdmin {

	public function VARelatorio() {
		parent::VirtexAdmin();
	
	
	}
		
	public function processa($op=null) {
	
	if($op == "fatura"){	
		$this->arquivoTemplate = "relatorio_fatura.html";
	} else if ($op == "cortesia"){
		$this->arquivoTemplate = "relatorio_cortesia.html";
	} else if ($op == "geral"){
		$this->arquivoTemplate = "relatorio_geral.html";
	} else if ($op == "estat"){
		$this->arquivoTemplate = "relatorio_estat.html";
	} else if ($op == "filtro"){
		$this->arquivoTemplate = "relatorio_filtro.html";
	} else if ($op == "ap"){
		$this->arquivoTemplate = "relatorio_ap.html";
	} else if ($op == "pop"){
		$this->arquivoTemplate = "relatorio_pop.html";
	} else if ($op == "nas"){
		$this->arquivoTemplate = "relatorio_nas.html";
	}	
	
}


}



?>
