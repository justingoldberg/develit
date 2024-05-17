<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 11/03/2004
# Ultima altera��o: 11/03/2004
#    Altera��o No.: 001
#
# Fun��o:
#    Configura��es HTML

# Carregar Classe para PS e PDF

# Fun��o para Preenchimento de dados do HTML Template
function htmlPreencheDados($matriz, $idPaginaContrato) {

	global $tb, $conn, $corFundo, $corBorda, $arquivo, $sessLogin;
	
	$data=dataSistema();
	
	$consultaPaginas=buscaPaginasContratos($idPaginaContrato,'id','igual','id');
	
	if($consultaPaginas && contaConsulta($consultaPaginas)>0) {

		$id=resultadoSQL($consultaPaginas, 0, 'id');
		$nome=resultadoSQL($consultaPaginas, 0, 'nomePagina');
		
		$numero=resultadoSQL($consultaPaginas, 0, 'numeroPagina');
		 
		$descricao=resultadoSQL($consultaPaginas, 0, 'descricao');
		
		$conteudo=resultadoSQL($consultaPaginas, 0, 'conteudo');
		
		$dtCadastro=resultadoSQL($consultaPaginas, 0, 'dtCadastro');
		$status=resultadoSQL($consultaPaginas, 0, 'status');
		
		# Gravar arquivo tempor�rio no disco
		$htmlDestino=$arquivo[tmpHTML];
		$htmlDestino.=$data[ano] . 
					  $data[mes] . 
					  $data[dia] . 
					  $data[hora] . 
					  $data[min] . 
					  $data[seg] . "-" . 
					  "pag" . $numero . "-" . 
					  $sessLogin[login] . ".html";
		
		# Apagar arquivo em caso de existir
		@unlink($htmlDestino);
		
		$fileHTML=k_fileOpen($htmlDestino, "a+");
		
		# Fazer parse em paginas
		## $matriz possui campos que dever�o ser subtituidos no arquivo
		## exemplo: %_DIA_CONTRATO_% = $matriz[DIA_CONTRATO]
		$parseHTML=k_templateParse($conteudo, $matriz);

		# Gravar arquivos no disco
		fputs($fileHTML[handler], $parseHTML);
		fclose($fileHTML[handler]);
		
		# Retornar lista de arquivos gerados
		return($htmlDestino);
	}
}


