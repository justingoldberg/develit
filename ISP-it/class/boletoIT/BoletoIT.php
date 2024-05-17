<?

class BoletoIT {

	#banco
	var $nosso_numero;		 		// Nosso Numero S/ DAC
	var $carteira = "175";					// Código da Carteira
	
	#cedente
	var $cedente;					// Nome do cedente
	var $agencia;					// Numero da Agência 4 Digitos s/DAC
	var $conta; 					// Numero da Conta 5 Digitos s/ DAC
	var $digito_conta;			  	// Digito da Conta Corrente 1 Digito
	var $endereco;
	var $cidade;
	
	var $numero_documento;			// Numero do Pedido ou Nosso Numero
	
	#dados cobranca
	var $data_vencimento;			//data de vencimento do boleto.
	var $data_documento;			// Data de emissão do Boleto
	
	var $especie;
	var $valor;						// Valor do Boleto (Utilizar virgula como separador decimal, não use pontos)
		
	#dados sacado
	var $sacado;					// Nome do Seu Cliente
	var $cpf_cnpj_cedente;
	var $endereco1; 				// Rua Teste do Seu Cliente";
	var $endereco2;					// "São Paulo - SP- CEP: 06130-000";
	var $instrucoes = array(); 		//Instruçoes para o Cliente
	
	#dados do documento	
	var $data_processamento;
	var $quantidade;
	var $valor_unitario;
	var $aceite = "N";			
	var $uso_banco = ""; 

	var $codigo_barras;
	
	var $dirImagens="../../class/boletoIT/imagens/";
	
	function BoletoIT(  ) {

	}
	
	
	function set( $campo, $valor, $warn=true ){
		
		$this->$campo = $valor;
		
		if ( empty( $valor ) ){
			avisoNOURL( "ERRO", "campo ".$campo." do boleto ".$this->sacado." vazio", "40%");
		}
		
	}
	
	
	
	
	function carregaDadosBoleto(){ 
		$V4ab10179 = "341";
 		$V92f52e6e = "9";
 		$V077effb5 = "000";
 		$V540e4d39 = $this->F540e4d39( $this->data_vencimento );
		$V01773a8a = $this->F6266027b( $this->valor,10,"0","v" );
		$V9f808afd = $this->F6266027b( $this->agencia,4,"0" );
		$this->agencia = $V9f808afd;
		$Vef0ad7ba = $this->F6266027b( $this->conta,5,"0" );
		$this->conta = $Vef0ad7ba ;
		$V5b3b7abe = $this->F6266027b( $this->nosso_numero,8,"0" );
		$V7c3c1e38 = $this->carteira;
		$V1c90f9c3 = $this->Fd1ea9d43( "$V9f808afd$Vef0ad7ba$V7c3c1e38$V5b3b7abe" );
		$Va5a7044f = $this->Fd1ea9d43( "$V9f808afd$Vef0ad7ba" );
		$Vc21a9e1d = "$V4ab10179$V92f52e6e$V540e4d39$V01773a8a$V7c3c1e38$V5b3b7abe$V1c90f9c3$V9f808afd$Vef0ad7ba$Va5a7044f$V077effb5";
		$V28dfab58 = $this->F80457cf3( $Vc21a9e1d );
		$Vc21a9e1d = "$V4ab10179$V92f52e6e$V28dfab58$V540e4d39$V01773a8a$V7c3c1e38$V5b3b7abe$V1c90f9c3$V9f808afd$Vef0ad7ba$Va5a7044f$V077effb5";
		$Vaf2c4191 = $V9f808afd ."/". $Vef0ad7ba . "-" . $this->digito_conta;
		$V5b3b7abe = $V7c3c1e38 ."/". $V5b3b7abe ."-". $V1c90f9c3;
		$this->codigo_barras = "$Vc21a9e1d";
		$this->linha_digitavel = $this->F5aef63b6( $Vc21a9e1d );
		$this->agencia_codigo = $Vaf2c4191 ;
		$this->nosso_numero = $V5b3b7abe;
 }  
 
 
	function F80457cf3($V0842f867){
 		
		$V0842f867 = $this->F11efdac1($V0842f867);
 		
		if($V0842f867==0 || $V0842f867 >9) $V0842f867 = 1;
 		
 		return $V0842f867;
 		
 	}
 	
