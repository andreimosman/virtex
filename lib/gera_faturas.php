<? 

					/*<<=====INICIO DA PARTE DE CONTRATA��O=====>>*/
					
					//Cadastra o contrato do cliente.
					$id_produto = @$_REQUEST["id_produto"];
					
					//Informa��es sobre o provedor
					
					/*$sSQL  = "SELECT ";
					$sSQL .= " pc.tx_juros, pc.multa, pc.dia_venc, pc.carencia, pc.cod_banco, pc.carteira, pc.agencia, pc.num_conta, pc.convenio, pc.observacoes, pc.pagamento, ";
					$sSQL .= " pg.dominio_padrao, pg.nome, pg.radius_server, pg.hosp_server, pg.hosp_ns1, pg.hosp_ns2, pg.hosp_uid, pg.hosp_gid, pg.mail_server, pg.mail_uid, pg.mail_gid, pg.pop_host, pg.smtp_host, pg.hosp_base, ";
					$sSQL .= " pp.endereco, pp.localidade, pp.cep, pp.cnpj ";
					$sSQL .= "FROM ";
					$sSQL .= "pftb_preferencia_cobranca pc, pftb_preferencia_geral pg, pftb_preferencia_provedor pp ";
					$sSQL .= "WHERE pc.id_provedor = '1' ";*/

					//$info_prov = $this->bd->obtemUnicoRegistro($sSQL);
					$info_prov = $this->prefs->obtem();
					
					$cod_banco = $info_prov["cobranca"]["cod_banco"];
					$carteira = $info_prov["cobranca"]["carteira"];
					$agencia = $info_prov["cobranca"]["agencia"];
					$num_conta = $info_prov["cobranca"]["num_conta"];
					$convenio = $info_prov["cobranca"]["convenio"];
					$pagamento = $info_prov["cobranca"]["pagamento"];
					
					if (!$cod_banco) $cod_banco = 0;
					if (!$carteira) $carteira = 0;
					if (!$agencia) $agencia = 0;
					if (!$num_conta) $num_conta = 0;
					if (!$convenio) $convenio = 0;
					
					//Informa��es sobre o produto
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
					$valor_prorata = @$_REQUEST["valor_prorata"];
					$carencia = @$_REQUEST["carencia_pagamento"];
					$vencimento = @$_REQUEST["dia_vencimento"];
					
					
					//Informa��es sobre banco e cart�o de cr�dito
					$cc_vencimento = @$_REQUEST["cc_vencimento"];
					$cc_numero = @$_REQUEST["cc_numero"];
					$cc_operadora = @$_REQUEST["cc_operadora"];
					
					$db_banco = @$_REQUEST["db_banco"];
					$db_agencia = @$_REQUEST["db_agencia"];
					$db_conta = @$_REQUEST["db_conta"];
					
					

					//Corrige poss�veis falhas de entrada em alguns campos
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
					
					$valor_contrato = number_format($valor_produto + $valor_comodato, 2, '.', '');					
					$valor_cont_temp = $valor_contrato;
					//Diminui o desconto no valor real do contrato caso este tenha mesmo per�odo que a vig�ncia do contrato
					if ($periodo_desconto >= $vigencia) $valor_contrato -= $desconto_promo;
					
																									
					$sSQL =  "INSERT INTO cbtb_contrato ( ";
					$sSQL .= "	id_cliente_produto, data_contratacao, vigencia, data_renovacao, valor_contrato, id_cobranca, status, ";
					$sSQL .= "	tipo_produto, valor_produto, num_emails, quota_por_conta, tx_instalacao, comodato, valor_comodato, ";
					$sSQL .= "	desconto_promo, periodo_desconto, id_produto, cod_banco, agencia, num_conta, carteira, ";
					$sSQL .= "	convenio, cc_vencimento, cc_numero, cc_operadora, db_banco, db_agencia, db_conta, carencia, vencimento";
					$sSQL .= ") VALUES ( ";
					$sSQL .= "	$id_cliente_produto, '$data_contratacao', $vigencia, '$data_renovacao', $valor_contrato, $id_cobranca, '$status', ";
					$sSQL .= "	'$tipo_produto', $valor_produto, $num_emails, $quota, $tx_instalacao, '$comodato', $valor_comodato, ";
					$sSQL .= "	$desconto_promo, $periodo_desconto, $id_produto, $cod_banco, $agencia, $num_conta, $carteira, ";
					$sSQL .= "	$convenio, '$cc_vencimento', '$cc_numero', '$cc_operadora', $db_banco, $db_agencia, $db_conta, $carencia, $vencimento ";
					$sSQL .= ")";
					
					
								
					//echo "CONTRATO: $sSQL"."<br>\n";
					$this->bd->consulta($sSQL);	//Salva as configura��es de contrato
					
					
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
							
							//echo "$sSQL";
							
							break;
					}
					
					//echo "<br>$sSQL<br>";
					$this->bd->consulta($sSQL);
					
					$this->contratoHTML($id_cliente,$id_cliente_produto,$tipo_produto);
					

					/*<<=====FIM DA PARTE DE CONTRATA��O=====>>*/


					/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/
					$_id_carne = $this->bd->proximoID('cbsq_id_carne');
					$q = 0;

					$sSQL  = "INSERT INTO cbtb_carne ";
					$sSQL .= "(id_carne, data_geracao,id_cliente_produto,valor,status,vigencia,id_cliente) ";
					$sSQL .= "VALUES ";
					$sSQL .= "('$_id_carne','$data_contratacao','$id_cliente_produto','$valor_contrato','A','$vigencia','$id_cliente') ";

					$this->bd->consulta($sSQL);
					//echo "CARNE: $sSQL <br>";

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
					
					$pos = 0; //Jogar para o pr�ximo m�s
					
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
					
					$estilo = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
					fputs($fd,$estilo,strlen($estilo));

		
					//fputs($fd,$estilo);
					
					switch($forma_pagamento) {
						case 'POS':
								
								if($id_cobranca == 2) { //Tipo de pagamento for carn�
								
										require_once( PATH_LIB . "/inc_carne_pos.php");
								
								} else if ($id_cobranca == 1){ // caso a cobran�a seja boleto	
								
										require_once("inc_boleto_pop.php");								
									
								} else { //Caso a cobran�a n�o seja do tipo carn� nem boleto (carne = 2 e boleto = 1).

									
									$fatura_valor = $valor_cont_temp;

									//Aplica descontos, caso haja algum per�odo de desconto declarado
									if($qt_desconto > 0) {
										$fatura_desconto = $desconto_promo;
										$qt_desconto--;
									} else
										$fatura_desconto = 0;

									//Adiciona taxa de instala��o na fatura, caso haja.
									if($tx_instalacao != 0) {
										$fatura_valor = $tx_instalacao;
										
										//Calcula a data dos pr�ximos pagamentos de fatura.
										
										$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
										
										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago, id_carne ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
										$sSQL .= ")";

										//echo "FATURA: $sSQL<br>";
										$this->bd->consulta($sSQL);
									}								
									
								}									
								
							break;
							
						case 'PRE':
								if($id_cobranca == 2) {	//Tipo pagamento for Carn�.
										
										require_once( PATH_LIB ."/inc_carne_pre.php");

								}else if ($id_cobranca == 1){// se cobranca � por boleto
										
										require_once( PATH_LIB . "/inc_boleto_pre.php");
										
								} else {//se a cobranca n�o � por carne nem boleto
								
								
									$fatura_valor = $valor_cont_temp;

									//Aplica descontos, caso haja algum per�odo de desconto declarado
									if($qt_desconto > 0) {
										$fatura_desconto = $desconto_promo;
										$qt_desconto--; 
									} else
										$fatura_desconto = 0;


									if($tx_instalacao != 0) $fatura_valor += $tx_instalacao;

									//Calcula a data dos pr�ximos pagamentos de fatura.
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
									$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
									$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
									$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
									$sSQL .= ")";

									//echo "FATURA: $sSQL<br>";
									$this->bd->consulta($sSQL);
								}
							break;						
					}
					
								$hoje = date("Y-m-d");
								$nome_arquivo = "carne-".$hoje."-".$id_cliente_produto;
								$host = "dev.mosman.com.br";
								//$footer = "</html</body>";
								//fputs($fd,$footer);
					
								//$p = new MHTML2PDF();
								//$p->setDebug(1);
								//$arqPDF = $p->converte($arqtmp,$host,'/tmp');
								
								//copy($arqtmp, "/mosman/virtex/dados/carnes/".$nome_arquivo.".html");
								//copy($arqtmp, "carnes/".$nome_arquivo.".html");					
								fclose($fd);
								
								//$arquivo = $nome_arquivo.".pdf";
								
								//header('Pragma: public');
								//header('Expires: 0');
								//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
								//header('Content-Type: application/pdf');
								//header('Content-Disposition: attachment; filename="'.$arquivo.'.pdf"');
								//readfile($arqPDF);

								//echo "<br>$username";
										

					/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/
?>