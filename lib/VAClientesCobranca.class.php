<?


	class VAClientesCobranca extends VAClientes {
	
		protected static $_formas_pagamento;
		protected $rotina;
	
		public function __construct() {
			parent::__construct();
			
			self::$_formas_pagamento = array("PRE" => "Pré Pago", "POS" => "Pós Pago");

			// $tl substituiu $rotina
			$this->rotina = @$_REQUEST["rotina"];
			$this->tpl->atribui("rotina", $this->rotina);

		}
		
		public function processa($op=nul) {
		
			
			switch($this->rotina) {
			
				case 'contratar':
					$tela = new VAClientesCobrancaContrato();
					$tela->processa();
					$tela->exibe();
					break;

				default:
					$tela = new VAClientesCobrancaHome();
					$tela->processa();
					$tela->exibe();
			
			}
		
		
		}
	
	}


?>
