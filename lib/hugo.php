<?

					$sSQL  = "SELECT ";
					$sSQL .= "  id_cobranca, nome_cobranca, disponivel ";
					$sSQL .= "FROM ";
					$sSQL .= "cftb_forma_pagamento ";
					$sSQL .= "WHERE ";
					$sSQL .= "disponivel = true";
															
															
					$tipo_cobranca = $this->bd->obtemRegistros($sSQL);
										
					$sSQL  = "SELECT dominio_padrao FROM cftb_preferencias where id_provedor = '1'";
					$preferencias = $this->bd->obtemRegistros($sSQL);

					global $_LS_FORMA_PAGAMENTO;
					$this->tpl->atribui("forma_pagamento",$_LS_FORMA_PAGAMENTO);

?>