<?

 class VAPersisteCliente extends VAPersiste {
		
		protected $campos;
		
		
		
		public function __construct($bd) {
			parent::__construct($bd);
			
			
			$this->campos["cltb_cliente"] = array("id_cliente","data_cadastro","nome_razao", "tipo_pessoa","rg_inscr", "rg_expedicao", "cpf_cnpj","email","endereco","complemento","id_cidade","cidade","estado","cep","bairro","fone_comercial","fone_residencial","fone_celular","contato","banco","conta_corrente","agencia","dia_pagamento","ativo","obs","provedor","excluido","info_cobranca");
			
		}
		
		/*
		 *Cadastra
		 */
		public function cadastraCliente($dados){
			
			return $this->cadastra("cltb_cliente",$dados);
			
		}
		
		
		/*
		 *Atualiza
		 */
		
		public function atualizaCliente($dados,$condicao){
		
			return $this->atualiza("cltb_cliente",$dados,$condicao);
		
		}
		
		
		/*
		 *Deleta
		 */
		 
		public function excluiCliente($condicao){
		
			return $this->delete("cltb_cliente",$condicao);
		
		}
		
		
		/*
		 *Obtem
		 */
		
		
		public function obtemCliente($condicao){

			return $this->obtem("cltb_cliente",$condicao);						
				
		}
		
		
		/**
		 *Obtem Unico
		 */
		
		public function obtemUnicoCliente($condicao){
		
			return $this->obtemUnico("cltb_cliente",$condicao);						
		
		}	
		
	
	}
			
?>

