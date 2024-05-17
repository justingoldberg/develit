<?

class BoletoModelo {
	#banco
	var $nosso_numero;		 		// Nosso Numero S/ DAC
	var $carteira;					// Código da Carteira
	var $codigo_banco;				// Código do Banco (sobrescrita na classes filhas)
	var $logo_banco;				// Nome da imagem da logo do Banco (sobrescrita na classes filhas)
	var $codigo_banco_com_dv;		// Digito do Código do Banco (sobrescrita na classes filhas)
	
	#cedente
	var $cedente;					// Nome do cedente
	var $agencia;					// Numero da Agência 4 Digitos s/DAC
	var $conta; 					// Numero da Conta 5 Digitos s/ DAC
	var $digito_conta;			  	// Digito da Conta Corrente 1 Digito
	var $endereco;
	var $cidade;
	
	var $numero_documento;			// Numero do Pedido ou Nosso Numero
	var $linha_digitavel;	
	#dados cobranca
	var $data_vencimento;			//data de vencimento do boleto.
	var $data_documento;			// Data de emissão do Boleto
	
	var $especie = 'R$';
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
	var $num_moeda;
	var $codigo_barras;
	var $codigo_barras_dg; // digito do código de barras
	var $dirImagens="../../class/boletoIT/imagens";
	
	/**
	 * Construtor
	 *
	 * @return BoletoModelo
	 */
	function BoletoModelo( ) {
		
	}
	
	
	function set( $campo, $valor, $warn=true ){
		
		$this->$campo = $valor;
		
		if ( empty( $valor ) ){
			avisoNOURL( "ERRO", "campo ".$campo." do boleto ".$this->sacado." vazio", "40%");
		}
		
	}

	function carregaDadosBoleto(){
		
	}
		
