<?

	/**
	 * Modelo de Produtos: Especializašao: Produtos Banda Larga
	 */
	class VM_PRTB_PRODUTO_BANDALARGA extends VM_PRTB_PRODUTO {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			
			$this->_campos 		= array_merge($this->_campos, array("banda_upload_kbps","banda_download_kbps","franquia_trafego_mensal_gb", "valor_trafego_adicional_gb") );
			$this->_tabela		= "prtb_produto_bandalarga";
			
			$this->_filtros 	= array_merge($this->_filtros,array("franquia_trafego_mensal_gb" => "number", "valor_trafego_adicional_gb" => "number"));
			
		}
	}

?>
