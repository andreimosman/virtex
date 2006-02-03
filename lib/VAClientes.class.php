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
	} else if ($op =="clientemod"){
		$this->arquivoTemplate = "ficha_cliente.html";
	} else if ($op == "clientedisc"){
		$this->arquivoTemplate = "cliente_discado.html";
	} else if ($op == "clienteAdisc"){
		$this->arquivoTemplate = "cliente_discado_altera.html";
	} else if ($op == "clienteAmail"){
		$this->arquivoTemplate = "cliente_email_altera.html";
	} else if ($op == "clienteAhosp"){
		$this->arquivoTemplate = "cliente_hospedagem_altera.html";
	} else if ($op == "clientehosp"){
		$this->arquivoTemplate = "cliente_hospedagem.html";
	} else if ($op == "clientebl"){
		$this->arquivoTemplate = "cliente_bandalarga.html";
	} else if ($op == "clienteAbl"){
		$this->arquivoTemplate = "cliente_bandalarga_altera.html";
	} else if ($op == "clienteCob"){
		$this->arquivoTemplate = "cliente_cobranca.html";
	} else if ($op == "clienteCobHist"){
		$this->arquivoTemplate = "cliente_cobranca_historico.html";
	} else if ($op == "clienteCobContr"){
		$this->arquivoTemplate = "cliente_cobranca_contratos.html";
	} else if ($op == "clienteAcob"){
		$this->arquivoTemplate = "cliente_cobranca_alteracob.html";
	} else if ($op == "clienteAcontr"){
		$this->arquivoTemplate = "cliente_cobranca_alteracontr.html";
	} else if ($op == "clienteContrExibe"){
		$this->arquivoTemplate = "cliente_contrato.html";
	} else if ($op == "clienteContr"){
		$this->arquivoTemplate = "cliente_novo_contrato.html";
	} else if ($op =="clnada"){
		$this->arquivoTemplate = "cliente_cobranca_nada.html";
	} else if ($op == "clBl"){
		$this->arquivoTemplate = "cliente_cobranca_bl.html";
		}else if ($op == "clDisc"){
		$this->arquivoTemplate = "cliente_cobranca_disc.html";
		} else if ($op == "clHosp"){
		$this->arquivoTemplate = "cliente_cobranca_hosp.html";
		} 





	}


}



?>