	function digitoVerificador_barra($numero) {

		$resto2 = $this->modulo_11($numero, 9, 1);
		if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
			$dv = 1;
		} else {
			$dv = 11 - $resto2;
		}
		return $dv;
	}
	
	function formata_numero($numero,$loop,$insert,$tipo = "geral") {
		if ($tipo == "geral") {
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo == "valor") {
			/*
			retira as virgulas
			formata o numero
			preenche com zeros
			*/
			$numero = str_replace(",","",$numero);
			//acrescentada linha abaixo para substituir os pontos em valores acima de 1.000,00
			//gerava erro ao montar o código do boleto
			$numero = str_replace(".","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo = "convenio") {
			while(strlen($numero)<$loop){
				$numero = $numero . $insert;
			}
		}
		return $numero;
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


		$ret = "<img src={$this->dirImagens}/p.gif width=".$fino." height=".$altura." border=0><img  ".
			         "src={$this->dirImagens}/b.gif width=".$fino." height=".$altura." border=0><img ".
					 "src={$this->dirImagens}/p.gif width=".$fino." height=".$altura." border=0><img ". 
					 "src={$this->dirImagens}/b.gif width=".$fino." height=".$altura." border=0><img ";

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
		
			$ret .= "src={$this->dirImagens}/p.gif width=".$f1." height=".$altura." border=0><img ";
		
		    if (substr($f,$i,1) == "0") {
		      $f2 = $fino ;
		    }else{
		      $f2 = $largo ;
		    }
		
		    $ret .= "src={$this->dirImagens}/b.gif width=".$f2." height=".$altura." border=0><img ";
		
		   }
		}
		
		$ret .= "src={$this->dirImagens}/p.gif width=".$largo." height=".$altura." border=0><img ".
				"src={$this->dirImagens}/b.gif width=".$fino." height=".$altura." border=0><img " .
				"src={$this->dirImagens}/p.gif width=1 height=".$altura." border=0>";
				
		return $ret;
	}

	function esquerda($entra,$comp){
		return substr($entra,0,$comp);
	}
	
	function direita($entra,$comp){
		return substr($entra,strlen($entra)-$comp,$comp);
	}
	
	function fator_vencimento($data) {
		$data = split("/",$data);
		$ano = $data[2];
		$mes = $data[1];
		$dia = $data[0];
		return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
	}
	
	function _dateToDays($year,$month,$day) {
		$century = substr($year, 0, 2);
		$year = substr($year, 2, 2);
		if ($month > 2) {
			$month -= 3;
		} else {
			$month += 9;
			if ($year) {
				$year--;
			} else {
				$year = 99;
				$century --;
			}
		}
		return ( floor((  146097 * $century)    /  4 ) +
		floor(( 1461 * $year)        /  4 ) +
		floor(( 153 * $month +  2) /  5 ) +
		$day +  1721119);
	}
	
	function modulo_10($num) {
		$numtotal10 = 0;
		$fator = 2;
	
		// Separacao dos numeros
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num,$i-1,1);
			// Efetua multiplicacao do numero pelo (falor 10)
			// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
			$temp = $numeros[$i] * $fator;
			$temp0=0;
			foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
			$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
			// monta sequencia para soma dos digitos no (modulo 10)
			$numtotal10 += $parcial10[$i];
			if ($fator == 2) {
				$fator = 1;
			} else {
				$fator = 2; // intercala fator de multiplicacao (modulo 10)
			}
		}
	
		// várias linhas removidas, vide função original
		// Calculo do modulo 10
		$resto = $numtotal10 % 10;
		$digito = 10 - $resto;
		if ($resto == 0) {
			$digito = 0;
		}
	
		return $digito;
	
	}
	
	function modulo_11($num, $base=9, $r=0)  {
		/**
	     *   Autor:
	     *           Pablo Costa <pablo@users.sourceforge.net>
	     *
	     *   Função:
	     *    Calculo do Modulo 11 para geracao do digito verificador 
	     *    de boletos bancarios conforme documentos obtidos 
	     *    da Febraban - www.febraban.org.br 
	     *
	     *   Entrada:
	     *     $num: string numérica para a qual se deseja calcularo digito verificador;
	     *     $base: valor maximo de multiplicacao [2-$base]
	     *     $r: quando especificado um devolve somente o resto
	     *
	     *   Saída:
	     *     Retorna o Digito verificador.
	     *
	     *   Observações:
	     *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
	     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
	     */                                        
	
		$soma = 0;
		$fator = 2;
	
		/* Separacao dos numeros */
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num,$i-1,1);
			// Efetua multiplicacao do numero pelo falor
			$parcial[$i] = $numeros[$i] * $fator;
			// Soma dos digitos
			$soma += $parcial[$i];
			if ($fator == $base) {
				// restaura fator de multiplicacao para 2
				$fator = 1;
			}
			$fator++;
		}
	
		/* Calculo do modulo 11 */
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			if ($digito == 10) {
				$digito = 0;
			}
			return $digito;
		} elseif ($r == 1){
			$resto = $soma % 11;
			return $resto;
		}
	}
	
	function geraCodigoBanco() {
		$parte1 = substr($this->codigo_banco, 0, 3);
		$parte2 = $this->modulo_11($parte1);
		return $parte1 . "-" . $parte2;
	}	

	/**
	 * Retorno o Cabeçalho HTML dos Boletos 
	 *
	 * @return string
	 */
	function getCabecalho(){
		return "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<html>
<head>
<!-- <title>Boleto-IT</title> -->
<META http-equiv=Content-Type content=text/html; charset=windows-1252>
<meta name=\"Generator\" content=\"Projeto BoletoPHP - www.boletophp.com.br - Licença GPL\" />
<style type=text/css>.cp {  font: bold 10px Arial; color: black}
.ti {  font: 9px Arial, Helvetica, sans-serif}
.ld { font: bold 15px Arial; color: #000000}
.ct { font-size: 9px; font-family: \"Arial Narrow\"; COLOR: #000033}
.cn { FONT: 9px Arial; COLOR: black }
.bc { font: bold 22px Arial; color: #000000 }
.ld2 { font: bold 12px Arial; color: #000000 }
</style> 
</head>
<body text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0>\n";
	}
	
	/**
	 * Retorna o Rodapé HTML dos Boletos
	 *
	 * @return string
	 */
	function getRodape(){
		return "</body>\n</html>";
	}

	function getHTML(){
		$imagem_codigo_barra = $this->fbarcode();
		
		return <<<HTM
<table cellspacing=0 cellpadding=0 width=666 border=0><tbody>
 <tr>
  <td width=666><img height=1 src={$this->dirImagens}/6.gif width=665 border=0></td>
 </tr>
 <tr>
  <td width=666 align=right><font  size=1 face="Arial Narrow"; color="#000033"><b>Recibo do Sacado</b></font></td>
 </tr>
</tbody></table>
<br>

<!--
<table width=666 cellspacing=5 cellpadding=0 border=0><tr><td width=41></td></tr></table>
<table width=666 cellspacing=5 cellpadding=0 border=0 align=Default>
 <tr>
  <td width=41><img src="{$this->dirImagens}/logo_empresa.gif"></td>
  <td class=ti width=455>
   <font  size=1 face="Arial Narrow"; color="#000000">
    {$this->cedente}<br>
    {$this->cpf_cnpj_cedente}<br>
    {$this->endereco}<br>
    {$this->cidade}<br>
   </font>
  </td>
   <td align=RIGHT width=150 class=ti>&nbsp;</td>
 </tr>
</table>
-->

<table cellspacing=0 cellpadding=0 width=666 border=0>
 <tr>
  <td class=cp width=150><span class="campo"><img src="{$this->dirImagens}/{$this->logo_banco}" width="150" height="40" border=0></span></td>
  <td width=3 valign=bottom><img height=22 src={$this->dirImagens}/3.gif width=2 border=0></td>
  <td class=cpt width=58 valign=bottom align=center><font size="+1" face="Arial Narrow"><b>{$this->codigo_banco_com_dv}</b></font></td><td width=3 valign=bottom><img height=22 src={$this->dirImagens}/3.gif width=2 border=0></td>
  <td class=ld align=right width=453 valign=bottom><span class=ld> <span class="campotitulo">{$this->linha_digitavel}</span></span></td>
 </tr>
 <tbody><tr>
  <td colspan=5><img height=2 src={$this->dirImagens}/2.gif width=666 border=0></td>
 </tr></tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
 <tbody>
  <tr>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=280 height=13><font  size=1 face="Arial Narrow"; color="#000033">Cedente</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=140 height=13><font  size=1 face="Arial Narrow"; color="#000033">Agência / Código do Cedente</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=38  height=13><font  size=1 face="Arial Narrow"; color="#000033">Espécie</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=55  height=13><font  size=1 face="Arial Narrow"; color="#000033">Quantidade</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=118 height=13><font  size=1 face="Arial Narrow"; color="#000033">Nosso número</font></td>
  </tr>
  <tr>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=280 height=12> <font  size=1 face="Arial Narrow"; color="#000000">{$this->cedente}</font></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=140 height=12><font  size=1 face="Arial Narrow"; color="#000000"> {$this->agencia_codigo}</font></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=38  height=12><font  size=1 face="Arial Narrow"; color="#000000"> {$this->especie}</font></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=55  height=12><font  size=1 face="Arial Narrow"; color="#000000"> {$this->quantidade}</font> </td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=118 height=12 align=right> <font  size=1 face="Arial Narrow"; color="#000000">{$this->nosso_numero}</font></td>
  </tr>
  <tr>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=280 height=1><img height=1 src={$this->dirImagens}/2.gif width=280 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=140 height=1><img height=1 src={$this->dirImagens}/2.gif width=140 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=38  height=1><img height=1 src={$this->dirImagens}/2.gif width=38 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=55  height=1><img height=1 src={$this->dirImagens}/2.gif width=55 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=118 height=1><img height=1 src={$this->dirImagens}/2.gif width=118 border=0></td>
  </tr>
 </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
 <tbody>
  <tr>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top colspan=3 height=13><font size="1" face="Arial Narrow" color="#000033">Número do documento</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=132 height=13><font size="1" face="Arial Narrow" color="#000033">CPF/CNPJ</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=134 height=13><font size="1" face="Arial Narrow" color="#000033">Vencimento</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=180 height=13><font size="1" face="Arial Narrow" color="#000033">Valor documento</font></td>
  </tr>
  <tr>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top colspan=3 height=12> <font  size=1 face="Arial Narrow"; color="#000000">{$this->numero_documento}</font></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=132 height=12> <font  size=1 face="Arial Narrow"; color="#000000">{$this->cpf_cnpj_cedente}</font></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=134 height=12> <font  size=1 face="Arial Narrow"; color="#000000">{$this->data_vencimento}</font></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=180 height=12 align=right><font  size=1 face="Arial Narrow"; color="#000000"> {$this->valor}</font></td>
  </tr>
  <tr>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=113 height=1><img height=1 src={$this->dirImagens}/2.gif width=113 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=72  height=1><img height=1 src={$this->dirImagens}/2.gif width=72 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=132 height=1><img height=1 src={$this->dirImagens}/2.gif width=132 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=134 height=1><img height=1 src={$this->dirImagens}/2.gif width=134 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
  </tr>
 </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
 <tbody>
  <tr>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=128 height=13><font size="1" face="Arial Narrow" color="#000033">(-) Desconto / Abatimentos</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=112 height=13><font size="1" face="Arial Narrow" color="#000033">(-) Outras deduções</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=98  height=13><font size="1" face="Arial Narrow" color="#000033">(+) Mora / Multa</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=113 height=13><font size="1" face="Arial Narrow" color="#000033">(+) Outros acréscimos</font></td>
   <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=180 height=13><font size="1" face="Arial Narrow" color="#000033">(=) Valor cobrado</font></td>
  </tr>
  <tr>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=128 height=12 align=right></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=112 height=12 align=right></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=98  height=12 align=right></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=113 height=12 align=right></td>
   <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=180 height=12 align=right></td>
  </tr>
  <tr>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=128 height=1><img height=1 src={$this->dirImagens}/2.gif width=128 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=112 height=1><img height=1 src={$this->dirImagens}/2.gif width=112 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=98  height=1><img height=1 src={$this->dirImagens}/2.gif width=98 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=113 height=1><img height=1 src={$this->dirImagens}/2.gif width=113 border=0></td>
   <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
  </tr>
 </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
 <tbody>
  <tr>
   <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td valign=top width=659 height=13><font size="1" face="Arial Narrow" color="#000033">Sacado</font></td>
  </tr>
  <tr>
   <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
   <td class=cp valign=top width=659 height=12> <font  size=1 face="Arial Narrow"; color="#000000">{$this->sacado}</font></td>
  </tr>
  <tr>
   <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
   <td valign=top width=659 height=1><img height=1 src={$this->dirImagens}/2.gif width=659 border=0></td>
  </tr>
 </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
 <tbody>
  <tr>
   <td width=7 height=12></td>
   <td width=544 ><font size="1" face="Arial Narrow" color="#000033">Instruções</font></td>
   <td width=7 height=12></td>
   <td width=108 align=right ><font size="1" face="Arial Narrow" color="#000033">Autenticação mecânica</font></td>
  </tr>
  <tr>
   <td width=7></td>
   <td class=cp width=544><font  size=1 face="Arial Narrow"; color="#000000"> {$this->instrucoes[4]} </font></td>
   <td width=7></td>
   <td width=108></td>
  </tr>
 </tbody>
</table>
<br>
<table cellspacing=0 cellpadding=0 width=666 border=0>
 <tbody>
  <tr>
   <td width=7></td>
   <td width=500 class=cp> <br> </td>
   <td width=159></td>
  </tr>
 </tbody>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
 <tr>
  <td width=666></td>
 </tr>
 <tbody><tr>
  <td width=666 align=right><font  size=1 face="Arial Narrow"; color="#000033">Corte na linha pontilhada</font></td>
 </tr>
 <tr>
  <td width=666><img height=1 src={$this->dirImagens}/6.gif width=665 border=0></td>
 </tr></tbody>
</table>
<br>

<!-- parte do banco 
<table width=666 cellspacing=5 cellpadding=0 border=0 align=Default>
 <tr>
  <td width=41><img src="{$this->dirImagens}/logo_empresa.gif"></td>
  <td class=ti width=455>
   <font  size=1 face="Arial Narrow"; color="#000000">
    {$this->cedente}<br>
    {$this->cpf_cnpj_cedente}<br>
    {$this->endereco}<br>
    {$this->cidade}<br>
   </font>
  </td>
   <td align=RIGHT width=150 class=ti>&nbsp;</td>
 </tr>
</table>
-->

<table cellspacing=0 cellpadding=0 width=666 border=0>
 <tr>
  <td class=cp width=150> <span class="campo"><img src="{$this->dirImagens}/{$this->logo_banco}" width="150" height="40" border=0></span></td>
  <td width=3 valign=bottom><img height=22 src={$this->dirImagens}/3.gif width=2 border=0></td>
  <td class=cpt width=58 valign=bottom align=center><font size="+1" face="Arial Narrow"><b>{$this->codigo_banco_com_dv}</b></font></td>
  <td width=3 valign=bottom><img height=22 src={$this->dirImagens}/3.gif width=2 border=0></td>
  <td class=ld align=right width=453 valign=bottom><span class=ld> <span class="campotitulo">{$this->linha_digitavel}</span></span></td>
 </tr>
 <tbody><tr>
  <td colspan=5><img height=2 src={$this->dirImagens}/2.gif width=666 border=0></td>
 </tr></tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0><tbody>
 <tr>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=472 height=13><font size="1" face="Arial Narrow" color="#000033">Local de pagamento</font></td>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=180 height=13><font size="1" face="Arial Narrow" color="#000033">Vencimento</font></td>
 </tr>
 <tr>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=472 height=12><font  size=1 face="Arial Narrow"; color="#000000">Pagável em qualquer Banco até o vencimento</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=180 height=12 align=right> <font size=1 face="Arial Narrow"; color="#000000">{$this->data_vencimento}</font></td>
 </tr>
 <tr>
  <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=472 height=1><img height=1 src={$this->dirImagens}/2.gif width=472 border=0></td>
  <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
 </tr>
</tbody></table>
<table cellspacing=0 cellpadding=0 border=0><tbody>
 <tr>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=472 height=13><font size="1" face="Arial Narrow" color="#000033">Cedente</font></td>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=180 height=13><font size="1" face="Arial Narrow" color="#000033">Agência/Código cedente</font></td>
 </tr>
 <tr>
  <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=472 height=12> <font size=1 face="Arial Narrow"; color="#000000">{$this->cedente}</span></td>
  <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top align=right width=180 height=12> <font size=1 face="Arial Narrow"; color="#000000">{$this->agencia_codigo}</font></td>
 </tr>
 <tr>
  <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=472 height=1><img height=1 src={$this->dirImagens}/2.gif width=472 border=0></td>
  <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
 </tr>
</tbody></table>
<table cellspacing=0 cellpadding=0 border=0><tbody>
 <tr>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=113 height=13><font size="1" face="Arial Narrow" color="#000033">Data do documento</font></td>
  <td valign=top width=7   height=13> <img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=140 height=13><font size="1" face="Arial Narrow" color="#000033">N<u>o</u> documento</font></td>
  <td valign=top width=7   height=13> <img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=62  height=13><font size="1" face="Arial Narrow" color="#000033">Espécie doc.</font></td>
  <td valign=top width=7   height=13> <img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=34  height=13><font size="1" face="Arial Narrow" color="#000033">Aceite</font></td>
  <td valign=top width=7   height=13> <img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=95  height=13><font size="1" face="Arial Narrow" color="#000033">Data processamento</font></td>
  <td valign=top width=7   height=13> <img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=180 height=13><font size="1" face="Arial Narrow" color="#000033">Nosso número</font></td>
 </tr>
 <tr>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=113 height=12><font size=1 face="Arial Narrow"; color="#000000">{$this->data_documento}</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=140 height=12><font size=1 face="Arial Narrow"; color="#000000">{$this->numero_documento}</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=62  height=12><font size=1 face="Arial Narrow"; color="#000000">{$this->especie}</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=34  height=12><font size=1 face="Arial Narrow"; color="#000000"> {$this->aceite} </font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=95  height=12><font size=1 face="Arial Narrow"; color="#000000">{$this->data_processamento}</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=180 height=12 align=right><font size=1 face="Arial Narrow"; color="#000000">{$this->nosso_numero}</font></td>
 </tr>
 <tr>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7   border=0></td>
  <td valign=top width=113 height=1><img height=1 src={$this->dirImagens}/2.gif width=113 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7   border=0></td>
  <td valign=top width=140 height=1><img height=1 src={$this->dirImagens}/2.gif width=140 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7   border=0></td>
  <td valign=top width=62  height=1><img height=1 src={$this->dirImagens}/2.gif width=62  border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7   border=0></td>
  <td valign=top width=34  height=1><img height=1 src={$this->dirImagens}/2.gif width=34  border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7   border=0></td>
  <td valign=top width=95  height=1><img height=1 src={$this->dirImagens}/2.gif width=95  border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7   border=0></td>
  <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
 </tr>
</tbody></table>
<table cellspacing=0 cellpadding=0 border=0><tbody>
 <tr> 
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top colspan=3 height=13><font size="1" face="Arial Narrow" color="#000033">Uso do banco</font></td>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=83  height=13><font size="1" face="Arial Narrow" color="#000033">Carteira</font></td>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=53  height=13><font size="1" face="Arial Narrow" color="#000033">Espécie</font></td>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=110 height=13><font size="1" face="Arial Narrow" color="#000033">Quantidade</font></td>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=85  height=13><font size="1" face="Arial Narrow" color="#000033">Valor Documento</font></td>
  <td valign=top width=7   height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td valign=top width=180 height=13><font size="1" face="Arial Narrow" color="#000033">(=) Valor documento</font></td>
 </tr>
 <tr>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top height=12 colspan=3><font size=1 face="Arial Narrow"; color="#000000"> </font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=83><font size=1 face="Arial Narrow"; color="#000000"> {$this->carteira}</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=53><font size=1 face="Arial Narrow"; color="#000000">{$this->especie}</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=110><font size=1 face="Arial Narrow"; color="#000000">{$this->quantidade}</font></td>
  <td class=cp valign=top width=7   height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=85><font size=1 face="Arial Narrow"; color="#000000"> {$this->valor_unitario}</font></td>
  <td class=cp valign=top width=7   height=12> <img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
  <td class=cp valign=top width=180 height=12 align=right><font size=1 face="Arial Narrow"; color="#000000"> {$this->valor}</font></td>
 </tr>
 <tr>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=75  height=1><img height=1 src={$this->dirImagens}/2.gif width=75 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=31  height=1><img height=1 src={$this->dirImagens}/2.gif width=31 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=83  height=1><img height=1 src={$this->dirImagens}/2.gif width=83 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=53  height=1><img height=1 src={$this->dirImagens}/2.gif width=53 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=110 height=1><img height=1 src={$this->dirImagens}/2.gif width=110 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=85  height=1><img height=1 src={$this->dirImagens}/2.gif width=85 border=0></td>
  <td valign=top width=7   height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
  <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
 </tr>
</tbody></table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
<tbody>
<tr>
 <td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr>
 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
</tr>
<tr> 
 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
</tr>
<tr> 
 <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=1 border=0></td>
</tr></tbody></table></td>
<td valign=top width=468 rowspan=5><font size=1 face="Arial Narrow"; color="#000000">Instruções (Texto de responsabilidade do cedente)</font><br>
<span class=cp> <font size=1 face="Arial Narrow"; color="#000000">
{$this->instrucoes[0]}<br>
{$this->instrucoes[1]}<br>
{$this->instrucoes[2]}<br>
{$this->instrucoes[3]}</font> 
</span></td>
<td align=right width=188>
 <table cellspacing=0 cellpadding=0 border=0>
  <tbody>
   <tr>
	<td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	<td valign=top width=180 height=13><font size=1 face="Arial Narrow"; color="#000033">(-) Desconto / Abatimentos</font></td>
   </tr>
   <tr>
	<td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	<td class=cp valign=top align=right width=180 height=12></td>
   </tr>
   <tr> 
	<td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
	<td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
   </tr>
  </tbody>
 </table>
</td>
</tr>
<tr>
 <td align=right width=10>
  <table cellspacing=0 cellpadding=0 border=0 align=left>
   <tbody>
    <tr>
	 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
	<tr>
	 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
	<tr>
	 <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=1 border=0></td>
	</tr>
   </tbody>
  </table>
 </td>
 <td align=right width=188>
  <table cellspacing=0 cellpadding=0 border=0>
   <tbody>
	<tr>
	 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	 <td valign=top width=180 height=13><font size=1 face="Arial Narrow"; color="#000033">(-) Outras deduções</font></td>
	</tr>
    <tr>
	 <td class=cp valign=top width=7 height=12> <img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	 <td class=cp valign=top align=right width=180 height=12></td>
	</tr>
	<tr>
	 <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
	 <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
	</tr>
   </tbody>
  </table>
 </td>
</tr>
<tr>
 <td align=right width=10>
  <table cellspacing=0 cellpadding=0 border=0 align=left>
   <tbody>
    <tr> 
	 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
	<tr>
	 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
	<tr>
	 <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=1 border=0></td>
	</tr>
   </tbody>
  </table>
 </td>
 <td align=right width=188>
  <table cellspacing=0 cellpadding=0 border=0>
   <tbody>
    <tr>
	 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	 <td valign=top width=180 height=13><font size=1 face="Arial Narrow"; color="#000033">(+) Mora / Multa</font></td>
	</tr>
	<tr>
	 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	 <td class=cp valign=top align=right width=180 height=12></td>
	</tr>
	<tr>
	 <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
	 <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
	</tr>
   </tbody>
  </table>
 </td>
</tr>
</tr>
<tr>
 <td align=right width=10>
  <table cellspacing=0 cellpadding=0 border=0 align=left>
   <tbody>
    <tr> 
	 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
	<tr>
	 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
	<tr>
	 <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=1 border=0></td>
	</tr>
   </tbody>
  </table>
 </td>
 <td align=right width=188>
  <table cellspacing=0 cellpadding=0 border=0>
   <tbody>
    <tr>
	 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	 <td valign=top width=180 height=13><font size=1 face="Arial Narrow"; color="#000033">(+) Outros acréscimos</font></td>
	</tr>
	<tr>
	 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	 <td class=cp valign=top align=right width=180 height=12></td>
	</tr>
	<tr>
	 <td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
	 <td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
	</tr>
   </tbody>
  </table>
 </td>
</tr>
<tr>
 <td align=right width=10>
  <table cellspacing=0 cellpadding=0 border=0 align=left>
   <tbody>
    <tr>
	 <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
	<tr>
	 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	</tr>
   </tbody>
  </table>
 </td>
 <td align=right width=188>
  <table cellspacing=0 cellpadding=0 border=0>
   <tbody>
    <tr>
     <td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
     <td valign=top width=180 height=13><font size=1 face="Arial Narrow"; color="#000033">(=) Valor cobrado</font></td>
    </tr>
    <tr>
	 <td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
	 <td class=cp valign=top align=right width=180 height=12></td>
	</tr>
   </tbody>
  </table>
 </td>
</tr>
</tbody></table>
<table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr>
<td valign=top width=666 height=1><img height=1 src={$this->dirImagens}/2.gif width=666 border=0></td>
</tr></tbody></table>
<table cellspacing=0 cellpadding=0 border=0><tbody><tr>
<td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
<td valign=top width=659 height=13><font size=1 face="Arial Narrow"; color="#000033">Sacado</font></td>
</tr><tr>

<td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
<td class=cp valign=top width=659 height=12><font size=1 face="Arial Narrow"; color="#000000">{$this->sacado}</font></td>
</tr></tbody></table>
<table cellspacing=0 cellpadding=0 border=0><tbody><tr>
<td class=cp valign=top width=7 height=12><img height=12 src={$this->dirImagens}/1.gif width=1 border=0></td>
<td class=cp valign=top width=659 height=12><font size=1 face="Arial Narrow"; color="#000000">{$this->endereco1}</font> </td>
</tr></tbody></table>
<table cellspacing=0 cellpadding=0 border=0><tbody><tr>
<td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
<td class=cp valign=top width=472 height=13><font size=1 face="Arial Narrow"; color="#000000"> {$this->sacado}</font></td>
<td valign=top width=7 height=13><img height=13 src={$this->dirImagens}/1.gif width=1 border=0></td>
<td valign=top width=180 height=13><font size=1 face="Arial Narrow"; color="#000033">Cód. baixa</font></td>
</tr><tr>
<td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
<td valign=top width=472 height=1><img height=1 src={$this->dirImagens}/2.gif width=472 border=0></td>
<td valign=top width=7 height=1><img height=1 src={$this->dirImagens}/2.gif width=7 border=0></td>
<td valign=top width=180 height=1><img height=1 src={$this->dirImagens}/2.gif width=180 border=0></td>
</tr></tbody></table>
<table cellSpacing=0 cellPadding=0 border=0 width=666><tbody><tr>
<td  width=7 height=12></td>
<td  width=409 ><font size=1 face="Arial Narrow"; color="#000033">Sacador/Avalista</font></td>
<td  width=250 ><font size=1 face="Arial Narrow"; color="#000033">Autenticação mecânica<font> - <font size=1 face="Arial Narrow"; color="#000000"><b>Ficha de Compensação</b></font></td>
</tr><tr>
<td  colspan=3 ></td>
</tr></tbody></table>
<table cellSpacing=0 cellPadding=0 width=666 border=0><tbody><tr>
<td valign=bottom align=left height=50>{$imagem_codigo_barra}</td>
</tr></tbody></table>
<table cellSpacing=0 cellPadding=0 width=666 border=0><tr>
<td width=666></td>
</tr><tbody><tr>
<td width=666 align=right><font size=1 face="Arial Narrow"; color="#000033">Corte na linha pontilhada</font></td>
</tr><tr>
<td width=666><img height=1 src={$this->dirImagens}/6.gif width=665 border=0></td>
</tr></tbody></table><br>
<!-- NEW PAGE -->	
HTM;
	}

}

?>