 	function F540e4d39($V0842f867){
 		$V0842f867 = str_replace("/","-",$V0842f867);
 		$V465b1f70 = explode("-",$V0842f867);
 		
 		return $this->F1b261b5c($V465b1f70[2], $V465b1f70[1], $V465b1f70[0]);
 	}
 	
 	
 	function F1b261b5c($Vbde9dee6, $Vd2db8a61, $V465b1f70) {
 		return ( abs( ( $this->F5a66daf8("1997","10","07") ) - ( $this->F5a66daf8( $Vbde9dee6, $Vd2db8a61, $V465b1f70 ) ) ) );
 	}
 
 	function F5a66daf8($V84cdc76c,$V7436f942,$V628b7db0) {
 		$V151aa009 = substr($V84cdc76c, 0, 2);
 		$V84cdc76c = substr($V84cdc76c, 2, 2);
		
 		if ($V7436f942 > 2) { 
			$V7436f942 -= 3;
		} 
		else { 
			$V7436f942 += 9;
			if ($V84cdc76c) { 
				$V84cdc76c--;
			} 
			else { $V84cdc76c = 99;
				$V151aa009 --;
		 	}
		}
		 
		 return ( floor((146097 * $V151aa009)/4 ) + floor(( 1461 * $V84cdc76c)/4 ) + floor(( 153 * $V7436f942 +2) /5 ) + $V628b7db0 +1721119);	 
	}
	
	
	function F11efdac1($V0fc3cfbc, $V593616de=9, $V4b43b0ae=0) {
		$V15a00ab3 = 0;
		$V44f7e37e = 2;
		for ($V865c0c0b = strlen($V0fc3cfbc);
		$V865c0c0b > 0;
		$V865c0c0b--) { 
		$V5e8b750e[$V865c0c0b] = substr($V0fc3cfbc,$V865c0c0b-1,1);
		 
		$Vb040904b[$V865c0c0b] = $V5e8b750e[$V865c0c0b] * $V44f7e37e;
		 
		$V15a00ab3 += $Vb040904b[$V865c0c0b];
		if ($V44f7e37e == $V593616de) { 
		$V44f7e37e = 1;
		} $V44f7e37e++;
		}if ($V4b43b0ae == 0) { $V15a00ab3 *= 10;
		$V05fbaf7e = $V15a00ab3 % 11;
		if ($V05fbaf7e == 10) { $V05fbaf7e = 0;
		} return $V05fbaf7e;
		} elseif ($V4b43b0ae == 1){ $V9c6350b0 = $V15a00ab3 % 11;
		return $V9c6350b0;
	}
	
	}
	function Fd1ea9d43($V0fc3cfbc) {$V4ec61c61 = 0;
		$V44f7e37e = 2;
		  
		for ($V865c0c0b = strlen($V0fc3cfbc);
		$V865c0c0b > 0;
		$V865c0c0b--) { 
		$V5e8b750e[$V865c0c0b] = substr($V0fc3cfbc,$V865c0c0b-1,1);
		
		$Vee487e79[$V865c0c0b] = $V5e8b750e[$V865c0c0b] * $V44f7e37e;
		 
		$V4ec61c61 .= $Vee487e79[$V865c0c0b];
		if ($V44f7e37e == 2) { $V44f7e37e = 1;
		} else { $V44f7e37e = 2;
		 
		} }$V15a00ab3 = 0;
		
		for ($V865c0c0b = strlen($V4ec61c61);
		$V865c0c0b > 0;
		$V865c0c0b--) { $V5e8b750e[$V865c0c0b] = substr($V4ec61c61,$V865c0c0b-1,1);
		$V15a00ab3 += $V5e8b750e[$V865c0c0b];
		}$V9c6350b0 = $V15a00ab3 % 10;
		$V05fbaf7e = 10 - $V9c6350b0;
		if ($V9c6350b0 == 0) { $V05fbaf7e = 0;
		}return $V05fbaf7e;
	}
	
	
	
