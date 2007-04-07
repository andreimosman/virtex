<?


	class VAClientesCobrancaContrato extends VAClientesCobranca {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function processa($op=null) {

			if( ! $this->privPodeGravar("_CLIENTES_COBRANCA") ) {
				$this->privMSG();
				return;
			}

			$dominioPadrao = $this->prefs->obtem("geral","dominio_padrao");

			
			$exibirFormulario = true;
			

			$acao = $_REQUEST["acao"];
			
			/**
			 * Tela de Confirmação da Contratação
			 */
			if( $acao == "conf" ) {
				// TODO: VALIDACAO
				
				$tipo = @$_REQUEST["tipo"];

				/**
				 * Informações do contratante
				 */
				$cltb_cliente = VirtexModelo::factory("cltb_cliente");
				$cliente = $cltb_cliente->obtemUnico(array("id_cliente" => $this->id_cliente));
				$this->tpl->atribui("cliente",$cliente);
				
				/**
				 * Informações do produto
				 */
				
				// Produto geral
				$prtb_produto = VirtexModelo::factory("prtb_produto");
				// Produto específico
				$prtb_produto_tipo = $prtb_produto->factoryByType($tipo);
				$produto=$prtb_produto_tipo->obtemUnico(array("id_produto"=>@$_REQUEST["id_produto"]));
				$this->tpl->atribui("produto",$produto);
				
				$id_pop = @$_REQUEST["id_pop"];
				$id_nas = @$_REQUEST["id_nas"];
				
				switch($tipo) {
					case 'BL':
						/**
						 * Informações do POP
						 */
						$cftb_pop = VirtexModelo::factory("cftb_pop");
						$pop = $cftb_pop->obtemUnico(array("id_pop" => $id_pop));
						$this->tpl->atribui("pop",$pop);

						/**
						 * Informações do POP
						 */
						$cftb_nas = VirtexModelo::factory("cftb_nas");
						$nas = $cftb_nas->obtemUnico(array("id_nas" => $id_nas));
						$this->tpl->atribui("nas",$nas);
						
						break;
				}
				
				
				/**
				 * Cálculo de Pró-Rata
				 */
				
				$valor_contrato = $produto["valor"];

				$comodato = @$_REQUEST["comodato"];
				$valor_comodato = @$_REQUEST["valor_comodato"]; 

				if( $comodato ) {
					$valor_contrato += $valor_comodato;
				}
				

				$prorata = @$_REQUEST["prorata"];
				
				if( $prorata ) {
					// Operador selecionou a opção de pró-rata
					
					$pri_venc = @$_REQUEST["pri_venc"];		// Data do primeiro vencimento
					
					if( $pri_venc && $pri_venc != "" ) {
						// Pega data da cobrança preenchida
						@list($d,$m,$a) = explode("/",$pri_venc);
					} else {
						// Pega now()
						$m = date("m");
						$d = date("d");
						$a = date("Y");
					}
					
					
					$proxima = ($m+1)."/".$dia_vencimento."/".$a;
					$primeiro = $m."/".$d."/".$a;

					$diferenca = $this->days_diff($primeiro,$proxima);

					$valor_dia = $valor_contrato / 30;
					$valor_prorata = $valor_dia * $diferenca;
				
				}
				
				
				$this->tpl->atribui("valor_prorata",(float)@$valor_prorata);
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				while(list($vr,$vl)=each(@$_REQUEST)) {
					$this->tpl->atribui($vr,$vl);
				}
				

				$this->arquivoTemplate="cliente_contrato_detalhe.html";
				
				return;

			}
			




			
			
			
			
			
			if( $exibirFormulario ) {

				/**
				 * Informações dos produtos
				 */

				$prtb_produto 				= VirtexModelo::factory("prtb_produto");
				$prtb_produto_bandalarga 	= VirtexModelo::factory("prtb_produto_bandalarga");
				$prtb_produto_discado 		= VirtexModelo::factory("prtb_produto_discado");
				$prtb_produto_hospedagem 	= VirtexModelo::factory("prtb_produto_hospedagem");

				$filtro = array("disponivel" => "1");

				$lista_bandalarga 	= $prtb_produto_bandalarga->obtem($filtro);
				$lista_discado 		= $prtb_produto_discado->obtem($filtro);
				$lista_hospedagem 	= $prtb_produto_hospedagem->obtem($filtro);

				$this->tpl->atribui("lista_bandalarga",$lista_bandalarga);
				$this->tpl->atribui("lista_discado",$lista_discado);
				$this->tpl->atribui("lista_hospedagem",$lista_hospedagem);

				/**
				 * Preferencias
				 */
				$this->tpl->atribui("carencia", $this->prefs->obtem("total","carencia"));
				$this->tpl->atribui("tx_juros", $this->prefs->obtem("total","tx_juros"));
				$this->tpl->atribui("multa", $this->prefs->obtem("total","multa"));
				$this->tpl->atribui("dia_venc", $this->prefs->obtem("total","dia_venc"));
				$this->tpl->atribui("dominio_padrao",$this->prefs->obtem("total","dominio_padrao"));
				$this->tpl->atribui("nome_provedor",$this->prefs->obtem("total","nome"));
				$this->tpl->atribui("localidade",$this->prefs->obtem("total","localidade"));
				$this->tpl->atribui("cod_banco",$this->prefs->obtem("total","cod_banco"));
				$this->tpl->atribui("carteira",$this->prefs->obtem("total","carteira"));
				$this->tpl->atribui("agencia",$this->prefs->obtem("total","agencia"));
				$this->tpl->atribui("num_conta",$this->prefs->obtem("total","num_conta"));
				$this->tpl->atribui("convenio",$this->prefs->obtem("total","convenio"));
				$this->tpl->atribui("observacoes",$this->prefs->obtem("total","observacoes"));
				$this->tpl->atribui("cnpj",$this->prefs->obtem("total","cnpj"));
				$this->tpl->atribui("pagamento",$this->prefs->obtem("total","pagamento"));


				/**
				 * Equipamentos
				 */
				$cftb_pop = VirtexModelo::factory("cftb_pop");
				$lista_pops = $cftb_pop->obtemPopsDisponiveis();
				$this->tpl->atribui("lista_pops",$lista_pops);
				
				$cftb_nas = VirtexModelo::factory("cftb_nas");
				$lista_nas = $cftb_nas->obtemNasBandaLarga();
				$this->tpl->atribui("lista_nas",$lista_nas);
				
				/**
				 * Opções de Pagamento
				 */
				
				$cftb_forma_pagamento = VirtexModelo::factory("cftb_forma_pagamento");
				$this->tpl->atribui("tipo_cobranca",$cftb_forma_pagamento->obtemFormasPagamentoDisponiveis());


				/**
				 * Outros valores
				 */
				
				$this->tpl->atribui("hoje",date("d/m/Y"));
				
				//echo "<PRE>";
				//print_r(self::$_formas_pagamento);
				//echo "</PRE>";
				
				$this->tpl->atribui("forma_pagamento", self::$_formas_pagamento);









				/**
				 * Arquivo de Template utilizado
				 */
				$this->arquivoTemplate = "cliente_cobranca_contratar.html";
			}
		}
	
	}


?>
