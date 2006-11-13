<?

	/**
	 * VABELimpeza.class.php
	 *
	 * Procedimentos de limpeza do sistema.
	 */
	
	require_once(PATH_LIB."/VirtexAdminBackend.class.php");
	require_once(PATH_LIB."/AtuadorBandaLarga.class.php");
	//require_once(PATH_LIB."/Atuador.class.php");
	require_once("SOFreeBSD.class.php");

	class VABELimpeza extends VirtexAdminBackend {

		/**
		 * Construtor
		 */
		public function __construct() {
			parent::__construct();
			$this->initVars();
			
			// Configura o getopt e chama as opções para processamento posterior
			$this->_shortopts = "DM";	// Diária ou mensal
			$this->_longopts = array("cron-info");
			$this->getopt();
		
		}

		/**
		 * Inicializa as propriedades do objeto;
		 */
		protected function initVars() {

		}
		
		public function executa() {

			/**
			 * Varre as opções
			 *
			 *    Cada linha é um array contendo um par 0 => opcao, 1 => parametro
			 *
			 */
			
			for($i=0;$i<count($this->options);$i++) {
				switch($this->options[$i][0]) {
					case 'D':
						// Limpeza diária
						$this->limpezaDiaria();
						break;
					case 'M':
						// Limpeza mensal
						$this->limpezaMensal();
						break;
					case 'h':
					case '--help':
					
						break;
					case '--cron-info':
						echo "#\n";
						echo "# CERTIFIQUE-SE DE ADICIONAR AO CRON AS SEQUINTES LINHAS:\n";
						echo "#\n\n";
						echo "# Limpeza diária\n";
						echo "# executada as 4:00 todos os dias.\n";
						echo "##########################################\n";
						echo "0 4 * * * /usr/local/bin/php /mosman/virtex/app/bin/vtx-limpeza.php -D";
						echo "\n\n";
						echo "# Limpeza mensal\n";
						echo "# executada as 4:00 do dia primeiro.\n";
						echo "##########################################\n";
						echo "0 4 1 * * /usr/local/bin/php /mosman/virtex/app/bin/vtx-limpeza.php -M";
						echo "\n\n";
						break;
				}
			}		
		
		}
		
		/**
		 * Cria tabela para armazenamento dos logs envelhecidos do radius
		 */
		protected function criaAgedAccounting() {
			$sSQL = "CREATE TABLE aged_rdtb_accounting AS SELECT * FROM rdtb_accounting WHERE true = false";
			$this->bd->consulta($sSQL,false);
		}
		
		/**
		 * Envelhece o log de erros do radius
		 */
		protected function envelheceErros() {
			$sSQL = "INSERT INTO aged_rdtb_accounting SELECT * FROM rdtb_accounting WHERE login < date_trunc('day',now() - interval '2 days') AND session_id like 'E:%'";
			$this->bd->consulta($sSQL,false);
			$sSQL = "DELETE FROM rdtb_accounting WHERE login < date_trunc('day',now() - interval '2 days') AND session_id like 'E:%'";
			$this->bd->consulta($sSQL);

			$this->vacuum("rdtb_accounting");
		}
		
		/**
		 * Envelhece os registros do radius
		 */
		protected function envelheceAccounting() {
			$sSQL = "INSERT INTO aged_rdtb_accounting SELECT * FROM rdtb_accounting WHERE login < date_trunc('month',now() - interval '2 month')";
			$this->bd->consulta($sSQL,false);
			$sSQL = "DELETE FROM rdtb_accounting WHERE login < date_trunc('month',now() - interval '2 month')";
			$this->bd->consulta($sSQL,false);
			
			$this->vacuum("rdtb_accounting");
		}
		
		protected function vacuum($tabela=""){
			$sSQL = "VACUUM FULL $tabela";
			$this->bd->consulta($sSQL,false);
		}
		
		
		
		protected function limpezaDiaria() {
			$this->criaAgedAccounting();
			$this->envelheceErros();
		}
		
		protected function limpezaMensal() {
			$this->criaAgedAccounting();
			$this->envelheceAccounting();
			$this->vacuum();
		}
	
	}

?>
