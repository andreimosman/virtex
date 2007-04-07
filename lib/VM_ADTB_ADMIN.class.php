<?

	class VM_ADTB_ADMIN extends VirtexModelo {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_admin", "admin", "senha", "status", "nome", "email", "primeiro_login");
			$this->_chave 		= "id_admin";
			$this->_ordem 		= "admin";
			$this->_tabela		= "adtb_admin";
			$this->_sequence	= "adsq_id_admin";
		}

	}
		
?>
