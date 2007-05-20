<?


	class VAClientesPesquisa extends VAClientes {
	
		public function __construct() {
			parent::__construct();
		}
		
		// Processa apenas a pesquisa
		public function processa($op = null) {

			if( ! $this->privPodeLer("_CLIENTES_FICHA") ) {
				$this->privMSG();
				return;
			}

			if (( ! $this->privPodeGravar("_CLIENTES_FICHA"))&&( ! $this->privPodeLer("_CLIENTES_FICHA") )) {
				return;
			}		

			$erros = array();

			$texto_pesquisa = trim(@$_REQUEST['texto_pesquisa']);
			$tipo_pesquisa = @$_REQUEST['tipo_pesquisa'];
			$retorno = @$_REQUEST['retorno'];

			if( $retorno == "XML" ) {
				// Retorno em XML para utilização com ajax
				$this->arquivoTemplate = "clientes_pesquisa.xml";
				header("Content-type: text/xml");
			} else {
				$this->arquivoTemplate = "clientes_pesquisa.html";
			}

			$cltb_cliente = VirtexModelo::factory("cltb_cliente");
			$ordem = $cltb_cliente->obtemOrdem();	// Pega a ordenacao padrao
			$filtro = array("excluido" => 'f');
			$limite="";

			$obtemClientes = true;
			
			
			$cli = new VNCliente();


			if(!$tipo_pesquisa){
				$tipo_pesquisa = "NOME";

				// Ultimos registros cadastrados
				$ordem = "id_cliente DESC";
				$limite=10;
				
				$clientes = $cli->obtemUltimos($limite);

			} else {
			
				switch($tipo_pesquisa) {
					case 'NOME':
						$clientes = $cli->obtemPeloNome('%'.$texto_pesquisa.'%');
						break;
					case 'DOCTOS':
						$clientes = $cli->obtemPorDOCTO($texto_pesquisa);
						break;
					case 'CONTA':
					case 'EMAIL':
						$clientes = $cli->obtemPelaConta($texto_pesquisa);
						break;
				}
			
			}

			// Substitui todos os caracteres especiais da array.
			$clientes = $this->specialChars($clientes);

			$this->tpl->atribui("erros",$erros);	// Não tratados.
			$this->tpl->atribui("clientes",$clientes);

			$this->tpl->atribui("tipo_pesquisa",$tipo_pesquisa);
			$this->tpl->atribui("texto_pesquisa",$texto_pesquisa);

		
		}
	
	}
	
?>
