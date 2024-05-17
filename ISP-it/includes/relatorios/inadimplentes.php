<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/4004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Includes de Consultas


function relatorioInadimplentes($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	# Formatar Datas
	$data=dataSistema();
	$matriz[dtFinal]=formatarData($matriz[dtFinal]);
	if(substr($matriz[dtFinal],0,2) == $data[mes] && substr($matriz[dtFinal],2,4) == $data[ano] ) {
		$dtFinal=substr($matriz[dtFinal],2,4)."-".substr($matriz[dtFinal],0,2).'-'.$data[dia];
	}
	else {
		$dtFinal=substr($matriz[dtFinal],2,4)."-".substr($matriz[dtFinal],0,2).'-'.dataDiasMes(mktime(0,0,0,substr($matriz[dtFinal],0,2),01,substr($matriz[dtFinal],2,4)));
	}
	
	# Montar 
	if(!$matriz[pop_todos] && $matriz[pop]) {
		$i=0;
		$sqlADDPOP="$tb[POP].id in (";
		while($matriz[pop][$i]) {
			
			$sqlADDPOP.="'".$matriz[pop][$i]."'";
			
			if($matriz[pop][$i+1]) $sqlADDPOP.=",";
			$i++;
		}
		$sqlADDPOP.=")";
		
		$consultaPOP=buscaPOP($sqlADDPOP, '','custom','nome');
		
	}
	elseif($matriz[pop_todos]) {
		# consultar todos os POP
		$consultaPOP=buscaPOP('','','todos','nome');
	}

	
	# Consultar POPs
	if( $matriz['pop_todos'] || $matriz['pop'] ){
		echo "<br>";
		novaTabela("[Inadimplentes]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 8);

		# Consultar POPs
		if($consultaPOP && contaConsulta($consultaPOP)>0) {
			
			for($x=0;$x<contaConsulta($consultaPOP);$x++) {
			
				$idPOP=resultadoSQL($consultaPOP, $x, 'id');
				$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');
				$totalGeral=array();
				$total=array();
				$l = 0;
				
				# Totalizar
				$matPendente=array();
				$matDetalhe = array();
				$matTotal = array();
						
	
				# SQL para consulta de emails por dominios do cliente informado
				# Consultas de Pendentes
				$sql="
					SELECT 
						$tb[PessoasTipos].id idPessoaTipo, 
						$tb[Pessoas].nome nomePessoa, 
						$tb[ContasReceber].dtVencimento dtVencimento, 
						SUM($tb[ContasReceber].valor) valor, 
						SUM($tb[ContasReceber].valorRecebido) recebido, 
						SUM($tb[ContasReceber].valorJuros) juros, 
						SUM($tb[ContasReceber].valorDesconto) desconto, 
						$tb[ContasReceber].status status 
					FROM 
						$tb[POP], 
						$tb[Pessoas], 
						$tb[PessoasTipos], 
						$tb[ContasReceber], 
						$tb[DocumentosGerados] 
					WHERE 
						$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
						AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
						AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
						AND $tb[Pessoas].idPOP = $tb[POP].id 
						AND $tb[ContasReceber].status = 'P'
						AND $tb[POP].id='$idPOP'" ;
					
				//adicionado para poder filtrar por quantidade de dias em atraso para eventos subjacentes. gustavo 20050304
				if($matriz[diasAtraso] == "todos")
					$sql .= "AND $tb[ContasReceber].dtVencimento  < '$dtFinal' AND $tb[ContasReceber].dtVencimento > '0000-00-00'";
				elseif($matriz[diasAtraso] == "quarenta")
					$sql .= "AND DATE_ADD($tb[ContasReceber].dtVencimento, INTERVAL 45 DAY) < '".$dtFinal."'";
				elseif ($matriz[diasAtraso] == "vinte")
					$sql .= "AND $tb[ContasReceber].dtVencimento between DATE_SUB('".$dtFinal."', INTERVAL  45 DAY) AND DATE_SUB('".$dtFinal."', INTERVAL  20 DAY)";
				//Fim
								
				$sql .= "
					GROUP BY 
						$tb[PessoasTipos].id,
						$tb[ContasReceber].dtVencimento
					ORDER BY 
						$tb[Pessoas].nome,
						$tb[ContasReceber].dtVencimento				
				";
	
				$consultaPendentes=consultaSQL($sql, $conn);
				
				if($consultaPendentes && contaConsulta($consultaPendentes)>0) {
					for($i=0;$i<contaConsulta($consultaPendentes);$i++) {
						$idPessoaTipo=resultadoSQL($consultaPendentes, $i, 'idPessoaTipo');
						$nomePessoa=resultadoSQL($consultaPendentes, $i, 'nomePessoa');
						$valor=resultadoSQL($consultaPendentes, $i, 'valor');
						$dtVencimento=resultadoSQL($consultaPendentes, $i, 'dtVencimento');
						
						$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
						$matPendente["$idPessoaTipo"]["$dtVencimento"] = $valor;
						$matTotal["$idPessoaTipo"]["$dtVencimento"] += $valor;
					}
					
				}
				
				if(is_array($matTotal) && count($matTotal)>0) {
					
					$keys=array_keys($matTotal);
					$matResultado=array();
					$matCabecalho=array("Nome do Cliente", "Telefone", "Vecto", "Pendente");
					
					$l=0;
					for($a=0;$a<count($keys);$a++) {
						
						//echo "Debug: $keys[$a]";
						$meses=array_keys($matTotal[$keys[$a]]);
						
						# Buscar Telefone
						$telefone=telefonesPessoasTipos($keys[$a]);					
						
						if(is_array($meses)) {
							
							for($m=0;$m<count($meses);$m++) {
								
								$dtVencimento=$meses[$m];
							
								$nomePessoa=$matDetalhe[$keys[$a]][nomePessoa];
								$totalPendente=$matPendente[$keys[$a]][$dtVencimento];
								
								$c=0;
								
								$matResultado[$matCabecalho[$c++]][$l]=$nomePessoa;
								$matResultado[$matCabecalho[$c++]][$l]=$telefone;
								$matResultado[$matCabecalho[$c++]][$l]=converteData($dtVencimento,'banco','formdata');
								$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalPendente);
								
								$l++;
								
								$totalGeral[pendente]+=$totalPendente;
								
							}
						}
						
					} #fecha laco de montagem de tabela
					
					# Alimentar Array de Detalhe com mais um campo - totais
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='<b>Total</b>';
					$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[pendente]).'</b>';
					
					# Alimentar Matriz Geral
					$matrizRelatorio[detalhe]=$matResultado;
					
					# Alimentar Matriz de Header
					$matrizRelatorio[header][TITULO]="INADIMPLENTES ATÉ ".converteData($dtFinal,'banco','formdata');
					$matrizRelatorio[header][POP]=$nomePOP;
					$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
					
					# Configurações
					$matrizRelatorio[config][linhas]=25;
					$matrizRelatorio[config][layout]='landscape';
					$matrizRelatorio[config][marginleft]='1.0cm;';
					$matrizRelatorio[config][marginright]='1.0cm;';
									
					$matrizGrupo[]=$matrizRelatorio;
									
				}
			}
			
			if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','inadimplentes'),'inadimplentes',$matrizRelatorio[config]);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório Inadimplentes</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
			}
			else {
				# Não há registros
				itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');	
			}
	
		}
		else {
			# Não há registros
			itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');
		}
		
		fechaTabela();
	}
	else{
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}
	
	
}

