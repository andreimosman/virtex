<?

	/**
	 * Classe de negócios para o modelo de clientes
	 * TODO: Tratar erros em exceptions.
	 */
	class VNCliente extends VirtexNegocio {

		protected $cltb_cliente;
	
		public function __construct() {
			parent::__construct();
			
			$this->cltb_cliente = VirtexModelo::factory("cltb_cliente");
		}

		/**
		 * Cadastra um cliente novo.
		 */
		public function cadastra($dados) {
			
		}
		
		/**
		 * Altera os dados de um cliente
		 */
		public function altera($id_cliente,$dados) {
		
		}
		
		/**
		 * Obtem registros de cltb_cliente.
		 * @param $condicao		Array de condicoes no formato MPersiste
		 * @param $unico		Boolean indicando se a pesquisa retornará apenas um registro.
		 */
		public function obtem($condicao,$unico=false) {
			return $unico ? $this->cltb_cliente->obtemUnico($condicao) : $this->cltb_cliente->obtem($condicao);
		}
		
		public function obtemPeloID($id_cliente) {
			return($this->obtem(array("id_cliente" => $id_cliente), true));
		}
		
		public function obtemPorDOCTO($docto) {
			$filtro["*OR*0"] = array("cpf_cnpj" => $docto, "rg_inscr" => $docto);
			return($this->obtem($filtro));
		}
		
		public function obtemUltimos($limite) {
			return($this->cltb_cliente->obtemUltimos($limite));
		}
		
		public function obtemPeloNome($nome) {
			$filtro["nome_razao"] = "%:" . $nome;
			return($this->obtem($filtro));
		}
		
		/**
		 * Código não testado pois não tinham registros para testes. Assim que tiver registros e testar esta pesquisa REMOVER este comentário.
		 */
		public function obtemPelaConta($info) {
			$erros = array();
			$clientes = array();
			
			$tp = "USER";
		
			// Pesquisa pela conta pode ser pelo username, pelo e-mail, pelo ip/rede ou pelo MAC
			if( MRegex::email($info) ) {
				$tp = "EMAIL";
			} else {
				if( MRegex::ip($info) ) {
					$tp = "IP";

					@list($endIP,$bitsREDE) = explode("/",$info);

					$qr = $bitsREDE ? $info : $r = $endIP . "/24";
					$r = new RedeIP($qr);

					if( ! $r->isValid() ) {
						$erros[] = "O endereço IP entrado não é válido.";
					} else {
						if( $bitsREDE ) { 
							$info = $r->obtemRede() . "/" . $bitsREDE;
						}
					}


				} else {
					if( MRegex::mac($info) ) {
						$tp = "MAC";
					}
				}
			}
			
			echo "TIPO: $tp\n";

			$contas = array();
			
			if(!count($erros)) {
				
				$filtroConta = array();

				$cntb_conta 			= VirtexModelo::factory("cntb_conta");
				$cntb_conta_bandalarga 	= VirtexModelo::factory("cntb_conta_bandalarga");
				
				
				switch($tp) {

					case 'EMAIL':
						@list($usr,$dom) = explode("@",$info);
						$filtroConta["dominio"] = $dom;
						$filtroConta["tipo_conta"] = "E";
					case 'USER':
						$filtroConta["username"] = '%:' . (@$usr ? $usr : $info);
						break;
						
					case 'MAC':
						$contas = $cntb_conta_bandalarga->obtemPeloMAC($info);
						break;
						
					case 'IP':
						$contas = $cntb_conta_bandalarga->obtemPeloEndereco($info);
						break;
				
				}
				
				if( count($filtroContas) ) {
					$contas = $cntb_conta->obtem($filtroConta);
				}


				$clientes = array();
				$cntCli = array();

				// Cria um array de "atalhos" para as contas de um cliente
				for($i=0;$i<count($contas);$i++) {
					$cntCli[$contas[$i]["id_cliente"]][] = $contas[$i];	
				}
				
				if( count($cntCli) ) {
					$filtro = array("id_cliente" => "in:" . implode("::", array_keys($cntCli)));
					$clientes = $this->cltb_cliente->obtem($filtro);
					
					// Joga as contas de cada cliente para a array clientes;
					for($i=0;$i<count($clientes);$i++) {
						$clientes[$i]["contas"] = $cntCli[$clientes[$i]["id_cliente"]];
					}
					
				}
				
			}
			
			return( $clientes );
			
		}
		
	}

?>
