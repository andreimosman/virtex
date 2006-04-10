<?


					//Verifica o cadastramento anterior do contrato
					
					$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
					$sSQL = "SELECT COUNT(*) FROM cbtb_contrato WHERE id_cliente_produto = $id_cliente_produto";
					
					
										
					
					//Cadastro de contrato					
					//Pega informações sobre forma de pagamento					
					$tipo_cobranca = @$_REQUEST['tipo_cobranca'];
					
					$sSQL = "SELECT * FROM cftb_forma_pagamento WHERE tipo_cobranca = $tipo_cobranca ";
					
					//Coleta informações sobre o tipo de pagamento escolhido
					$forma_pagamento = $this->bd->obtemUnicoRegistro($sSQL);
										
					
					$vigencia = @$_REQUEST["vigencia"];
					$data_renovacao = @$_REQUEST["data_renovacao"];
					$valor_contrato = @$_REQUEST["valor_contrato"];
					$id_cobranca = $tipo_cobranca;
					$status = @$_REQUEST["status"];
					
					$sSQL = "INSERT INTO cbtb_contrato (";
					$sSQL .= "	id_cliente_produto, vigencia, data_renovacao, valor_contrato, id_cobranca, status";
					$sSQL .= ") VALUES (";
					$sSQL .= "	$id_cliente_produto, $vigencia, '$data_renovacao', $valor_contrato, $id_cobranca, '$status'";
					
					$this->bd->consulta($sSQL);
										
					/*
					//Taxa de juros padrao
					$sSQL = "SELECT * from cftb_preferencias WHERE id_provedor = 1";
					$prov_prefs = $this->bd->obtemUnicoRegistro($sSQL);					
					$tx_juros = prev_profs["tx_juros"];
					
					//Cadastro da Forma de Pagamento
					
					$valor = @$_RESQUEST["valor"];
					
					
					$sSQL = "INSERT INTO cbtb_fatura (";
					$sSQL .= "	id_cliente_produto, vencimento, cobranca, tx_juros, valor, qtde_faturas ",
					$sSQL .= ") VALUES (";
					$sSQL .= "	$id_cliente_produto, $vencimento, $cobranca, "*/
					
?>