<?
################################################################################
#       Criado por: Lis
#  Data de criação: 07/03/2007
# Ultima alteração: 07/03/2007
#    Alteração No.: 001
#
# Função:
#    Impressão Nota Fiscal - Classe para impressão de nota fiscal modelo B
################################################################################

class ImpressaoNFModeloB extends ImpressaoNF {
	
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

	/**
	 * Função que recebe o objeto contendo os dados da nota
	 * e seta os valores de configuração do modelo
	 * 
	 * @param unknown_type $obj
	 * @return ImpressaoNFModeloB
	 */
	function ImpressaoNFModeloB( $obj ){
		#1. objNF recebe o conteúdo do objeto que contémas informações para impressão na nota
		#2. setar valores para configuração do modelo
		#3. variável que receberá valores já formatados para a impressão
		
		#1
		$this->objNF = $obj;
		#2
		$this->colsPrint = 156;
		$this->rowsDetail = 12;
		$this->saltoNumNF = 4;
		$this->saltoCabecalho = 3;
		$this->saltoDetalhe = 1;
		$this->saltoRodape = 6;
		$this->totalItens = 0;
		$this->totalDescontos = 0;
		$this->totalGeralNota = 0;
		#3
		$this->arquivo = '';
			
	}
	
	/**
	 * Função que gerencia a impressão da nota e retorna o arquivo com dados para serem impressos
	 *
	 * @return unknown
	 */
	function imprimir(){
		// inicia o arquivo texto que será enviado à impressora

		$this->cabecalho(); //cabeçalho da nota
		$this->detalhe();   // área de descrição dos serviços prestados
		$this->desconto();  // área de desconto de impostos da nota
		
		//$this->arquivo .= chr(18);
	
		#inicio alteracao
			
		$this->arquivo .= $this->espacos( 5 );
		$this->descontarISSQN();
			
		#fim alteracao
		
		//imprime o total Geral da Nota
		$parametros = carregaParametrosConfig();	
		
		$this->totalGeralNota = $this->totalItens;
		
//		if ( strtoupper( $parametros[aplicar_discontos_nf] ) != "N" ){
			$this->totalGeralNota -= $this->totalDescontos ;
//		}
		
		$this->arquivo .= $this->saltaLinhas( 1 );
		//$this->arquivo .= chr(27).chr(69); //formata o efeito negrito da impressora
		$this->arquivo .= $this->preencheCampos( 126, $this->moeda( $this->totalGeralNota ), 'right' );
		//$this->arquivo .= chr(27).chr(70); // desliga o efeito negrito da impressora
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoRodape );
		

		$this->arquivo .= $this->espacos( 6 );
		
		//$this->arquivo .= chr(18);
		
		$this->arquivo .= chr(27).chr(14);
		//$this->arquivo .= $this->objNF->numNF; //
		$this->arquivo .= $this->preencheCampos( 9, $this->objNF->numNF, 'right' );
		$this->arquivo .= chr(20);
		
		for ( $x=0; $x<5 ;$x++ )
			$this->arquivo .= "\r\n";
		
