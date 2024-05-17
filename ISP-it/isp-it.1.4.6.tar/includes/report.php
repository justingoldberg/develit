<?
################################################################################
#       Criado por: Jos? Roberto Kerne - joseroberto@kerne.org
#  Data de cria??o: 08/04/2004
# Ultima altera??o: 09/04/2004
#    Altera??o No.: 002
#
# Fun??o:
#    P?gina principal (index) da aplica??o


# Funcao para geracao de relatorio baseado em template
function k_report($matriz, $tipo, $tpl) {
	
	$header=k_templateLoad($tpl."_header");
	$body=k_templateLoad($tpl);
	$detail=k_templateLoad($tpl."_detalhe");
	$footer=k_templateLoad($tpl."_footer");
	
	# Verificar Grupos
	$keysGrupo=array_keys($matriz);
	$pageGeral=0;
	for($i=0;$i<count($keysGrupo);$i++) {
		
		# Parse do Detalhe
		$tmpArray=$matriz[$keysGrupo[$i]];

		# Verificar Matriz de Cabecalho
		$tmpHeader=$tmpArray[header];
		$tmpConfig=$tmpArray[config];
		$headerParse=k_templateParse($header, $tmpHeader);

		$dadosDetalhe=$tmpArray[detalhe];
		$keys=array_keys($dadosDetalhe);
		
		# Zerar resultado temporario para nao interferir nas tabelas montadas
		if(isset($resultado)) unset($resultado);
		
		for($a=0;$a<count($keys);$a++) {
			
			$cabecalho["$a"]=$keys[$a];
			
			# Dados de detalhe para o cabe?alho
			$keys_detalhe=array_keys($dadosDetalhe[$keys[$a]]);
			# Faz parse coluna a coluna - nao linha a linha, ja que indice primario
			# e uma coluna, ou seja, um titulo
			for($b=0;$b<count($keys_detalhe);$b++) {
				# Montar array de detalhe
				//echo "$b - ".$dadosDetalhe[$keys[$a]][$b]."<br>";
				$matrizDetalhe[$b][$a]=$dadosDetalhe[$keys[$a]][$b];
			}
		}
	
		# Parse Linha a Linha
		# Aqui faz linha a linha, ja com todos os valores preenchidos
		# Zerar contador de linhas do detalhe
		$linhaDetalhe=0;
		$page=1;
		$resultado=array();
		for($c=0;$c<count($keys_detalhe);$c++) {

			//$matrizDetalhe[$linhaDetalhe][0]=$linhaDetalhe." ".$matrizDetalhe[$linhaDetalhe][0];
			$resultado[$page-1].=k_templateParse($detail, $matrizDetalhe[$linhaDetalhe]);
			
			# Fazer saltos de pagina e quebras de linha
			if($tmpConfig && is_array($tmpConfig)) {
				if( 
					( 
						(intval(($linhaDetalhe) / $tmpConfig[linhas] / $page)) == 1 
						&& strstr(strtoupper($matrizDetalhe[$linhaDetalhe][count($matrizDetalhe[$linhaDetalhe])-1]),"<!--QUEBRA-->")
					) ||
					( 
						(intval(($linhaDetalhe) / $tmpConfig[linhas] / $page)) == 1 
						&& !strstr(strtoupper($matrizDetalhe[$linhaDetalhe+1][count($matrizDetalhe[$linhaDetalhe])-1]),"<!--QUEBRA-->")
					)
				) {
					# salto de pagina e inser��o de cabe�alho
					$page++;
				}
			}
			
			$linhaDetalhe++;
			
		}

		# Cabe?alho
		$cabecalho[CABECALHO]=$headerParse;

		# Parsear todos os resultados - por pagina
		for($p=0;$p<count($resultado);$p++) {
			$cabecalho[DETALHE]=$resultado[$p];
			
			# Adicionar quebra de pagina
			$retorno[$pageGeral++]=k_templateParse($body, $cabecalho);			
		}
	}
	
	
	# Parse do Template
	$tmpRetorno='';
	$keys=array_keys($retorno);
	for($x=0;$x<count($keys);$x++) {
		$tmpRetorno.=$retorno[$x];
		$tmpRetorno.="<!--NewPage-->";
		if(($x+1) < count($keys)) $tmpRetorno.="<br>";
	}

	return($tmpRetorno);
}


