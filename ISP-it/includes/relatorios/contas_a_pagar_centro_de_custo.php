<?

/**
 * Exibe o formulário para gerar relatório de Contas à Pagar por Centro de Custo
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function formContasAPagarCentroDeCusto( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	novaTabela2("[Contas à Pagar X Centro de Custo]", "center", "100%", 0, 2, 1, $corFundo, $corBorda, 2);
		
		#cabecalho com campos hidden
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );

		#pop de acesso
		$combo = formSelectPOP($matriz['pop'],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
		getCampo( "combo", "<b>POP:</b><br><span class=normal10>Selecione o POP de Acesso</span>", "", $combo );
		
		#anos do período
		$anos 	  = array();
		$anoAtual = date("Y");
		for($z = $anoAtual; $z < $anoAtual + 10; $z++ ){
			$anos[] = $z;
		}
		getCampoCombo(	"Ano", "matriz[ano]", $anos, "", $matriz['ano'], "", '' );

		
		#semetres do relatorio
		$opcaoSemestre = array( "Anual",	"1&ordm; trimestre",	"2&ordm; trimestre",	"3&ordm; trimestre",	"4&ordm; trimestre"	);
		$valorSemestre = array( "",			"1",				"2",				"3",				"4"					);
		getCampoCombo(	"Período", "matriz[periodo]", $opcaoSemestre, $valorSemestre, $matriz['periodo'], "", '' );
				
		getBotoesConsRel();
		
		fechaFormulario();
	
	fechaTabela();
	
}

/**
 * Cria uma matriz para gerar o relatório de Contas à Pagar por Centro de Custo
 *
 * @param array $matriz
 * @return array
 */
