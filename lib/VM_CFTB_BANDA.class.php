<?

	class VM_CFTB_BANDA extends VirtexModelo {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("banda","id");
			$this->_chave 		= "id";
			$this->_ordem 		= "id";
			$this->_tabela		= "cftb_banda";
			$this->_sequence	= null;
		}
	}

?>
