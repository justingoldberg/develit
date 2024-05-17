<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 18/08/2003
# Ultima alteração: 14/04/2004
#    Alteração No.: 004
#  Reutilizado por: Rogério Ramos - rogerio@devel.it
#
# Função:
# Funções para relatórios


# função para form de seleção de filtros de faturamento
function formRelatorioFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
		$data=dataSistema();
		# Motrar tabela de busca
		novaTabela2("[Faturamento por Grupos de Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			
			// detalhes
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Detalhar:</b><br><span class=normal10>Exibe os nomes dos clientes</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[detalhar]) $opcDetalhar='checked';
				$texto2="<input type=checkbox name=matriz[detalhar] value=S $opcDetalhar><b>Nome dos clientes</b>";
				itemLinhaForm($texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');		
			fechaLinhaTabela();
			
			//Periodo
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano Inicial:</b><br><span class=normal10>Informe o mês/ano inicial </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtInicial] size=7 value='$matriz[dtInicial]' onBlur=verificaDataMesAno2(this.value,8)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano Final:</b><br><span class=normal10>Informe o mês/ano final </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,9)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			// Somente Titulos vencidos
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Vencidos:</b><br><span class=normal10>Lista somente os titulos vencidos</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[vencido]) $opcVencido='checked';
				$texto2="<input type=checkbox name=matriz[vencido] value=S $opcVencido><b>só vencidos</b>";
				itemLinhaForm($texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');		
			fechaLinhaTabela();
			
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			// Botoes
			getBotoesConsRel();
		
			htmlFechaLinha();
		fechaTabela();
	}
	
}



