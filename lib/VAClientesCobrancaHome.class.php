<?


	class VAClientesCobrancaHome extends VAClientesCobranca {
	
		public function __construct() {
			parent::__construct();
			
			$tipo_lista = @$_REQUEST["tipo_lista"];
			$this->tpl->atribui("tipo_lista",$tipo_lista);
		}
		
		public function processa($op=null) {
			$this->arquivoTemplate = "cliente_cobranca_resumo.html";
		}
	
	}


?>