# Fun??o grava??o e convers?o de relatorio gerado para PDF
function k_reportHTML2PDF($conteudo, $nomeArquivo, $config) {

	global $arquivo, $sessLogin;
	
	$config=pdfConfig($config);
	
	$data=dataSistema();
	
	# Apagar Arquivo de relat?rio, em caso de existente
	$fileHTML=$arquivo[tmpHTML].$nomeArquivo."-".$sessLogin[login];
	$filePDF=$arquivo[tmpPDF].$nomeArquivo."-".$sessLogin[login].'.pdf';
	
	@unlink($fileHTML);
	@unlink($filePDF);
	
	# Criar arquivo HTML novo
	$fp=k_fileOpen($fileHTML, "a+");
	
	if($fp) {
		
		fputs($fp[handler], $conteudo);
		fclose($fp[handler]);
		
		# Converter para PDF
		# Instanciar objeto
		$pdf =& new HTML_ToPDF($fileHTML, $defaultDomain, $filePDF);
		
		# N?o deixar CSS do HTML ser utilizado
		$pdf->setUseCSS(false);
		
		$pdf->setAdditionalCSS("
								BODY { 
									font-family: helvetica;
									font-size: 9pt;
								}
								P {
									line-height: 1.0em;
									text-align: justify;
									text-indent: 1;
									margin-top: 1cm;
									margin-bottom: 0cm;
								}
								HR {
									
								}
								@page {
									width: $config[width];
									height: $config[height];
									margin-left: $config[marginleft];
									margin-right: $config[marginright];
									margin-top: $config[margintop];
									margin-bottom: $config[marginbottom];
								}
								@html2ps {
									option {
										landscape: $config[landscape];
									}
								}
		");
		
		# Converter PDF
		$result = $pdf->convert();
		
		// check if the result was an error
		if (PEAR::isError($result)) {
			die($result->getMessage());
		}
		else {
			return($result);
		}
	}
	else {
		echo "Erro na cria��o do arquivo. Por favor, consulte o administrador. ($fileHTML / $filePDF)";
		return (0);
	}
}

/**
 * Monta a exibi��o do relat�rio
 *
 * @param array $detalhes
 * @param array $cabecalho
 */
function exibeRelatorio( $detalhes, $cabecalho ) {
	global $corFundo, $corBorda, $sessLogin;

	foreach( $detalhes as $detal ) {
		echo "<br>";
		novaTabela( $detal['header']['POP'], "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($cabecalho['largura']) );
			
			# mostra o cabe�alho dos detalhes das ordens de servi�o
			htmlAbreLinha($corFundo);
				for( $i=0; $i<count( $cabecalho['cabecalho'] ); $i++){
					itemLinhaTMNOURL($cabecalho['cabecalho'][$i], $cabecalho['alinhamento'][$i], 'middle', $cabecalho['largura'][$i], $corFundo, 0, 'tabfundo0');
				}
			htmlFechaLinha();
			
			$totalLinhas = count( $detal['detalhe'][$cabecalho['cabecalho'][0]] ); //conta a quantidade de linhas do pop
			$totalColunas = count( $detal['detalhe'] ); //conta a quantidade de colunas
			$cabecalhos = array_keys( $detal['detalhe']); 
			# verifica cada cliente(linha) do pop correspondente
			for( $i = 0; $i < $totalLinhas; $i++ ) {
				# mostra cada detalhe(coluna) do cliente do pop correspondente
				htmlAbreLinha( $corFundo );
				for( $j = 0; $j < $totalColunas; $j++ ) {
	
					itemLinhaNOURL( $detal['detalhe'][$cabecalhos[$j]][$i], $cabecalho['alinhamento'][0], $corFundo, 0, 'tabfundo1' );
				}
				htmlFechaLinha();	
			}
			
		
		fechaTabela();
	}
}
?>