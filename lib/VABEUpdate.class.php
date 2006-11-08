<?

	/**
	 * VABEUpdate.class.php
	 *
	 * Sistema do atualização automática do VA.
	 *
	 */

	require_once(PATH_LIB."/VirtexAdminBackend.class.php");
	require_once("SOFreeBSD.class.php");

	class VABEUpdate extends VirtexAdminBackend {
		protected $sep;

		/**
		 * Construtor
		 */
		public function __construct() {
			parent::__construct();
			$this->initVars();

			// Opções de linha de comando
			$this->_shortopts = "ABC:";
			$this->getopt();
			
		}
		
		protected function initVars() {
			$this->sep = ":::";
		}
		
		public function executa() {
		
		
			for($i=0;$i<count($this->options);$i++) {
				switch($this->options[$i][0]) {
				
					// Gera a informação do banco de dados local
					// Usado geralmente para publicação da nossa estrutura.
					case 'B':
						echo $this->writeDatabaseInfoText($this->loadLocalDatabaseInfo());
						break;
					
					// Compara o banco de dados local com o arquivo especificado.
					// Usado geralmente nos procedimentos pré-atualização
					case 'A':
						$arquivo = "upd/vabd.info";
					case 'C':

						$texto = "";
						if(!@$arquivo) {
							$arquivo = $this->options[$i][1];
						}
						$erro = 0;
						
						//echo "Arquivo: $arquivo\n";

						$fd = @fopen($arquivo,"r");
						if( !$fd ) {
							// Erro ao abrir o arquivo
							$erro = 2;
						} else {
							$texto = fread($fd,filesize($arquivo));
							fclose($fd);
						}
						
						//echo "ERRO: $erro \n";
						//echo "TEXTO: $texto \n";
						
						if( !$erro ) {
							$local_info = $this->loadLocalDatabaseInfo();
							$file_info  = $this->loadDatabaseInfoFromText($texto);
							
							$this->compareDatabaseInfo($local_info,$file_info);
							
						}
				
				}
			}
		
		
			return;
		
		
		
		
		
		
		
		
		
		
		
			// Informações sobre o banco de dados local
			//$info = $this->loadLocalDatabaseInfo();
			//echo $this->writeDatabaseInfoText();
			
			// Informações do banco de dados local.
			$info_local = $this->loadLocalDatabaseInfo();
			
			$this->compareDatabaseInfo($info_local,array());
			
		}
		
		
		/**
		 * Joga para o formato TXT.
		 */
		public function writeDatabaseInfoText($tableinfo) {
			$r = "";
			$sep = $this->sep;
		
			while( list($tabela,$campos) = each($tableinfo) ) {
				for($i=0;$i<count($campos);$i++) {
					$linha = implode($sep,array($tabela,$campos[$i]["nome"],$campos[$i]["tipo"],$campos[$i]["tamanho"],str_replace("public.","",$campos[$i]["flags"])));
					$r .= $linha ."\n";
				}
			}
			
			return($r);

		}
		
		/**
		 * Carrega as informações de um BD a partir de um texto.
		 * Este texto geralmente será publicado no pacote de atualizações
		 */
		public function loadDatabaseInfoFromText($text) {
			$linhas = explode("\n",$text);
			
			$r = array();
			$tabela = "";
			$ultima_tabela = "";
			$c=0;
			$campos = array();
			for($i=0;$i<count($linhas);$i++) {
				//echo "LINHA: " . $linhas[$i] . "\n";
				$linhas[$i]=trim($linhas[$i]);
				if(!strlen($linhas[$i])) {
					continue;
				}
				$info = explode($this->sep,$linhas[$i]);
				$tabela = $info[0];


				$campos[$c]["nome"] 	= $info[1];
				$campos[$c]["tipo"] 	= $info[2];
				$campos[$c]["tamanho"] 	= $info[3];
				$campos[$c]["flags"]	= $info[4];
				$c++;

				
				if( $tabela != $ultima_tabela ) {
					if( $ultima_tabela ) {
						$tmp = array_pop($campos);
						//echo "tab: $ultima_tabela\n";
						//echo "tmp: \n";
						//print_r($campos);
						//echo "------------";
						$r[$ultima_tabela] = $campos;
						/**
						echo "TABELA: $ultima_tabela\n";
						print_r($campos);
						echo "-------------\n";
						
						*/
						$campos = array();
						array_push($campos,$tmp);
						$c=1;
					}

					$ultima_tabela = $tabela;
				}
			}
			
			if( count($campos) && $tabela ) {
				$r[$tabela] = $campos;
				$c=0;	
			}
			
			return($r);
			
		}
		
		/**
		 * Carrega as informações sobre o banco de dados local para análise comparativa
		 */
		protected function loadLocalDatabaseInfo() {
			$sSQL = "SELECT tablename,tableowner,tablespace,hasindexes,hasrules,hastriggers FROM pg_tables WHERE schemaname='public'";
			$tabelas=$this->bd->obtemRegistros($sSQL);
			
			// Array de retorno;
			$r = array();
			
			for($i=0;$i<count($tabelas);$i++) {
				$r[ $tabelas[$i]["tablename"] ] = $this->bd->tableInfo($tabelas[$i]["tablename"]);
			}
			
			return($r);
		}
		
		/**
		 * Converte um array indexado por numeros em um array indexado pelo nome do campo
		 */
		protected function preparaCampos($lista) {
			$r=array();
			for($i=0;$i<count($lista);$i++) {
				$campo = $lista[$i];
				$r[ $campo["nome"] ]  = array( "tipo" => $campo["tipo"], "tamanho" => $campo["tamanho"], "flags" => $campo["flags"] );
			}
			return($r);
		}
		
		/**
		 * Retorna um array contendo
		 * [faltando_a] => Tabelas faltando em A
		 * [faltando_b] => Tabelas faltando em B
		 * [comuns] => Tabelas comuns em ambos os bancos
		 */
		protected function diffTabelas($a,$b) {
			$tabelasA = array_keys($a);
			$tabelasB = array_keys($b);
		
			$diff_a = array_diff($tabelasB,$tabelasA);
			$diff_b = array_diff($tabelasA,$tabelasB);
			
			$faltandoEmA 	= array();
			$faltandoEmB 	= array();
			$tabelasComuns 	= array();
			
			while(list($vr,$vl)=each($diff_a)) {
				$faltandoEmA[]=$vl;
			}

			while(list($vr,$vl)=each($diff_b)) {
				$faltandoEmB[]=$vl;
			}
			
			for($i=0;$i<count($tabelasA);$i++) {
				if( !in_array($tabelasA[$i],$faltandoEmB) ) {
					$tabelasComuns[] = $tabelasA[$i];
				}
			}
			
			return(array("faltando_a" => $faltandoEmA,"faltando_b" => $faltandoEmB,"comuns"=>$tabelasComuns));
		}
		
		/**
		 * Retorna um array contendo
		 * [faltando_a] => Campos faltando em A
		 * [faltando_b] => Campos faltando em B
		 * [comuns] => Campos comuns em ambas as tabelas
		 */
		function diffCampos($campos_a,$campos_b) {
			// Verifica os campos faltando
			$camposA = array_keys($campos_a);
			$camposB = array_keys($campos_b);

			$diff_a = array_diff($camposB,$camposA);
			$diff_b = array_diff($camposA,$camposB);

			$cFaltandoEmA = array();
			$cFaltandoEmB = array();
			$camposComuns = array();

			while(list($vr,$vl)=each($diff_a)) {
				$cFaltandoEmA[]=$vl;
			}

			while(list($vr,$vl)=each($diff_b)) {
				$cFaltandoEmB[]=$vl;
			}

			for($i=0;$i<count($camposA);$i++) {
				if( !in_array($camposA[$i],$cFaltandoEmB) ) {
					$camposComuns[] = $camposA[$i];
				}				
			}

			return(array("faltando_a" => $cFaltandoEmA,"faltando_b" => $cFaltandoEmB,"comuns"=>$camposComuns));
		
		}
		
		/**
		 * Retorna array contendo:
		 * [faltando_] => faltando_a, faltando_b
		 *     [tabelas] => array com os nomes das tabelas
		 *     [campos]  => campos faltando (subarray)
		 *        [$tabela] => nome da tabela com campos faltando (subarray com lista dos campos faltando)
		 *           [$x][nome]
		 *           [$x][tipo]
		 *           [$x][tamanho]
		 *           [$x][flags]
		 * [diff] => campos que diferem em ambas as tabelas
		 *		[$tabela] => nome da tabela com campos diferentes
		 *			[$campo]["?"] => nome do campo, ? = ref na a ou b
		 *				[tipo]
		 *				[tamanho]
		 *				[flags]
		 *		
		 */
		protected function diff($a,$b) {
			$r = array();
			$r["diff"] = array();

			$diffTab = $this->diffTabelas($a,$b);
			$r["faltando_a"]["tabelas"] = $diffTab["faltando_a"];
			$r["faltando_b"]["tabelas"] = $diffTab["faltando_b"];

			// Varre as tabelas comuns
			for($i=0;$i<count($diffTab["comuns"]);$i++) {
				$tabela = $diffTab["comuns"][$i];
				//echo "Verificando: $tabela\n";
				$campos_a = $this->preparaCampos($a[$tabela]);
				$campos_b = $this->preparaCampos($b[$tabela]);
				$diffCmp = $this->diffCampos($campos_a,$campos_b);
				
				// Grava os campos faltando em B
				$c=0; // Zera o contador
				$faltando_b = array();
				for($x=0;$x<count($diffCmp["faltando_b"]);$x++) {
					$campo = $diffCmp["faltando_b"][$x];
					$faltando_b[$tabela][$c] = array("nome"=>$campo,"tipo" =>$campos_a[$campo]["tipo"], "tamanho" => $campos_a[$campo]["tamanho"], "flags" => $campos_a[$campo]["flags"]);
					$c++;
				}
				$r["faltando_b"]["campos"] = $faltando_b;

				// Grava os campos faltando em A
				$c=0; // Zera o contador
				$faltando_a = array();
				for($x=0;$x<count($diffCmp["faltando_a"]);$x++) {
					$campo = $diffCmp["faltando_a"][$x];
					$faltando_a[$tabela][$c] = array("nome"=>$campo,"tipo" =>$campos_b[$campo]["tipo"], "tamanho" => $campos_b[$campo]["tamanho"], "flags" => $campos_b[$campo]["flags"]);
					$c++;
				}
				$r["faltando_a"]["campos"] = $faltando_a;

				// Varre os campos comuns e verifica se são iguais
				for($x=0;$x<count($diffCmp["comuns"]);$x++) {
					$campo = $diffCmp["comuns"][$x];
					// Compara
					if( ($campos_a[$campo]["tipo"] 		!= $campos_b[$campo]["tipo"]) ||
					    ($campos_a[$campo]["tamanho"] 	!= $campos_b[$campo]["tamanho"]) ||
					    ($campos_a[$campo]["flags"]		!= $campos_b[$campo]["flags"])
					  ) {
						$r["diff"][$tabela][$campo]["a"] = $campos_a[$campo];
						$r["diff"][$tabela][$campo]["b"] = $campos_b[$campo];
					}
					
				}
				
				
			}
			
			//print_r($r);
			return($r);

		}
		
		/**
		 * Compara as informações de dois bancos de dados
		 * Retorna a relação do que precisa ser criado em A (presumindo que A é o banco de dados local)
		 */
		protected function compareDatabaseInfo($a,$b) {

			/**
			 * Computa a diferença entre os dos databases
			 */
			
			$diffs = $this->diff($a,$b);
			
			
			/**
			 * Exibe o resumo
			 */
			
			echo "+============================================+\n";
			echo "|                                            |\n";
			echo "|   ANALISE COMPARATIVA DO BANCO DE DADOS    |\n";
			echo "|                                            |\n";
			echo "+============================================+\n";
			echo "\n";
			
			$exibArquivo = 0;
			
			if( count($diffs["faltando_b"]["tabelas"]) || count($diffs["faltando_b"]["campos"]) ) {
				$exibArquivo = 1;
				echo "+--------------------------------------------+\n";			
				echo "| DADOS LOCAIS NAO PRESENTES DO ARQUIVO:     |\n";
				echo "+--------------------------------------------+\n";
				echo "\n";
				echo " TABELAS FALTANDO NO ARQUIVO: \n";
				echo "\n";
				$tabelas = $diffs["faltando_b"]["tabelas"];
				for($i=0;$i<count($tabelas);$i++) {
					echo "  - " . $tabelas[$i] . "\n";
				}
				echo "\n";
				echo " CAMPOS FALTANDO NO ARQUIVO: \n";
				echo "\n";
				while( list($tabela,$campos) = each($diffs["faltando_b"]["campos"]) ) {
					echo "  - " . $tabela . "\n";
					for($i=0;$i<count($campos);$i++) {
						echo "    - " . $campos[$i]["nome"] . ":" . $campos[$i]["tipo"] . ":" . $campos[$i]["tamanho"] . ":" . $campos[$i]["flags"] . "\n";
					}
				}

				echo "\n";
			
			}
			
			
			$exibBD = 0;
			
			if( count($diffs["faltando_a"]["tabelas"]) || count($diffs["faltando_a"]["campos"]) ) {
				$exibBD = 1;
			
				echo "+--------------------------------------------+\n";			
				echo "| DADOS DO ARQUIVO FALTANDO NO BANCO LOCAL   |\n";
				echo "+--------------------------------------------+\n";

				echo "\n";
				echo " TABELAS FALTANDO NO BANCO LOCAL: \n";
				echo "\n";
				$tabelas = $diffs["faltando_a"]["tabelas"];
				for($i=0;$i<count($tabelas);$i++) {
					echo "  - " . $tabelas[$i] . "\n";
				}
				echo "\n";
				echo " CAMPOS FALTANDO NO BANCO LOCAL: \n";
				echo "\n";
				while( list($tabela,$campos) = each($diffs["faltando_a"]["campos"]) ) {
					echo " - " . $tabela . "\n";
					for($i=0;$i<count($campos);$i++) {
						echo "   - " . $campos[$i]["nome"] . ":" . $campos[$i]["tipo"] . ":" . $campos[$i]["tamanho"] . ":" . $campos[$i]["flags"] . "\n";
					}
				}

				echo "\n";

			}
			
			$exibDiff = 0;
			
			if( count($diffs["diff"] ) ) {
				$exibDiff = 1;
				echo "+--------------------------------------------+\n";			
				echo "| CAMPOS QUE DIFEREM NO BANCO E NO ARQUIVO   |\n";
				echo "+--------------------------------------------+\n";
				
				while( list($tabela,$campos) = each($diffs["diff"]) ) {
					echo "  - " . $tabela . "\n";
					//for($i=0;$i<count($campos);$i++) {
					while( list($campo,$dados) = each($campos) ) {
						echo "   - " . $campo . "\n";
						echo "     - banco....: " . $dados["a"]["tipo"] . "/" . $dados["a"]["tamanho"] . "/" .$dados["a"]["flags"] . "\n";
						echo "     - arquivo..: " . $dados["b"]["tipo"] . "/" . $dados["b"]["tamanho"] . "/" .$dados["b"]["flags"] . "\n";
					}
				}
				
				echo "\n";
			}
			
			if( !$exibArquivo && !$exibBD && !$exibDiff ) {
				echo "      BANCO DE DADOS IDENTICO AO ARQUIVO\n";
			}
			

			echo "\n";

			echo "+--------------------------------------------+\n";			
			echo "| FIM DA ANALISE                             |\n";
			echo "+--------------------------------------------+\n";
			echo "\n";

			
		}
	
	
	}
	
?>