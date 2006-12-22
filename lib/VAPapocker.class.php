<?

class VAPapocker extends VirtexAdmin {

	public function VAPapocker() {
		parent::VirtexAdmin();
	}
	
	
	public function processa($op="") {
	
	
		if ($op == "acerto_contrato"){
		
			$sSQL = "SELECT * FROM cbtb_cliente_produto";
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			
			
			for ($i=0;$i<count($produtos);$i++){
			
				$id_cliente_produto = $produtos[$i]["id_cliente_produto"];
				$id_produto = $produtos[$i]["id_produto"];
			
			
				$sSQL = "UPDATE cbtb_contrato SET id_produto = $id_produto WHERE id_cliente_produto = $id_cliente_produto";
				$this->bd->consulta($sSQL);
			
				echo "<br>$i<br>";
				echo "id_cliente_produto = $id_cliente_produto<br>";
				echo "id_produto: $id_produto<br>";
			
				
			
			
			
			}
		
		
		
		
		
		}else if ($op == "cria_contratos"){
		
		
			$sSQL = "SELECT cp.id_cliente, cp.id_cliente_produto, cp.id_produto, cn.tipo_conta FROM cbtb_cliente_produto cp, cntb_conta cn WHERE cp.excluido = false AND cp.id_cliente_produto = cn.id_cliente_produto";
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			
			
			
			for ($i=0;$i<count($produtos);$i++){
			
				$id_cliente_produto = $produtos[$i]["id_cliente_produto"];
				$id_produto = $produtos[$i]["id_produto"];
				$id_cobranca = 3;
				$tipo_produto = $produtos[$i]["tipo_conta"];
				
				
				$sSQL  = "INSERT INTO cbtb_contrato (id_cliente_produto, id_produto, data_contratacao, valor_contrato, id_cobranca, status, tipo_produto, ";
				$sSQL .= "valor_produto, num_emails, quota_por_conta, tx_instalacao, valor_comodato, carencia) VALUES ( ";
				$sSQL .= "$id_cliente_produto, $id_produto, '2006-10-23', '0.00',$id_cobranca, 'A', '$tipo_produto', '0.00', '1', '50000', '0.00', '0.00', '20' ) ";
				$this->bd->consulta($sSQL);
				
				echo "SQL: $sSQL <br>";
				echo "NUM: $i<br>ICP: $id_cliente_produto<BR>IP: $id_produto<br>TIPO: $tipo_produto<br><br><hr><br><br>";
				
				
			}
			
			echo "TODOS OS CLIENTES CADASTRADOS: TOTAL: ". $i+1 ."<br>";
			
			
			
			
		
		
		}else if ($op == "natal"){
		
		
			$sSQL = "SELECT username, dominio, tipo_conta FROM cntb_conta WHERE tipo_conta = 'E' and status = 'A'";
			//$sSQL .= " AND username = 'web' AND dominio = 'mosman.com.br' ";
			$emails = $this->bd->obtemRegistros($sSQL);
			
			for ($i=0;$i<count($emails);$i++){
			
				$email = $emails[$i]["username"]."@".$emails[$i]["dominio"];
				
				//echo $i ."-". $email ."<br>";
				
				// MANDA O EMAIL COM ANEXO
				
				$from = "firme@firme.com.br";
				$subject = "FELIZ NATAL A TODOS";
								
				$mailheaders = "From: $from\n";
				$mailheaders .= "Reply-To: $from\n";
				//$mailheaders .= "Cc: $cc\n";
				//$mailheaders .= "Bcc: $bcc\n";
				$mailheaders .= "X-Mailer: Script para enviar arquivo atachado\n";
				
				$msg_body = stripslashes($body); 
				
				
				
				$attach = "/mosman/virtex/dados/hospedagem/mosman.com.br/lixo/Feliz_Natal.jpg";
				
				$file = fopen($attach, "r");
				//$attach_size = $file['size'];
				$contents = fread($file, filesize($attach));
				$encoded_attach = chunk_split(base64_encode($contents));
				fclose($file);
				
				$attach_name = "Feliz_Natal.jpg";
				
				
				$mailheaders .= "MIME-version: 1.0\n";
				$mailheaders .= "Content-type: multipart/mixed; ";
				$mailheaders .= "boundary=\"Message-Boundary\"\n";
				$mailheaders .= "Content-transfer-encoding: 7BIT\n";
				$mailheaders .= "X-attachments: $attach_name";


				$body_top = "--Message-Boundary\n";
				$body_top .= "Content-type: text/plain; charset=US-ASCII\n";
				$body_top .= "Content-transfer-encoding: 7BIT\n";
				$body_top .= "Content-description: Mail message body\n\n";
				
				$msg_body = $body_top . $msg_body;
				
				$msg_body .= "\n\n--Message-Boundary\n";
				$msg_body .= "Content-type: image/jpeg; name=\"$attach_name\"\n";
				$msg_body .= "Content-Transfer-Encoding: BASE64\n";
				$msg_body .= "Content-disposition: inline; filename=\"$attach_name\"\n\n";
				
				$msg_body .= "$encoded_attach\n";
				$msg_body .= "--Message-Boundary--\n";
				$msg_body .="
									A FIRME.COM.BR E A VIRTEX.COM.BR DESEJAM A TODOS UM FELIZ NATAL
									
									
				";
				
				mail($email,$subject,$msg_body,$mailheaders);
				//mail($to, stripslashes($subject), $msg_body, $mailheaders);  
				
				echo $i ."-". $email ."<br>";
			
			}
		
		
		
		}
	
	
	
	
	
	
	
	
	}
	
	
	
	
	
	
	

public function __destruct() {
      	parent::__destruct();
}

}



?>
