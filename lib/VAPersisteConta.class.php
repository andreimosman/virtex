<?


	class VAPersisteConta extends VAPersiste {
		
		protected $campos;
		
		
		
		public function __construct($bd) {
			parent::__construct($bd);
			
			// Definir os campos utilizados nas tabelas
			$this->campos["cntb_conta"] = array("username","dominio","tipo_conta","senha","id_cliente","id_cliente_produto","id_conta","conta_mestre","status","observacoes");
			$this->campos["cntb_conta_bandalarga"] = array("username","dominio","tipo_conta","id_pop","tipo_bandalarga","ipaddr","rede","upload_kbps","download_kbps","status","mac","id_nas","ip_externo");
			$this->campos["cntb_conta_dsicado"] = array("username","dominio","tipo_conta","fone_info");
			$this->campos["cntb_conta_hospedagem"] = array("username","dominio","tipo_conta","tipo_hospedagem","senha_cript","uid","gid","home","shell","dominio_hospedagem");
			$this->campos["cntb_conta_email"] = array("username","dominio","tipo_conta","quota","email");			
			

		}
		
	///BLOCO DE CADASTRO	

		/**
		 * Cadastra conta
		 */
		public function cadastraConta($dados) {
			return $this->cadastra("cntb_conta",$dados);
		}	
		
		
		/**
		 * Cadastra uma conta de banda larga
		 */
		public function cadastraContaBandalarga($dados) {
			return $this->cadastra("cntb_conta_bandalarga",$dados);
		}
		
		/**
		 * Cadastra uma conta de discado
		 */
		public function cadastraContaDiscado($dados) {
			return $this->cadastra("cntb_conta_discado",$dados);
		}		
		
		/**
		 * Cadastra uma conta de discado
		 */
		public function cadastraContaEmail($dados) {
			return $this->cadastra("cntb_conta_email",$dados);
		}		
	///FIM DOS CADASTROS
	
	
	///UPDATE NA CONTA
		/**
		 * Cadastra conta
		 */
		public function atualizaConta($dados,$condicao) {
			return $this->atualiza("cntb_conta",$dados,$condicao);
		}


		/**
		 * Cadastra uma conta de banda larga
		 */
		public function atualizaContaBandalarga($dados,$condicao) {
			return $this->atualiza("cntb_conta_bandalarga",$dados,condicao);
		}

		/**
		 * Cadastra uma conta de discado
		 */
		public function atualizaContaDiscado($dados,$condicao) {
			return $this->atualiza("cntb_conta_discado",$dados,$condicao);
		}		

		/**
		 * Cadastra uma conta de discado
		 */
		public function atualizaContaEmail($dados,$condicao) {
			return $this->atualiza("cntb_conta_email",$dados,$condicao);
		}
	///FIM DO UPDATE
	
	
	
	///EXCLUI CONTAS
		/**
		 * Exclui Conta
		 */
		public function exlcuiConta($condicao) {
			return($this->delete("cntb_conta",$condicao);
		}
		
		
		/**
		 * Exclui Conta BandaLarga
		 */
		public function exlcuiContaBandaLarga($condicao) {
			return($this->delete("cntb_conta_bandalarga",$condicao);
		}
		
		
		/**
		 * Exclui Conta Hospedagem
		 */
		public function exlcuiContaHospedagem($condicao) {
			return($this->delete("cntb_conta_hospedagem",$condicao);
		}
		
		
		/**
		 * Exclui Conta Email
		 */
		public function exlcuiContaEmail($condicao) {
			return($this->delete("cntb_conta_email",$condicao);
		}
		/**
		 * Exclui Conta Discado
		 */
		public function exlcuiContaDiscado($condicao) {
		return($this->delete("cntb_conta_discado",$condicao);
		}

	///FIM DO EXCLUI CONTAS

	
	
	///SELECT NA CONTA

		/**
		 * Obtem Conta
		 */
		public function obtemConta($condicao) {
			return($this->obtem("cntb_conta",$condicao);
		}
		
		/**
		 * Obtem Conta BandaLarga
		 */
		public function obtemContaBandaLarga($condicao) {
			return($this->obtem("cntb_conta_bandalarga",$condicao);
		}
		/**
		 * Obtem Conta Discado
		 */
		public function obtemContaDiscado($condicao) {
			return($this->obtem("cntb_conta_discado",$condicao);
		}
		
		/**
		 * Obtem Conta Hospedagem
		 */
		public function obtemContaHospedagem($condicao) {
			return($this->obtem("cntb_conta_hospedagem",$condicao);
		}
		
		/**
		 * Obtem Conta Email
		 */
		public function obtemContaEmail($condicao) {
			return($this->obtem("cntb_conta_email",$condicao);
		}

		/*
		 *Obtem Unica Conta
		 */
		public function obtemUnicaConta($condicao) {
			return($this->obtemUnico("cntb_conta",$condicao);
		}
		
		/**
		 * Obtem Unica Conta BandaLarga
		 */
		public function obtemUnicaContaBandaLarga($condicao) {
			return($this->obtemUnico("cntb_conta_bandalarga",$condicao);
		}
		/**
		 * Obtem Unica Conta Discado
		 */
		public function obtemUnicaContaDiscado($condicao) {
			return($this->obtemUnico("cntb_conta_discado",$condicao);
		}
		/**
		 * Obtem Conta Hospedagem
		 */
		public function obtemUnicaContaHospedagem($condicao) {
			return($this->obtemUnico("cntb_conta_hospedagem",$condicao);
		}
		/**
		 * Obtem Conta Email
		 */
		public function obtemUnicaContaEmail($condicao) {
			return($this->obtemUnico("cntb_conta_email",$condicao);
		}

	///FIM DOS SELECTS

	
	}

?>
