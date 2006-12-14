<?


	class VAPersisteConta extends VAPersiste {
		
		protected $campos;
		
		
		
		public function __construct($bd) {
			parent::__construct($bd);
			
			// Definir os campos utilizados nas tabelas
			$this->campos["cntb_conta"] = array("username","dominio","tipo_conta","senha","id_cliente","id_cliente_produto","id_conta","conta_mestre","status","observacoes");
			$this->campos["cntb_conta_bandalarga"] = array("username","dominio","tipo_conta","id_pop","tipo_bandalarga","ipaddr","rede","upload_kbps","download_kbps","status","mac","id_nas","ip_externo");

		}
		
		

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
		
	}

?>
