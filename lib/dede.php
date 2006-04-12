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
					
					
					//Substitui todos as "," por "."
					$desconto_promo = str_replace(",",".",$desconto_promo);
					$$valor_comodato = str_replace(",",".",$valor_comodato);
					$tx_instalacao = str_replace(",",".",$tx_instalacao);
										
					
					//Calcula o valor do contrato
					//$valor_contrato = ($valor_produto *  $vigencia) - ($desconto_promo * $periodo_desconto);
					//$valor_contrato += $valor_comodato + $tx_instalacao;
					
					$valor_contrato = $valor_produto + $valor_comodato;
					$valor_cont_temp = $valor_contrato;
					//Diminui o desconto no valor real do contrato caso este tenha mesmo período que a vigência do contrato
					if ($periodo_desconto >= $vigencia) $valor_contrato -= $desconto_promo;
					
																									
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
							
							echo "$sSQL";
							
							break;
					}
					
					//echo "<br>$sSQL<br>";
					$this->bd->consulta($sSQL);
					
										
					//Cadastro de faturas do contrato.
					
					$pro_rata = @$_REQUEST["prorata"];
					
					$dia_vencimento = @$_REQUEST["dia_vencimento"];
										
					$qt_desconto = $periodo_desconto;
					
					$fatura_status = "A";
					$fatura_v_pago = 0;
					$fatura_dt_vencimento="";
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
								
								if($id_cobranca == 2) { //Tipo de pagamento for carnê
								
									$ini_carne = @$_REQUEST["ini_carne"];
									$data_carne = @$_REQUEST["data_carne"];
									
									list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
									list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);
									
									$stamp_inicial = mktime(0,0,0, $ini_m, $ini_d, $ini_a);
									$stamp_final = mktime(0,0,0, $dat_m, $dat_d, $dat_a);
									
									$diferenca_meses = (($stamp_final - $stamp_inicial) / 86400) / 30;
									
									
									for($i=0; $i<=floor($diferenca_meses); $i++) {
									
										//Aplica descontos, caso haja algum período de desconto declarado
										if($qt_desconto > 0) {
											$fatura_desconto = $desconto_promo;
											$qt_desconto--; 
										} else
											$fatura_desconto = 0;

										//Adiciona taxa de instalação na fatura, caso haja.
										if ($i==0) { //Cria primeira fatura pós-paga

											//Adiciona-se ao valor da fatura o valor do pro-rata																														

											//Se houver taxa de instalação no pós pago, então a primeira fatura do carnê será referente à taxa de instalação
											if($tx_instalacao > 0) {
												$fatura_valor = $tx_instalacao;
												
												//Calcula a data dos próximos pagamentos de fatura.
												$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));


												$sSQL =  "INSERT INTO cbtb_faturas(";
												$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
												$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
												$sSQL .= "	acrescimo, valor_pago ";
												$sSQL .= ") VALUES (";
												$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
												$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
												$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago ";
												$sSQL .= ")";												
												$this->bd->consulta($sSQL);
													
											}

										} 
										
										
										$fatura_valor = $valor_cont_temp;
										
										//Calcula o desconto sobre a fatura.
										$fatura_valor -= $fatura_desconto;

										//Calcula a data dos próximos pagamentos de fatura.
										$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m+$i+1, $ini_d, $ini_a));


										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago ";
										$sSQL .= ")";

										//echo "$sSQL<br>";
										$this->bd->consulta($sSQL);
										
										/*
										list($dt_final_a, $dt_final_m, $dt_final_d) = explode("-", $fatura_dt_vencimento);
										
										$stamp_dt1 = mktime(0,0,0,$dt_final_m, $dt_final_d, $dt_final_a);
										
										//if("$dt_final_d/$dt_final_m/$dt_final_a" == "$data_carne") break; */
										
									}																	
								} else { //Caso a cobrança não seja do tipo carnê.

									
									$fatura_valor = $valor_cont_temp;

									//Aplica descontos, caso haja algum período de desconto declarado
									if($qt_desconto > 0)
										$fatura_desconto = $desconto_promo;
									else
										$fatura_desconto = 0;

									//Adiciona taxa de instalação na fatura, caso haja.
									if($tx_instalacao != 0) {
										$fatura_valor = $tx_instalacao;
										
										//Calcula a data dos próximos pagamentos de fatura.
										$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));										
										
										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago ";
										$sSQL .= ")";

										echo "$sSQL<br>";
										$this->bd->consulta($sSQL);										
									}								
									
								}									
								
							break;
							
						case 'PRE':
								if($id_cobranca == 2) {	//Tipo pagamento for Carnê.
									/*
									$ini_carne = @$_REQUEST["ini_carne"];
									$data_carne = @$_REQUEST["data_carne"];
									
									list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
									list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);
																		
									for ($i=0; $i < $vigencia; $i++) */
									
									$ini_carne = @$_REQUEST["ini_carne"];
									$data_carne = @$_REQUEST["data_carne"];
																		
									list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
									list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);
																		
									$stamp_inicial = mktime(0,0,0, $ini_m, $ini_d, $ini_a);
									$stamp_final = mktime(0,0,0, $dat_m, $dat_d, $dat_a);
																		
									$diferenca_meses = (($stamp_final - $stamp_inicial) / 86400) / 30;
																		
																		
									for($i=0; $i<=floor($diferenca_meses); $i++) {									

										$fatura_valor = $valor_cont_temp;

										//Aplica descontos, caso haja algum período de desconto declarado
										if($qt_desconto > 0) {
											$fatura_desconto = $desconto_promo;
											$qt_desconto--; 
										} else
											$fatura_desconto = 0;


										//Adiciona taxa de instalação na fatura, caso haja.
										if ($i==0) { //Cria primeira fatura pré-paga

											//Adiciona-se ao valor da fatura o valor do pro-rata																														

											//TODO: Procurar função de adição do pro-rata
											if($tx_instalacao > 0) $fatura_valor += $tx_instalacao;

										}

										//Calcula a data dos próximos pagamentos de fatura.
										$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));

										//Calcula o desconto sobre a fatura.
										$fatura_valor -= $fatura_desconto;


										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago ";
										$sSQL .= ")";

										//echo "$sSQL<br>";
										$this->bd->consulta($sSQL);
									}
								} else {
								
								
									$fatura_valor = $valor_cont_temp;

									//Aplica descontos, caso haja algum período de desconto declarado
									if($qt_desconto > 0) {
										$fatura_desconto = $desconto_promo;
										$qt_desconto--; 
									} else
										$fatura_desconto = 0;


									if($tx_instalacao != 0) $fatura_valor += $tx_instalacao;

									//Calcula a data dos próximos pagamentos de fatura.
									
									$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $dia_vencimento, $ca));

									//Calcula o desconto sobre a fatura.
									$fatura_valor -= $fatura_desconto;


									$sSQL =  "INSERT INTO cbtb_faturas(";
									$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
									$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
									$sSQL .= "	acrescimo, valor_pago ";
									$sSQL .= ") VALUES (";
									$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
									$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
									$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago ";
									$sSQL .= ")";

									//echo "$sSQL<br>";
									$this->bd->consulta($sSQL);
								}
							break;						
					}

					
?>