function InadimplentesRelatorioCSV( $matriz ){
	global $conn, $tb, $html, $arquivo, $corFundo, $corBorda, $sessLogin;
	
	# Formatar Datas
	$data = dataSistema();
	$matriz['dtFinal'] = formatarData( $matriz['dtFinal'] );
	if(substr($matriz['dtFinal'],0,2) == $data['mes'] && substr($matriz['dtFinal'],2,4) == $data['ano'] ) {
		$dtFinal = substr($matriz['dtFinal'],2,4)."-".substr($matriz['dtFinal'],0,2).'-'.$data['dia'];
	}
	else {
		$dtFinal = substr($matriz['dtFinal'],2,4)."-".substr($matriz['dtFinal'],0,2).'-'.dataDiasMes(mktime(0,0,0,substr($matriz['dtFinal'],0,2),01,substr($matriz['dtFinal'],2,4)));
	}
	
	
	#Seleciona os padrões de filtragem
	//ordem de exibição da consulta
	$ordem = "{$tb['POP']}.nome, {$tb['Pessoas']}.nome, {$tb['ContasReceber']}.dtVencimento ; ";
	$opcQuery[] = "{$tb['ContasReceber']}.status = 'P'";	
		
	if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
		$opcQuery[] = "{$tb['Pessoas']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
	}
	# Verifica se existe período
	if( $matriz['diasAtraso'] == "todos" ){
		$opcQuery[] = "{$tb['ContasReceber']}.dtVencimento < '$dtFinal' AND $tb[ContasReceber].dtVencimento > '0000-00-00'";
	}			
	elseif( $matriz['diasAtraso'] == "quarenta" ){
		$opcQuery[] = "DATE_ADD({$tb['ContasReceber']}.dtVencimento, INTERVAL 45 DAY) < '".$dtFinal."'";
	}			
	elseif( $matriz['diasAtraso'] == "vinte" ){
		$opcQuery[] = "{$tb['ContasReceber']}.dtVencimento between DATE_SUB('".$dtFinal."', INTERVAL  45 DAY) AND DATE_SUB('".$dtFinal."', INTERVAL  20 DAY)";
	}
	
	$consulta = consultaContasReceber( $matriz, 'consultar', '', $opcQuery, $ordem ); 
	
	//verifica quantidade de registros na consulta
	$total = count( $consulta );
	//se houver registros...
	$arquivoCSV = '';
	
	if( $total ){
		
		$arquivoCSV .= "POP; Cliente; Telefone; Vencimento; Pendente\n";
		
		$totalPendente = 0;
		$Pendente =0;
		
		
		foreach( $consulta as $i => $linha ) {

			$arquivoCSV .= $linha->pop.";";
			$arquivoCSV .= $linha->pessoa.";";
			$telefone = telefonesPessoasTipos($linha->idPessoaTipo);
			$telefone = str_replace('&nbsp;', ' ', $telefone );
			$arquivoCSV .= $telefone.";";
			$arquivoCSV .= converteData( $linha->dtVencimento, 'banco', 'formdata' ).";";
			$arquivoCSV .= number_format($linha->valor,2,',','.')."\n";
	
			$totalPendente += $linha->valor;
		
			$j = $i + 1;
			
			if( $j == $total || $linha->pop != $consulta[$j]->pop ) { 
				
				$arquivoCSV .= "Total;";
				$arquivoCSV .= ";";
				$arquivoCSV .= ";";
				$arquivoCSV .= ";";
				$arquivoCSV .= number_format($totalPendente,2,',','.')."\n";

		        $Pendente += $totalPendente;
				
		        if($j==$total){

		        	$arquivoCSV .= "Total Geral;";
					$arquivoCSV .= ";";
					$arquivoCSV .= ";";
					$arquivoCSV .= ";";
					$arquivoCSV .= number_format($Pendente,2,',','.');
		        }
		        else{
		        	$arquivoCSV .="\n";
		        }
		        		        
				$totalPendente = 0;
			}				
		}		
	}
	
	if ( $arquivoCSV ) {
		$data = dataSistema();
		
		$nome = $arquivo['tmpCSV']."inadimplentes_$sessLogin[login]_$data[dataBancoGrapi].csv";
		
		criaArquivoCSV( $nome, $arquivoCSV );
		
		echo "<br />";
		novaTabela2('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			htmlAbreLinha($corfundo);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$nome>Relatório de Clientes Inadimplentes</a>",'relatorio'), 'center', $corFundo, 7, 'txtaviso' );
			htmlFechaLinha();
			htmlAbreLinha($corfundo);
				itemTabelaNOURL('Atenção: Clique com o botão direito sobre o link e selecione "Salvar link como" para fazer o download do arquivo.', 'center', $corFundo, 7, 'txtaviso' );
			htmlFechaLinha();
		fechaTabela();
	}
}

?>
