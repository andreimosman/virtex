<?
/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/

										
					//Cadastro de faturas do contrato.
					
					//* CARNE - INICIO

					$_id_carne = $this->bd->proximoID('cbsq_id_carne');
					$q = 0;

					$sSQL  = "INSERT INTO cbtb_carne ";
					$sSQL .= "(id_carne, data_geracao,id_cliente_produto,valor,status,vigencia,id_cliente) ";
					$sSQL .= "VALUES ";
					$sSQL .= "('$_id_carne','$data_contratacao','$id_cliente_produto','$valor_contrato','A','$vigencia','$id_cliente') ";

					$this->bd->consulta($sSQL);
					//echo "CARNE: $sSQL <br>";
					
					$id_carne = $_id_carne;
					
					//* CARNE - FIM
					
					$pro_rata = @$_REQUEST["prorata"];
					
					$dia_vencimento = @$_REQUEST["dia_vencimento"];
					$pri_venc = @$_REQUEST["pri_venc"];
										
					$qt_desconto = $periodo_desconto;
					
					$fatura_status = "A";
					$fatura_v_pago = 0;
					$fatura_dt_vencimento="";
					$fatura_obs="";
					
					$fatura_desc = $info_produto["nome"];
					
					$fatura_pg_acrescimo = 0;
					$fatura_pg_parcial=0;
					$fatura_vl_pago=0;
					$fatura_desconto=0;
					
					$pos = 0; //Jogar para o próximo mês
					
					$forma_pagamento = @$_REQUEST["forma_pagamento"];
					
					/*====> PRO-RATA - INICIO <=====
					
					
					$prorata = @$_REQUEST["prorata"];
					if($prorata == true){
					
					
						
					$pri_venc = @$_REQUEST["pri_venc"];
					
					
						if ($pri_venc && $privenc != ""){
							@list($d,$m,$a) = explode("/",$pri_venc);
						} else {
							$m = date(m);
							$d = date(d);
							$a = date(Y);
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
					
					$this->tpl->atribui("dias_prorata",$diferenca);
					$this->tpl->atribui("valor_prorata",$valor_prorata);
					
					}
					
					
					====> PRO-RATA - FINAL <=====*/
					
					
					
					list($ca, $cm, $cd) = explode("-", $data_contratacao);
					
					$arqtmp = tempnam("/tmp","cn-");
					$fd = fopen($arqtmp,"w");

					
					switch($forma_pagamento) {
						case 'POS':
								
								if($id_cobranca == 2) { //Tipo de pagamento for carnê
								
									$ini_carne = @$_REQUEST["ini_carne"];
									$data_carne = @$_REQUEST["data_carne"];
									$prorata = @$_REQUEST["prorata"];
									$valor_prorata = @$_REQUEST["valor_prorata"];
									$pri_venc = @$_REQUEST["pri_venc"];
									
									//echo "PRORATA: $prorata <br>";
									//echo "VALOR: $valor_prorata <br>";
									
									
									
									
									
									list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
									list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);
									
									$stamp_inicial = mktime(0,0,0, $ini_m, $ini_d, $ini_a);
									$stamp_final = mktime(0,0,0, $dat_m, $dat_d, $dat_a);
									
									$diferenca_meses = (($stamp_final - $stamp_inicial) / 86400) / 30;
									
									if($tx_instalacao > 0) {
										
										$fatura_valor = $tx_instalacao;
										$hoje = date("Y-m-d");
										
										if (!$pri_venc){
											$fatura_dt_vencimento = $hoje;
										}else{
											$fatura_dt_vencimento = $pri_venc;
										}
										
										//Calcula a data dos próximos pagamentos de fatura.
										
										//echo "VALOR_FATURA: $fatura_valor <br>";
										// $id_cliente_produto_new = ao id_cliente_produto do contrato/conta nova.
																					
									
										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago, id_carne";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto_new, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne";
										$sSQL .= ")";
																					
										//Echo "Fatura:  $sSQL<br>\n";
										$this->bd->consulta($sSQL);
																						
									}
									
									for($i=0; $i<=floor($diferenca_meses); $i++) {
									
										//Aplica descontos, caso haja algum período de desconto declarado
										if($qt_desconto > 0) {
											
											$fatura_desconto = $desconto_promo;
											$qt_desconto--;
											
										} else {
											
											$fatura_desconto = 0;
										
										}
										//Adiciona taxa de instalação na fatura, caso haja.
										if ($i==0) { //Cria primeira fatura pós-paga

												if ($prorata == true){ // pega se existe prorata e soma no valor da primeira fatura
													
													$fatura_valor = $valor_prorata;
													//echo "valor com prorata: $fatura_valor <br>";
												}
												
												if($pri_venc != ""){
													list($ini_d, $ini_m, $ini_a) = explode("/", $pri_venc);
													$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
												}else{
													$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
												}


											//Adiciona-se ao valor da fatura o valor do pro-rata																														

											//Se houver taxa de instalação no pós pago, então a primeira fatura do carnê será referente à taxa de instalação
											
											/*if($tx_instalacao > 0) {
											   
												$fatura_valor += $tx_instalacao;
												
												//Calcula a data dos próximos pagamentos de fatura.
												
												//echo "VALOR_FATURA: $fatura_valor <br>";
												

												$sSQL =  "INSERT INTO cbtb_faturas(";
												$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
												$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
												$sSQL .= "	acrescimo, valor_pago ";
												$sSQL .= ") VALUES (";
												$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
												$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
												$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago ";
												$sSQL .= ")";
												
												//echo "Fatura:  $sSQL<br>\n";
												$this->bd->consulta($sSQL);
													
											}*/
											$fatura_valor = $valor_contrato;
											
											//$data = $fatura_dt_vencimento;
											//$fatura = $this->carne($id_cliente_produto_new,$data,$id_cliente);
																				
											//fputs($fd,$fatura);
											


										}else{
										
										
											$fatura_valor = $valor_cont_temp;
										
										}
										
										//Calcula o desconto sobre a fatura.
										$fatura_valor -= $fatura_desconto;

										//Calcula a data dos próximos pagamentos de fatura.
											$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m+$i, $ini_d, $ini_a));
										

										//echo "VALOR FATURA: $fatura_valor <br>";
										//echo "DT VENC: $fatura_dt_vencimento <br>";
										
										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago, id_carne ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto_new, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
										$sSQL .= ")";

									//echo "FATURAS: $sSQL<br>";
										$this->bd->consulta($sSQL);
										
										$data = $fatura_dt_vencimento;
										$fatura = $this->carne($id_cliente_produto_new,$data,$id_cliente,$forma_pagamento);
										
										fputs($fd,$fatura);

										
										/*
										list($dt_final_a, $dt_final_m, $dt_final_d) = explode("-", $fatura_dt_vencimento);
										
										$stamp_dt1 = mktime(0,0,0,$dt_final_m, $dt_final_d, $dt_final_a);
										
										//if("$dt_final_d/$dt_final_m/$dt_final_a" == "$data_carne") break; */
										
										
										
										
									}																	
								} else { //Caso a cobrança não seja do tipo carnê.

									
									$fatura_valor = $valor_cont_temp;

									//Aplica descontos, caso haja algum período de desconto declarado
									if($qt_desconto > 0) {
										$fatura_desconto = $desconto_promo;
										$qt_desconto--;
									} else
										$fatura_desconto = 0;

									//Adiciona taxa de instalação na fatura, caso haja.
									if($tx_instalacao != 0) {
										$fatura_valor = $tx_instalacao;
										
										//Calcula a data dos próximos pagamentos de fatura.
										
										
										$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
										
										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago, id_carne ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto_new, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
										$sSQL .= ")";

									//echo "FATURAS: $sSQL<br>";
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
									$prorata = @$_REQUEST["prorata"];
									$valor_prorata = @$_REQUEST["valor_prorata"];

																	
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
											if ($prorata == true){
												$fatura_valor += $valor_prorata;
												$fatura_valor -= $valor_cont_temp;
											}
											
											if ($pri_venc) {
												list($d, $m, $a) = explode("/",$pri_venc);
												$fatura_dt_vencimento = $a."-".$m."-".$d;
												//echo "DT: $fatura_dt_vencimento <br>";
											}
											//TODO: Procurar função de adição do pro-rata
											if($tx_instalacao > 0) $fatura_valor += $tx_instalacao;

										}else{
										
											$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));
										
										}
										

										
										//Calcula a data dos próximos pagamentos de fatura.
										
										//$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));
										


										//Calcula o desconto sobre a fatura.
										$fatura_valor -= $fatura_desconto;


										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago, id_carne ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto_new, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne";
										$sSQL .= ")";

									//echo " FATURAS: $sSQL<br>";
										$this->bd->consulta($sSQL);
										
										$data = $fatura_dt_vencimento;
										
										$fatura = $this->carne($id_cliente_produto_new,$data,$id_cliente);
										fputs($fd,$fatura);

										
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
									if($pri_venc != ""){
									list($cd, $cm, $ca) = explode("/", $pri_venc);
										$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
									}else{
										$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
									}

									//$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $dia_vencimento, $ca));

									//Calcula o desconto sobre a fatura.
									$fatura_valor -= $fatura_desconto;


									$sSQL =  "INSERT INTO cbtb_faturas(";
									$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
									$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
									$sSQL .= "	acrescimo, valor_pago, id_carne ";
									$sSQL .= ") VALUES (";
									$sSQL .= "	$id_cliente_produto_new, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
									$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
									$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
									$sSQL .= ")";

								//echo "FATURAS: $sSQL<br>";
									$this->bd->consulta($sSQL);
								}
							break;						
					}
					
								$hoje = date("Ymdhms");
								$nome_arquivo = $hoje."-".$id_cliente;
								$host = "dev.mosman.com.br";
					
					
								//$p = new MHTML2PDF();
								//$p->setDebug(1);
								//$arqPDF = $p->converte($arqtmp,$host,$defaultPath='/tmp');
								
								//copy($arqPDF, "./faturas/".$nome_arquivo.".pdf");
					
								fclose($fd);
								


					
					
					
					
					
					
					
					
					//echo "<br>$username";
										

					/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/
?>