	function F5aef63b6($V41ef8940) { 
 
		$Vec6ef230 = substr($V41ef8940, 0, 4);
		$V1d665b9b = substr($V41ef8940, 19, 5);
		$V7bc3ca68 = $this->Fd1ea9d43("$Vec6ef230$V1d665b9b");
		$V13207e3d = "$Vec6ef230$V1d665b9b$V7bc3ca68";
		$Ved92eff8 = substr($V13207e3d, 0, 5);
		$Vc6c27fc9 = substr($V13207e3d, 5);
		$V8a690a8f = "$Ved92eff8.$Vc6c27fc9";
		  
		 
		$Vec6ef230 = substr($V41ef8940, 24, 10);
		$V1d665b9b = $this->Fd1ea9d43($Vec6ef230);
		$V7bc3ca68 = "$Vec6ef230$V1d665b9b";
		$V13207e3d = substr($V7bc3ca68, 0, 5);
		$Ved92eff8 = substr($V7bc3ca68, 5);
		$V4499f7f9 = "$V13207e3d.$Ved92eff8";
		  
		 
		$Vec6ef230 = substr($V41ef8940, 34, 10);
		$V1d665b9b = $this->Fd1ea9d43($Vec6ef230);
		$V7bc3ca68 = "$Vec6ef230$V1d665b9b";
		$V13207e3d = substr($V7bc3ca68, 0, 5);
		$Ved92eff8 = substr($V7bc3ca68, 5);
		$V9e911857 = "$V13207e3d.$Ved92eff8";
		  
		$V0db9137c = substr($V41ef8940, 4, 1);
		
		$Va7ad67b2 = substr($V41ef8940, 5, 14);
		return "$V8a690a8f $V4499f7f9 $V9e911857 $V0db9137c $Va7ad67b2";
	}
	
	function F294e91c9($V4d5128a0) {
		$Ve2b64fe0 = substr($V4d5128a0, 0, 3);
		$V284e2ffa = $this->F11efdac1($Ve2b64fe0);
		return $Ve2b64fe0 . "-" . $V284e2ffa;
	}
 
 
 
	function F6266027b($V0842f867, $Vce2db5d6, $V0152807c, $V401281b0 = "e"){
		if($V401281b0=="v"){ 
			$V0842f867 = str_replace(".","",$V0842f867);
			 
			$V0842f867 = str_replace(",",".",$V0842f867);
		 
			$V0842f867 = number_format($V0842f867,2,"","");
			$V0842f867 = str_replace(".","",$V0842f867);
		 
			$V401281b0 = "e";
		} 
		
		while(strlen($V0842f867)<$Vce2db5d6){ 
			if($V401281b0=="e"){ 
				$V0842f867 = $V0152807c . $V0842f867;
			}
			else{ 
				$V0842f867 = $V0842f867 . $V0152807c;
			} 
		} 
		
		return $V0842f867;
	}
	
	
	
	function fbarcode(){

		$fino = 1 ;
		$largo = 3 ;
		$altura = 50 ;
		
		$barcodes[0] = "00110" ;
		$barcodes[1] = "10001" ;
		$barcodes[2] = "01001" ;
		$barcodes[3] = "11000" ;
		$barcodes[4] = "00101" ;
		$barcodes[5] = "10100" ;
		$barcodes[6] = "01100" ;
		$barcodes[7] = "00011" ;
		$barcodes[8] = "10010" ;
		$barcodes[9] = "01010" ;
		for($f1=9;$f1>=0;$f1--){ 
	    	for($f2=9;$f2>=0;$f2--){  
	      		$f = ($f1 * 10) + $f2 ;
	      		$texto = "" ;
	      		for($i=1;$i<6;$i++){ 
	        		$texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
	      		}
	      	$barcodes[$f] = $texto;
	   		}
		}


		$ret = "<img src=" . $this->dirImagens . "p.gif width=".$fino." height=".$altura." border=0><img  ".
			         "src=" . $this->dirImagens . "b.gif width=".$fino." height=".$altura." border=0><img ".
					 "src=" . $this->dirImagens . "p.gif width=".$fino." height=".$altura." border=0><img ". 
					 "src=" . $this->dirImagens . "b.gif width=".$fino." height=".$altura." border=0><img ";

		$texto = $this->codigo_barras ;
		if((strlen($texto) % 2) <> 0){
			$texto = "0" . $texto;
		}
		
		// Draw dos dados
		while (strlen($texto) > 0) {
		  $i = round($this->esquerda($texto,2));
		  $texto = $this->direita($texto,strlen($texto)-2);
		  $f = $barcodes[$i];
		  for($i=1;$i<11;$i+=2){
		    if (substr($f,($i-1),1) == "0") {
		      $f1 = $fino ;
		    }else{
		      $f1 = $largo ;
		    }
		
			$ret .= "src=" . $this->dirImagens . "p.gif width=".$f1." height=".$altura." border=0><img ";
		
		    if (substr($f,$i,1) == "0") {
		      $f2 = $fino ;
		    }else{
		      $f2 = $largo ;
		    }
		
		    $ret .= "src=" . $this->dirImagens . "b.gif width=".$f2." height=".$altura." border=0><img ";
		
		   }
		}
		
		$ret .= "src=" . $this->dirImagens . "p.gif width=".$largo." height=".$altura." border=0><img ".
				"src=" . $this->dirImagens . "b.gif width=".$fino." height=".$altura." border=0><img " .
				"src=" . $this->dirImagens . "p.gif width=1 height=".$altura." border=0>";
				
		return $ret;
	}



