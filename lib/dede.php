<? 
					//Cadastra o contrato do cliente.
					$id_produto = @$_REQUEST["id_produto"];
					
					
					//Informações sobre o produto
					$sSQL = "SELECT * from prtb_produto where id_produto = $id_produto";
					$info_produto = $this->bd->obtemUnicoRegistro($sSQL);
					
					$data_contratacao = @$_REQUEST["data_contratacao"];
					$vigencia = @$_REQUEST["vigencia"];
					
					list($d, $m, $a) = explode("/", $data_contratacao);
					$data_renovacao = date("Y-m-d", mktime(0, 0, 0, $m+$vigencia, $d, $a));										
					$data_contratacao = "$a-$m-$d";
					
					//$valor_contrato					
					$id_cobranca = @$_REQUEST['tipo_cobranca'];
					$status = @$_REQUEST["status"];
					$tipo_produto = @$_REQUEST["tipo"];
					$valor_produto = $info_produto["valor"];
					$num_emails = $info_produto["num_emails"];
					$quota = $info_produto["quota_por_conta"];
					$tx_instalacao = @$_REQUEST["tx_instalacao"];
					$comodato = @$_REQUEST["comodato"];
					$valor_comodato = @$_REQUEST["valor_comodato"];
					$desconto_promo = @$_REQUEST["desconto_promo"];
					$periodo_desconto = @$_REQUEST["periodo_desconto"];
					
					if (!$comodato) {
						$comodato = 'f';
						$valor_comodato = 0;
					}
					////////////
					if (!$desconto_promo) $descpmtp_promo = 0;
					
					//Calcula o valor do contrato
					$valor_contrato = ($valor_produto *  $vigencia) - ($desconto_promo * $periodo_desconto);
					$valor_contrato += ((!$valor_comodato || !$comodato || $comodato=='f')?0:$valor_comodato);
					$valor_contrato += $tx_instalacao;			
					
														
																									
					$sSQL =  "INSERT INTO cbtb_contrato ( ";
					$sSQL .= "	id_cliente_produto, data_contratacao, vigencia, data_renovacao, valor_contrato, id_cobranca, status, ";
					$sSQL .= "	tipo_produto, valor_produto, num_emails, quota_por_conta, tx_instalacao, comodato, valor_comodato, desconto_promo, periodo_desconto " ;
					$sSQL .= " ) VALUES ( ";
					$sSQL .= "	$id_cliente_produto, '$data_contratacao', $vigencia, '$data_renovacao', $valor_contrato, $id_cobranca, '$status', ";
					$sSQL .= "	'$tipo_produto', $valor_produto, $num_emails, $quota, $tx_instalacao, '$comodato', $valor_comodato, $desconto_promo, $periodo_desconto ";
					$sSQL .= ")";
										
									
					//echo "$sSQL"."<br>\n";
					$this->bd->consulta($sSQL);	//Salva as configurações de contrato
					
					
					switch($tipo_produto) {
						case 'BL':
							$sSQL = "SELECT * FROM prtb_produto_bandalarga WHERE id_produto = $id_produto";
							$info_ad_produto = $this->bd->obtemUnicoRegistro($sSQL);
														
							$bl_banda_upload_kbps = $info_ad_produto["banda_upload_kbps"];
							$bl_banda_download_kbps = $info_ad_produto["banda_download_kbps"];
							$bl_franquia_trafego_mensal_gb = $info_ad_produto["franquia_trafego_mensal_gb"];
  							$bl_valor_trafego_adicional_gb = $info_ad_produto["valor_trafego_adicional_gb"];
  							
  							$sSQL =  "UPDATE cbtb_contrato SET";
  							$sSQL .= "	bl_banda_upload_kbps = $bl_banda_upload_kbps, ";
							$sSQL .= "	bl_banda_download_kbps = $bl_banda_download_kbps, ";
							$sSQL .= "	bl_franquia_trafego_mensal_gb = $bl_franquia_trafego_mensal_gb,";
							$sSQL .= "	bl_valor_trafego_adicional_gb = $bl_valor_trafego_adicional_gb ";
							$sSQL .= "WHERE id_cliente_produto = $id_cliente_produto";
  							
							break;
							
						case 'D':
							$sSQL = "SELECT * FROM prtb_produto_discado WHERE id_produto = $id_produto";
							$info_ad_produto = $this->bd->obtemUnicoRegistro($sSQL);
														
							$disc_franquia_horas = $info_ad_produto["franquia_horas"];
							$disc_permitir_duplicidade = $info_ad_produto["permitir_duplicidade"];
							$disc_valor_hora_adicional = $info_ad_produto["valor_hora_adicional"];

							$sSQL =  "UPDATE cbtb_contrato SET";
							$sSQL .= "	disc_franquia_horas = $disc_franquia_horas,";
							$sSQL .= "	disc_permitir_duplicidade = '$disc_permitir_duplicidade',";
							$sSQL .= "	disc_valor_hora_adicional = $disc_valor_hora_adicional ";
							$sSQL .= "WHERE id_cliente_produto = $id_cliente_produto";
							break;
							
						case 'H':
							$sSQL = "SELECT * FROM prtb_produto_hospedagem WHERE id_produto = $id_produto";
							$info_ad_produto = $this->bd->obtemUnicoRegistro($sSQL);
										
					
							$hosp_dominio = $info_ad_produto["dominio"];
							$hosp_franquia_em_mb = $info_ad_produto["franquia_em_mb"];
  							$hosp_valor_mb_adicional = $info_ad_produto["valor_mb_adicional"];
						
													
							$sSQL =  "UPDATE cbtb_contrato SET";
							$sSQL .= "	hosp_dominio = '$hosp_dominio',";
							$sSQL .= "	hosp_franquia_em_mb = $hosp_franquia_em_mb, ";
  							$sSQL .= "	hosp_valor_mb_adicional = $hosp_valor_mb_adicional ";
							$sSQL .= "WHERE id_cliente_produto = $id_cliente_produto";
							break;
					}
					
					echo "<br>$sSQL<br>";
					$this->bd->consulta($sSQL);
					
					//$tipo_cobranca = @$_REQUEST['tipo_cobranca']; 
					//$id_cobranca = $tipo_cobranca
					
					//Calcula Data de renovação contrato
					
					/*
					
					//adicionais -----------------------------------
					
					$desconto_promo = @$_REQUEST["desconto_promo"];
					$periodo_desconto = @$_REQUEST["periodo_desconto"];
					
					$tx
										
					*/
					
					/*
					//Busca pela ultima requisição de produto feita.					
					$sSQL = "SELECT id_cliente_produto FROM cbtb_cliente_produto ";
					$sSQL .= "WHERE id_cliente = $id_cliente AND id_produto = $id_produto AND excluido = 'f' ";
					$sSQL .= "ORDER BY id_cliente_produto DESC";
									
					$id_cliente_p = $this->bd->obtemUnicoRegistro($sSQL);
					$id_cliente_produto = $id_cliente_p["id_cliente_produto"];
					*/
	
					/*
					$sSQL =  "INSERT INTO cbtb_contato ( ";
					$sSQL .= "	id_cliente_produto, data_contratacao, vigencia, data_renovacao, valor_contrato, ";
					$sSQL .= "	id_cobranca, status, tipo_produto, valor_produto, num_emails, ";
					$sSQL .= "	quota_por_conta, comodato, valor_comodato, desconto_promo, periodo_desconto, " ;
					$sSQL .= "	hosp_dominio, hosp_franquia_em_mb, hosp_valor_mb_adicional, disc_franquia_horas, ";
					$sSQL .= "	disc_permitir_duplicidade, disc_valor_hora_adicional, bl_banda_upload_kbps, ";
					$sSQL .= "	bl_banda_download_kbps, bl_franquia_trafego_mensal_gb, bl_valor_trafego_adicional_gb ";
					$sSQL .= ") VALUES ( ";
					$sSQL .= "	$id_cliente_produto, '$data_contratacao', $vigencia, '$data_renovacao', $valor_contrato,";
					$sSQL .= "	$id_cobranca, '$status', $tipo_produto, $valor_produto, num_emails, ";
					$sSQL .= "	$quota, $comodato, ";
					*/
	
					/*
					$sSQL =  "INSERT INTO cftb_forma_pagamento ( ";
					$sSQL .= "	id_cliente_produto, vencimento, tipo_cobranca, tx_juros, valor, qtde_faturas ";
					$sSQL .= ") VALUES ( ";
					$sSQL .= "	$id_cliente_produto, $vencimento, $tipo_cobranca, $tx_juros, $valor, $qtde_faturas )";
					
					$this->bd->consulta($sSQL);
					*/
					
					
					
?>