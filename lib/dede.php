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
					
					//Corrige possíveis falhas de entrada em alguns campos
					if (!$comodato) {
						$comodato = 'f';
						$valor_comodato = 0;
					} else if(!$valor_comodato) {
						$valor_comodato = 0;
					}
					
					if (!$desconto_promo) $desconto_promo = 0;
					if (!$periodo_desconto) $periodo_desconto = 0;
					if (!$tx_instalacao) $tx_instalacao = 0;
					
					
					//Calcula o valor do contrato
					//$valor_contrato = ($valor_produto *  $vigencia) - ($desconto_promo * $periodo_desconto);
					//$valor_contrato += $valor_comodato + $tx_instalacao;
					
					$valor_contrato = $valor_produto + $valor_comodato;					
					
					//Diminui o desconto no valor real do contrato caso este tenha mesmo período que a vigência do contrato
					//if ($periodo_desconto >= $vigencia) $valor_contrato -= $desconto;
					
																									
					$sSQL =  "INSERT INTO cbtb_contrato ( ";
					$sSQL .= "	id_cliente_produto, data_contratacao, vigencia, data_renovacao, valor_contrato, id_cobranca, status, ";
					$sSQL .= "	tipo_produto, valor_produto, num_emails, quota_por_conta, tx_instalacao, comodato, valor_comodato, desconto_promo, periodo_desconto " ;
					$sSQL .= ") VALUES ( ";
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
					
					//echo "<br>$sSQL<br>";
					$this->bd->consulta($sSQL);
					
										
					//Cadastro de faturas do contrato.
					
					$dia_vencimento = @$_REQUEST["dia_vencimento"];
										
					$qt_descontos = $periodo_desconto;
					
					$fatura_status = "A";
					$fatura_v_pago = 0;
					$fatura_dt_vencimento="";
					$fatura_dt_pagamento="";
					$fatura_dt_reagendamento="";
					$fatura_obs="";
					$fatura_desc="";
					$fatura_pg_acrescimo = 0;
					$fatura_pg_parcial=0;
					$fatura_vl_pago=0;
					$fatura_desconto=0;
					$pos = 0; //Jogar para o próximo mês
					
					$forma_pagamento = @$_REQUEST["forma_pagamento"];
					
					
					list($ca, $cm, $cd) = explode("-", $data_contratacao);
					
					
					switch($forma_pagamento) {
						case 'POS':
								for ($i=0; $i < $vigencia; $i++) {									
									$fatura_valor = $valor_contrato;
									
									//desconto sobre a fatura.
									if(qt_descontos > 0) {
										$fatura_desconto = $desconto_promo;
										qt_descontos--;
									} else
										$fatura_desconto = 0;
									
									
									$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i+$pos, $dia_vencimento, $ca));
									
									$sSQL =  "INSERT INTO (";
									$sSQL .= "	id_cliente_produto, data, valor, status, observacoes, ";
									$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
									$sSQL .= "	acrescimo, valor_pago, adesao ";
									$sSQL .= ") VALUES (";
									$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', $fatura_valor, '$fatura_status', '$fatura_obs', ";
									$sSQL .= "	'$fatura_dt_reagendamento', $fatura_pg_parcial, '$fatura_dt_pagamento', $fatura_desconto, ";
									$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, 'f' ";
									$sSQL .= ")";
									
									//echo "$sSQL<br>";
									$this->bd->consulta($sSQL);
								}
							break;
							
						case 'PRE':
								for ($i=0; $i < $vigencia; $i++) {									
									$fatura_valor = $valor_contrato;
									$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));
									
									$sSQL =  "INSERT INTO (";
									$sSQL .= "	id_cliente_produto, data, valor, status, observacoes, ";
									$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
									$sSQL .= "	acrescimo, valor_pago, adesao ";
									$sSQL .= ") VALUES (";
									$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', $fatura_valor, '$fatura_status', '$fatura_obs', ";
									$sSQL .= "	'$fatura_dt_reagendamento', $fatura_pg_parcial, '$fatura_dt_pagamento', $fatura_desconto, ";
									$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, 'f' ";
									$sSQL .= ")";
								}
							break;
								
					}
						
					

					
					
										
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