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

class ImpressaoNF{

	var $colsPrint; // qtde colunas imprimiveis na nota
	var $rowsDetail; // qtde de linhas imprimiveis no detalhe da nota
	var $saltoNumNF; // linhas entre o numero nota e o cabeçalho
	var $saltoCabecalho; // linhas entre o cabeçalho e o detalhe da nota
	var $saltoDetalhe; // linhas entre o Detalhe e o rodapé
	var $saltoRodape; // linhas entre o rodapé e o fim da nota ( por haver numero de nota no inicio e fim da nota )
//	var $dadosNota; // dados para geracao do arquivo texto e impressao da Nota Fiscal
	var $objNF; // objeto passado como parametro para esta classe
	var $totalItens;
	var $totalDescontos;
	var $valorISSQN;##  // valor do imposto
	var $totalGeralNota; // valor final da nota itens + acresc _ descontos
	
	var $arquivo; // variavel que recebe o conteudo da nota para impressao
	
	function ImpressaoNF( $obj ){

		$this->objNF = $obj;
		$this->colsPrint = 106;
		$this->rowsDetail = 22;
		$this->saltoNumNF = 6; //valor original 7
		$this->saltoCabecalho = 5;
		$this->saltoDetalhe = 1;
		$this->saltoRodape = 4;
		$this->totalItens = 0;
		$this->totalDescontos = 0;
		$this->totalGeralNota = 0;
		$this->arquivo = '';
	}
	
	function imprimir(){

		//imprime o total Geral da Nota
		$parametros = carregaParametrosConfig();
		
		// inicia o arquivo texto que será enviado à impressora
		$this->cabecalho();
		$this->detalhe();
		$this->desconto();
		
		//$this->arquivo .= chr(18);
	
		#inicio alteracao
			
		$this->arquivo .= $this->espacos( 8 );
		$this->descontarISSQN();
			
		#fim alteracao
		
	
		
		$this->totalGeralNota = $this->totalItens;
		
//		if ( strtoupper( $parametros[aplicar_discontos_nf] ) != "N" ){
			$this->totalGeralNota -= $this->totalDescontos ;
//		}
		
		//$this->arquivo .= chr(27).chr(69); //formata o efeito negrito da impressora
		$this->arquivo .= $this->preencheCampos( 78, $this->moeda( $this->totalGeralNota ), 'right' );
		//$this->arquivo .= chr(27).chr(70); // desliga o efeito negrito da impressora
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoRodape );
		

		$this->arquivo .= $this->espacos( 91 );
		
		//$this->arquivo .= chr(18);
		
		$this->arquivo .= chr(27).chr(14);
		$this->arquivo .= $this->objNF->numNF; //$this->preencheCampos( 14, $this->objNF->numNF, 'left' );
		$this->arquivo .= chr(20);
		
		for ( $x=0; $x<5 ;$x++ )
			$this->arquivo .= "\r\n";
		
