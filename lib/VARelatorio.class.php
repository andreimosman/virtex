<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VARelatorio extends VirtexAdmin {

	public function VARelatorio() {
		parent::VirtexAdmin();
	
	
	}
		
	public function processa($op=null) {
	
	if($op == "fatura"){	
		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_fatura.html";
	} else if ($op == "cortesia"){
		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_cortesia.html";
	} else if ($op == "geral"){
	// RELATORIO GERAL DE CLIENTES
			$erros = array();
			$inicial = @$_REQUEST['inicial'];
			$inicial_up = strtoupper($inicial);
			$inicial_lo = strtolower($inicial);
			$acao = @$_REQUEST['acao'];
																	
										
																
		if( !$inicial ){	
		
		
		
		
			if( $acao ){
				
				$aSQL  = "SELECT ";
				$aSQL .= "   cl.id_cliente, cl.data_cadastro, cl.nome_razao, cl.tipo_pessoa, ";
				$aSQL .= "   cl.rg_inscr, cl.rg_expedicao, cl.cpf_cnpj, cl.email, cl.endereco, cl.complemento, cl.id_cidade, ";
				$aSQL .= "   cl.cidade, cl.estado, cl.cep, cl.bairro, cl.fone_comercial, cl.fone_residencial, ";
				$aSQL .= "   cl.fone_celular, cl.contato, cl.banco, cl.conta_corrente, cl.agencia, cl.dia_pagamento, ";
				$aSQL .= "   cl.ativo, cl.obs, ";
				$aSQL .= "   c.id_cidade, c.cidade ";				
				$aSQL .= "FROM ";
				$aSQL .= "cltb_cliente cl, cftb_cidade c ";
				$aSQL .= "WHERE c.id_cidade = cl.id_cidade";

						
				$reg = $this->bd->obtemRegistros($aSQL);
																		
				$this->tpl->atribui("ult_cli",$reg);
			
			}else if (!$acao){

				$aSQL  = "SELECT ";
				$aSQL .= "   cl.id_cliente, cl.data_cadastro, cl.nome_razao, cl.tipo_pessoa, ";
				$aSQL .= "   cl.rg_inscr, cl.rg_expedicao, cl.cpf_cnpj, cl.email, cl.endereco, cl.complemento, cl.id_cidade, ";
				$aSQL .= "   cl.cidade, cl.estado, cl.cep, cl.bairro, cl.fone_comercial, cl.fone_residencial, ";
				$aSQL .= "   cl.fone_celular, cl.contato, cl.banco, cl.conta_corrente, cl.agencia, cl.dia_pagamento, ";
				$aSQL .= "   cl.ativo, cl.obs, ";
				$aSQL .= "   c.id_cidade, c.cidade ";
				$aSQL .= "FROM cltb_cliente cl, cftb_cidade c ";
				$aSQL .= "WHERE c.id_cidade = cl.id_cidade ";
				$aSQL .= "ORDER BY id_cliente DESC LIMIT (5)";
											
				$reg = $this->bd->obtemRegistros($aSQL);
											
				$this->tpl->atribui("ult_cli",$reg);
			}
	
	
		}else {
		
			$aSQL  = "SELECT ";
			$aSQL .= "   cl.id_cliente, cl.data_cadastro, cl.nome_razao, cl.tipo_pessoa, ";
			$aSQL .= "   cl.rg_inscr, cl.rg_expedicao, cl.cpf_cnpj, cl.email, cl.endereco, cl.complemento, cl.id_cidade, ";
			$aSQL .= "   cl.cidade, cl.estado, cl.cep, cl.bairro, cl.fone_comercial, cl.fone_residencial, ";
			$aSQL .= "   cl.fone_celular, cl.contato, cl.banco, cl.conta_corrente, cl.agencia, cl.dia_pagamento, ";
			$aSQL .= "   cl.ativo, cl.obs, ";
			$aSQL .= "   c.id_cidade, c.cidade ";
			$aSQL .= "FROM cltb_cliente cl, cftb_cidade c ";
			$aSQL .= "WHERE (cl.nome_razao ilike '$inicial_up%' OR cl.nome_razao ilike '$inicial_lo%') AND c.id_cidade = cl.id_cidade ";
												
			$reg = $this->bd->obtemRegistros($aSQL);
												
			$this->tpl->atribui("ult_cli",$reg);

		
		
		}
	
	
		$this->arquivoTemplate = "relatorio_clientes.html";
	
	
	} else if ($op == "estat"){



		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_estat.html";





	} else if ($op == "filtro"){
	
			
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		$this->tpl->atribui("op",$op);
	
		$this->arquivoTemplate = "cobranca_versaolight.html";
		//$this->arquivoTemplate = "relatorio_filtro.html";
		
		
	} else if ($op == "config"){
	
		$pg = @$_REQUEST["pg"];
	
		switch($pg) {
			
			case 'carga_ap':
			
				$tipo = "AP";
			
				$sSQL  = "SELECT ";
				$sSQL .= "   p.id_pop,p.nome, ";
				$sSQL .= "   CASE WHEN ";
				$sSQL .= "      cli_pop.clientes_associados is null ";
				$sSQL .= "   THEN ";
				$sSQL .= "      0 ";
				$sSQL .= "   ELSE ";
				$sSQL .= "      cli_pop.clientes_associados ";
				$sSQL .= "   END as clientes_associados, ";
				$sSQL .= "   cli_pop.carga_up,cli_pop.carga_down ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_pop p LEFT OUTER JOIN ";
				$sSQL .= "   ( ";
				$sSQL .= "  SELECT ";
				$sSQL .= "     pop.id_ap,count(cbl.id_pop) as clientes_associados, ";
				$sSQL .= "        sum(upload_kbps) as carga_up, sum(download_kbps) as carga_down ";
				$sSQL .= "  FROM ";
				$sSQL .= "     cntb_conta_bandalarga cbl, ";
				$sSQL .= "     ( ";
				$sSQL .= "          SELECT ";
				$sSQL .= "             p.id_pop, ";
				$sSQL .= "             CASE WHEN ";
				$sSQL .= "                p.id_pop_ap is null ";
				$sSQL .= "             THEN ";
				$sSQL .= "                p.id_pop ";
				$sSQL .= "             ELSE ";
				$sSQL .= "                p.id_pop_ap ";
				$sSQL .= "             END as id_ap ";
				$sSQL .= "          FROM ";
				$sSQL .= "             cftb_pop p  ";
				$sSQL .= "     ) pop ";
				$sSQL .= "  WHERE ";
				$sSQL .= "     cbl.id_pop = pop.id_pop ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     pop.id_ap ";
				$sSQL .= "   ) cli_pop ON( p.id_pop = cli_pop.id_ap)  ";
				$sSQL .= "WHERE ";
				$sSQL .= "   p.tipo = 'AP' ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   p.nome ";
				
				break;

			case 'carga_pop':
			
				$tipo = "POP";
			
				$sSQL  = "SELECT ";
				$sSQL .= "   p.id_pop,p.nome, ";
				$sSQL .= "   CASE WHEN ";
				$sSQL .= "      cli_pop.clientes_associados is null ";
				$sSQL .= "   THEN ";
				$sSQL .= "      0 ";
				$sSQL .= "   ELSE ";
				$sSQL .= "      cli_pop.clientes_associados ";
				$sSQL .= "   END as clientes_associados, ";
				$sSQL .= "   cli_pop.carga_up,cli_pop.carga_down ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_pop p LEFT OUTER JOIN ";
				$sSQL .= "   ( ";
				$sSQL .= "  SELECT ";
				$sSQL .= "     pop.id_pop,count(cbl.id_pop) as clientes_associados,sum(upload_kbps) as carga_up, sum(download_kbps) as carga_down  ";
				$sSQL .= "  FROM ";
				$sSQL .= "     cntb_conta_bandalarga cbl, ";
				$sSQL .= "     cftb_pop pop ";
				$sSQL .= "  WHERE ";
				$sSQL .= "     cbl.id_pop = pop.id_pop ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     pop.id_pop ";
				$sSQL .= "   ) cli_pop ON( p.id_pop = cli_pop.id_pop)  ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   p.nome ";

				break;
				
			case 'carga_nas':
				$tipo = "NAS";

				$sSQL  = "SELECT ";
				$sSQL .= "   nas.id_nas,nas.nome, ";
				$sSQL .= "   CASE WHEN ";
				$sSQL .= "      cli_nas.clientes_associados is null ";
				$sSQL .= "   THEN ";
				$sSQL .= "      0 ";
				$sSQL .= "   ELSE ";
				$sSQL .= "      cli_nas.clientes_associados ";
				$sSQL .= "   END as clientes_associados, ";
				$sSQL .= "   cli_nas.carga_up,cli_nas.carga_down ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_nas nas LEFT OUTER JOIN ";
				$sSQL .= "   ( ";
				$sSQL .= "  SELECT ";
				$sSQL .= "     nas.id_nas,count(cbl.id_nas) as clientes_associados,sum(upload_kbps) as carga_up, sum(download_kbps) as carga_down  ";
				$sSQL .= "  FROM ";
				$sSQL .= "     cntb_conta_bandalarga cbl, ";
				$sSQL .= "     cftb_nas nas ";
				$sSQL .= "  WHERE ";
				$sSQL .= "     cbl.id_nas = nas.id_nas ";
				$sSQL .= "  GROUP BY ";
				$sSQL .= "     nas.id_nas ";
				$sSQL .= "   ) cli_nas ON( nas.id_nas = cli_nas.id_nas) ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   nas.nome ";
			
				break;
				
		}
		
		
		$carga = $this->bd->obtemRegistros($sSQL);
		$bgcolor1="#FFFFFF";
		$bgcolor2="#F1F1F1";
		$bgcolor=$bgcolor1;

		for($x=0;$x<count($carga);$x++) {
			$carga[$x]["carga_up"] = $carga[$x]["carga_up"] ? $carga[$x]["carga_up"] : "-";
			$carga[$x]["carga_down"] = $carga[$x]["carga_down"] ? $carga[$x]["carga_down"] : "-";
			$carga[$x]["bgcolor"] = $bgcolor;
			$bgcolor = $bgcolor == $bgcolor1 ? $bgcolor2 : $bgcolor1;
		}
		
		$this->tpl->atribui("tipo",$tipo);
		$this->tpl->atribui("carga",$carga);

		$this->arquivoTemplate = "relatorio_config_carga.html";

	} else if ($op == "pop"){
		$this->arquivoTemplate = "relatorio_pop.html";
	} else if ($op == "nas"){
		$this->arquivoTemplate = "relatorio_nas.html";
	}	
	
}

public function __destruct() {
      	parent::__destruct();
}



}



?>
