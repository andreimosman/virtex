<?


	class VACobrancaProduto extends VACobranca {

		public function __construct() {
			parent::__construct();
		}


		public function processa($op = null) {
			if( ! $this->privPodeLer("_COBRANCA_PRODUTOS") ) {
				$this->privMSG();
				return;
			}
			
			$tipo = @$_REQUEST["filtro"] ? @$_REQUEST["filtro"] : @$_REQUEST["tipo"];

			switch($tipo) {
				case 'BL':
					$tabela = "prtb_produto_bandalarga";
					break;
				case 'D':
					$tabela = "prtb_produto_discado";
					break;
				case 'H':
					$tabela = "prtb_produto_hospedagem";
					break;
				default:
					$tabela = "prtb_produto";
					break;

			}

			$prtb_produto = VirtexModelo::factory($tabela);

			
			if( $op == "lista" ) {
				$disponivel = @$_REQUEST["disponivel"];
				if( !$disponivel ) $disponivel = 't';
				$this->tpl->atribui("disponivel",$disponivel);
				
				$produtos = $prtb_produto->obtem(array("disponivel" => $disponivel));
				
				$this->tpl->atribui("lista_produtos", $produtos);
				$this->arquivoTemplate = "cobranca_produtos_lista.html";
			
			} else if( $op == "cadastro" ) {
				$acao = @$_REQUEST["acao"];
				$id_produto = @$_REQUEST["id_produto"];
				
				$enviando = false;
				

				if( $acao ) {
					// Dados vindos de um submit
					$enviando = true;

				} else {

					if( $id_produto ) {
						$acao = "alt";
						$reg = $prtb_produto->obtemUnico(array("id_produto"=>$id_produto));
						while(list($campo,$valor) = each($reg)) {
							$this->tpl->atribui($campo,$valor);
						}
					} else {
						$acao = "cad";
					}

				}
				
				if( $acao == "alt" ) {
					$msg_final = "Produto alterado com sucesso!";				
				} else {
					$msg_final = "Produto cadastrado com sucesso!";				
				}
				
				if( $enviando ) {
				
					// Rotina de cadastro
					if( $id_produto ) {
						// Alteração
						$prtb_produto->altera($_REQUEST,array("id_produto" => $id_produto));
						
					} else {
						// Cadastro
						$prtb_produto->insere($_REQUEST);
					}

					$this->tpl->atribui("mensagem",$msg_final); //pega o conteúdo de msg_final e envia para mensagem que é uma val do smart.
					$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=lista");
					$this->tpl->atribui("target","_self");
					$this->arquivoTemplate="msgredirect.html"; //faz exibir o msgredirect.html que tem vai receber a mensagem de erro ou sucesso.
			      
					return;
				}
				
				$this->tpl->atribui("tipo",$tipo);
				$this->tpl->atribui("acao",$acao);
				$this->tpl->atribui("mensagem",$msg_final);
				//$this->tpl->atribui(""
				

				// Atribui as listas
				//global $_LS_DISPONIVEL;
				$this->tpl->atribui("enum_disponivel",$prtb_produto->enumDisponivel());

				//$bSQL = " SELECT banda,id FROM cftb_banda ";
				$cftb_banda = VirtexModelo::factory("cftb_banda");
				$ls_banda = $cftb_banda->obtem();

				$this->tpl->atribui("enum_banda_download_kbps",$ls_banda);
				$this->tpl->atribui("enum_banda_upload_kbps",$ls_banda);

				if( $tipo == "D" ) {
					$this->tpl->atribui("enum_permitir_duplicidade",$prtb_produto->enumPermitirDuplicidade());
				}

				if( $tipo == "H" ) {
					$this->tpl->atribui("enum_dominio",$prtb_produto->enumDominio());
				}

				$this->tpl->atribui("enum_permitir_outros_dominios",$prtb_produto->enumPermitirOutrosDominios());

				$this->arquivoTemplate = "cobranca_produtos_novo.html";	

			}

		}
	
	}
	
?>