		$this->arquivo .= chr(18);
		return( $this->arquivo );
	
	}
	
	function cabecalho(){
		
		//faz a impressao do cabecalho
		$this->arquivo = chr(27).chr(15);
		$this->arquivo .= $this->espacos( 95 );
		
		
		$this->arquivo .= chr(27).chr(14); // formata caracter expandido da impressora
		$this->arquivo .= $this->objNF->numNF; //preencheCampos( 9, $this->objNF->numNF, 'left' );
		$this->arquivo .= chr(20); // desliga o efeito expandido da impressora
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoNumNF );

		$this->arquivo .= $this->preencheCampos( 68, $this->formatString( $this->objNF->razao ), 'left' );

		$this->arquivo .= $this->espacos( 1 );

		$this->arquivo .= $this->centralizaDados( 23, $this->objNF->cnpj );

		$this->arquivo .= $this->espacos( 1 );

		$this->arquivo .= $this->centralizaDados( 13, $this->data( $this->objNF->dtEmissao ) );

		$this->arquivo .= $this->saltaLinhas( 2 );
		
		$this->arquivo .= $this->preencheCampos( 61, $this->formatString( $this->objNF->endereco ));

		$this->arquivo .= $this->espacos( 1 );
	
		$this->arquivo .= $this->centralizaDados( 19, $this->formatString($this->objNF->bairro ));

		$this->arquivo .= $this->espacos( 1 );

		$this->arquivo .= $this->preencheCampos( 10, $this->objNF->cep );

		$this->arquivo .= $this->espacos( 1 );

		$this->arquivo .= $this->centralizaDados(13, $this->objNF->natOper );

		$this->arquivo .= $this->saltaLinhas( 2 );
		
		$this->arquivo .= $this->preencheCampos( 43, $this->formatString($this->objNF->cidade), 'left' );

		$this->arquivo .= $this->espacos( 1 );

		$this->arquivo .= $this->centralizaDados( 19, $this->objNF->fone );

		$this->arquivo .= $this->espacos( 1 );

		$this->arquivo .= $this->centralizaDados( 5, $this->objNF->uf );

		$this->arquivo .= $this->espacos( 1 );
	
		$this->arquivo .= $this->centralizaDados( 23, $this->objNF->inscrEst );
		
		$this->arquivo .= $this->preencheCampos( 13, '', 'left' );

		$this->arquivo .= $this->saltaLinhas( $this->saltoCabecalho );		
	}
	
	function detalhe(){
		$totalItem = 0;
		$totalItens = 0;
		$linhasDetalhe = 0; // guarda a qtde de linhas utilizadas no detalhe

		foreach ( $this->objNF->Itens as $detalhes ){
				$this->arquivo .= $this->preencheCampos( 4, $detalhes->qtde, '', '0' );
				$this->arquivo .= $this->espacos( 1 );
				$this->arquivo .= $this->preencheCampos( 5, $detalhes->unid);
				$this->arquivo .= $this->espacos( 1 );
				$this->arquivo .= $this->preencheCampos( 58, $this->formatString($detalhes->descricao));
				$this->arquivo .= $this->espacos( 1 );
				$this->arquivo .= $this->preencheCampos( 14, $this->moeda( $detalhes->valorUnit ), 'right');
				$this->arquivo .= $this->espacos( 1 );
				$totalItem = ( $detalhes->valorUnit*$detalhes->qtde );
				$totalItens += $totalItem;
				$this->arquivo .= $this->preencheCampos( 21, $this->moeda( $totalItem ), 'right');
				$this->arquivo .= $this->saltaLinhas( 1 );
				$linhasDetalhe++;
		}
		
		$this->totalItens = $totalItens;

		$this->arquivo.= $this->saltaLinhas( $this->rowsDetail - $linhasDetalhe );

		
		//imprime o total Geral dos Servicos
		$this->arquivo .= $this->preencheCampos( 106, $this->moeda( $this->totalItens ), 'right' );
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoDetalhe );		
	}
	
	function desconto(){
		/*
		IMPLMENTAR A UTILIZACAO DE DOIS PARAMETROS
		+ ALIQUOTA DE IMPOSTO
		+ VALOR MINIMO DE IMPOSTO A SER COBRADO
		PARA AUTOMATIZAR ESTAS TAREFAS
		*/	
		
		$tributo = getTipoTributosPessoaTipo( $this->objNF->getIdPessoaTipo() );
		
		//$valorIRRF = ( $this->totalItens*0.015 );
		$parametros = carregaParametrosConfig();
		$valorMinimoIRRF = $parametros['trib'.ucfirst($tributo).'IrrfMin'] ;
		
		//if ( $valorIRRF >= 10  && strtoupper($parametros[descontar_irrf]) != 'N'){*
		if ( $valorMinimoIRRF && $valorMinimoIRRF <= $this->totalItens ){
			$valorIRRF = ( $this->totalItens*0.015 );
			$this->arquivo .= $this->saltaLinhas( 1 );
			$this->arquivo .= $this->espacos( 1 );
			$this->arquivo .= $this->preencheCampos( 84,"IRRF 1.5%" );
			$this->arquivo .= $this->espacos( 1 );
			
			if( $parametros['trib'.ucfirst($tributo).'IrrfApN'] == "S" ){
				$this->totalDescontos += $valorIRRF;
			}
			
			$this->arquivo .= $this->preencheCampos( 20 , $this->moeda( $this->totalDescontos ), 'right' );
			$this->arquivo .= $this->saltaLinhas( 2 );#$this->arquivo .= $this->saltaLinhas( 2 );
		}
		else{
			$this->arquivo .= $this->saltaLinhas( 3 );#$this->arquivo .= $this->saltaLinhas( 3 );
		}

		
		/*$valorDesc= 0;
		
		foreach ( $this->objNF as $detalhes ){
			foreach ( $detalhes as $detalhe ){
				$this->arquivo .= $this->preencheCampos( 85, $detalhe->descricao );
				$this->arquivo .= $this->espacos( 1 );
				$valorDesc = ( ( $this->totalGeralNota*$detalhe->valor )/100 ); // calcula a aliquota de imposto a descontar
				$this->arquivo .= $this->preencheCampos( 5, $valorDesc );
				$this->arquivo .= $this->saltaLinhas( 1 );
			}
		}
		
		$this->totalGeralNota -= $valorDesc;*/	
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
	
	/**
	 * caso na nota fiscal tenha o atributo para descontar o issqn, gera o desconot e imprime no campo correto
	 * caso nao, retorna com o espaco reservado em branco.
	 */
	function descontarISSQN () {

		if ($this->objNF->ISSQN > 0){
			
			$valorDesconto = $this->totalItens * ($this->objNF->ISSQN / 100);
						
			$this->arquivo .= formatarValoresForm( $this->objNF->ISSQN ) . '%';
			$this->arquivo .= $this->espacos( 10 );
			
			$this->arquivo .=  formatarValoresForm( $valorDesconto ); 
			
			$parametros = carregaParametrosConfig();
			$tributo = ucfirst( getTipoTributosPessoaTipo( $this->objNF->getIdPessoaTipo() ) );
			
			if ( $parametros['trib'.$tributo.'IssApN'] == 'S' ){
				$this->totalDescontos += $valorDesconto;
			}
			
		}
		else
			$this->arquivo .=  $this->espacos(20);  
	}
}
