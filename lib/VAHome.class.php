<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAHome extends VirtexAdmin {

	public function VAHome() {
		parent::VirtexAdmin();
	
		$adm = $this->admLogin->obtemAdmin();
		$this->tpl->atribui("admin",$adm);	

			$privilegio_cliente = 'sim';
			$privilegio_radius = 'sim';
			$privilegio_config = 'sim';
			$privilegio_cobranca = 'sim';
			$privilegio_gravar_cliente = 'sim';
			$privilegio_registro  = 'sim';

			if( ! $this->privPodeLer("_SUPORTE") ) {
				$privilegio_radius = 'nao';
			}
			if( ! $this->privPodeLer("_CLIENTES") ) {
				$privilegio_cliente = 'nao';
			}
			if( ! $this->privPodeLer("_CONFIG_PREFERENCIAS") ) {
				$privilegio_config = 'nao';
			}
			if( ! $this->privPodeLer("_COBRANCA") ) {
				$privilegio_cobranca = 'nao';
			}
			if ( ! $this->privPodeGravar("_CLIENTES") ) {
				$privilegio_gravar_cliente  = 'nao';
			}
			if( ! $this->privPodeLer("_CONFIG_REGISTRO") ) {
				$privilegio_registro  = 'nao';
			}

			$this->tpl->atribui("privilegio_config",$privilegio_config);
			$this->tpl->atribui("privilegio_registro",$privilegio_registro);
			$this->tpl->atribui("privilegio_gravar_cliente",$privilegio_gravar_cliente);
			$this->tpl->atribui("privilegio_cobranca",$privilegio_cobranca);
			$this->tpl->atribui("privilegio_radius",$privilegio_radius);
			$this->tpl->atribui("privilegio_cliente",$privilegio_cliente);

		$this->arquivoTemplate = "home.html";
	
	}
	
	
	public function processa($op=null) {
	
		if( $op == "home" ) {

				$licenca = $this->lic->obtemLicenca();
				$hoje = Date("Y-m-d");
				//echo $hoje;
				if($licenca["geral"]["expira_em"] < $hoje && $licenca["geral"]["congela_em"] > $hoje){
					$status = "expirado";
				}else if ($licenca["geral"]["congela_em"] < $hoje ){
					$status = "congelado";
				}else if ($licenca["geral"]["congela_em"] > $hoje && $licenca["geral"]["expira_em"] > $hoje){
					$status = "ativo";
				}

			$sSQL  = "SELECT ";
			$sSQL .= "f.id_cliente_produto, cl.nome_razao, f.data_renovacao, f.valor_contrato, p.nome, ";
			$sSQL .= " c.id_cliente ";
			$sSQL .= "FROM ";
			$sSQL .= " cbtb_cliente_produto c, cltb_cliente cl, cbtb_contrato f, prtb_produto p ";
			$sSQL .= "WHERE ";
			$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
			$sSQL .= "AND p.tipo = f.tipo_produto ";
			$sSQL .= "AND f.data_renovacao < now() + interval '30 day' ";
			$sSQL .= "AND cl.id_cliente = c.id_cliente  ";
			$sSQL .= "AND c.id_cliente_produto = f.id_cliente_produto ";
			$sSQL .= "AND p.id_produto = c.id_produto ";
			$sSQL .= "ORDER BY f.data_renovacao ASC ";

				$lista_contrato = $this->bd->obtemRegistros($sSQL);
		
		
		$cbr = new VACobranca();
		
			$cli_bloq = $cbr->clientesParaBloqueio();
			
			$num_cli = count($cli_bloq);
	
		$this->tpl->atribui("status",$status);
		$this->tpl->atribui("licenca",$licenca);
		$this->tpl->atribui("lista_contrato",$lista_contrato);
		$this->tpl->atribui("num_cli",$num_cli);

	   $this->arquivoTemplate = "home_principal.html";
		   
		}
		if ($op == "renovacao_contrato"){

				if( ! $this->privPodeGravar("_COBRANCA") ) {
							$this->privMSG();
							return;
				}


		
			$sSQL  = "SELECT ";
			$sSQL .= "f.id_cliente_produto, cl.nome_razao, f.data_renovacao, f.valor_contrato, p.nome, ";
			$sSQL .= " c.id_cliente ";
			$sSQL .= "FROM ";
			$sSQL .= " cbtb_cliente_produto c, cltb_cliente cl, cbtb_contrato f, prtb_produto p ";
			$sSQL .= "WHERE ";
			$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
			$sSQL .= "AND p.tipo = f.tipo_produto ";
			$sSQL .= "AND f.data_renovacao <= now() + interval '30 day' ";
			$sSQL .= "AND cl.id_cliente = c.id_cliente  ";
			$sSQL .= "AND c.id_cliente_produto = f.id_cliente_produto ";
			$sSQL .= "AND p.id_produto = c.id_produto ";
			$sSQL .= "AND f.status = 'A' ";
			$sSQL .= "ORDER BY f.data_renovacao ASC ";
		
				$lista_contrato = $this->bd->obtemRegistros($sSQL);
				$this->tpl->atribui("lista_contrato",$lista_contrato);
				$this->arquivoTemplate = "renovacao_contrato.html";
		
		}
	
	
	
	
	}

public function __destruct() {
      	parent::__destruct();
}

}



?>
