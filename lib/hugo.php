<?

					$sSQL  = "SELECT ";
					$sSQL .= "  id_cobranca, nome_cobranca, disponivel ";
					$sSQL .= "FROM ";
					$sSQL .= "cftb_forma_pagamento ";
					$sSQL .= "WHERE ";
					$sSQL .= "disponivel = true";
															
															
					$tipo_cobranca = $this->bd->obtemRegistros($sSQL);
					
					
					//$sSQL  = "SELECT ";
					//$sSQL .= " dominio_padrao, nome, localidade, tx_juros, multa, dia_venc, carencia, ";
					//$sSQL .= " cod_banco, carteira, agencia, num_conta, convenio, observacoes, cnpj, pagamento ";
					//$sSQL .= "FROM ";
					//$sSQL .= " cftb_preferencias ";
					//$sSQL .= "WHERE ";
					//$sSQL .= " id_provedor = '1'";
					
					$preferencias = $this->prefs->obtem("total");


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
					$this->tpl->atribui("dominio_padrao",$preferencias["dominio_padrao"]);
					$this->tpl->atribui("nome_provedor",$preferencias["nome"]);
					$this->tpl->atribui("localidade",$preferencias["localidade"]);
					$this->tpl->atribui("cod_banco",$preferencias["cod_banco"]);
					$this->tpl->atribui("carteira",$preferencias["carteira"]);
					$this->tpl->atribui("agencia",$preferencias["agencia"]);
					$this->tpl->atribui("num_conta",$preferencias["num_conta"]);
					$this->tpl->atribui("convenio",$preferencias["convenio"]);
					$this->tpl->atribui("observacoes",$preferencias["observacoes"]);
					$this->tpl->atribui("cnpj",$preferencias["cnpj"]);
					$this->tpl->atribui("pagamento",$preferencias["pagamento"]);
					$this->tpl->atribui("hoje",$hoje);

					

?>
