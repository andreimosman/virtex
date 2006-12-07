<?
	/**
	 * Manipulagco de redes ip
	 * CopyRight(C) 2004 - Andrei de Oliveira Mosman
	 * Todos os Direitos Reservados
	 */
	






	class RedeIP {
		public $addr;
		public $mask;
		public $network;
		public $wildcard;
		public $broadcast;
		
		/**
		 * Métodos estáticos
		 */
		public static function bin2addr($bin) {
			$bin = str_pad($bin,32,'0',STR_PAD_LEFT);
			$a = substr($bin,0,8);
			$b = substr($bin,8,8);
			$c = substr($bin,16,8);
			$d = substr($bin,24,8);

			return(RedeIP::b2d($a).".".RedeIP::b2d($b).".".RedeIP::b2d($c).".".RedeIP::b2d($d));
		}

		public static function addr2bin($addr) {
			list($a,$b,$c,$d) = explode(".",$addr);

			if( $a > 255 || $b > 255 || $c > 255 || $d > 255 ) return -1;

			return( RedeIP::to8bin($a).RedeIP::to8bin($b).RedeIP::to8bin($c).RedeIP::to8bin($d) );
		}

		public static function d2b($num) {
			return base_convert($num,10,2);
		}

		public static function b2d($num) {
			return (double)base_convert($num,2,10);
		}

		public static function to8bin($numero) {
			return( str_pad(RedeIP::d2b($numero),8,'0',STR_PAD_LEFT) );
		}

		public static function bitcount2bitmask($bitCount) {
			return( str_repeat('1',$bitCount) . str_repeat('0', 32 - $bitCount) );
		}






		/**
		 * Constructor
		 */
		function RedeIP($rede,$mask="") {
			$bits=0;
			if( $mask ) {
				$this->mask = RedeIP::addr2bin($mask);
			} else {
				//echo "Rede: ". $rede;
				list($rede,$bits) = explode("/", $rede);
				$this->mask = RedeIP::bitcount2bitmask($bits);
			}
			$this->addr = RedeIP::addr2bin($rede);
			$this->network = $this->addr & $this->mask;
			$this->wildcard = str_pad( RedeIP::d2b( RedeIP::b2d($this->mask) ^ RedeIP::b2d(str_repeat('1',32))), 32, '0', STR_PAD_LEFT);

			if( $bits ) {
				$wc = str_repeat('0', $bits ) . str_repeat('1', 32 - $bits );
				$this->wildcard = $wc;
			} else {
				$this->wildcard = str_pad( RedeIP::d2b( RedeIP::b2d($this->mask) ^ RedeIP::b2d(str_repeat('1',32))), 32, '0', STR_PAD_LEFT);
			}
			$this->broadcast = $this->network | $this->wildcard;
		}

		// Retorna lista com todos os ips validos de uma determinada classe.
		function listaIPs() {
			$retorno = Array();
			for($x=RedeIP::b2d($this->network)+1;$x<RedeIP::b2d($this->broadcast);$x++) {
				$retorno[] = RedeIP::bin2addr(RedeIP::d2b($x));
				//echo bin2addr(RedeIP::d2b($x)) . "<br>\n";
			}
			return($retorno);
		}

		function numHosts() {
			return( RedeIP::b2d($this->broadcast) - (RedeIP::b2d($this->network)+1) );
		}

		function minHost() {
			return( RedeIP::bin2addr(RedeIP::d2b(RedeIP::b2d($this->network)+1)) );
		}

		function maxHost() {
			return( RedeIP::bin2addr(RedeIP::d2b(RedeIP::b2d($this->broadcast)-1)) );
		}
		function numIPs() {
			return( RedeIP::b2d($this->broadcast) - RedeIP::b2d($this->network) + 1 );
		}
		
		function isValid() {
			return( $this->addr > 0 );
		}

		function obtemRede() {
			return( RedeIP::bin2addr($this->network) );
		}
		
		

		/**
		 * Pega o numero de bits de uma dada mascara e subclasseia a rede
		 * retorna um array de objetos RedeIP
		 */

		function listaSubRedes($bits) {

			$retorno = Array();

			$fim = 0;

			$rede = RedeIP::b2d($this->network);
			

			while( $rede < RedeIP::b2d($this->broadcast) ) {
				$subrede = new RedeIP( RedeIP::bin2addr(RedeIP::d2b($rede)) . "/" . $bits );

				//echo "NW:".RedeIP::b2d($subrede->network) . "<br>NW:" . RedeIP::b2d($this->network) . "<br><br>\n";
				//echo "BC:".RedeIP::b2d($subrede->broadcast) . "<br>BC:" . RedeIP::b2d($this->broadcast) . "<br><br>\n";

				if( RedeIP::b2d($subrede->network) >= RedeIP::b2d($this->network) && RedeIP::b2d($subrede->broadcast) <= RedeIP::b2d($this->broadcast) ) {
					// echo bin2addr($subrede->network) . "/" . $bits . "<br>\n";
					$retorno[] = $subrede;
				}

				$rede += $subrede->numIPs();
			}
		
			return($retorno);
		}

		function imprimeDebug() {

			echo "A: " . $this->addr ."(". RedeIP::bin2addr($this->addr)  .")". "<br>\n";
			echo "M: " . $this->mask ."(". RedeIP::bin2addr($this->mask)  .")". "<br>\n";
			echo "N: " . $this->network ."(". RedeIP::bin2addr($this->network)  .")". "<br>\n";
			echo "W: " . $this->wildcard ."(". RedeIP::bin2addr($this->wildcard)  .")". "<br>\n";
			echo "BC:" . $this->broadcast ."(". RedeIP::bin2addr($this->broadcast)  .")". "<br>\n";
			echo "<hr>";
			echo "IPs: " . $this->numIPs() . "<br>\n";
			echo "numHosts: " . $this->numHosts() . "<br>\n";
			echo "minHost: " . $this->minHost() . "<br>\n";
			echo "maxHost: " . $this->maxHost() . "<br>\n";

			echo "<hr>";
			//$this->listaIPs();
			//echo "<hr>";


		}
		
		function mascara() {
			return(RedeIP::bin2addr($this->mask));
		}


	}

	//$teste = new RedeIP("192.168.2.128/25");

	//echo "A: " . $teste->addr . "<br>\n";
	//echo "M: " . $teste->mask . "<br>\n";
	//echo "N: " . $teste->network . "<br>\n";
	//echo "W: " . $teste->wildcard . "<br>\n";
	//echo "BC:" . $teste->broadcast . "<br>\n";
	//echo "<hr>";
	//echo "IPs: " . $teste->numIPs() . "<br>\n";
	//echo "numHosts: " . $teste->numHosts() . "<br>\n";
	//echo "minHost: " . $teste->minHost() . "<br>\n";
	//echo "maxHost: " . $teste->maxHost() . "<br>\n";

	//echo "<hr>";
	//$teste->listaIPs();
	//echo "<hr>";
	//$bitsSubRede = 30;
	//$subRedes = $teste->listaSubRedes($bitsSubRede);

	//for( $x=0;$x<count($subRedes);$x++ ) {
	//	echo bin2addr($subRedes[$x]->network) . "/" . $bitsSubRede . "<br>\n";
	//}

?>
