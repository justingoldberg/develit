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
function formRelatorioInadimplenteGrupoServico($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		$data=dataSistema();
		# Motrar tabela de busca
		novaTabela2("[Inadimplentes por Grupos de Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
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
				if($matriz[grupos_todos]) $opcServico='checked';
				$texto2="<input type=checkbox name=matriz[grupos_todos] value=S $opcServico><b>Todos</b>";
				itemLinhaForm(formSelectGruposServicos($matriz[grupos],'idGrupos','multi').$texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');		
			fechaLinhaTabela();
			
			//Periodo
			getPeriodo(8, 9, $matriz);
			/*
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mes/Ano Inicial:</b><br><span class=normal10>Informe o mes/ano inicial </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtInicial] size=7 value='$matriz[dtInicial]' onBlur=verificaDataMesAno2(this.value,7)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mes/Ano Final:</b><br><span class=normal10>Informe o mes/ano final </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,8)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			*/
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
				//$texto.="&nbsp;<input type=submit name=matriz[bntRelatorio] value='Gerar Relatório' class=submit2>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		
			htmlFechaLinha();
		fechaTabela();
	}
	
}



#
# faz a consulta e o relatorio
#
function relatorioInadimplenteGrupoServico($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml, $sessLogin, $conn, $tb;
	
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
			$matriz[dtInicial]=formatarData($matriz[dtInicial]);
			if ($matriz[diaDe]) $dia=$matriz[diaDe];
			else $dia='01';
			$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/'.$dia.' 00:00:00';
			$matriz[dtInicial]=substr($matriz[dtInicial],0,2)."/".substr($matriz[dtInicial],2,4);
		}
	
		if ($matriz[dtFinal]) {
			$matriz[dtFinal]=formatarData($matriz[dtFinal]);
			if ($matriz[diaAte]) $dia=$matriz[diaAte];
			else $dia=dataDiasMes(substr($matriz[dtFinal],0,2));
			$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.$dia.' 23:59:59';
			$matriz[dtFinal]=substr($matriz[dtFinal],0,2)."/".substr($matriz[dtFinal],2,4);
		}
	
		// Ajusta o sql para determinar o periodo escolhido
		$sqlDT="";
		if($matriz[dtInicial] && $matriz[dtFinal]) {
			$sqlDT=" AND $tb[ContasReceber].dtVencimento between '$dtInicial' and '$dtFinal' ";
			$periodo="de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
		} 
		elseif ($matriz[dtInicial]) {
			$sqlDT=" AND $tb[ContasReceber].dtVencimento >= '$dtInicial' ";
			$periodo="a partir de ".$matriz[dtInicial];
		} 
		elseif ($matriz[dtFinal])  {
			$sqlDT=" AND $tb[ContasReceber].dtVencimento <= '$dtFinal' ";
			$periodo="até ".$matriz[dtFinal];
		}
				
		// Se forem todos os pops gera a lista na matriz
		if($matriz[pop_todos]) {
			$consultaPop=buscaPOP('','','todos', 'id');
			if( $consultaPop && contaconsulta($consultaPop) ) {
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					$matriz[pop][$a]=resultadoSQL($consultaPop, $a, 'id');
				}
			}
		}
		
		
		
		/* 
		Faz o in para os grupos
		*/
		$grupoRec=array();
		$grupoFat=array();
		
		$sqlGRUPO=getSQLGrupoServicos($matriz);
		
		
		/* 
		Seleciona somente os não pagos 
		*/
		$sqlVencido=" and $tb[ContasReceber].status='P' ".
					" and $tb[ContasReceber].dtVencimento < now() ";
		
		// Prepara as variaveis de ajuste
		$pp=0;
		$totalGeral=array();
		$total=array();
		
		$largura=array(       '15%',   '40%',     '10%',    '15%',   "20%");
		$matCabecalho=array(  "Grupo", "Cliente", "Vencto", "Valor", "Opções");
		$matAlinhamento=array("left",  "left",    "left",   "right", "left");
		
		while($matriz[pop][$pp]) {
			
			/* 
			Cria uma tabela temporia para receber os dados da consulta 
			*/
			$nomeTmp="tmbGrupoServico" . validaNomeUsuario( $sessLogin['login'] );
			$criaTabela="DROP TEMPORARY TABLE IF EXISTS $nomeTmp ";
			consultaSQL($criaTabela, $conn);
			$criaTabela="CREATE TEMPORARY TABLE $nomeTmp (id BIGINT(20) NOT NULL AUTO_INCREMENT,
														  grupo BIGINT(20),
														  idCliente BIGINT(20), 
														  cliente VARCHAR(255),
														  valor DOUBLE,
														  recebido DOUBLE,
														  vencimento DATETIME,
														  PRIMARY KEY(id)
											 			 )";
			
			//echo "sql: $criaTabela";
			consultaSQL($criaTabela, $conn);
			
			
			// nome do pop para exbição
			$nome=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			
			/* 	1a. Consulta
				Consultando todas as cobranças do POP, 1 a 1
				nome do pop
				valor do doc gerado
				status do doc (B = baixado)
				
				$tb[DocumentosGerados].idPessoaTipo idCliente, 
			*/
			$sqlPOP=" AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];
			
			$sql="SELECT 	$tb[Pessoas].nome cliente, 
							$tb[DocumentosGerados].id idDocumentoGerado, 
							$tb[ContasReceber].id idContasAReceber, 
						    $tb[DocumentosGerados].idPessoaTipo idCliente, 
							$tb[ContasReceber].valor, 
							$tb[ContasReceber].valorRecebido, 
							$tb[ContasReceber].status, 
							$tb[ContasReceber].dtVencimento vencimento  
					FROM 	$tb[Pessoas], 
							$tb[PessoasTipos], 
							$tb[DocumentosGerados], 
							$tb[ContasReceber] 
					WHERE 	$tb[Pessoas].id = $tb[PessoasTipos].idPessoa AND 
							$tb[PessoasTipos].id = $tb[DocumentosGerados].idPessoaTipo AND 
							$tb[DocumentosGerados].id = $tb[ContasReceber].idDocumentosGerados 
							$sqlPOP 
							$sqlDT 
							$sqlVencido
					ORDER BY $tb[Pessoas].id";
			
			//echo "sql: $sql";
			
			$consultaPop=consultaSQL($sql, $conn);
			
			if( $consultaPop && contaconsulta($consultaPop) ) {
				
				# Cabeçalho
				echo "<br>";
				novaTabela2($nome." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
				
				$cor='tabfundo0';
				htmlAbreLinha($cor);
					for ($cc=0;$cc<count($matCabecalho);$cc++) 
						itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);					
				htmlFechaLinha();
				
				$matResultado=array();
				
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					// 	2a. Consulta
					//	Consultando idDocumentoGerado e fazendo total
					//	Recebe o idDocumentoGerado e a lista de Grupos
					//  Coloca os grupos em uma matriz para totalizacao
					
					// soma o valor da conta
					$faturado+=resultadoSQL($consultaPop, $a, 'valor');
					
					// pega o iddocgerado pra procurar e validar o servico
					$docGerado=resultadoSQL($consultaPop, $a, 'idDocumentoGerado');
					$nomeCliente=resultadoSQL($consultaPop, $a, 'cliente');
					$vencimento=resultadoSQL($consultaPop, $a, 'vencimento');
					$idCliente=resultadoSQL($consultaPop, $a, 'idCliente');
					
					$sql2="SELECT   $tb[Servicos].nome, 
									$tb[PlanosDocumentosGerados].idDocumentoGerado, 
									$tb[PlanosDocumentosGerados].idDocumentoGerado as vencimento, 
									$tb[ServicosGrupos].idGrupos as grupo, 
									sum($tb[ServicosPlanosDocumentosGerados].valor) valor 
							FROM 	$tb[Servicos], 
									$tb[ServicosPlanos], 
									$tb[PlanosDocumentosGerados], 
									$tb[ServicosPlanosDocumentosGerados], 
									$tb[ServicosGrupos] 
							WHERE 	$tb[Servicos].id = $tb[ServicosPlanos].idServico AND 
									$tb[ServicosPlanos].id = $tb[ServicosPlanosDocumentosGerados].idServicosPlanos AND 
									$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado  = $tb[PlanosDocumentosGerados].id AND 
									$tb[ServicosPlanos].idServico = $tb[ServicosGrupos].idServico AND 
									$tb[PlanosDocumentosGerados].idDocumentoGerado=$docGerado 
									$sqlGRUPO 
							GROUP BY $tb[ServicosGrupos].idGrupos";
					
					$consultaServ=consultaSQL($sql2, $conn);
					
					if($consultaServ && contaconsulta($consultaServ)) {
						// pode ser que o valor contenha varios servicos
						for($g=0;$g<contaConsulta($consultaServ);$g++) {
							//pego o id do grupo
							$grp=resultadoSQL($consultaServ, $g, 'grupo');
							$ff=resultadoSQL($consultaServ, $g, 'valor');
	
							$grupoFat["$grp"] += $ff;
							
							consultaSQL("INSERT INTO $nomeTmp 
											SET grupo=$grp, 
												cliente='$nomeCliente',
												idCliente=$idCliente, 
												valor=$ff,
												vencimento='$vencimento'
							
										",
								   	    $conn);
						}
					}
					
				} //for da consulta dos docs
				
				$tf=0;
				$tr=0;
				$l=0;
				
				$keys=array_keys($grupoFat);
				for($a=0;$a<count($keys);$a++) {
					
					$sqlGrupos=buscaGruposServicos($keys[$a], 'id', 'igual', 'id');
					$nomeGrupo=resultadoSQL($sqlGrupos, 0, 'nome');
					$ttGrupo=0;
					
					$sql="SELECT 	GruposServicos.nome as grupo, 
			 						$nomeTmp.cliente as cliente, 
									$nomeTmp.idCliente as idCliente, 
									$nomeTmp.vencimento as vencimento, 
			 						sum($nomeTmp.valor) as faturado
						  FROM		GruposServicos, 
			 						$nomeTmp 
						  WHERE		GruposServicos.id=$nomeTmp.grupo 
									and GruposServicos.id=$keys[$a]
						  GROUP BY  $nomeTmp.grupo, 
			 						$nomeTmp.cliente, 
									$nomeTmp.vencimento 
						  ORDER BY  GruposServicos.nome, 
			 						$nomeTmp.cliente";
					
					$consultaCliente=consultaSQL($sql, $conn);
					
					$ttreg=contaConsulta($consultaCliente);
					$zebra="normal9";
					if ($consultaCliente && $ttreg>0) {
						$cliAn='';
						$anterior = '';
						$ttCli='0';
						
						for ($nm=0;$nm<$ttreg;$nm++) {
							
							$opcoes="&nbsp;";
							
							$cc=0;
							$campos[$cc++]=resultadoSQL($consultaCliente, $nm, 'grupo');
							$campos[$cc++]=resultadoSQL($consultaCliente, $nm, 'cliente');
							$campos[$cc++]=resultadoSQL($consultaCliente, $nm, 'vencimento');
							$campos[$cc++]=resultadoSQL($consultaCliente, $nm, 'faturado');
							
							if($anterior==$campos[0]) $campos[0]="&nbsp;";
							else $anterior=$campos[0];
								
							if($cliAnt==$campos[1]) {
								$campos[1]="&nbsp;";
								$opcoes="&nbsp;";
							}
							else {
								if($nm > 0) {
									$cor="tabfundo1";
									htmlAbreLinha($corFundo);
										itemLinhaTMNOURL("<b>A Receber</b>", "right", 'middle', '10%', $corFundo, 3, $cor);
										itemLinhaTMNOURL("<b>".formatarValoresForm($ttCli)."</b>", 'right', 'middle', $lagura[4], $corFundo, 0, $cor);
										itemLinhaTMNOURL("&nbsp;", 'right', 'middle', $lagura[5], $corFundo, 0, $cor);
									htmlFechaLinha();
									
									$c=0;
									$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
									$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
									$matResultado[$matCabecalho[$c++]][$l]="A Receber";
									$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($ttCli)."</b>";
									$l++;
									
									$ttGrupo+=$ttCli;
									$ttCli=0;
								}
								
								// pula linha
								htmlAbreLinha($corFundo);
									itemLinhaTMNOURL("&nbsp;", "right", 'middle', '10%', $corFundo, 5, 'normal10');
								htmlFechaLinha();
								
								$dados=dadosPessoasTipos(resultadoSQL($consultaCliente, $nm, 'idCliente'));
								$cliAnt=$campos[1];
								$campos[1].="<br>".$dados[endereco][telefones];
								
								#opcoes
								$idCli=resultadoSQL($consultaCliente, $nm, 'idCliente');
								$def="<a href=?modulo=ocorrencias&sub=&registro=".$idCli;
								$fnt="<font size='2'>";
								
								$opcoes=htmlMontaOpcao($def." target='_blank'>".$fnt."Ocorrencia</font></a>",'ocorrencia');
								
							}
							
							$campos[$cc++]=$opcoes;
							
							$ttCli+=$campos[3];
							$cc=0;
							
							htmlAbreLinha($corFundo);
								itemLinhaTMNOURL("<b>".$campos[$cc]."</b>", $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
								$dt=substr($campos[$cc], 8,2)."/".substr($campos[$cc], 5,2)."/".substr($campos[$cc], 0,4);
								itemLinhaTMNOURL($dt, $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL(formatarValoresForm($campos[$cc]), $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
							htmlFechaLinha();
							
							$cc=0;
							$matResultado[$matCabecalho[$cc]][$l]="<b>".$campos[$cc++]."</b>";
							$matResultado[$matCabecalho[$cc]][$l]=" " . $campos[$cc++];
							$matResultado[$matCabecalho[$cc]][$l]=" " . $campos[$cc++];
							$matResultado[$matCabecalho[$cc]][$l]=formatarValoresForm($campos[$cc++]);
							$l++;
						}
					}
					
					/* verifica se a ultima linha tem total */
					if($ttCli>0) {
						$cor="tabfundo1";
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL("<b>A Receber</b>", "right", 'middle', '10%', $corFundo, 3, $cor);
							itemLinhaTMNOURL("<b>".formatarValoresForm($ttCli)."</b>", 'right', 'middle', $lagura[4], $corFundo, 0, $cor);
							itemLinhaTMNOURL("&nbsp;", 'right', 'middle', $lagura[5], $corFundo, 0, $cor);
						htmlFechaLinha();
						
						$c=0;
						$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
						$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
						$matResultado[$matCabecalho[$c++]][$l]="A Receber";
						$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($ttCli)."</b>";
						$l++;
						
						$ttGrupo+=$ttCli;
						$ttCli=0;
					}
					
					/* Total do Grupo */
					// pula linha
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL("&nbsp;", "right", 'middle', '10%', $corFundo, 5, 'normal10');
					htmlFechaLinha();
					
					$zebra="tabfundo2";
					$cc=0;
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL("Total de ".$nomeGrupo, 'right', 'middle',$lagura[$cc++], $corFundo, 3, $zebra);
						$cc++;
						itemLinhaTMNOURL("<b>".formatarValoresForm($ttGrupo)."</b>", 'right', 'middle', $lagura[4], $corFundo, 0, $zebra);
						itemLinhaTMNOURL("&nbsp;", 'right', 'middle', $lagura[5], $corFundo, 0, $zebra);
					htmlFechaLinha();
					
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]="<b>Total do Grupo</b>";
					$matResultado[$matCabecalho[$c++]][$l]="<b>$nomeGrupo</b>";
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($ttGrupo)."</b>";
				
					$l++;
	
					$tf+=$ttGrupo;
					$grupoFat[$keys[$a]]=0;
				}
				
				$col = 3;
					
				$zebra="tabfundo0";
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL('<b>Total do POP</br>', 'right', 'middle', '40%', $corFundo, $col, $zebra);
					itemLinhaTMNOURL(formatarValoresForm($tf), 'right', 'middle', '20%', $corFundo, 0, 'txtcheck');
					itemLinhaTMNOURL("&nbsp;", 'right', 'middle', $lagura[5], $corFundo, 0, $zebra);
				htmlFechaLinha();
				fechaTabela();
				
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]='<b>Total do POP</b>';
				$matResultado[$matCabecalho[$c++]][$l]="<b>$nome</b>";
				$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($tf).'</b>';

				$l++;
				
				$totalGeral[faturado]+=$tf;
				$totalGeral[recebido]+=$tr;
				$allPOP+=$tf;
				
			} // if sql1
			$pp++;
			
			if (! $matriz[pop][$pp]) {
				# Alimentar Array de Detalhe com mais um campo - totais se for o ultimo
				novaTabela2SH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL('<b>Total GERAL: '.formatarValoresForm($totalGeral[faturado]).'</br>', 'center', 'middle', '100%', $corFundo, $col, "txtAviso");
					htmlFechaLinha();
				fechaTabela();
				
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]='<b>Total Geral</b>';
				$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
				$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
			}
			
			//para nao exibir erro caso o pop nao esteje com nenhum faturamento pendente nos grupos
			if (count($matResultado)>0){
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="INADIMPLENTES POR GRUPOS DE SERVIÇOS";
				$matrizRelatorio[header][POP]=$nome.'<br>'.$periodo;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
					$matrizRelatorio[config][linhas]=32;
					$matrizRelatorio[config][layout]='portrait';
					$matrizRelatorio[config][marginleft]='1.0cm;';
					$matrizRelatorio[config][marginright]='1.0cm;';
				
				$matrizGrupo[]=$matrizRelatorio;
			} 
		} // while
		
		
		# Converter para PDF:
	
			$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','inadimplentes'),'inadimplentes',$matrizRelatorio[config]);
			
		if ($arquivo) {
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Faturamento por Grupo de Serviço</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
				htmlFechaLinha();
			fechaTabela();
		}

		return(0);
		
	} else {
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}	
	
	echo "<script>location.href='#ancora';</script>";
}

?>

