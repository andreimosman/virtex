<?


					/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/
					
						/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/
						//echo $op ."<br>";
						//echo $rotina ."<br>";
						//echo "ID_CLIENTE_PRODUTO: $id_cliente_produto <br>";
						//echo "ID_CLIENTE_PRODUTO NOVO: $id_cliente_produto_novo <br>";
						if ($op == "contratos" && $rotina == "modificar"){
							//echo "ID_CLIENTE_PRODUTO: $id_cliente_produto <br>";
							$id_cliente_produto = $id_cliente_produto_novo;
							//echo "ID_CLIENTE_PRODUTO NOVO: $id_cliente_produto";
						}
						
						
						
						$_id_carne = $this->bd->proximoID('cbsq_id_carne');
						$q = 0;

						$sSQL  = "INSERT INTO cbtb_carne ";
						$sSQL .= "(id_carne, data_geracao,id_cliente_produto,valor,status,vigencia,id_cliente) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$_id_carne','$data_contratacao','$id_cliente_produto','$valor_contrato','A','$vigencia','$id_cliente') ";

						$this->bd->consulta($sSQL);
						////if ($this->bd->obtemErro())  break;
						
						$id_carne = $_id_carne;

						//Cadastro de faturas do contrato.

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

						$fatura_valor = $valor_produto;

						$pos = 0; //Jogar para o próximo mês

						$forma_pagamento = @$_REQUEST["forma_pagamento"];

						list($ca, $cm, $cd) = explode("-", $data_contratacao);

						$arqtmp = tempnam("/tmp","cn-");
						$fd = fopen($arqtmp,"w");

						$estilo = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
						fputs($fd,$estilo,strlen($estilo));

						switch($forma_pagamento) {
							case 'POS':
									if($id_cobranca == 2) { //Tipo de pagamento for carnê

	// CARNÊ POS PAGO - INICIO

												$ini_carne = @$_REQUEST["ini_carne"];
												$data_carne = @$_REQUEST["data_carne"];
												$prorata = @$_REQUEST["prorata"];
												$valor_prorata = @$_REQUEST["valor_prorata"];
												$pri_venc = @$_REQUEST["pri_venc"];

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

													$sSQL  =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL);
													////if ($this->bd->obtemErro())  break;
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
															}

															if($pri_venc != ""){
																list($ini_d, $ini_m, $ini_a) = explode("/", $pri_venc);
																$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
															}else{
																$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
															}


													}else{


														$fatura_valor = $valor_cont_temp;

													}

													//Calcula o desconto sobre a fatura.
													$fatura_valor -= $fatura_desconto;

													//Calcula a data dos próximos pagamentos de fatura.
														$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m+$i, $ini_d, $ini_a));

													$sSQL =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL);
													////if ($this->bd->obtemErro())  break;
													
													$data = $fatura_dt_vencimento;
													$fatura = $this->carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento);

													if( $i>0 && $i % 3 == 0 ) {
														$new_page = "<hr>";
														fputs($fd,$new_page);
													}


													fputs($fd,$fatura);
												}

	// CARNÊ POS PAGO - FINAL




									} else if ($id_cobranca == 1){ // caso a cobrança seja boleto	

	// BOLETO POS PAGO - INICIO

											$ini_carne = @$_REQUEST["ini_carne"];
											$data_carne = @$_REQUEST["data_carne"];
											$prorata = @$_REQUEST["prorata"];
											$valor_prorata = @$_REQUEST["valor_prorata"];
											$pri_venc = @$_REQUEST["pri_venc"];

											$hoje = date("d/m/Y");
											list ($d,$m,$a) = explode("/",$hoje);
											$data_pri_venc = date("d/m/Y", mktime($m+1,$d,$a));

											$forma_pagamento = "POS";

											if($tx_instalacao > 0) {

												$fatura_valor = $tx_instalacao;
												$_hoje = date("Y-m-d");

												if (!$pri_venc){
													$fatura_dt_vencimento = $_hoje;
												}else{
													$fatura_dt_vencimento = $pri_venc;
												}

												$sSQL =  "INSERT INTO cbtb_faturas(";
												$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
												$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
												$sSQL .= "	acrescimo, valor_pago, id_carne ";
												$sSQL .= ") VALUES (";
												$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
												$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
												$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
												$sSQL .= ")";

												$this->bd->consulta($sSQL);
												////if ($this->bd->obtemErro())  break;
												
												
												$data = $fatura_dt_vencimento;
												$fatura = $this->boleto($id_cliente_produto,$data,$id_cliente,$forma_pagamento);
	// BOLETO POS PAGO - FINAL
											}
											
											
									} else { //Caso a cobrança não seja do tipo carnê nem boleto (carne = 2 e boleto = 1).


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

											$sSQL  =  "INSERT INTO cbtb_faturas(";
											$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
											$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
											$sSQL .= "	acrescimo, valor_pago, id_carne ";
											$sSQL .= ") VALUES (";
											$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
											$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
											$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
											$sSQL .= ")";

											$this->bd->consulta($sSQL);
																						
											////if ($this->bd->obtemErro())  break;
										}

								}									


							break;
							case 'PRE':
							
									
									if($id_cobranca == 2) {	//Tipo pagamento for Carnê.

	// CARNE PRE PAGO - INICIO

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
														}
														//TODO: Procurar função de adição do pro-rata
														if($tx_instalacao > 0) $fatura_valor += $tx_instalacao;

													}else{

														$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));

													}

													//Calcula o desconto sobre a fatura.
													$fatura_valor -= $fatura_desconto;


													$sSQL =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL);
													////if ($this->bd->obtemErro())  break;

													$data = $fatura_dt_vencimento;

													$fatura = $this->carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento);

													if( $i>0 && $i % 3 == 0 ) {
														$new_page = "<hr>";
														fputs($fd,$new_page);
													}


													fputs($fd,$fatura);

												}


	// CARNE PRE PAGO - FINAL

									}else if ($id_cobranca == 1){// se cobranca é por boleto

	// BOLETO PRE PAGO - INICIO

												$ini_carne = @$_REQUEST["ini_carne"];
												$data_carne = @$_REQUEST["data_carne"];
												$prorata = @$_REQUEST["prorata"];
												$valor_prorata = @$_REQUEST["valor_prorata"];

												//Adiciona-se ao valor da fatura o valor do pro-rata																														
												if ($prorata == true){
													$fatura_valor += $valor_prorata;
													$fatura_valor -= $valor_cont_temp;
												}

												if ($pri_venc) {

													list($d, $m, $a) = explode("/",$pri_venc);
													$fatura_dt_vencimento = $a."-".$m."-".$d;
												}
												//TODO: Procurar função de adição do pro-rata
												if($tx_instalacao > 0) $fatura_valor += $tx_instalacao;

													//echo $fatura_valor ."<br>";
													$sSQL =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL); 
													////if ($this->bd->obtemErro()) break;

													$data = $fatura_dt_vencimento;
													//echo "SQL: $sSQL <br>";
													//echo "ICP: $id_cliente_produto - DT: $data - IC: $id_cliente - FP: $forma_pagamento - IDCO: $id_cobranca <br>"; 
													$fatura = $this->boleto($id_cliente_produto,$data,$id_cliente,$forma_pagamento);




	// BOLETO PRE PAGO - FINAL
									} else {//se a cobranca não é por carne nem boleto


										$fatura_valor = $valor_cont_temp;

										//Aplica descontos, caso haja algum período de desconto declarado
										if($qt_desconto > 0) {
											$fatura_desconto = $desconto_promo;
											$qt_desconto--; 
										} else{
											$fatura_desconto = 0;
										}if($tx_instalacao != 0){
											$fatura_valor += $tx_instalacao;
										}

										//Calcula a data dos próximos pagamentos de fatura.
										if($pri_venc != ""){
										list($cd, $cm, $ca) = explode("/", $pri_venc);
											$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
										}else{
											$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
										}
										
										//Calcula o desconto sobre a fatura.
										$fatura_valor -= $fatura_desconto;

										$sSQL  =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago, id_carne ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
										$sSQL .= ")";

										$this->bd->consulta($sSQL);
										////if ($this->bd->obtemErro())  break;
									}
									
								break;						
						}//FIM DO SWITCH DE FORMA DE PAGAMENTO

									$hoje = date("Y-m-d");
									$nome_arquivo = "carne-".$hoje."-".$id_cliente_produto;
									$host = "dev.mosman.com.br";
									fclose($fd);


						/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/											
?>
