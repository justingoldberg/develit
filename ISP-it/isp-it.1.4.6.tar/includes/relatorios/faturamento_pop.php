<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/4004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#. 
# Função:
#      Includes de Consultas


function relatorioFaturamentoClientesPOP($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	# Formatar Datas
	$matriz[dtInicial]=formatarData($matriz[dtInicial]);
	$matriz[dtFinal]=formatarData($matriz[dtFinal]);
	$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/01 00:00:00';
	$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.dataDiasMes(substr($matriz[dtFinal],0,2))." 23:59:59";	
	
	#escolhe a data base
	if($matriz[baixa]=='baixa') {
		$dtBase='dtBaixa';
		$ttBase=' - Por Baixa';
		$grvMes='Baixado';
	}
	else {
		$dtBase='dtVencimento';
		$ttBase=' - Por Vencimento';
		$grvMes='Vencimento';
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

	echo "<br>";
	novaTabela("[Faturamento Detalhado por Clientes]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 7);

	# Consultar POPs
	if($consultaPOP && contaConsulta($consultaPOP)>0) {
		
		for($x=0;$x<contaConsulta($consultaPOP);$x++) {
		
			$idPOP=resultadoSQL($consultaPOP, $x, 'id');
			$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');
			$totalGeral=array();
			$total=array();
		
			$sql="
				SELECT 
					$tb[PessoasTipos].id idPessoaTipo, 
					$tb[Pessoas].nome nomePessoa, 
					LEFT($tb[ContasReceber].$dtBase,7) mes, 
					SUM($tb[ContasReceber].valor) valor, 
					SUM($tb[ContasReceber].valorRecebido) recebido, 
					SUM($tb[ContasReceber].valorJuros) juros, 
					SUM($tb[ContasReceber].valorDesconto) desconto 
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
					AND $tb[ContasReceber].status = 'B'
					AND $tb[ContasReceber].$dtBase 
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id='$idPOP'
				GROUP BY 
					$tb[PessoasTipos].id,
					LEFT($tb[ContasReceber].$dtBase,7)
				ORDER BY 
					$tb[Pessoas].nome,
					$tb[ContasReceber].$dtBase			
			";
			
			$consultaBaixados=consultaSQL($sql, $conn);
			
			if($consultaBaixados && contaConsulta($consultaBaixados)>0) {
				# Totalizar
				$matRecebido=array();
				$matJuros=array();
				$matDesconto=array();
				$matTotal=array();
				for($i=0;$i<contaConsulta($consultaBaixados);$i++) {
					$idPessoaTipo=resultadoSQL($consultaBaixados, $i, 'idPessoaTipo');
					$nomePessoa=resultadoSQL($consultaBaixados, $i, 'nomePessoa');
					$valor=resultadoSQL($consultaBaixados, $i, 'valor');
					$recebido=resultadoSQL($consultaBaixados, $i, 'recebido');
					$juros=resultadoSQL($consultaBaixados, $i, 'juros');
					$desconto=resultadoSQL($consultaBaixados, $i, 'desconto');
					$mes=resultadoSQL($consultaBaixados, $i, 'mes');
					
					$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
					$matTotal["$idPessoaTipo"]["$mes"] += $valor;
					$matRecebido["$idPessoaTipo"]["$mes"] += $recebido;
					$matJuros["$idPessoaTipo"]["$mes"] += $juros;
					$matDesconto["$idPessoaTipo"]["$mes"] += $desconto;
				}
				
			}

			# SQL para consulta de emails por dominios do cliente informado
			# Consultas de Pendentes
			$sql="
				SELECT 
					$tb[PessoasTipos].id idPessoaTipo, 
					$tb[Pessoas].nome nomePessoa, 
					LEFT($tb[ContasReceber].$dtBase,7) mes, 
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
					AND $tb[ContasReceber].$dtBase 
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id='$idPOP'
				GROUP BY 
					$tb[PessoasTipos].id,
					LEFT($tb[ContasReceber].$dtBase,7)
				ORDER BY 
					$tb[Pessoas].nome,
					$tb[ContasReceber].$dtBase				
			";

			$consultaPendentes=consultaSQL($sql, $conn);
			
			if($consultaPendentes && contaConsulta($consultaPendentes)>0) {
				# Totalizar
				$matPendente=array();
				for($i=0;$i<contaConsulta($consultaPendentes);$i++) {
					$idPessoaTipo=resultadoSQL($consultaPendentes, $i, 'idPessoaTipo');
					$nomePessoa=resultadoSQL($consultaPendentes, $i, 'nomePessoa');
					$valor=resultadoSQL($consultaPendentes, $i, 'valor');
					$mes=resultadoSQL($consultaPendentes, $i, 'mes');

					$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
					$matPendente["$idPessoaTipo"]["$mes"] = $valor;
					$matTotal["$idPessoaTipo"]["$mes"] += $valor;
				}
				
			}
			
			if(is_array($matTotal)) {
				
				$keys=array_keys($matTotal);
				$matResultado=array();
				$matCabecalho=array("Nome do Cliente", $grvMes, "Faturado", "Recebido", "Pendente", "Juros", "Descontos");
				
				$l=0;
				for($a=0;$a<count($keys);$a++) {
					
					//echo "Debug: $keys[$a]";
					$meses=array_keys($matTotal[$keys[$a]]);
					
					if(is_array($meses)) {
						
						for($m=0;$m<count($meses);$m++) {
							
							$mes=$meses[$m];
							$ano=substr($mes,0,4);
							$mesFormatado=$configMeses[intval(substr($mes, 5, 2))]."/".$ano;
						
							$nomePessoa=$matDetalhe[$keys[$a]][nomePessoa];
							$totalPendente=$matPendente[$keys[$a]][$mes];
							$totalValor=$matTotal[$keys[$a]][$mes];
							$totalRecebido=$matRecebido[$keys[$a]][$mes];
							$totalJuros=$matJuros[$keys[$a]][$mes];
							$totalDesconto=$matDesconto[$keys[$a]][$mes];
							
							$c=0;
							$matResultado[$matCabecalho[$c++]][$l]=$nomePessoa;
							$matResultado[$matCabecalho[$c++]][$l]=$mesFormatado;
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalValor);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalRecebido);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalPendente);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalJuros);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalDesconto);
							
							$totalGeral[faturado]+=$totalValor;
							$totalGeral[recebido]+=$totalRecebido;
							$totalGeral[pendente]+=$totalPendente;
							$totalGeral[juros]+=$totalJuros;
							$totalGeral[desconto]+=$totalDesconto;
							
							$l++;

						}
					}
					
				} #fecha laco de montagem de tabela
				
				# Alimentar Array de Detalhe com mais um campo - totais
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$l]='<b>Total</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[recebido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[pendente]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[juros]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[desconto]).'</b>';
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="TOTAL DE FATURAMENTO POR CLIENTE$ttBase";
				$matrizRelatorio[header][POP]=$nomePOP;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=25;
				$matrizRelatorio[config][layout]='landscape';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
								
				$matrizGrupo[]=$matrizRelatorio;
								
			}
			else {
				# Não há registros
				//itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'left', $corFundo, 7, 'txtaviso');
			}
		
		}

		# Converter para PDF:
		$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_pop'),'faturamento_pop',$matrizRelatorio[config]);
		itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório Total de Faturamento por POP$ttBase</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');

	}
	else {
		# Não há registros
		itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');
	}
		
	fechaTabela();
}


?>
