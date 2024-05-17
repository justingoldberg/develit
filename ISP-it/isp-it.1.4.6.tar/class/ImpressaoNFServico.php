<?
################################################################################
#       Criado por: Rogério aka Popó
#  Data de criação: 17/01/2005
# Ultima alteração: 17/01/2005
#    Alteração No.: 001
#
# Função:
#    Impressão Nota Fiscal - Classe para impressão de nota fiscal
################################################################################

class ImpressaoNFServico{
	
	var $codigo;
	var $natPrestacao;
	var $colsPrint; // qtde colunas imprimiveis na nota
	var $rowsDetail; // qtde de linhas imprimiveis no detalhe da nota
	var $saltoNumNF; // linhas entre o numero nota e o cabeçalho
	var $saltoCabecalho; // linhas entre o cabeçalho e o detalhe da nota
	var $saltoDetalhe; // linhas entre o Detalhe e o rodapé
	var $saltoRodape; // linhas entre o rodapé e o fim da nota ( por haver numero de nota no inicio e fim da nota )
	var $objNFS; // objeto passado como parametro para esta classe
	var $totalItens;
	var $valorICMS;##  // valor do imposto
	var $baseCalculo; // valor de itens para base de cálculo
	
	var $arquivo; // variavel que recebe o conteudo da nota para impressao
	
	function ImpressaoNFServico( $obj ){

		$this->objNFS = $obj;
		$this->colsPrint = 138;
		$this->rowsDetail = 20;
		$this->saltoNumNF = 2;
		$this->saltoCabecalho = 4;
		$this->saltoDetalhe = 1;
		$this->saltoRodape = 3;
		$this->totalItens = 0;
		$this->baseCalculo = 0;
		$this->arquivo = '';
	}
	
	function imprimir(){

//		imprime o total Geral da Nota
		
		// inicia o arquivo texto que será enviado à impressora
		$this->cabecalho();
		$this->detalhe();
		
		#inicio alteracao
		$this->arquivo .= $this->espacos( 5 );
		$this->arquivo .= $this->preencheCampos( 103, $this->objNFS->obs, 'left' );
		
			
		#fim alteracao
		
	
		
		$this->totalGeralNota = $this->totalItens;

		$this->arquivo .= $this->preencheCampos( 25, $this->moeda( $this->totalGeralNota ), 'right' );
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoRodape );		

		$this->calculaICMS();
		
		
		for ( $x=0; $x<5 ;$x++ )
			$this->arquivo .= "\r\n";
		
