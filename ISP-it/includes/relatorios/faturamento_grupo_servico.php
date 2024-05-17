<?

################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 18/08/2003
# Ultima alteração: 14/04/2004
#    Alteração No.: 004
#
# Função:
# Funções para relatórios

# função para form de seleção de filtros de faturamento
function formRelatorioFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin[login], 'login', 'igual', 'login');

	if (!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg = "ATENÇÃO: Você não tem permissão para executar esta função";
		$url = "?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	} else {
		$data = dataSistema();
		# Motrar tabela de busca
		novaTabela2("[Faturamento por Grupos de Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto = "			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>&nbsp;";
		itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		//POP
		getPop($matriz);

		// grupos
		novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Grupo:</b><br><span class=normal10>Selecione o(s) grupo(s) de serviços</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		if ($matriz[grupos_todos])
			$opcServico = 'checked';
		$texto2 = "<input type=checkbox name=matriz[grupos_todos] value=S $opcServico><b>Todos</b>";
		itemLinhaForm(formSelectGruposServicos($matriz[grupos], 'idGrupos', 'multi').$texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		//Periodo
		novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Mês/Ano Inicial:</b><br><span class=normal10>Informe o mês/ano inicial </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto = "<input type=text name=matriz[dtInicial] size=7 value='$matriz[dtInicial]' onBlur=verificaDataMesAno2(this.value,8)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Mês/Ano Final:</b><br><span class=normal10>Informe o mês/ano final </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto = "<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,9)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		//status
		novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Pendentes:</b><br><span class=normal10>Lista somente os titulos pendentes</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto2 = "<input type=radio name=matriz[status] value=P onBlur='habilitaCampo( this.value, document.forms[0].elements[\"matriz[vencido]\"].name )'><b>só pendentes</b>";
		itemLinhaForm($texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Baixados:</b><br><span class=normal10>Lista somente os titulos baixados</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto2 = "<input type=radio name=matriz[status] value=B><b>só baixados</b>";
		itemLinhaForm($texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Todos:</b><br><span class=normal10>Lista titulos pendentes e baixados</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto2 = "<input type=radio name=matriz[status] value=PB><b>pendentes e baixados</b>";
		itemLinhaForm($texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		// Somente Titulos vencidos
		/*novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Vencidos:</b><br><span class=normal10>Lista somente os titulos vencidos</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if($matriz[vencido]) $opcVencido='checked';
			$texto2="<input type=checkbox name=matriz[vencido] value=S $opcVencido disabled><b>só vencidos</b>";
			itemLinhaForm($texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');		
		fechaLinhaTabela();*/

		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');

		// Botoes
		getBotoesConsRel();

		htmlFechaLinha();
		fechaTabela();

	}

}

#
# Função para consultar de Simulação de Faturamento
function consultaFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml;

	// Se forem todos os pops gera a lista na matriz
	if ($matriz[pop_todos]) {
		$consultaPop = buscaPOP('', '', 'todos', 'id');
		if ($consultaPop && contaconsulta($consultaPop)) {
			for ($a = 0; $a < contaConsulta($consultaPop); $a ++) {
				$matriz[pop][$a] = resultadoSQL($consultaPop, $a, 'id');
			}
		}
	}
	
	if (is_array($matriz[pop])) {

		# Formatar Datas
		if ($matriz[dtInicial]) {
			$matriz[dtInicial] = formatarData($matriz[dtInicial]);
			if ($matriz[diaDe])
				$dia = $matriz[diaDe];
			else
				$dia = '01';
			$dtInicial = substr($matriz[dtInicial], 2, 4)."/".substr($matriz[dtInicial], 0, 2).'/'.$dia.' 00:00:00';
			$matriz[dtInicial] = substr($matriz[dtInicial], 0, 2)."/".substr($matriz[dtInicial], 2, 4);
		}

		if ($matriz[dtFinal]) {
			$matriz[dtFinal] = formatarData($matriz[dtFinal]);
			if ($matriz[diaAte])
				$dia = $matriz[diaAte];
			else
				$dia = dataDiasMes(substr($matriz[dtFinal], 0, 2));
			$dtFinal = substr($matriz[dtFinal], 2, 4)."/".substr($matriz[dtFinal], 0, 2).'/'.$dia.' 23:59:59';
			$matriz[dtFinal] = substr($matriz[dtFinal], 0, 2)."/".substr($matriz[dtFinal], 2, 4);
		}

		// Ajusta o sql para determinar o periodo escolhido
		$sqlDT = "";
		if ($matriz[dtInicial] && $matriz[dtFinal]) {
			$sqlDT = " AND $tb[ContasReceber].dtVencimento between '$dtInicial' and '$dtFinal' ";
			$periodo = "de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
		}
		elseif ($matriz[dtInicial]) {
			$sqlDT = " AND $tb[ContasReceber].dtVencimento >= '$dtInicial' ";
			$periodo = "a partir de ".$matriz[dtInicial];
		}
		elseif ($matriz[dtFinal]) {
			$sqlDT = " AND $tb[ContasReceber].dtVencimento <= '$dtFinal' ";
			$periodo = "até ".$matriz[dtFinal];
		}

		// Ajusta o status para definir quais deles serao listados
		$sqlStatus = '';
		if ($matriz['status']) {
			if ($matriz['status'] == "P")
				$sqlStatus = " AND ContasAReceber.status = 'P' ";
			elseif ($matriz['status'] == "B") $sqlStatus = " AND ContasAReceber.status = 'B' ";
			else
				$sqlStatus = " AND ( ContasAReceber.status = 'P' OR  ContasAReceber.status = 'B' ) ";
		}

		/*
		Se forem todos os grupos gera a lista na matriz
		*/
		if ($matriz[grupos_todos]) {
			$consultaGrupo = buscaGruposServicos('', '', 'todos', 'id');
			if ($consultaGrupo && contaconsulta($consultaGrupo)) {
				for ($a = 0; $a < contaConsulta($consultaGrupo); $a ++) {
					$matriz[idGrupos][$a] = resultadoSQL($consultaGrupo, $a, 'id');
				}
			}
		}

		/* 
		Faz o in para os grupos
		*/
		$grupoRec = array ();
		$grupoFat = array ();
		$cg = 0;

		$sqlGRUPO = "AND $tb[ServicosGrupos].idGrupos in (";
		while ($matriz[idGrupos][$cg]) {
			//zera matriz de totais dos grupos
			$grupoRec[$matriz[idGrupos][$cg]] = 0;
			$grupoFat[$matriz[idGrupos][$cg]] = 0;

			if ($cg > 0)
				$sqlGRUPO .= ", ";
			$sqlGRUPO .= $matriz[idGrupos][$cg ++];
		}

		if ($cg <= 0)
			$sqlGRUPO = "";
		else
			$sqlGRUPO .= ") ";
		//

		//condicao de vencimento
		/*if ($matriz[vencido]) 
			$sqlVencido=" and $tb[ContasReceber].status='P' ".
						" and $tb[ContasReceber].dtVencimento < now() ";
		else
			$sqlVencido=" and $tb[ContasReceber].status<>'C' ";*/

		// Prepara as variaveis de ajuste
		$pp = 0;
		$totalGeral = array ();
		$total = array ();
		if ($matriz[detalhar]) {
			//$largura=array(       '20%',   '50%',     '10%',      '10%',      '10%');
			//$matCabecalho=array(  "Grupo", "Cliente", "Faturado", "Recebido", "Saldo");
			//$matAlinhamento=array("left",  "left",    "right",    "right",    "right");
			$largura = array ('20%', "50%", '30%');
			$matCabecalho = array ("Grupo", "Cliente", "Faturado");
			$matAlinhamento = array ("left", "left", "right");
		} else {
			//$largura=array(       '40%',   '20%',      '20%',      '20%');
			//$matCabecalho=array(  "Grupo", "Faturado", "Recebido", "Saldo");
			//$matAlinhamento=array("left",  "right",    "right",    "right");
			$largura = array ('45%', '10%', '10%', '10%', '10%'); #,   '15%' );
			$matCabecalho = array ("Grupo", "Faturado", "Juros", "Descontos", "Recebido"); #, "Fat-Liq");
			$matAlinhamento = array ("left", "right", "right", "right", "right"); #,  "right" );
		}

		#
		#Consulta o bd POP por POP do q foi selecionado
		#		
		while ($matriz[pop][$pp]) {

			// nome do pop para exbição
			$nomePop = resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');

			$sqlPOP = " AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];

			$sql = "
						SELECT 
							Pop.id idPOP, 
							Pop.nome nomePOP, 
							GruposServicos.id idGrupoServico, 
							GruposServicos.nome grupo, 
							LEFT(ContasAReceber.dtVencimento, 7) dtVencimento,
							SUM(ServicosPlanosDocumentosGerados.valor) valor,
							SUM(ContasAReceber.valorJuros) juros, 
							SUM(ContasAReceber.valorDesconto) desconto
						FROM 	
							Pop,
							Pessoas, 
							PessoasTipos, 
							DocumentosGerados, 
							ContasAReceber, 
							PlanosDocumentosGerados, 
							ServicosPlanosDocumentosGerados, 
							ServicosPlanos, 
							ServicosGrupos, 
							GruposServicos 
						WHERE
							Pop.id = Pessoas.idPOP
							AND Pessoas.id = PessoasTipos.idPessoa 
							AND PessoasTipos.id=DocumentosGerados.idPessoaTipo 
							AND DocumentosGerados.id=ContasAReceber.idDocumentosGerados 
							AND PlanosDocumentosGerados.idDocumentoGerado=DocumentosGerados.id  
							AND ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado=PlanosDocumentosGerados.id 
							AND ServicosPlanos.id=ServicosPlanosDocumentosGerados.idServicosPlanos 
							AND ServicosGrupos.idServico=ServicosPlanos.idServico 
							AND GruposServicos.id=ServicosGrupos.idGrupos
								$sqlPOP 
								$sqlGRUPO
								$sqlDT
								$sqlStatus
								$sqlVencido
						GROUP BY
							GruposServicos.id, 
							Pop.id,
							LEFT($tb[ContasReceber].dtVencimento,7)
						ORDER BY 
							Pop.id, 
							GruposServicos.nome,
							ContasAReceber.dtVencimento,
							Pessoas.nome";

			#echo "sql: $sql"; 
			#AND ContasAReceber.status='P'
			$consultaPop = consultaSQL($sql, $conn);

			if ($consultaPop && contaconsulta($consultaPop) > 0) {

				if ($matriz[consulta]) {

					# Cabeçalho
					echo "<br>";
					novaTabela($nomePop." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 4);

					$cor = 'tabfundo0';
					htmlAbreLinha($cor);
					for ($cc = 0; $cc < count($matCabecalho); $cc ++)
						itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);
					htmlFechaLinha();
				}

				$matResultado = array ();

				#para controle da exibicao

				$dtVencimentoAnterior = '';
				$grupoAnterior = '';
				$l = 0;
				$tf = 0;
				$tj = 0;
				$td = 0;
				$tr = 0;

				#inicia a varredura e joga 
				for ($a = 0; $a < contaConsulta($consultaPop); $a ++) {

					$nomeGrupo = resultadoSQL($consultaPop, $a, 'grupo');
					$dtVencimento = resultadoSQL($consultaPop, $a, 'dtVencimento');

					// totaliza o valor faturado
					$valor = resultadoSQL($consultaPop, $a, 'valor');
					$faturado += $valor;
					$grupoFat["$nomeGrupo"] += $valor;

					$juros = resultadoSQL($consultaPop, $a, 'juros');
					$grupoJur["$nomeGrupo"] += $juros;

					$desc = resultadoSQL($consultaPop, $a, 'desconto');
					$grupoDesc["$nomeGrupo"] += $desc;

					// Se status for B soma no recebido
					//$status=resultadoSQL($consultaPop, $a, 'status');
					$valorRecebido = ($valor - ($juros + $desc));
					//if ($status=='B') { #se for B joga o valor em recebido
					$grupoRec["$nomeGrupo"] += $valorRecebido;
					$recebido += $valorRecebido;
					//}
					/////$saldo=$valor-$valorRecebido;
					/////if ($saldo<0) $saldo = 0;

					$cc = 0;
					$campos[$cc ++] = "<b>".$nomeGrupo."</b>";
					if ($matriz[detalhar])
						$campos[$cc ++] = resultadoSQL($consultaPop, $a, 'cliente');
					$campos[$cc ++] = formatarValoresForm($valor);
					$campos[$cc ++] = formatarValoresForm($juros);
					$campos[$cc ++] = formatarValoresForm($desc);
					$campos[$cc ++] = formatarValoresForm($valorRecebido);
					/////$campos[$cc++]=formatarValoresForm($saldo);

					#verifica se exibe ou nao o nome do grupo
					/*if($anterior==$campos[0]) $campos[0]="&nbsp;";
					else $anterior=$campos[0];*/

					#exibe a linha detalhe
					if ($matriz[consulta]) {
						if ($matriz[detalhar]) {
							htmlAbreLinha($corFundo);
							for ($cc = 0; $cc < count($campos); $cc ++) {
								itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, "normal9");
							}
							htmlFechaLinha();
						}
					}

					# soma na matriz
					/////for ($cc=0; $cc<count($campos); $cc++) {
					/////$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
					/////}
					/////$l++;

					# monta o agrupamento de grupos de servicos
					# Muda de grupo
					# echo "<br>Grupo [$nomeGrupo] lido ".resultadoSQL($consultaPop, $a, 'grupo');

					//if ($nomeGrupo != $anterior) {
					if ($nomeGrupo != $grupoAnterior)
						$dtVencimentoAnterior = '';

					if ($dtVencimento != $dtVencimentoAnterior) {
						/////if ( $nomeGrupo != $grupoAnterior) { #se for vazio eh a primeira vez e nao tem total
						/* Total do Grupo */
						if ($matriz[consulta]) {
							$zebra = "tabfundo1";
							$cc = 0;
							htmlAbreLinha($corFundo);
							if ($matriz[detalhar]) {
								itemLinhaTMNOURL('Total de '.$nomeGrupo, 'right', 'middle', $largura[$cc ++], $corFundo, 2, $zebra);
								$cc ++;
							} else {
								$periodo = substr($dtVencimento, 5, 2)."/".substr($dtVencimento, 0, 4);
								itemLinhaTMNOURL($nomeGrupo." - ".$periodo, 'left', 'middle', $largura[$cc ++], $corFundo, 0, $zebra);
							}
							itemLinhaTMNOURL(formatarValoresForm($grupoFat[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, $zebra);
							itemLinhaTMNOURL(formatarValoresForm($grupoJur[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, $zebra);
							itemLinhaTMNOURL(formatarValoresForm($grupoDesc[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, $zebra);
							itemLinhaTMNOURL(formatarValoresForm($grupoRec[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, $zebra);
							/////itemLinhaTMNOURL(formatarValoresForm($grupoFat[$nomeGrupo] - $grupoRec[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
							htmlFechaLinha();
						}

						$cc = 0;
						if ($matriz[detalhar]) {
							$matResultado[$matCabecalho[$cc ++]][$l] = '<b>Total do Grupo</b>';
							$matResultado[$matCabecalho[$cc ++]][$l] = "<b>$nomeGrupo</b>";
						} else {
							$periodo = substr($dtVencimento, 5, 2)."/".substr($dtVencimento, 0, 4);
							$matResultado[$matCabecalho[$cc ++]][$l] = $nomeGrupo." - ".$periodo;
						}

						$matResultado[$matCabecalho[$cc ++]][$l] = formatarValoresForm($grupoFat[$nomeGrupo]);
						$matResultado[$matCabecalho[$cc ++]][$l] = formatarValoresForm($grupoJur[$nomeGrupo]);
						$matResultado[$matCabecalho[$cc ++]][$l] = formatarValoresForm($grupoDesc[$nomeGrupo]);
						$matResultado[$matCabecalho[$cc ++]][$l] = formatarValoresForm($grupoRec[$nomeGrupo]);
						/////$matResultado[$matCabecalho[$cc++]][$l]=formatarValoresForm($grupoFat[$nomeGrupo] - $grupoRec[$nomeGrupo]);

						$l ++;

						$tf += $grupoFat["$nomeGrupo"];
						$tj += $grupoJur["$nomeGrupo"];
						$td += $grupoDesc["$nomeGrupo"];
						$tr += $grupoRec["$nomeGrupo"];
						$grupoFat["$nomeGrupo"] = 0;
						$grupoJur["$nomeGrupo"] = 0;
						$grupoDesc["$nomeGrupo"] = 0;
						$grupoRec["$nomeGrupo"] = 0;
						/////}

						// muda de grupo e zera os totais
						//$nomeGrupo=resultadoSQL($consultaPop, $a, 'grupo');
						//						$grupoFat["$nomeGrupo"]=0;
						//						$grupoJur["$nomeGrupo"]=0;
						//						$grupoDesc["$nomeGrupo"]=0;
						//						$grupoRec["$nomeGrupo"]=0;
					}
					# fim do agrupamento

					$dtVencimentoAnterior = $dtVencimento; # define o vencto anterior = o atual para testar no proximo loop
					$grupoAnterior = $nomeGrupo; # define o grupo anterior = o atual para testar no proximo loop
				}

				# --------- totalizacao --------------#
				# Total do POP
				if ($matriz[detalhar])
					$col = 2;
				else
					$col = 0;

				if ($matriz[consulta]) {
					$cc = 0;
					$zebra = "tabfundo0";
					htmlAbreLinha($corFundo);
					itemLinhaTMNOURL('<b>Total do POP</br>', 'right', 'middle', $largura[$cc ++], $corFundo, $col, $zebra);
					itemLinhaTMNOURL(formatarValoresForm($tf), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, 'txtcheck');
					itemLinhaTMNOURL(formatarValoresForm($tj), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, 'txtok');
					itemLinhaTMNOURL(formatarValoresForm($td), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, 'txtok');
					itemLinhaTMNOURL(formatarValoresForm($tr), $matAlinhamento[$cc], 'middle', $largura[$cc ++], $corFundo, 0, 'txtok');
					/////itemLinhaTMNOURL(formatarValoresForm($tf - $tr), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, 'txtaviso');
					htmlFechaLinha();
					fechaTabela();
				}

				$cc = 0;
				if ($matriz[detalhar]) {
					$matResultado[$matCabecalho[$cc ++]][$l] = '<b>Total do POP</b>';
					$matResultado[$matCabecalho[$cc ++]][$l] = "<b>$nomePop</b>";
				} else {
					$matResultado[$matCabecalho[$cc ++]][$l] = "<b>Total do POP $nomePop</b>";
				}
				$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($tf).'</b>';
				$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($tj).'</b>';
				$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($td).'</b>';
				$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($tr).'</b>';
				/////$matResultado[$matCabecalho[$cc++]][$l]='<b>'.formatarValoresForm($tf-$tr).'</b>';
				$l ++;

				$totalGeral[faturado] += $tf;
				$totalGeral[juros] += $tj;
				$totalGeral[desc] += $td;
				$totalGeral[recebido] += $tr;

				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe] = $matResultado;
				#---------- fim totalizacao -----------#				
				$vazio = 0;
			} else {
				$vazio = 1;
			}

			$pp ++;

			if (!$vazio) { // pra que um total geral em cada pagina, e ainda nao funcional.

				//exibe o total no ultimo pop
				if ($pp == count($matriz[pop])) {
					$cc = 0;
					$matResultado[$matCabecalho[$cc ++]][$l] = '<b>Total Geral</b>';
					$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
					$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($totalGeral[juros]).'</b>';
					$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($totalGeral[desc]).'</b>';
					$matResultado[$matCabecalho[$cc ++]][$l] = '<b>'.formatarValoresForm($totalGeral[recebido]).'</b>';
				}
				/////$matResultado[$matCabecalho[$cc++]][$l]='<b>'.formatarValoresForm($totalGeral[faturado]-$totalGeral[recebido]).'</b>';

				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe] = $matResultado;

				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO] = "FATURAMENTO POR GRUPOS DE SERVIÇOS";
				$matrizRelatorio[header][POP] = $nomePop.'<br>'.$periodo;
				$matrizRelatorio[header][IMG_LOGO] = $html[imagem][logoRelatorio];

				# Configurações
				$matrizRelatorio[config][linhas] = 38;
				$matrizRelatorio[config][layout] = 'portrait';
				$matrizRelatorio[config][marginleft] = '1.0cm;';
				$matrizRelatorio[config][marginright] = '1.0cm;';
				$matrizGrupo[] = $matrizRelatorio;
			}

		} // while

		# total geral
		if ($matriz[consulta]) {
			echo "<br>";
			$cc = 0;
//			novaTabela($nomePop." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			htmlAbreLinha($corFundo);
			itemLinhaTMNOURL("<b> Total Geral </b>", "right", "middle", $largura[$cc ++], $corFundo, 0, "tabfundo0");
			itemlinhaTMNOURL(formatarValoresForm($totalGeral[faturado]), $matAlinhamento[$cc], "middle", $largura[$cc ++], $corFundo, 0, "tabfundo0");
			itemlinhaTMNOURL(formatarValoresForm($totalGeral[juros]), $matAlinhamento[$cc], "middle", $largura[$cc ++], $corFundo, 0, "tabfundo0");
			itemlinhaTMNOURL(formatarValoresForm($totalGeral[desc]), $matAlinhamento[$cc], "middle", $largura[$cc ++], $corFundo, 0, "tabfundo0");
			itemlinhaTMNOURL(formatarValoresForm($totalGeral[recebido]), $matAlinhamento[$cc], "middle", $largura[$cc ++], $corFundo, 0, "tabfundo0");
			/////itemlinhaTMNOURL(formatarValoresForm($totalGeral[faturado]-$totalGeral[recebido]), $matAlinhamento[$cc], "middle", $largura[$cc++], $corFundo, 0, "tabfundo0");
			fechaLinhaTabela();
			fechaTabela();

		}
		//else
		if (!$matriz[consulta]) {

			# Converter para PDF:
			if ($matriz[detalhar]) {
				//$nomeArquivo = criaTemplates('faturamento_gruposervicodet', $matCabecalho );
				$arquivo = k_reportHTML2PDF(k_report($matrizGrupo, 'html', 'faturamento_gruposervicodet'), 'faturamento_gruposervicodet', $matrizRelatorio[config]);
			} else
				//$nomeArquivo = criaTemplates('faturamento_gruposervico', $matCabecalho );
				$arquivo = k_reportHTML2PDF(k_report($matrizGrupo, 'html', 'faturamento_gruposervico'), 'faturamento_gruposervico', $matrizRelatorio[config]);

			if ($arquivo) {

				echo "<br>";
				novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corFundo);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Faturamento por Grupo de Serviço</a>", 'pdf'), 'center', $corFundo, 7, 'txtaviso');
				htmlFechaLinha();
				fechaTabela();
			}
		}

		return (0);
	} else {
		echo "<br>";
		$msg = "Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}

	echo "<script>location.href='#ancora';</script>";
}
?>


