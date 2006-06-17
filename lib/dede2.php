<?

	// Pega dominio padrão 
	//$sSQL  = "select dominio_padrao from pftb_preferencia_geral ";
	
	$dominioPadrao = $this->prefs->obtem("geral","dominio_padrao");
	//$lista_dominop = $this->bd->obtemUnicoRegistro($sSQL);
	//$dominioPadrao = $lista_dominop["dominio_padrao"]; 
	

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
				
		$tipo = @$_REQUEST["tipo"];
		$id_produto = @$_REQUEST["id_produto"];
		$email_igual = @$_REQUEST["email_igual"];
		$data_contratacao = @$_REQUEST["data_contratacao"];
		$vigencia = @$_REQUEST["vigencia"];
		$status = @$_REQUEST["status"];
		$dia_vencimento = @$_REQUEST["dia_vencimento"];
		$carencia_pagamento = @$_REQUEST["carencia_pagamento"];
		$desconto_promo = @$_REQUEST["desconto_promo"];
		$periodo_desconto = @$_REQUEST["periodo_desconto"];
		$tx_instalacao = @$_REQUEST["tx_instalacao"];
		$comodato = @$_REQUEST["comodato"];
		$valor_comodato = @$_REQUEST["valor_comodato"]; 
		$prorata = @$_REQUEST["prorata"];
		$tipo_cobranca = @$_REQUEST["tipo_cobranca"];
		$forma_pagamento = @$_REQUEST["forma_pagamento"];
		$ini_carne = @$_REQUEST["ini_carne"];
		$data_carne = @$_REQUEST["data_carne"];
		$username = @$_REQUEST["username"];
		$senha = @$_REQUEST["senha"];
		$conf_senha = @$_REQUEST["conf_senha"];
		$id_pop = @$_REQUEST["id_pop"]; 
		$id_nas = @$_REQUEST["id_nas"];
		$selecao_ip = @$_REQUEST["selecao_ip"];
		$mac = @$_REQUEST["mac"];
		$p = @$_REQUEST["p"];
		$op = @$_REQUEST["op"];
		$id_cliente = @$_REQUEST["id_cliente"];
		$rotina = @$_REQUEST["rotina"];
		$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
		$acao = @$_REQUEST["acao"];
		$foneinfo = @$_REQUEST["foneinfo"];
		$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
		$tipo_hospedagem = @$_REQUEST["tipo_hospedagem"];
		$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
		$pagamento = @$_REQUEST["pagamento"];
		$pri_venc = @$_REQUEST["pri_venc"];	
		$redirecionar = @$_REQUEST["redirecionar"];
		$endereco_ip = @$_REQUEST["endereco_ip"];
		
		//Informações de banco e cartão de crédito
		$cc_vencimento = @$_REQUEST["cc_vencimento"];
		$cc_numero = @$_REQUEST["cc_numero"];
		$cc_operadora = @$_REQUEST["cc_operadora"];
		$db_banco = @$_REQUEST["db_banco"];
		$db_agencia = @$_REQUEST["db_agencia"];
		$db_conta = @$_REQUEST["db_conta"];
		
		if(!$db_banco) $db_banco=0;
		if(!$db_agencia) $db_agencia=0;
		if(!$db_conta) $db_conta=0;
		
		
		
		//Calcula a data de renovação do cotrato
		list($cd, $cm, $ca) = explode("/", $data_contratacao);
		$data_renovacao = date("d/m/Y", mktime(0,0,0, $cm + $vigencia, $cd, $ca));
		
		
		//Adquire informações sobre o cliente e sobre o produto
		$sSQL =  "SELECT ";
		$sSQL .= "	id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
		$sSQL .= "	rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
		$sSQL .= "	cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
		$sSQL .= "	fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
		$sSQL .= "	ativo,obs ";
		$sSQL .= "FROM ";
		$sSQL .= "	cltb_cliente WHERE id_cliente = '$id_cliente'";
		$info_cliente = $this->bd->obtemUnicoRegistro($sSQL);
		
		//echo ("$sSQL");
		
		$sSQL = "SELECT * FROM prtb_produto WHERE id_produto = '$id_produto'";
		$info_produto = $this->bd->obtemUnicoRegistro($sSQL);
		
		$sSQL = "SELECT nome_cobranca FROM cftb_forma_pagamento WHERE id_cobranca = '$tipo_cobranca'";
		$info_pagamento = $this->bd->obtemUnicoRegistro($sSQL);
		
		
		//$Calcula o valor do contato
		$valor_contrato = $info_produto["valor"];
		$valor_contrato += $valor_comodato;
		
		if ($periodo_desconto >= $vigencia)
			$valor_contrato -= $desconto_promo;
			
		
		
		//Informações referente ao cliente e ao produto
		$this->tpl->atribui("info_cliente", $info_cliente);
		$this->tpl->atribui("info_produto", $info_produto);
		$this->tpl->atribui("info_pagamento", $info_pagamento);
		
		//Formatação de valores monetárris
		$valor_comodato = number_format($valor_comodato, 2, '.', '');
		$desconto_promo = number_format($desconto_promo, 2, '.', '');
		$tx_instalacao = number_format($tx_instalacao, 2, '.', '');
		$valor_contrato = number_format($valor_contrato, 2, '.', '');
		
		//Joga Informações para o template
		//Variáveis de utilização do script de contratação
		
		$this->tpl->atribui("tipo", $tipo );
		$this->tpl->atribui("id_produto", $id_produto);
		$this->tpl->atribui("email_igual", $email_igual);
		$this->tpl->atribui("data_contratacao", $data_contratacao);
		$this->tpl->atribui("data_renovacao", $data_renovacao);
		$this->tpl->atribui("vigencia", $vigencia );
		$this->tpl->atribui("status", $status );
		$this->tpl->atribui("dia_vencimento", $dia_vencimento);
		$this->tpl->atribui("carencia_pagamento", $carencia_pagamento);
		$this->tpl->atribui("desconto_promo", $desconto_promo );
		$this->tpl->atribui("periodo_desconto", $periodo_desconto);
		$this->tpl->atribui("tx_instalacao", $tx_instalacao);
		$this->tpl->atribui("comodato", $comodato);
		$this->tpl->atribui("valor_comodato", $valor_comodato);
		$this->tpl->atribui("prorata", $prorata);
		$this->tpl->atribui("tipo_cobranca", $tipo_cobranca);
		$this->tpl->atribui("forma_pagamento", $forma_pagamento );
		$this->tpl->atribui("ini_carne", $ini_carne );
		$this->tpl->atribui("data_carne", $data_carne );
		$this->tpl->atribui("username", $username );
		$this->tpl->atribui("senha", $senha);
		$this->tpl->atribui("conf_senha", $conf_senha);
		$this->tpl->atribui("id_nas", $id_nas );
		$this->tpl->atribui("id_pop", $id_pop);
		$this->tpl->atribui("selecao_ip", $selecao_ip );
		$this->tpl->atribui("mac", $mac );
		$this->tpl->atribui("p", $p );
		$this->tpl->atribui("op", $op );
		$this->tpl->atribui("id_cliete", $id_cliente );
		$this->tpl->atribui("rotina", $rotina );
		$this->tpl->atribui("id_cliente_produto", $id_cliente_produto );
		$this->tpl->atribui("acao", $acao );
		$this->tpl->atribui("id_cliente_produto", $id_cliente_produto);
		$this->tpl->atribui("valor_contrato", $valor_contrato);
		$this->tpl->atribui("dominio_hospedagem", $dominio_hospedagem);
		$this->tpl->atribui("tipo_hospedagem", $tipo_hospedagem);
		$this->tpl->atribui("pri_venc",$pri_venc);
		$this->tpl->atribui("pagamento",$pagamento);
		$this->tpl->atribui("redirecionar",$redirecionar);
		$this->tpl->atribui("endereco_ip",$endereco_ip);
		
		
		$this->tpl->atribui("cc_vencimento",$cc_vencimento); 
		$this->tpl->atribui("cc_numero",$cc_numero);
		$this->tpl->atribui("cc_operadora",$cc_operadora);
		$this->tpl->atribui("db_banco",$db_banco);
		$this->tpl->atribui("db_agencia",$db_agencia);
		$this->tpl->atribui("db_conta",$db_conta);
		

		//Adquire informações adicionais do produto e os envia para o template
		switch($tipo) {
			case 'D':
					$sSQL = "SELECT * FROM prtb_produto_discado WHERE id_produto = '$id_produto'";
					$info_produto = $this->bd->obtemUnicoRegistro($sSQL);
					
					$d_franquia_horas = $info_produto["franquia_horas"];
					$d_permitir_duplicidade = $info_produto["permitir_duplicidade"];
					$d_valor_hora_adicional = $info_produto["valor_hora_adicional"];
					
					$this->tpl->atribui("foneinfo", $foneinfo );
					$this->tpl->atribui("d_franquia_horas", $d_franquia_horas);
					$this->tpl->atribui("d_permitir_duplicidade", $d_permitir_duplicidade);
					$this->tpl->atribui("d_valor_hora_adicional", $d_valor_hora_adicional);

				break;
			case 'H':
					$sSQL = "SELECT * FROM prtb_produto_hospedagem WHERE id_produto = '$id_produto'";
					$info_produto = $this->bd->obtemUnicoRegistro($sSQL);
					
					$h_dominio = $info_produto["dominio"];
					$h_franquia_em_mb = $info_produto["franquia_em_mb"];
					$h_valor_mb_adicional = $info_produto["valor_mb_adicional"];
					
					$this->tpl->atribui("h_dominio", $h_dominio);
					$this->tpl->atribui("h_franquia_em_mb", $h_franquia_em_mb);
					$this->tpl->atribui("h_valor_mb_adicional", $h_valor_mb_adicional);
					
				break;
			case 'BL':
					$sSQL = "SELECT * FROM prtb_produto_bandalarga WHERE id_produto = '$id_produto'";
					//echo "query: $sSQL <br>";
					
					$info_produto = $this->bd->obtemUnicoRegistro($sSQL);
			
					$bl_banda_upload_kbps = $info_produto["banda_upload_kbps"];
					$bl_banda_download_kbps = $info_produto["banda_download_kbps"];
					$bl_franquia_trafego_mensal_gb = $info_produto["franquia_trafego_mensal_gb"];
					$bl_valor_trafego_adicional_gb = $info_produto["valor_trafego_adicional_gb"];
					
					$this->tpl->atribui("bl_banda_upload_kbps", $bl_banda_upload_kbps);
					$this->tpl->atribui("bl_banda_download_kbps", $bl_banda_download_kbps);
					$this->tpl->atribui("bl_franquia_trafego_mensal_gb", $bl_franquia_trafego_mensal_gb);
					$this->tpl->atribui("bl_valor_trafego_adicional_gb", $bl_valor_trafego_adicional_gb);
				break;
		}
		
		/*====> PRO-RATA - INICIO <=====*/
							
		$sSQL = "SELECT * FROM cftb_nas where id_nas = $id_nas";
		$_nas = $this->bd->obtemUnicoRegistro($sSQL);

		$sSQL = "SELECT * FROM cftb_pop where id_pop = $id_pop";
		$_pop = $this->bd->obtemUnicoRegistro($sSQL);
		
		$this->tpl->atribui("nome_nas",$_nas["nome"]);
		$this->tpl->atribui("nome_pop",$_pop["nome"]);


							
		$prorata = @$_REQUEST["prorata"];
		if($prorata == true){
							
							
								
			$pri_venc = @$_REQUEST["pri_venc"];
							
							
			if ($pri_venc && $pri_venc != ""){
				@list($d,$m,$a) = explode("/",$pri_venc);
			} else {
				$m = date("m");
				$d = date("d");
				$a = date("Y");
			}
								
			$proxima = ($m+1)."/".$dia_vencimento."/".$a;
			$primeiro = $m."/".$d."/".$a;
							
			//$proxima = $dia_vencimento ."/".($m+1)."/".$a;
			//echo "PROXIMA: ".$proxima."<br>";
			$diferenca = $this->days_diff($primeiro,$proxima);
							
			$valor_dia = $valor_contrato / 30;
			$valor_prorata = $valor_dia * $diferenca;
			//echo "DIFERENCA: ".$diferenca."<br>";
			//echo "VALOR DIA: ".$valor_dia."<br>";
			//echo "VALOR_PRORATA: ".$valor_prorata."<br>";
			$valor_prorata = number_format($valor_prorata, 2, '.', '');
							
			$this->tpl->atribui("dias_prorata",$diferenca);
			$this->tpl->atribui("valor_prorata",$valor_prorata);
							
		}
							
							
		/*====> PRO-RATA - FINAL <=====*/

		
		$this->tpl->atribui("info_adicional", $info_produto);

		$this->arquivoTemplate="cliente_contrato_detalhe.html";
		return;
		
	} else{					
		switch($tipo){
		case "BL":
		$msg_final = "Existe outro usuário de Banda Larga cadastrado com esses dados!";
		break;
		case "E":
		$msg_final = "Existe outra conta de E-Mail cadastrada com esses dados!";
		break;
		case "H":
		$msg_final = "Existe outra conta de Hospedagem cadastrada com esses dados!";
		break;
		case "D":
		$msg_final = "Existe outro usuario de Discado cadastrado com esses dados!";
		break;
		}

		$this->tpl->atribui("mensagem",$msg_final);
		//$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=cobranca&rotina=contratar&id_cliente=$id_cliente");
		$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=cobranca&id_cliente=$id_cliente");
		$this->tpl->atribui("target","_self");

		$this->arquivoTemplate="msgredirect.html";
		return;
	}
	
	//echo ("Username - dede2.php: $username");

?>