#
# Função para consultar de Simulação de Faturamento
function simulaFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml;
	
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
		Se forem todos os grupos gera a lista na matriz
		*/
		if($matriz[grupos_todos]) {
			$consultaGrupo=buscaGruposServicos('','','todos', 'id');
			if( $consultaGrupo && contaconsulta($consultaGrupo) ) {
				for($a=0;$a<contaConsulta($consultaGrupo);$a++) {
					$matriz[idGrupos][$a]=resultadoSQL($consultaGrupo, $a, 'id');
				}
			}
		}
		
		/* 
		Faz o in para os grupos
		*/
		$grupoRec=array();
		$grupoFat=array();
		$cg=0;
		
		$sqlGRUPO="AND $tb[ServicosGrupos].idGrupos in (";
		while ($matriz[idGrupos][$cg]) {
			//zera matriz de totais dos grupos
			$grupoRec[$matriz[idGrupos][$cg]]=0;
			$grupoFat[$matriz[idGrupos][$cg]]=0;
			
			if ($cg>0) $sqlGRUPO.=", ";
			$sqlGRUPO.=$matriz[idGrupos][$cg++];
		}
		
		if ($cg<=0) 
			$sqlGRUPO="";
		else 
			$sqlGRUPO.=") ";
		//
		
		//condicao de vencimento
		if ($matriz[vencido]) 
			$sqlVencido=" and $tb[ContasReceber].status='P' ".
						" and $tb[ContasReceber].dtVencimento < now() ";
		else
			$sqlVencido=" and $tb[ContasReceber].status<>'C' ";
		
		// Prepara as variaveis de ajuste
		$pp=0;
		$totalGeral=array();
		$total=array();
		if ($matriz[detalhar]) {
			$largura=array(       '20%',   '50%',     '10%',      '10%',      '10%');
			$matCabecalho=array(  "Grupo", "Cliente", "Faturado", "Recebido", "Saldo");
			$matAlinhamento=array("left",  "left",    "right",    "right",    "right");
		}
		else {
			$largura=array(       '40%',   '20%',      '20%',      '20%');
			$matCabecalho=array(  "Grupo", "Faturado", "Recebido", "Saldo");
			$matAlinhamento=array("left",  "right",    "right",    "right");
		}
		
		#
		#Consulta o bd POP por POP do q foi selecionado
		#		
		while($matriz[pop][$pp]) {	
			
			// nome do pop para exbição
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			
			$sqlPOP=" AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];
			
			$sql="SELECT Pessoas.idPOP pop, 
						$tb[GruposServicos].nome grupo, 
						$tb[GruposServicos].id idGrupo, 
						Pessoas.nome cliente, 
						ContasAReceber.valor valor, 
						ContasAReceber.valorRecebido recebido, 
						ContasAReceber.status status,  
						ContasAReceber.dtVencimento vencto,  
						ServicosGrupos.idServico idServ,
						DocumentosGerados.id idDocumentoGerado
				FROM 	Pessoas, 
						PessoasTipos, 
						DocumentosGerados, 
						ContasAReceber, 
						PlanosDocumentosGerados, 
						ServicosPlanosDocumentosGerados, 
						ServicosPlanos, 
						ServicosGrupos, 
						GruposServicos 
				WHERE 	Pessoas.id = PessoasTipos.idPessoa 
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
						$sqlVencido
			   GROUP BY Pessoas.id, GruposServicos.id 
			   ORDER BY GruposServicos.nome, Pessoas.nome";
					
			#echo "sql: $sql";
			
			$consultaPop=consultaSQL($sql, $conn);
			
			if($consultaPop && contaconsulta($consultaPop)) {
				
				if ($matriz[consulta]) {
					
					# Cabeçalho
					echo "<br>";
					novaTabela($nomePop." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 4);

					$cor='tabfundo0';
					htmlAbreLinha($cor);
						for ($cc=0;$cc<count($matCabecalho);$cc++) 
							itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);					
					htmlFechaLinha();
				}
				
				$matResultado=array();
				
				#para controle da exibicao
				$nomeGrupo="";
				$anterior = '';
				$l=0;
				
				#inicia a varredura e joga 
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					
					# Muda de grupo
					# echo "<br>Grupo [$nomeGrupo] lido ".resultadoSQL($consultaPop, $a, 'grupo');
					
					if ($nomeGrupo!=resultadoSQL($consultaPop, $a, 'grupo')) {
						if ($nomeGrupo) { #se for vazio eh a primeira vez e nao tem total
							/* Total do Grupo */
							if ($matriz[consulta]) {
								$zebra="tabfundo1";
								$cc=0;
								htmlAbreLinha($corFundo);
									if ($matriz[detalhar]) {
										itemLinhaTMNOURL('Total de '.$nomeGrupo, 'right', 'middle',$lagura[$cc++], $corFundo, 2, $zebra);
										$cc++;
									} 
									else {
										itemLinhaTMNOURL($nomeGrupo, 'left', 'middle',$lagura[$cc++], $corFundo, 0, $zebra);
									}
									itemLinhaTMNOURL(formatarValoresForm($grupoFat[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
									itemLinhaTMNOURL(formatarValoresForm($grupoRec[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
									itemLinhaTMNOURL(formatarValoresForm($grupoFat[$nomeGrupo] - $grupoRec[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
								htmlFechaLinha();
							}
							
							$c=0;
							if ($matriz[detalhar]) {
								$matResultado[$matCabecalho[$c++]][$l]='<b>Total do Grupo</b>';
								$matResultado[$matCabecalho[$c++]][$l]="<b>$nomeGrupo</b>";
							} 
							else {
								$matResultado[$matCabecalho[$c++]][$l]=$nomeGrupo;								
							}
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($grupoFat[$nomeGrupo]);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($grupoRec[$nomeGrupo]);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($grupoFat[$nomeGrupo] - $grupoRec[$nomeGrupo]);
			
							$l++;
			
							$tf+=$grupoFat["$nomeGrupo"];
							$tr+=$grupoRec["$nomeGrupo"];
							$grupoFat["$nomeGrupo"]=0;
							$grupoRec["$nomeGrupo"]=0;
						}
						
						// muda de grupo e zera os totais
						$nomeGrupo=resultadoSQL($consultaPop, $a, 'grupo');
						$grupoFat["$nomeGrupo"]=0;
						$grupoRec["$nomeGrupo"]=0;
					}
					
					// totaliza o valor faturado
					$valor=resultadoSQL($consultaPop, $a, 'valor');
					$faturado+=$valor;
					$grupoFat["$nomeGrupo"] += $valor;
					
					// Se status for B soma no recebido
					$status=resultadoSQL($consultaPop, $a, 'status');
					$valorRecebido=resultadoSQL($consultaPop, $a, 'recebido');
					if ($status=='B') { #se for B joga o valor em recebido
						$grupoRec["$nomeGrupo"] += $valorRecebido;
						$recebido+=$valorRecebido;
					}
					$saldo=$valor-$valorRecebido;
					if ($saldo<0) $saldo = 0;
					
					$cc=0;
					$campos[$cc++]="<b>".$nomeGrupo."</b>";
					if ($matriz[detalhar]) $campos[$cc++]=resultadoSQL($consultaPop, $a, 'cliente');
					$campos[$cc++]=formatarValoresForm($valor);
					$campos[$cc++]=formatarValoresForm($valorRecebido);
					$campos[$cc++]=formatarValoresForm($saldo);
					
					
					#verifica se exibe ou nao o nome do grupo
					if($anterior==$campos[0]) $campos[0]="&nbsp;";
					else $anterior=$campos[0];
					
					#exibe a linha detalhe
					if ($matriz[consulta]) {
						if ($matriz[detalhar]) {
							htmlAbreLinha($corFundo);
								for ($cc=0; $cc<count($campos); $cc++) {
									itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $lagura[$cc], $corFundo, 0, "normal9");
								}
							htmlFechaLinha();
						}
					}
					
					# soma na matriz
					for ($cc=0; $cc<count($campos); $cc++) {
						$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
					}
					$l++;
				}
			} else {
				$vazio = 1;
			}
			if (! $vazio) {
				#verifica se nao faltou a totalizaçao do grupo
				if ($grupoFat["$nomeGrupo"]>0) {
					if ($nomeGrupo) {
						/* Total do Grupo */
						if ($matriz[consulta]) {
							$zebra="tabfundo1";
							$cc=0;
							htmlAbreLinha($corFundo);
								if ($matriz[detalhar]) {
									itemLinhaTMNOURL('Total de '.$nomeGrupo, 'right', 'middle',$lagura[$cc++], $corFundo, 2, $zebra);
									$cc++;
								} 
								else {
									itemLinhaTMNOURL($nomeGrupo, 'left', 'middle',$lagura[$cc++], $corFundo, 0, $zebra);
								}
								itemLinhaTMNOURL(formatarValoresForm($grupoFat[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL(formatarValoresForm($grupoRec[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL(formatarValoresForm($grupoFat[$nomeGrupo] - $grupoRec[$nomeGrupo]), $matAlinhamento[$cc], 'middle', $lagura[$cc++], $corFundo, 0, $zebra);
							htmlFechaLinha();
						}
						
						$c=0;
						if ($matriz[detalhar]) {
							$matResultado[$matCabecalho[$c++]][$l]='<b>Total do Grupo</b>';
							$matResultado[$matCabecalho[$c++]][$l]="<b>$nomeGrupo</b>";
							$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($grupoFat[$nomeGrupo])."</b>";
							$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($grupoRec[$nomeGrupo])."</b>";
							$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($grupoFat[$nomeGrupo] - $grupoRec[$nomeGrupo])."</b>";
						} 
						else {
							$matResultado[$matCabecalho[$c++]][$l]=$nomeGrupo;
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($grupoFat[$nomeGrupo]);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($grupoRec[$nomeGrupo]);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($grupoFat[$nomeGrupo] - $grupoRec[$nomeGrupo]);
						}
		
						$l++;
		
						$tf+=$grupoFat["$nomeGrupo"];
						$tr+=$grupoRec["$nomeGrupo"];
						$grupoFat["$nomeGrupo"]=0;
						$grupoRec["$nomeGrupo"]=0;
					}
				}
				
				# Total do POP
				if($matriz[detalhar])
					$col = 2;
				else
					$col = 0;
				
				if ($matriz[consulta]) {
					$zebra="tabfundo0";
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL('<b>Total do POP</br>', 'right', 'middle', '40%', $corFundo, $col, $zebra);
						itemLinhaTMNOURL(formatarValoresForm($tf), 'right', 'middle', '20%', $corFundo, 0, 'txtcheck');
						itemLinhaTMNOURL(formatarValoresForm($tr), 'right', 'middle', '20%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($tf - $tr), 'right', 'middle', '20%', $corFundo, 0, 'txtaviso');
					htmlFechaLinha();
					fechaTabela();
				}
				
				$c=0;
				if ($matriz[detalhar]) {
					$matResultado[$matCabecalho[$c++]][$l]='<b>Total do POP</b>';
					$matResultado[$matCabecalho[$c++]][$l]="<b>$nomePop</b>";
				} else {
					$matResultado[$matCabecalho[$c++]][$l]="<b>Total do POP $nomePop</b>";
				}
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($tf).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($tr).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($tf-$tr).'</b>';
				$l++;
				
				$totalGeral[faturado]+=$tf;
				$totalGeral[recebido]+=$tr;
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
			}
			$pp++;
		
			if(! $vazio) {
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]='<b>Total Geral</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[recebido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[faturado]-$totalGeral[recebido]).'</b>';
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="FATURAMENTO POR GRUPOS DE SERVIÇOS";
				$matrizRelatorio[header][POP]=$nomePop.'<br>'.$periodo;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=38;
				$matrizRelatorio[config][layout]='portrait';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
				
				$matrizGrupo[]=$matrizRelatorio;
			}
			
		} // while
		
		if (! $matriz[consulta]) {
			fechaTabela();
			# Converter para PDF:
			if($matriz[detalhar]) 
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_gruposervicodet'),'faturamento_gruposervicodet',$matrizRelatorio[config]);
			else
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_gruposervico'),'faturamento_gruposervico',$matrizRelatorio[config]);
				
			if ($arquivo) {
				
				echo "<br>";
				novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
					htmlAbreLinha($corfundo);
						itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Faturamento por Grupo de Serviço</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
					htmlFechaLinha();
				fechaTabela();
			}
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