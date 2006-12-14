<?

	/**
	 * Base do modelo de persistencia
	 * 
	 * O acesso ao banco de dados é feito através das classes de persistência.
	 * Tanto para pesquisa quanto para manipulação dos dados.
	 */

	class VAPersiste {
		protected $campos;

		public function __construct($bd) {
			$this->bd = $bd;
			$this->campos = array();

		}
		
		/**
		 * Insert Generico
		 */
		protected function _insere($tabela,$campos) {
			$sql = $this->bd->sqlInsert($tabela,$campos);
			return($this->bd->consulta($sql));
		}
		
		/**
		 * Update Generico
		 */
		protected function _atualiza($tabela,$campos,$condicao) {
			$sql = $this->bd->sqlUpdate($tabela,$campos,$condicao);
			retun($this->bd->consulta($sql));
		}
		
		/**
		 * Select Generico
		 */
		
		protected function _obtem($tabela,$campos,$condicao) {
			$sql = $this->bd->sqlSelect($tabela,$campos,$condicao);
			return($this->bd->obtemRegistros($sql));
		}
		
		protected function _obtemUnico($tabela,$campos,$condicao) {
			$sql = $this->bd->sqlSelect($tabela,$campos,$condicao);
			return($this->bd->obtemUnicoRegistro($sql));
		}


		/**
		 * Retorna apenas os dados requeridos pela tabela
		 */
		protected function dadosUteis($tabela,$dados) {
			$d = array();
			while(list($campo,$valor)=each($dados) {
				if( in_array($campo,@$this->campos[$tabela]) ) {
					$d[$campo] = $valor;
				}
			}
			return($d);

		}


		/**
		 * Cadastra os dados de acordo com a tabela
		 */
		protected function cadastra($tabela,$dados) {
			return($this->insere($tabela,$this->dadosUteis($tabela,$dados)));		
		}
		
		/**
		 * Obtem os dados de uma tabela
		 */
		protected function obtem($tabela,$condicao) {
			return($this->_obtem($tabela,$campos[$tabela],$condicao));
		}

		/**
		 * Obtem os dados de uma tabela (uma linha apenas)
		 */
		protected function obtemUnico($tabela,$condicao) {
			return($this->_obtemUnico($tabela,$campos[$tabela],$condicao));
		}
		

	
	
	}

?>
