<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );
// Criacao da classe para uso no sistema de cobrança.
class VASuporte extends VirtexAdmin {

	public function VASuporte() {
		parent::VirtexAdmin();
	
	
	}
	
// metodo para pegar as propriedadas enviadas via menu na interface.
	public function processa($op=null) {	
	if($op == "graf"){	
		$this->arquivoTemplate = "suporte_grafico.html";
	} else if ($op == "log"){
		$this->arquivoTemplate = "suporte_logradius.html";
	} else if ($op == "monit"){
		$this->arquivoTemplate = "suporte_monitoramento.html";
	} else if ($op == "calc"){
		$this->arquivoTemplate = "suporte_calculadoraip.html";
	} else if ($op == "arp"){
		$this->arquivoTemplate = "suporte_arp.html";
		}
}

public function __destruct() {
      	parent::__destruct();
}



}



?>