	function esquerda($entra,$comp){
		return substr($entra,0,$comp);
	}
	
	function direita($entra,$comp){
		return substr($entra,strlen($entra)-$comp,$comp);
	}
	
	/**
	 * Retorno o Cabeçalho HTML dos Boletos 
	 *
	 * @return string
	 */
	function getCabecalho(){
		return "
			<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
			<HTML>
			<HEAD>
			<TITLE>Devel-IT</TITLE><META http-equiv=Content-Type content=text/html; charset=windows-1252>
			<style type=text/css>
			<!--.cp {  font: bold 10px Arial; color: black}
			<!--.ti {  font: 9px Arial, Helvetica, sans-serif}
			<!--.ld { font: bold 15px Arial; color: #000000}
			<!--.ct { FONT: 9px \"Arial Narrow\"; COLOR: #000033}
			<!--.cn { FONT: 9px Arial; COLOR: black }
			<!--.bc { font: bold 22px Arial; color: #000000 }
			--></style> 
			</HEAD>
			<BODY text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0>";
	}
	
	/**
	 * Retorna o Rodapé HTML dos Boletos
	 *
	 * @return string
	 */
	function getRodape(){
		return "</BODY></HTML>";
	}

	function getHtml(){
			return "
<br>

<br><table cellspacing=0 cellpadding=0 width=666 border=0><TBODY><TR><TD class=ct width=666><img height=1 src=" . $this->dirImagens . "6.gif width=665 border=0></TD></TR><TR><TD class=ct width=666><div align=right><b class=cp>Recibo 
do Sacado</b></div></TD></tr></tbody></table><table width=666 cellspacing=5 cellpadding=0 border=0><tr><td width=41></TD></tr></table><BR><table cellspacing=0 cellpadding=0 width=661 border=0><tbody><tr><td class=cp width=137>
      <div align=left><img src=\"" . $this->dirImagens . "logo-itau.jpg\" width=\"135\" height=\"36\"></div></td><td width=4 valign=bottom><img height=22 src=" . $this->dirImagens . "3.gif width=2 border=0></td><td class=cpt  width=63 valign=bottom> 
      <div align=center><font class=bc>341-7</font></div></td><td width=4 valign=bottom><img height=22 src=" . $this->dirImagens . "3.gif width=2 border=0></td><td class=ld align=right width=458 valign=bottom><span class=ld> 
".$this->linha_digitavel."
&nbsp;&nbsp;&nbsp;&nbsp; </span></td>
</tr><tr><td colspan=5><img height=2 src=" . $this->dirImagens . "2.gif width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=298 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Cedente</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=126 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Agência/Código 
do Cedente</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=34 height=13>Espécie</td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=53 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Quantidade</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=120 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Nosso 
número</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=298 height=12> 
".$this->cedente." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=126 height=12> 
".$this->agencia_codigo." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=34 height=12> 
".$this->especie." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=53 height=12> 
".$this->quantidade." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=120 height=12> 
".$this->nosso_numero."
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=298 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=298 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=126 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=126 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=34 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=34 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=53 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=53 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=120 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=120 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td valign=top colspan=3 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Número 
do documento</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=132 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">CPF/CNPJ</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=134 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Vencimento</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Valor 
documento</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top colspan=3 height=12> 
".$this->numero_documento." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=132 height=12> 
".$this->cpf_cnpj_cedente." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=134 height=12> 
".$this->data_vencimento." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
".$this->valor."
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=72 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=72 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=132 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=132 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=134 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=134 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td valign=top width=113 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(-) 
Desconto / Abatimentos</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=112 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(-) 
Outras deduções</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=113 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(+) 
Mora / Multa</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td valign=top width=113 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(+) 
Outros acréscimos</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(=) 
Valor cobrado</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=112 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=112 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=112 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=659 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Sacado</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=659 height=12> 
".$this->sacado." </td></tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=659 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=659 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct  width=7 height=12></td><td class=ct  width=500 ><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Instruções</font></td><td class=ct  width=21 height=12></td><td class=ct  width=138 >Autenticação 
mecânica</td></tr><tr><td  width=7 ></td><td  width=500 ></td><td  width=21 ></td><td  width=138 ></td></tr></tbody></table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td width=7></td><td  width=500 class=cp> 
</td><td width=159></td></tr></tbody></table><table cellspacing=0 cellpadding=0 width=666 border=0><tr><td class=ct width=666></td></tr><tbody><tr><td class=ct width=666> 
<div align=right>Corte na linha pontilhada<span class=\"cp\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span class=\"cp\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div></td></tr><tr><td class=ct width=666><img height=1 src=" . $this->dirImagens . "6.gif width=665 border=0></td></tr></tbody></table><br><br><table cellspacing=0 cellpadding=0 width=664 border=0><tbody><tr><td class=cp width=137>
      <div align=left><IMG SRC=\"" . $this->dirImagens . "logo-itau.jpg\" WIDTH=\"136\" HEIGHT=\"36\"></div></td><td width=4 valign=bottom><img height=22 src=" . $this->dirImagens . "3.gif width=2 border=0></td><td class=cpt  width=65 valign=bottom> 
      <div align=center><font class=bc>341-7</font></div></td><td width=4 valign=bottom><img height=22 src=" . $this->dirImagens . "3.gif width=2 border=0></td><td class=ld align=right width=456 valign=bottom><span class=ld> 
".$this->linha_digitavel."
&nbsp;&nbsp;&nbsp;&nbsp; </span></td>
</tr><tr><td colspan=5><img height=2 src=" . $this->dirImagens . "2.gif width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=472 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Local 
de pagamento</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Vencimento</font></td></tr><tr>
    <td class=cp valign=top width=7 height=12><img height=25 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cn valign=top width=472 height=12>AT&Eacute; O VENCIMENTO, PREFERENCIALMENTE 
      NO ITAU OU BANERJ<br>
      AP&Oacute;S O VENCIMENTO, SOMENTE NO ITA&Uacute; OU BANERJ</td><td class=cp valign=top width=7 height=12><img height=25 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
".$this->data_vencimento."
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=472 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=472 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=472 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Cedente</font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Agência/Código 
cedente</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=472 height=12> 
".$this->cedente." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
".$this->agencia_codigo."
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=472 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=472 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13> 
<img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=113 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Data 
do documento</font></td><td class=ct valign=top width=7 height=13> <img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=163 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">N<u>o</u> 
documento</font></td><td class=ct valign=top width=7 height=13> <img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=62 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Espécie 
doc.</font></td><td class=ct valign=top width=7 height=13> <img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=34 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Aceite</font></td><td class=ct valign=top width=7 height=13> 
<img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=72 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Data 
processamento</font></td><td class=ct valign=top width=7 height=13> <img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Nosso 
número</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=113 height=12><div align=left> 
".$this->data_documento." </div></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=163 height=12> 
".$this->numero_documento." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=62 height=12><div align=left> 
".$this->especie." </div></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=34 height=12><div align=left> 
".$this->aceite." </div></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=72 height=12><div align=left> 
".$this->data_processamento." </div></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
".$this->nosso_numero."
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=113 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=163 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=163 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=62 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=62 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=34 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=34 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=72 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=72 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr> 
<td class=ct valign=top width=7 height=13> <img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top COLSPAN=\"3\" height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Uso 
do banco </font></td><td class=ct valign=top height=13 width=7> <img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=83 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Carteira</font></td><td class=ct valign=top height=13 width=7> 
<img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=53 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Espécie</font></td><td class=ct valign=top height=13 width=7> 
<img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=123 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">Quantidade</font></td><td class=ct valign=top height=13 width=7> 
<img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=72 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\"> 
Valor </font></td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\"><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(=) 
Valor documento</font></td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td valign=top class=cp height=12 COLSPAN=\"3\"><div align=left> 
".$this->uso_banco." </div></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=83> 
<div align=left> ".$this->carteira." </div></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=53><div align=left> 
".$this->especie." </div></td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=123> 
".$this->quantidade." </td><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top  width=72> 
".$this->valor_unitario." </td><td class=cp valign=top width=7 height=12> 
<img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
".$this->valor."
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
</tr><tr><td valign=top width=7 height=1> <img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=75 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=31 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=31 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=83 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=83 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=53 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=53 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=123 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=123 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=72 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=72 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody> 
</table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody> 
<tr> <td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr> 
<td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr> 
<td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=1 border=0></td></tr></tbody></table></td><td valign=top width=468 rowspan=5></font><br><span class=cp> ".$this->instrucoes[0]."<br> 
".$this->instrucoes[1]."<br> ".$this->instrucoes[2]."<br> ".$this->instrucoes[3]."<br> 
".$this->instrucoes[4]."<br> </span></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(-) 
Desconto / Abatimentos</font></td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr> 
<td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10> 
<table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr><td valign=top width=7 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=1 border=0></td></tr></tbody></table></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(-) 
Outras deduções</font></td></tr><tr><td class=cp valign=top width=7 height=12> <img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10> 
<table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13> 
<img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=1 border=0></td></tr></tbody></table></td><td align=right width=188> 
<table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(+) 
Mora / Multa</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr> 
<td valign=top width=7 height=1> <img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1> 
<img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr> 
<td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=1 border=0></td></tr></tbody></table></td><td align=right width=188> 
<table cellspacing=0 cellpadding=0 border=0><tbody><tr> <td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(+) 
Outros acréscimos</font></td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td></tr></tbody></table></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13><font  size=1 face=\"Arial Narrow\"; color=\"#000033\">(=) 
Valor cobrado</font></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr></tbody> 
</table></td></tr></tbody></table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td valign=top width=666 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=659 height=13>Sacado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=659 height=12> 
".$this->sacado." </td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=cp valign=top width=7 height=12><img height=12 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=659 height=12> 
".$this->endereco1." </td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=cp valign=top width=472 height=13> 
".$this->endereco2." </td><td class=ct valign=top width=7 height=13><img height=13 src=" . $this->dirImagens . "1.gif width=1 border=0></td><td class=ct valign=top width=180 height=13>Cód. 
baixa</td></tr><tr><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=472 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=472 border=0></td><td valign=top width=7 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=" . $this->dirImagens . "2.gif width=180 border=0></td></tr></tbody></table><TABLE cellSpacing=0 cellPadding=0 border=0 width=666><TBODY><TR><TD class=ct  width=7 height=12></TD><TD class=ct  width=409 >Sacador/Avalista</TD><TD class=ct  width=250 ><div align=right>Autenticação 
mecânica - <b class=cp>Ficha de Compensação&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><span class=\"cp\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div></TD></TR><TR><TD class=ct  colspan=3 ></TD></tr></tbody></table><TABLE cellSpacing=0 cellPadding=0 width=666 border=0><TBODY><TR><TD vAlign=bottom align=left height=50> 
" . $this->fbarcode()." </TD></tr></tbody></table><TABLE cellSpacing=0 cellPadding=0 width=666 border=0><TR><TD class=ct width=666></TD></TR><TBODY><TR><TD class=ct width=666><div align=right>Corte 
na linha pontilhada<span class=\"cp\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span class=\"cp\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div></TD></TR><TR><TD class=ct width=666><img height=1 src=" . $this->dirImagens . "6.gif width=665 border=0></TD></tr></tbody></table></span>
<!-- NEW PAGE -->	";
	}
	
}
?>
