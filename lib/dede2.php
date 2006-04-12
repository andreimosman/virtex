<?

	// Pega dominio padrão 
	$sSQL  = "select dominio_padrao from cftb_preferencias";
	$lista_dominop = $this->bd->obtemUnicoRegistro($sSQL);
	$dominioPadrao = $lista_dominop["dominio_padrao"]; 
	


	// Valida os dados

	// TODO: Colocar isso em uma funcao private
	$sSQL  = "SELECT ";
	$sSQL .= "   username ";
	$sSQL .= "FROM ";
	$sSQL .= "   cntb_conta ";
	$sSQL .= "WHERE ";
	$sSQL .= "   username = '".$this->bd->escape(trim(@$_REQUEST["username"]))."' ";
	$sSQL .= "   and tipo_conta = '". $this->bd->escape(trim(@$_REQUEST["tipo"])) ."' ";					
	$sSQL .= "   and dominio = '".$dominioPadrao."' ";
	$sSQL .= "ORDER BY ";
	$sSQL .= "   username ";

	$lista_user = $this->bd->obtemUnicoRegistro($sSQL);

	if(count($lista_user) && $lista_user["username"]){
		// ver como processar 
		$erros[] = "Já existe outra conta cadastrada com esse username";
	}

	//se não tiver erros continua a formação do contrato
	//Pega todas a informações necessárias deste aformuilário para a continuação da contratação
	
	// Se nao tiver erros faz o contrato
	if( !count($erros) ) {
			
		$id_cliente = @$_REQUEST["id_cliente"];
		$id_produto = @$_REQUEST["id_produto"];
		$username = @$_REQUEST["username"];
		$dominio = @$_REQUEST["dominio"];
		$tipo_conta = @$_REQUEST["tipo_conta"];
		$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
		$senha = @$_REQUEST["senha"];
		$tipo = @$_REQUEST["tipo"];
		$senha = @$_REQUEST["senha"];
		$foneinfo = @$_REQUEST["foneinfo"];
		$selecao_ip = @$_REQUEST["selecao_ip"];
		$id_nas = @$_REQUEST["id_nas"];
		$mac = @$_REQUEST["mac"];
		$id_pop = @$_REQUEST["id_pop"];
		$tipo_hospedagem = @$_REQUEST["tipo_hospedagem"];
		$vigencia = @$_REQUEST["vigencia"];
		$ini_carne = @$REQUEST["ini_carne"];
		$data_carne = @$REQUEST["data_carne"];
		
		$data_contratacao = @$_REQUEST["data_contratacao"];
		$status = @$_REQUEST["status"];
		$tx_instalacao = @$_REQUEST["tx_instalacao"];
		$comodato = @$_REQUEST["comodato"];
		$valor_comodato = @$_REQUEST["valor_comodato"];
		$desconto_promo = @$_REQUEST["desconto_promo"];
		$periodo_desconto = @$_REQUEST["periodo_desconto"];
		$dia_vencimento = @$_REQUEST["dia_vencimento"];
		$forma_pagamento = @$_REQUEST["forma_pagamento"];
		$ini_carne = @$_REQUEST["ini_carne"];
		$data_carne = @$_REQUEST["data_carne"];

		
		//Calcula a data de renovação do cotrato
		list($contrato_dia, $contrado_mes, $contrato_ano) = explode("/", $data_contratacao);
		$data_renovacao = date("d/m/Y", mktime(0,0,0, $contrato_mes + $vigencia, $contrato_dia, $contrato_ano);
				
		
		//Adquire informações sobre o cliente e sobre o produto
		$sSQL = "SELECT * FROM cftb_clientes WHERE id_cliente = $id_cliente";
		$info_cliente = $this->bd->obtemUnicoRegistro($sSQL);
		
		$sSQL = "SELECT * FROM prtb_produtos WHERE id_produto = $id_produto";
		$info_produto = $this->bd->obtemUnicoRegistro($sSQL);
		

		
		//Joga Informações para o template
		//Variáveis de utilização do script de contratação
		$this->tpl->atribui("id_cliete", $id_cliente);
		$this->tpl->atribui("id_produto", $id_cliente);
		$this->tpl->atribui("username", $id_cliente);
		$this->tpl->atribui("dominio", $id_cliente);
		$this->tpl->atribui("tipo_conta", $id_cliente);
		$this->tpl->atribui("dominio_hospedagem", $id_cliente);
		$this->tpl->atribui("senha", $id_cliente);
		$this->tpl->atribui("tipo", $id_cliente);
		$this->tpl->atribui("foneinfo", $id_cliente);
		$this->tpl->atribui("selecao_ip", $id_cliente);
		$this->tpl->atribui("id_nas", $id_cliente);
		$this->tpl->atribui("mac", $id_cliente);
		$this->tpl->atribui("id_pop", $id_cliente);
		$this->tpl->atribui("id_pop", $id_cliente);
		$this->tpl->atribui("tipo_hospedagem", $id_cliente);
		$this->tpl->atribui("vigencia", $vigencia);
		$this->tpl->atribui("ini_carne", $ini_carne);
		$this->tpl->atribui("data_carne", $data_carne);


		$this->tpl->atribui("data_contratacao", $data_contratacao);
		$this->tpl->atribui("data_renovacao", $data_renovacao);
		$this->tpl->atribui("status", $status);
		$this->tpl->atribui("tx_instalacao", $tx_instalacao);
		$this->tpl->atribui("comodato", $comodato);
		$this->tpl->atribui("valor_comodato", $valor_comodato);
		$this->tpl->atribui("desconto_promo", $desconto_promo);
		$this->tpl->atribui("periodo_desconto", $periodo_desconto);
		$this->tpl->atribui("dia_vencimento", $dia_vencimento);
		$this->tpl->atribui("data_carne", $data_carne);
		$this->tpl->atribui("forma_pagamento", $data_carne);
		$this->tpl->atribui("ini_carne", $data_carne);
		$this->tpl->atribui("data_carne", $data_carne);
		$this->tpl->atribui("data_carne", $data_carne);
		
						

		//Adquire informações adicionais do produto e os envia para o template
		switch($tipo) {
			case 'D':
					$sSQL = "SELECT * FROM prtb_produto_hospedagem WHERE id_produto = $id_produto";
					$info_pr_adicional = $this->bd->obtemUnicoRegistro($sSQL);
		
					$this->tpl->atribui("id_cliete", $id_cliente);
					$this->tpl->atribui("id_produto", $id_cliente);
					$this->tpl->atribui("username", $id_cliente);
					$this->tpl->atribui("dominio", $id_cliente);
				break;
			case 'H':
					$sSQL = "SELECT * FROM prtb_produto_discado WHERE id_produto = $id_produto";
					$info_pr_adicional = $this->bd->obtemUnicoRegistro($sSQL);
					
					$this->tpl->atribui("id_cliete", $id_cliente);
					$this->tpl->atribui("id_produto", $id_cliente);
					$this->tpl->atribui("username", $id_cliente);
					$this->tpl->atribui("dominio", $id_cliente);
				break;
			case 'BL':
					$sSQL = "SELECT * FROM prtb_produto_bandalarga WHERE id_produto = $id_produto";
					$info_pr_adicional = $this->bd->obtemUnicoRegistro($sSQL);
					
					$this->tpl->atribui("id_cliete", $id_cliente);
					$this->tpl->atribui("id_produto", $id_cliente);
					$this->tpl->atribui("username", $id_cliente);
					$this->tpl->atribui("dominio", $id_cliente);
				break;
		}
		
		
	} 



?>