function ContasAPagarCentroDeCustoPreparaRel( $matriz ){
	global $conn, $tb;
	
	$matrizGrupo 	= array();
	
	$ano 			= intval($matriz['ano']);
	
	$trimestres		= ( $matriz['periodo'] ? array( intval($matriz['periodo']) ) : array( 1, 2, 3, 4 ) );
	
	$meses = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
	
	# Montar 
	if(!$matriz['pop_todos'] && $matriz['pop']) {
		$i=0;
		$sqlADDPOP="{$tb['POP']}.id in (";
		while($matriz['pop'][$i]) {
			
			$sqlADDPOP.="'".$matriz['pop'][$i]."'";
			
			if($matriz['pop'][$i+1]) $sqlADDPOP.=",";
			$i++;
		}
		$sqlADDPOP.=")";
		
		$consultaPOP=buscaPOP($sqlADDPOP, '','custom','nome');
		
	}
	elseif($matriz['pop_todos']) {
		# consultar todos os POP
		$consultaPOP = buscaPOP('','','todos','nome');
	}
	
	$l = 0;	
	if( $consultaPOP && contaConsulta( $consultaPOP ) ) {
		
		for( $p=0; $p<contaConsulta( $consultaPOP ); $p++ ){
			
			$idPOP		= resultadoSQL( $consultaPOP, $p, 'id' );
			
			$nomePop	= resultadoSQL( buscaPOP( $idPOP, 'id', 'igual', 'nome'), 0, 'nome');
			
			$sqlPop = 'ContasAPagar.idPop = '.$idPOP;
						
			foreach( $trimestres as $trimestre ){

				$ttTriPrevi = 0;
				$ttTriGasto = 0;
				
				$iTri 	= $trimestre * 3 - 2;
				$fTri	= $trimestre * 3;
		
				$sqlOpcao = " AND {$tb['CentroDeCustoPrevisao']}.mes BETWEEN $iTri AND $fTri";
				
				$sql = "
					SELECT 
					  {$tb['CentroDeCustoPrevisao']}.mes,
					  {$tb['CentroDeCustoPrevisao']}.valor as valorPrevisao,
					  {$tb['CentroDeCusto']}.id as centroDeCusto,
					  SUM(IF({$tb['ContasAPagar']}.status='B', {$tb['ContasAPagar']}.valorPago, {$tb['ContasAPagar']}.valor)) as valorGasto
					FROM
					  CentroDeCustoPrevisao
					  LEFT JOIN CentroDeCusto
					    ON ({$tb['CentroDeCustoPrevisao']}.idCentroDeCusto = {$tb['CentroDeCusto']}.id)
					  LEFT JOIN PlanoDeContasDetalhes
					    ON ({$tb['CentroDeCusto']}.id = {$tb['PlanoDeContasDetalhes']}.idCentroDeCusto)
					  LEFT JOIN ContasAPagar
					    ON ({$tb['PlanoDeContasDetalhes']}.id = {$tb['ContasAPagar']}.idPlanoDeContasDetalhes)
					WHERE 
					  {$tb['CentroDeCustoPrevisao']}.mes = DATE_FORMAT({$tb['ContasAPagar']}.dtVencimento, '%m') AND
					  {$tb['CentroDeCustoPrevisao']}.ano='$ano'
					  AND $sqlPop
					  $sqlOpcao
					GROUP BY
					  {$tb['CentroDeCustoPrevisao']}.mes, {$tb['CentroDeCusto']}.id
					ORDER BY
					  {$tb['CentroDeCusto']}.id, {$tb['CentroDeCustoPrevisao']}.mes
				";
				
				$consulta = consultaSQL( $sql, $conn );
				# trata os dados para a matriz
				if( $consulta && contaConsulta($consulta) ){

					$ttTriPrevi = 0;
					$ttTriGasto = 0;
					
					$ttMesPrevisto	= array();
					$ttMesGasto		= array();
					
					# ordena os dados agrupando por centro de custo
					while( $dados = mysql_fetch_object($consulta) ) {
						$saldo			= $dados->valorPrevisao - $dados->valorGasto;
						$resultado[$dados->centroDeCusto][$dados->mes]['Previsao']	= $dados->valorPrevisao;
						$resultado[$dados->centroDeCusto][$dados->mes]['Gasto']		= $dados->valorGasto;
						$resultado[$dados->centroDeCusto][$dados->mes]['Saldo']		= $saldo;
						
						# soma o total de cada mes
						if( !isset($ttMesPrevisto[$dados->mes]) && !isset($ttMesPrevisto[$dados->mes]) ) {
							$ttMesPrevisto[$dados->mes]	= 0;
							$ttMesGasto[$dados->mes]	= 0;
						}
						$ttMesPrevisto[$dados->mes]	+= $dados->valorPrevisao;
						$ttMesGasto[$dados->mes]	+= $dados->valorGasto;
						$ttTriPrevi += $dados->valorPrevisao;
						$ttTriGasto += $dados->valorGasto;
					}
					
					# Trata os dados na matriz de relatório
					
					$centros = dbCentroDeCusto('', 'consultar', '', '', 'nome');
					if( count($centros) ){
						
						$cabecalho = array( 'CC', 'P1', 'G1', 'S1', 'P2', 'G2', 'S2', 'P3', 'G3', 'S3' );
						
						#Aloca os dados da tabela na matriz no respectivo mes
						$l = 0;				
						foreach( $centros as $centro ){
							$c = 0;
							$matResultado[$cabecalho[$c++]][$l] = $centro->nome;
		
							for( $i = $iTri; $i <= $fTri; $i++ ){
								$matResultado[$cabecalho[$c++]][$l] = number_format($resultado[$centro->id][$i]['Previsao']	, 2, ',', '.');
								$matResultado[$cabecalho[$c++]][$l] = number_format($resultado[$centro->id][$i]['Gasto']	, 2, ',', '.');	
								$matResultado[$cabecalho[$c]][$l] 	= number_format($resultado[$centro->id][$i]['Saldo']	, 2, ',', '.');
								$c = ( ( $c == count($cabecalho)-1 ) ? $c : $c+1 );
							}
							$l++;
						}
						
						# aloca os o total de cada mes na matriz
						$c = 0;
						$matResultado[$cabecalho[$c++]][$l]	= "<b style=\"font-weight:bold;\">Sub-total</b>";
						for( $i = $iTri; $i <= $fTri; $i++ ){
							$saldo = $ttMesPrevisto[$i] - $ttMesGasto[$i];
							$matResultado[$cabecalho[$c++]][$l] = "<b style=\"font-weight:bold;\">".number_format($ttMesPrevisto[$i]	, 2, ',', '.')."</b>";
							$matResultado[$cabecalho[$c++]][$l] = "<b style=\"font-weight:bold;\">".number_format($ttMesGasto[$i]		, 2, ',', '.')."</b>";
							$matResultado[$cabecalho[$c]][$l] 	= "<b style=\"font-weight:bold;\">".number_format($saldo				, 2, ',', '.')."</b>";
							$c = ( ( $c == count($cabecalho) - 1 ) ? $c : $c + 1 );
						}
						
					} // fim do se tem centros de custo

					$ttTriSaldo = $ttTriPrevi - $ttTriGasto;
					
					# Alimentar Matriz Geral
					$matrizRelatorio['detalhe'] = $matResultado;
					
					# Alimentar Matriz de Header
					$matrizRelatorio['header']['TITULO']	= "CONTAS À PAGAR POR CENTRO DE CUSTO";
					$matrizRelatorio['header']['POP']		= $nomePop;
					$matrizRelatorio['header']['IMG_LOGO']	= $html['imagem']['logoRelatorio'];
					$matrizRelatorio['header']['TRIMESTRE']	= $trimestre;
					
					# do cabeçaho de cada semestre
					$matrizRelatorio['cabecalho']['CC']		= "<b style=\"font-weight:bold;\">Centro de custo</b>";
					$matrizRelatorio['cabecalho']['mes1']	= "<b style=\"font-weight:bold;\">".$meses[$iTri]."</b>";
					$matrizRelatorio['cabecalho']['mes2']	= "<b style=\"font-weight:bold;\">".$meses[$iTri+1]."</b>";
					$matrizRelatorio['cabecalho']['mes3']	= "<b style=\"font-weight:bold;\">".$meses[$fTri]."</b>";
					$matrizRelatorio['cabecalho']['previ']	= "<b style=\"font-weight:bold;\">Prev."."</b>";
					$matrizRelatorio['cabecalho']['gasto']	= "<b style=\"font-weight:bold;\">Gasto</b>";
					$matrizRelatorio['cabecalho']['saldo']	= "<b style=\"font-weight:bold;\">Saldo</b>";					
					
					# rodape
					$matrizRelatorio['rodape']['TOTAL']	= "<b style=\"font-weight:bold;\">Total</b>";
					$matrizRelatorio['rodape']['PREVI']	= "<b style=\"font-weight:bold;\">" . number_format($ttTriPrevi, 2, ',', '.') . "</b>";
					$matrizRelatorio['rodape']['GASTO']	= "<b style=\"font-weight:bold;\">" . number_format($ttTriGasto, 2, ',', '.') . "</b>";
					$matrizRelatorio['rodape']['SALDO']	= "<b style=\"font-weight:bold;\">" . number_format($ttTriSaldo, 2, ',', '.') . "</b>";
										
					# Configurações
					$matrizRelatorio['config']['linhas']		= 25; //35
					$matrizRelatorio['config']['layout']		= 'landscape';
					$matrizRelatorio['config']['marginleft']	= '1.0cm;';
					$matrizRelatorio['config']['marginright']	= '1.0cm;';
				
					$matrizGrupo[] = $matrizRelatorio;
					
				
				} // fim do se 
				

	
			} //fim do loop trimestral
	
		} // fim do loop pop

		return $matrizGrupo;
		
	} // fim do se existe pop
	else {
		echo "<br>";
		$msg = "Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}

}


/**
 * Exibe o relatório de Contas à Pagar por Centro de Custo na tela baseado com base nos dadis de $relatorio
 *
 * @param array $relatorio
 */
function contasAPagarCentroDeCustoExibir( $relatorio ){
	global $corFundo, $corBorda, $sessLogin;
	
	$indice		= array( 'CC', 'P1', 'G1', 'S1', 'P2', 'G2', 'S2', 'P3', 'G3', 'S3' );
	

	$cabecalho	= array( 'Centro de Custo', 
						'Prev.', 'Gasto', 'Saldo', 
						'Prev.', 'Gasto', 'Saldo', 
						'Prev.', 'Gasto', 'Saldo' );
	$alinhamento	= array( 'left',	'right',	'right',	'right',	'right',	'right',	'right',	'right',	'right',	'right'	);
	$largura		= array( '28%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%' );
	
	#abre tabela do relatório
	novaTabela( "[Previsão de Centro de Custo por Contas à Pagar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
	htmlAbreLinha( $corFundo );
	htmlAbreColuna( '100%', 'left', $corFundo, 0, 'normal10' );	
	
	$popAnt = "";
	#lista por pops
	foreach( $relatorio as $popTrimestre ) {
		
		$detalhes = $popTrimestre['detalhe'];

		
		$nomePop = $popTrimestre['header']['POP'];
		
		#Verifica se mudou de pop
		if( $nomePop != $popAnt ) {		
			if( $popAnt ) {
				fechaTabela();
			}

			echo "<br />";
			novaTabela( $nomePop, "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
		}
		
		$popAnt = $nomePop;
		
		$trimestre = $popTrimestre['header']['TRIMESTRE'];
		

		#cabeçalho
		htmlAbreLinha($corFundo);			
			itemLinhaNOURL( '<span class="bold">'.$trimestre.'&ordm; Trimestre</span>'		, 'center', $corFundo, 10, 'tabfundo0');
		htmlFechaLinha();
		htmlAbreLinha($corFundo);			
			itemLinhaNOURL($popTrimestre['cabecalho']['CC']		, 'center', $corFundo, 0, 'tabfundo1', 2 );
			itemLinhaNOURL($popTrimestre['cabecalho']['mes1']	, 'center', $corFundo, 3, 'tabfundo1');
			itemLinhaNOURL($popTrimestre['cabecalho']['mes2']	, 'center', $corFundo, 3, 'tabfundo1');
			itemLinhaNOURL($popTrimestre['cabecalho']['mes3']	, 'center', $corFundo, 3, 'tabfundo1');
		htmlFechaLinha();
		htmlAbreLinha( $corFundo );
			for( $i = 0; $i < 3; $i++ ) {
				itemLinhaNOURL($popTrimestre['cabecalho']['previ']	, 'center', $corFundo, 0, 'tabfundo1');
				itemLinhaNOURL($popTrimestre['cabecalho']['gasto']	, 'center', $corFundo, 0, 'tabfundo1');
				itemLinhaNOURL($popTrimestre['cabecalho']['saldo']	, 'center', $corFundo, 0, 'tabfundo1');
			}
		htmlFechaLinha();
		
		#corpo :
		for($i = 0; $i < count($detalhes['CC']); $i++){
			htmlAbreLinha( $corFundo );
			foreach( $indice as $j => $celula ) {
				itemLinhaNOURL( $detalhes[$celula][$i], $alinhamento[$j], $corFundo, 0, 'normal9');
			}
			htmlFechaLinha();
		}
		
		#Exibe o rodape do trimestre
		htmlAbreLinha( $corFundo );
			itemLinhaNOURL($popTrimestre['rodape']['TOTAL']	, 'right', $corFundo, 7, 'tabfundo1');
			itemLinhaNOURL($popTrimestre['rodape']['PREVI']	, 'right', $corFundo, 0, 'normal9');
			itemLinhaNOURL($popTrimestre['rodape']['GASTO']	, 'right', $corFundo, 0, 'normal9');
			itemLinhaNOURL($popTrimestre['rodape']['SALDO']	, 'right', $corFundo, 0, 'normal9');
		htmlFechaLinha();
		
	}
	
	# Fecha a tabela do último pop
			htmlFechaColuna();
			htmlFechaLinha();
			fechaTabela();
	
	#Fecha a tabela do relatório
	htmlFechaColuna();
	htmlFechaLinha();
	fechaTabela();
}

/**
 * Cria o relatório de Contas à Pagar por Centro de Custo em PDF com base dos dados de $relatorio
 *
 * @param array $relatorio
 */
function contasAPagarCentroDeCustoRelatorio( $relatorio ){
	global $corFundo, $corBorda, $sessLogin;
	
	$alinhamento	= array( 'left',	'right',	'right',	'right',	'right',	'right',	'right',	'right',	'right',	'right'	);
	$indice		= array( 'CC', 'P1', 'G1', 'S1', 'P2', 'G2', 'S2', 'P3', 'G3', 'S3' );
	$largura		= array( '28%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%',		'8%' );
	
	if( count($relatorio) ) {
		
		$paginaHTML = "";
		
		foreach( $relatorio as $pagina => $popTrimestre ) {

			$detalhes	= $popTrimestre['detalhe'];
	
			$nomePop	= $popTrimestre['header']['POP'];
			
			$trimestre	= $popTrimestre['header']['TRIMESTRE'];
			
			$paginaHTML .=	"<html>\n".
							"<body>\n".
							"<table width=100%>\n".
							"<tr>\n".
							"<td><img src={$popTrimestre['header']['IMG_LOGO']} border=0></td>\n".
							"<td align=center>\n".
							"<H3>{$popTrimestre['header']['TITULO']}</h3><br>\n".
							"<p><b>{$popTrimestre['header']['POP']}</b><br>\n".
							"</td></tr>\n".
							"</table>\n".
							"<table width=100% cellpadding=2 border=1>\n".
							"<tr><td colspan=10 class=bold align=center>$trimestre&ordm; Trimestre</td></tr>\n".
							"<tr bgcolor=#ffffff>\n".
							"<td class=normal10 align=center rowspan=2><b style=\"font-weight:bold;\">{$popTrimestre['cabecalho']['CC']}</b></td>\n".
							"<td class=normal10 align=center colspan=3><b style=\"font-weight:bold;\">{$popTrimestre['cabecalho']['mes1']}</b></td>\n".
							"<td class=normal10 align=center colspan=3><b style=\"font-weight:bold;\">{$popTrimestre['cabecalho']['mes2']}</b></td>\n".
							"<td class=normal10 align=center colspan=3><b style=\"font-weight:bold;\">{$popTrimestre['cabecalho']['mes3']}</b></td>\n".
							"</tr>\n".
							"<tr bgcolor=#ffffff>\n";

			for( $i = 0; $i < 3; $i++ ) {
				$paginaHTML .= "<td class=normal10 align=center><b style=\"font-weight:bold;\">{$popTrimestre['cabecalho']['previ']}</b></td>";
				$paginaHTML .= "<td class=normal10 align=center><b style=\"font-weight:bold;\">{$popTrimestre['cabecalho']['gasto']}</b></td>";
				$paginaHTML .= "<td class=normal10 align=center><b style=\"font-weight:bold;\">{$popTrimestre['cabecalho']['saldo']}</b></td>";
			}
			$paginaHTML .= "</tr>";	
	
			
			#corpo :
			for($i = 0; $i < count($detalhes['CC']); $i++){
				$paginaHTML .= "<tr>";
				foreach( $indice as $j => $celula ) {
					$paginaHTML .= "<td class=normal10 align=".$alinhamento[$j]." width=8%>".$detalhes[$celula][$i]."</td>";
				}
				$paginaHTML .= "</tr>";
			}

			#Exibe o rodape do trimestre
			$paginaHTML	.=	"<tr bgcolor=#ffffff>\n".
							"<td class=normal10 align=right class=normal9 colspan=7>{$popTrimestre['rodape']['TOTAL']}</td>\n".
							"<td class=normal10 align=right class=normal9>{$popTrimestre['rodape']['PREVI']}</td>\n".
							"<td class=normal10 align=right class=normal9>{$popTrimestre['rodape']['GASTO']}</td>\n".
							"<td class=normal10 align=right class=normal9>{$popTrimestre['rodape']['SALDO']}</td>\n".
							"</tr>\n".
							"</table>\n".
							"</body>\n".
							"</html>\n";

			$paginaHTML.="<!--NewPage-->";
			
			if( $pagina < count($relatorio)-1 ) $paginaHTML .= "<br />";			
			
		}
		
		# Converter para PDF:
//		$nome = "contas_pagar_centro_custo";
//		criaTemplatesContasAPagarCentroDeCusto( $nome, $relatorio[0]['cabecalho'], $alinhamento, $largura, $indice, );	
//		$arquivo = k_reportHTML2PDF( k_report( $relatorio, 'html' , $nome ) , $nome , $relatorio[0]['config'] );

		$arquivo = k_reportHTML2PDF( $paginaHTML , $nome , $relatorio[0]['config'] );
		
		if ( $arquivo ) {
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Contas à pagar por Centro de Custo</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso' );
				htmlFechaLinha();
			fechaTabela();
		}	
	}
	else{

	}
}

?>