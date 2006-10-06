<?

/*
*
*
*  Classe para obtenção das Preferências.
*
*/

class Preferencias {

   protected $bd;

   protected $prefs;

   function __construct($bd){
      $this->bd = $bd;
      $this->prefs["geral"] = array();
      $this->prefs["provedor"] = array();
      $this->prefs["cobranca"] = array();
      $this->prefs["total"] = array();
   }

 

   function obtem($classe=null,$prop=null) {
   
   		if( !$classe ) {
   			$this->obtem("geral");
   			$this->obtem("provedor");
   			$this->obtem("cobranca");
   			
   			return($this->prefs);
   		}
   

      if(!count($this->prefs[$classe])) {

         // Faz o select

         if( $classe == "provedor" ){

			// Preferencias do provedor
			$sSQL  = "SELECT ";
			$sSQL .= "endereco, localidade, cep, cnpj, fone ";
			$sSQL .= "FROM pftb_preferencia_provedor ";
			


         }else if ( $classe == "geral" ){
         	// Preferencias gerais
		 	$sSQL  = "SELECT "; 
		 	$sSQL .= "dominio_padrao, nome, radius_server, hosp_server, hosp_ns1, hosp_ns2, hosp_uid, hosp_gid, mail_server, mail_uid, mail_gid, pop_host, smtp_host, hosp_base, agrupar, email_base ";
		 	$sSQL .= "FROM pftb_preferencia_geral";

         }else if ( $classe == "cobranca" ){
        	// Preferencias de Cobranca
			$sSQL = "SELECT ";
			$sSQL .= "tx_juros, multa, dia_venc, carencia, cod_banco, carteira, agencia, num_conta, convenio, observacoes, pagamento, path_contrato, cod_banco_boleto, carteira_boleto, agencia_boleto, conta_boleto, convenio_boleto, enviar_email, mensagem_email ";
			$sSQL .= "FROM pftb_preferencia_cobranca ";
			
         }else if ($classe == "total" ){
        	//Preferencias Totais
			$sSQL  = "SELECT ";
			$sSQL .= " pc.tx_juros, pc.multa, pc.dia_venc, pc.carencia, pc.cod_banco, pc.carteira, pc.agencia, pc.num_conta, pc.convenio, pc.observacoes, pc.pagamento, pc.path_contrato,cod_banco_boleto, carteira_boleto, agencia_boleto, conta_boleto, convenio_boleto, pc.enviar_email, pc.mensagem_email ";
			$sSQL .= " pg.dominio_padrao, pg.nome, pg.radius_server, pg.hosp_server, pg.hosp_ns1, pg.hosp_ns2, pg.hosp_uid, pg.hosp_gid, pg.mail_server, pg.mail_uid, pg.mail_gid, pg.pop_host, pg.smtp_host, pg.hosp_base,pg.email_base, ";
			$sSQL .= " pp.endereco, pp.localidade, pp.cep, pp.cnpj, pp.fone ";
			$sSQL .= "FROM ";
			$sSQL .= "pftb_preferencia_cobranca pc, pftb_preferencia_geral pg, pftb_preferencia_provedor pp ";
			$sSQL .= "WHERE pc.id_provedor = '1' ";
			      
         }
         
      //echo "PREFS: $sSQL <br>";
		
		
        $this->prefs[$classe] = $this->bd->obtemUnicoRegistro($sSQL);

      
      
      }
      
      

 

      if( $prop ) {
         return($this->prefs[$classe][$prop]);

      } else {
         return($this->prefs[$classe]);
      }

 

   }
  }
?>