		$this->arquivo .= chr(18);
		return( $this->arquivo );
	
	}
	
	function cabecalho(){
		
		$parametros = dbNaturezaPrestacao( '', 'consultar', '', "id = ".$this->objNFS->idNatPrestacao, '' );
		if( $parametros ){		
			$this->codigo = $parametros[0]->codigo;
			$this->natPrestacao = $parametros[0]->descricao;
		}
		
		$this->arquivo = $this->saltaLinhas( 4 );
		//faz a impressao do cabecalho
		$this->arquivo .= chr(27).chr(15);
		$this->arquivo .= $this->espacos( 88 );
		
		
		$this->arquivo .= chr(27).chr(14); // formata caracter expandido da impressora

		$this->arquivo .= $this->preencheCampos( 9, $this->objNFS->numNF, 'right' );
		$this->arquivo .= chr(20); // desliga o efeito expandido da impressora
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoNumNF );
		$this->arquivo .= $this->saltaLinhas( 1 );

		$this->arquivo .= $this->espacos( 103 );
		
		$this->arquivo .= $this->preencheCampos( 21, $this->formatString( $this->natPrestacao ), 'left' ); 
		$this->arquivo .= $this->espacos( 9 );
		$this->arquivo .= $this->preencheCampos( 6, $this->codigo, 'left' ); 
		$this->arquivo .= chr(17).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( 1 );
		
		$this->arquivo .= chr(27).chr(15);
		$this->arquivo .= $this->espacos( 100 );
		
		$this->arquivo .= chr(27).chr(14); // formata caracter expandido da impressora
		$this->arquivo .= $this->centralizaDados( 13, $this->data( $this->objNFS->dtEmissao ) ); 
		$this->arquivo .= chr(20); // desliga o efeito expandido da impressora
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( 3 );
		
		$this->arquivo .= $this->espacos( 16 );
		
		$this->arquivo .= $this->preencheCampos( 95, $this->formatString( $this->objNFS->razao ), 'left' );

		$this->arquivo .= $this->saltaLinhas( 2 );
		
		$this->arquivo .= $this->espacos( 16 );
		
		$this->arquivo .= $this->preencheCampos( 95, $this->formatString($this->objNFS->endereco) );
				
		$this->arquivo .= $this->saltaLinhas( 2 );
		
		$this->arquivo .= $this->espacos( 16 );
		
		$this->arquivo .= $this->preencheCampos( 80, $this->formatString($this->objNFS->cidade), 'left' );

		$this->arquivo .= $this->espacos( 2 );
		
		$this->arquivo .= $this->centralizaDados( 5, $this->objNFS->uf );

		$this->arquivo .= $this->saltaLinhas( 2 );	
		
		$this->arquivo .= $this->espacos( 16 );	
		
		$this->arquivo .= $this->preencheCampos( 65, $this->objNFS->cnpj, 'left' );

		$this->arquivo .= $this->espacos( 5 );
		
		$this->arquivo .= $this->preencheCampos( 23, $this->objNFS->inscrEst, 'left' );

		$this->arquivo .= $this->saltaLinhas( $this->saltoCabecalho );		
	}
	
	function detalhe(){
		
		$i = 0;
		$totalItem = 0;
		$totalItens = 0;
		$linhasDetalhe = 0; // guarda a qtde de linhas utilizadas no detalhe

		foreach( $this->objNFS->Itens as $detalhes ){
			$linhas['isento'][$i] = $detalhes->isento;
			$linhas['valor'][$i] = $detalhes->valor;
			$indice = strlen($detalhes->discriminacao);
			$string = $this->formataString( $detalhes->discriminacao);
			if( $indice > 100 ){
				$verifica = true;
				while( $verifica ){ 
					$j = 100;
					while( $string{$j} != " " || $string{$j+1} == " " ){
						$j--;
					}
					$linhas['discriminacao'][$i] = trim(substr($string, 0, $j));
					$string = substr($string, $j);
					$i++;
					if(strlen($string) <= 100 ){ 
					    $verifica = false;	
					    $linhas['discriminacao'][$i] = trim($string);
					    $i++;
					}
				}
			}
			else {
				$linhas['discriminacao'][$i] = $this->formataString($detalhes->discriminacao);
				$i++;
			}
		}
		for($a=0; $a < $this->rowsDetail ; $a++){
			$this->arquivo .= chr(27).chr(15);
			$this->arquivo .= $this->espacos( 2 );
			$this->arquivo .= $this->preencheCampos( 107, $this->formatString($linhas['discriminacao'][$a]), 'left');
			$this->arquivo .= $this->espacos( 1 );
			$this->arquivo .= $this->preencheCampos( 20, $linhas['valor'][$a] ? $this->moeda( $linhas['valor'][$a]):'' , 'right');
			//$this->arquivo .= $this->espacos( 1 );
			$totalItem += ( $linhas['isento'][$a] == 'N' ? $linhas['valor'][$a] : '' );
			$totalItens += $linhas['valor'][$a];
			$this->arquivo .= $this->saltaLinhas( 1 );
			$linhasDetalhe++;
		}
			
//		foreach( $this->objNFS->Itens as $detalhes ){
//				$this->arquivo .= $this->espacos( 5 );
//				$this->arquivo .= $this->preencheCampos( 105, $this->formatString($detalhes->discriminacao));
//				$this->arquivo .= $this->espacos( 1 );
//				$this->arquivo .= $this->preencheCampos( 14, $this->moeda( $detalhes->valor ), 'right');
//				$this->arquivo .= $this->espacos( 1 );
//				$totalItem += ( $detalhes->isento!='S' ? $detalhes->valor : '' );
//				$totalItens += $detalhes->valor;
//				$this->arquivo .= $this->saltaLinhas( 1 );
//				$linhasDetalhe++;
//		}
		
		$this->totalItens = $totalItens;
		$this->baseCalculo = $totalItem;
		$this->arquivo.= $this->saltaLinhas( $this->rowsDetail - $linhasDetalhe );
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoDetalhe );		
	}
	
	function centralizaDados( $lenTotal, $dado, $char = ' ' ){
		$lenDado = strlen( $dado );
		if ($lenTotal < $lenDado ){
			$dado = substr( $dado, 0, $lenTotal );
			$diferenca = 0;
		}
		else
			$diferenca = $lenTotal - $lenDado; //devolve a diferenca entre o tamanho do campo na nota e o len do valor do dado

		$preDado = $this->espacos( ( ( $diferenca/2 ) + ( $diferenca%2 ) ), $char ); // define os espacos depois do valor + o resto da diferencia se não for exata		
		$posDado = $this->espacos( ( $diferenca/2 ), $char ); // define os esapcos antes do valor

		$valor = $preDado.$dado.$posDado; // contatena tudo e retorna o resultado
		
		return $valor;
	}
	
	function preencheCampos( $lenTotal, $dado, $local='left', $char=' ' ){
		$lenDado = strlen( $dado );
		
		if ( $lenTotal < $lenDado ){
			$dado = substr( $dado, 0, $lenTotal );
			$diferenca = 0;
		}
		else 
			$diferenca = ( $lenTotal-$lenDado );
			
		if ( $local == "left" )
			$valor = $dado.$this->espacos( $diferenca, $char );
		else
			$valor = $this->espacos( $diferenca, $char ).$dado;
			
		return $valor;
	}
	
	function saltaLinhas( $qtde ){
		$linhas= '';
		for( $i=0; $i < $qtde; $i++ ){
			$linhas .= "\r\n";
		}
		
		return $linhas;
	}
	
	function espacos( $qtde, $char=' ' ){
		$espacos = '';
		$espacos = str_repeat($char, $qtde );
		
		return $espacos;
	}
	
	function moeda( $valor ){	
		return number_format( $valor, 2, ',', '.' );
	}
	
	function data ( $data ){
		$data = substr( $data, 8,2)."/".substr( $data, 5,2)."/".substr( $data, 0,4);
		return $data;
	}
	
	function formatString ( $string="", $mesma=1 ){

		if($string != ""){      
			$com_acento = "à á â ã ä è é ê ë ì í î ï ò ó ô õ ö ù ú û ü À Á Â Ã Ä È É Ê Ë Ì Í Î Ò Ó Ô Õ Ö Ù Ú Û Ü ç Ç ñ Ñ";   
			$sem_acento = "a a a a a e e e e i i i i o o o o o u u u u A A A A A E E E E I I I O O O O O U U U U c C n N";   
			$c = explode(' ',$com_acento);
			$s = explode(' ',$sem_acento);
			
			$i=0;
			foreach($c as $letra){
				if(ereg($letra, $string)){
					$pattern[] = $letra;
					$replacement[] = $s[$i];
				}      
				$i=$i+1;      
			}
			
			if(isset($pattern)){
				$i=0;
				foreach($pattern as $letra){             
					$string = eregi_replace($letra, $replacement[$i], $string);
					$i=$i+1;      
				}
				return $string; # retorna string alterada
			}   
			if ($mesma != 0){
				return $string; # retorna a mesma string se nada mudou
			}
		}
		return ""; # sem mudança retorna nada
	}
	
	function formataString ( $string ){
		$string = ereg_replace( chr(13), " ", $string);
		$string = eregi_replace( "\t", " ", $string);
		$string = eregi_replace( "\n", "", $string);
		$string = eregi_replace( ' +', " ", $string);
		
		return $string;
	}
	
	/**
	 * caso na nota fiscal tenha o atributo para descontar o issqn, gera o desconot e imprime no campo correto
	 * caso nao, retorna com o espaco reservado em branco.
	 */
	function calculaICMS () {

		if ($this->objNFS->ICMS > 0){
			$this->arquivo .= $this->espacos( 10 );
			$this->arquivo .= formatarValoresForm( $this->baseCalculo );
			$this->arquivo .= $this->espacos( 10 );
					
			$this->arquivo .= formatarValoresForm( $this->objNFS->ICMS ) . '%';
			$this->arquivo .= $this->espacos( 10 );
			
			$valorDesconto = $this->baseCalculo*$this->objNFS->ICMS/100;
			$this->arquivo .=  formatarValoresForm( $valorDesconto ); 
			$this->arquivo .= $this->espacos( 10 );			
		
		}
		else{
			$this->arquivo .=  $this->espacos(80);  
		}
			$this->arquivo .= $this->preencheCampos( 106, $this->objNFS->dtPrestacao, 'left' );
	}
}
