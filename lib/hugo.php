<?

					$sSQL  = "SELECT ";
					$sSQL .= "  id_cobranca, nome_cobranca, disponivel ";
					$sSQL .= "FROM ";
					$sSQL .= "cftb_forma_pagamento ";
					$sSQL .= "WHERE ";
					$sSQL .= "disponivel = true";
															
															
					$tipo_cobranca = $this->bd->obtemRegistros($sSQL);
										
					$sSQL  = "SELECT carencia, tx_juros, multa, dia_venc, dominio_padrao FROM cftb_preferencias where id_provedor = '1'";
					$preferencias = $this->bd->obtemUnicoRegistro($sSQL);


					$sSQL  = "SELECT ";
					$sSQL .= "	id_produto, nome, descricao, tipo, valor, disponivel, num_emails, quota_por_conta, ";
					$sSQL .= "	vl_email_adicional, permitir_outros_dominios, email_anexado, numero_contas ";
					$sSQL .= "FROM ";
					$sSQL .= "	prtb_produto ";
					
					$lista_de_produtos = $this->bd->obtemRegistros($sSQL);
					
					global $_LS_FORMA_PAGAMENTO;
					$this->tpl->atribui("forma_pagamento",$_LS_FORMA_PAGAMENTO);
					$this->tpl->atribui("listaGeralProdutos",$lista_de_produtos);
					
					//echo "carencia: ".$preferencias["carencia"]."<br>";
					//echo "tx_juros: ".$preferencias["tx_juros"]."<br>";
					//echo "multa: ".$preferencias["multa"]."<br>";
					//echo "dia_venc: ".$preferencias["dia_venc"]."<br>";
					
					$hoje = date("d/m/Y"); 
					
					$this->tpl->atribui("carencia", $preferencias["carencia"]);
					$this->tpl->atribui("tx_juros", $preferencias["tx_juros"]);
					$this->tpl->atribui("multa", $preferencias["multa"]);
					$this->tpl->atribui("dia_venc", $preferencias["dia_venc"]);
					$this->tpl->atribui("hoje",$hoje);

?>