		$this->arquivo .= chr(18);
		return( $this->arquivo );
		
	}
	
	/**
	 * Função responsável pelo cabeçalho da nota fiscal
	 * Nº da NF, data de emissão e demais dados do cliente
	 *
	 */
	function cabecalho(){
		$this->arquivo = $this->saltaLinhas( 3 );
		//faz a impressao do cabecalho
		$this->arquivo .= chr(27).chr(15);
		$this->arquivo .= $this->espacos( 115 );
		
		
		$this->arquivo .= chr(27).chr(14); // formata caracter expandido da impressora
		//$this->arquivo .= $this->objNF->numNF; //
		$this->arquivo .= $this->preencheCampos( 9, $this->objNF->numNF, 'right' );
		$this->arquivo .= chr(20); // desliga o efeito expandido da impressora
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( $this->saltoNumNF );
		
		$this->arquivo .= chr(27).chr(15);
		$this->arquivo .= $this->espacos( 111 );
		
		$this->arquivo .= chr(27).chr(14); // formata caracter expandido da impressora
		$this->arquivo .= $this->centralizaDados( 13, $this->data( $this->objNF->dtEmissao ) ); 
		$this->arquivo .= chr(20); // desliga o efeito expandido da impressora
		
		$this->arquivo .= chr(27).chr(15); // formata o efeito condensado da impressora
		
		$this->arquivo .= $this->saltaLinhas( 2 );
		
		$this->arquivo .= $this->preencheCampos( 95, $this->formatString( $this->objNF->razao ), 'left' );

		$this->arquivo .= $this->saltaLinhas( 2 );
		
		$this->arquivo .= $this->preencheCampos( 78, $this->formatString($this->objNF->endereco) );
				
		$this->arquivo .= $this->espacos( 1 );
	
		$this->arquivo .= $this->centralizaDados( 19, $this->formatString($this->objNF->bairro) );
		
		$this->arquivo .= $this->saltaLinhas( 2 );
		
		$this->arquivo .= $this->preencheCampos( 80, $this->formatString($this->objNF->cidade), 'left' );

		$this->arquivo .= $this->espacos( 2 );
		
		$this->arquivo .= $this->centralizaDados( 5, $this->objNF->uf );

		$this->arquivo .= $this->espacos( 4 );
		
		$this->arquivo .= $this->preencheCampos( 10, $this->objNF->cep );
		
		$this->arquivo .= $this->saltaLinhas( 2 );		
		
		$this->arquivo .= $this->preencheCampos( 65, $this->objNF->cnpj, 'left' );

		$this->arquivo .= $this->espacos( 1 );
		
		$this->arquivo .= $this->preencheCampos( 23, $this->objNF->inscrEst, 'left' );

		$this->arquivo .= $this->saltaLinhas( 5 );
		
		$this->arquivo .= $this->preencheCampos( 13, '', 'left' );

		$this->arquivo .= $this->saltaLinhas( $this->saltoCabecalho );	
	}
	
	/**
	 * Função que verifica os itens de descrição dos serviços da nota fiscal
	 *
	 */
	function detalhe(){
		$totalItem = 0;
		$totalItens = 0;
		$linhasDetalhe = 0; // guarda a qtde de linhas utilizadas no detalhe

		foreach ( $this->objNF->Itens as $detalhes ){
				$this->arquivo .= $this->preencheCampos( 4, $detalhes->qtde, '', '0' );
				$this->arquivo .= $this->espacos( 5 );
				$this->arquivo .= $this->preencheCampos( 85, $this->formatString($detalhes->descricao));
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
		$this->arquivo .= $this->preencheCampos( 130, $this->moeda( $this->totalItens ), 'right' );
		
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
			$this->arquivo .= $this->saltaLinhas( 1 );#$this->arquivo .= $this->saltaLinhas( 3 );
		}
	}
	
	/**
	* caso na nota fiscal tenha o atributo para descontar o issqn, gera o desconto e imprime no campo correto
	* caso não, retorna com o espaço reservado em branco.
	*/
	function descontarISSQN () {

		if ($this->objNF->ISSQN > 0){
			//$this->arquivo.= $this->saltaLinhas(1);
			$this->arquivo .= $this->preencheCampos(40, $this->moeda( $this->totalItens ), 'right' );
			
			$this->arquivo .=  $this->espacos(15); 
			
			$valorDesconto = $this->totalItens * ($this->objNF->ISSQN / 100);
						
			$this->arquivo .= formatarValoresForm( $this->objNF->ISSQN ) . '%';
			$this->arquivo .= $this->espacos( 15 );
			
			$this->arquivo .=  formatarValoresForm( $valorDesconto ); 
			
			$parametros = carregaParametrosConfig();
			$tributo = ucfirst( getTipoTributosPessoaTipo( $this->objNF->getIdPessoaTipo() ) );
			
			if ( $parametros['trib'.$tributo.'IssApN'] == 'S' ){
				$this->totalDescontos += $valorDesconto;
			}
			$this->arquivo.= $this->saltaLinhas(2);
			$this->arquivo.= $this->preencheCampos( 58, "Desconto de I.S.S. Retido na Fonte.");
		}
		else
			$this->arquivo .=  $this->espacos(20);  
			$this->arquivo.= $this->saltaLinhas(2);
	}
}

?>