# Fun��o para Gera��o de PDF
function pdfConverterArquivo($htmlFile) {
	
	global $sessLogin, $arquivo;
	
	$data=dataSistema();
	
	$arquivoDestino=$data[ano] . $data[mes] . $data[dia] . $data[hora] . $data[min] . $data[seg] . "-" . $sessLogin[login] . ".pdf";
	
	# Path completo dos arquivos
	$pdfFile = $arquivo[tmpPDF] . $arquivoDestino;
	
	# remover arquivo antigo
	@unlink($pdfFile);
	
	# Instanciar objeto
	$pdf =& new HTML_ToPDF($htmlFile, $defaultDomain, $pdfFile);
	
	# N�o deixar CSS do HTML ser utilizado
	$pdf->setUseCSS(false);
	
	$pdf->setAdditionalCSS('
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
		width: 21.0cm;
		height: 29.7cm;
		margin-left: 2.5cm;
		margin-right: 1.0cm;
		margin-top: 1cm;
		margin-bottom: 1cm;
	}
	');
	
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



/**
 * @return unknown
 * @param html  $htmlFile
 * @param array $matriz
 * @desc Converte o arquivo acrescentando o Header e o PDF
*/
function pdfConverterArquivoHF($htmlFile, $matriz) {
	
	global $sessLogin, $arquivo;
	
	$data=dataSistema();
	
	$arquivoDestino="inadip-" . $sessLogin[login] . ".pdf";
	
	# Path completo dos arquivos
	$pdfFile = $arquivo[tmpPDF] . $arquivoDestino;
	
	# remover arquivo antigo
	@unlink($pdfFile);
	
	# Instanciar objeto
	$pdf =& new HTML_ToPDF($htmlFile, $defaultDomain, $pdfFile);
	
	# N�o deixar CSS do HTML ser utilizado
	$pdf->setUseCSS(false);
	
	$pdf->setAdditionalCSS('
	BODY { 
		font-family: courier;
		font-size: 10pt;
	}
	P {
		line-height: 1.0em;
		text-align: left;
		text-indent: 1;
		margin-top: 1cm;
		margin-bottom: 0cm;
	}
	HR {
		
	}
	@page {
		width: 21.0cm;
		height: 29.7cm;
		margin-left: 2.5cm;
		margin-right: 1.0cm;
		margin-top: 1cm;
		margin-bottom: 1cm;
	}
	');
	
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
/**
 * @return html
 * @param array		$matriz
 * @param String	$logo
 * @param String	$empresa
 * @param String	$titulo
 * @param String	$corpo
 * @param String	$rodape
 * @desc Gera um arquivo HTML via template a partir da consulta.
 na matriz devem ser setados os seguintes parametros:
 LOGO: logo da empresa
 EMPRESA: dados da empresa
 TITULO: titulo do relatorio
 CORPO: corpo do relatorio
 RODAPE: rodape
*/

function htmlGerarRelatorio($matriz, $logo, $empresa, $titulo, $corpo, $rodape) {

	global $tb, $conn, $corFundo, $corBorda, $arquivo, $sessLogin;
	
	$data=dataSistema();
	
	# seta a matriz
	$matriz[LOGO]=$logo;
	$matriz[EMPRESA]=$empresa;
	$matriz[TITULO]=$titulo;
	$matriz[CORPO]=$corpo;
	$matriz[RODAPE]=$rodape;
	
	# Gravar arquivo tempor�rio no disco
	$htmlDestino=$arquivo[tmpHTML];
	$htmlDestino.="inadip-". 
				  $sessLogin[login] . ".html";
	
	# Apagar arquivo em caso de existir
	@unlink($htmlDestino);
	
	$fileHTML=k_fileOpen($htmlDestino, "a+");
	
	# Fazer parse em paginas
	## $matriz possui campos que dever�o ser subtituidos no arquivo
	## exemplo: %_DIA_CONTRATO_% = $matriz[DIA_CONTRATO]
	$conteudo=k_templateLoad('relatorio'); #relatorio.tpl
	
	$parseHTML=k_templateParse($conteudo, $matriz);

	# Gravar arquivos no disco
	fputs($fileHTML[handler], $parseHTML);
	fclose($fileHTML[handler]);
	
	# Retornar lista de arquivos gerados
	return($htmlDestino);

}



# Funcao para validacao e atribuicao de valores padrao de geracao PDF
function pdfConfig($config) {
	global $configPDF;
	
	$config=alimentaForm($config, $configPDF);
	
	if($config[layout]=='landscape') $config[landscape]=1;
	elseif($config[layout]=='portrait') $config[landscape]=0;
	
	if(!$config[marginleft]) $config[marginleft]="2.5cm";
	if(!$config[marginright]) $config[marginright]="1.0cm";
	if(!$config[margintop]) $config[margintop]="0.5cm";
	if(!$config[marginbottom]) $config[marginbottom]="0.5cm";
	if(!$config[width]) $config[width]="21.0cm";
	if(!$config[height]) $config[height]="29.7cm";
	
	return($config);
}

/**
 * Converte o HTML para um PDF utilizando executando via linha de comando HTMLDOC (www.htmldoc.org)
 *
 * html => caminho+nome do arquivo html de origem
 * pdf  => nome do arquivo pdf a ser gerado
 * 
 * @param string $html
 * @param pdf $pdf
 * @return boolean
 */
function geraPdfHtmlDoc( $html, $pdf ) {
	return (exec( 'htmldoc --webpage --left 1cm --top 0cm -f '.$pdf." ".$html) ? true : false );	
}

?>
