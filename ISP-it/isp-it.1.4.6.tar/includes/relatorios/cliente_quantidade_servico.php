<?
##############################################################
#       Criado por: Alexandre Neto - Devel-it    			 #
#  Data de criação: 26/02/2007                   			 #
# Ultima alteração: 00/00/0000                   			 #
#    Alteração No.: 000                          			 #
#                                                			 #
# Função:                                        			 #
# Funções para relatório de Quantidades/Clientes por Serviço #
##############################################################



# função para form de seleção de filtros de faturamento
function formRelatorioClienteQuantidadeServico($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin, $tb;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[visualizar] && !$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		$data=dataSistema();
		
		# Motrar tabela de busca
		novaTabela2("[Quantidades de Clientes por Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
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
				itemLinhaNOURL($texto, 'left', $corFundo, 3, 'tabfundo1');
			fechaLinhaTabela();
			
			# POP
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>POP:</b><br>
				<span class=normal10>Selecione o POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[pop_todos]) $opcPOP='checked';
				$texto="<input type=checkbox name=matriz[pop_todos] value='S' $opcPOP><b>Todos</b>";
				itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			# Serviços
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Serviço:</b><br>
				<span class=normal10>Selecione o Serviço</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[servico_todos]) $opcServicos='checked';
				$texto = getSelectDados( $matriz[servico], ''/*'servico'*/, 'matriz[servico]', /*'multi'*/'formnochange', $tb[Servicos], 'nome', '').
				"<input type=checkbox name=matriz[servico_todos] value='S' $opcServicos><b>Todos</b>";
				itemLinhaForm( $texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			# Status do Serviços
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b><br>
				<span class=normal10>Selecione o Status do Serviço</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[status_todos]) $opcStatus='checked';
				$texto = getSelectDados( ( $matriz[status]) ? $matriz[status] : 4, '', 'matriz[status]', 'form', $tb[StatusServicos], 'descricao', '') .
				"<input type=checkbox name=matriz[status_todos] value='S' $opcStatus><b>Todos</b>";
				itemLinhaForm( $texto, 'left', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			# Periodos
			$data=dataSistema();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data '.$complemento.' Inicial:</b><br><span class=normal10>Informe a data inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtInicial] size=10 value='$matriz[dtInicial]' onBlur=verificaData(this.value,10)>&nbsp;<span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
			    itemLinhaForm($texto, 'left', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data '.$complemento.' Final:</b><br><span class=normal10>Informe a data final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtFinal] size=10 value='$matriz[dtFinal]'  onBlur=verificaData(this.value,11)>&nbsp;<span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
			    itemLinhaForm($texto, 'left', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			# Botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
				$texto.="&nbsp;<input type=submit name=matriz[bntRelatorio] value='Gerar Relatório' class=submit2>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 3, 'tabfundo1');
			fechaLinhaTabela();
			
			htmlFechaLinha();
		fechaTabela();
	}
	
}

#
# faz a consulta e o relatorio
#
function relatorioClienteQuantidadeServico($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml, $sessLogin, $conn, $tb;
	
	
	if ( ( $matriz[pop] ) || ( $matriz[pop_todos] ) ) {
		
		# Se forem todos os pops gera a lista na matriz
		if($matriz[pop_todos]) {
			$consultaPop=buscaPOP("status='A'",'','custom', 'id');
			if( $consultaPop && $fechaTag = contaconsulta($consultaPop) ) {
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					$matriz[pop][$a]=resultadoSQL($consultaPop, $a, 'id');
				}
			}
		}
		
		# Se forem todos os servicos gera a lista na matriz
		if($matriz[servico_todos]) {
			$consultaServico=buscaServicos( '', '', 'todos', 'nome' );
			if( $consultaServico && contaconsulta($consultaServico) ) {
				for($a=0;$a<contaConsulta( $consultaServico ); $a++) {
					$servico[$a]=resultadoSQL($consultaServico, $a, 'id');
				}
				$sqlServico = " AND $tb[ServicosPlanos].idServico in (".implode( ",", $servico ).")  ";
			}
		}
		else{
			$sqlServico = " AND $tb[ServicosPlanos].idServico = " . $matriz[servico];
		}
		
		# Se forem todos os status de serviços gera a lista na matriz
		if($matriz[status_todos]) {
			$consultaStatus=buscaStatusServicos( '', '', 'todos', 'id');
			if( $consultaStatus && contaconsulta($consultaStatus) ) {
				for($a=0;$a<contaConsulta($consultaStatus);$a++) {
					$status[$a]=resultadoSQL($consultaStatus, $a, 'id');
				}
				$sqlStatus = " AND ServicosPlanos.idStatus in (".implode( ",", $status ).")  ";
			}
		}
		else{
			$sqlStatus = " AND ServicosPlanos.idStatus=".$matriz[status];
		}
		
		# Formatar Datas
		if ($matriz[dtInicial]) {
			$data=formatarData($matriz[dtInicial]);
			$dtInicial=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 00:00:00';
		}
	
		if ($matriz[dtFinal]) {
			$data=formatarData($matriz[dtFinal]);
			$dtFinal=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 23:59:59';
		}
	
		# Ajusta o sql para determinar o periodo escolhido
		if (!$matriz[tipoData]) $tipoData='dtCadastro';
		else $tipoData=$matriz[tipoData];
				
		$sqlDT="";
		if($matriz[dtInicial] && $matriz[dtFinal]) {
			$sqlDT=" AND $tb[ServicosPlanos].$tipoData between '$dtInicial' and '$dtFinal' ";
			$periodo="de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
		} 
		elseif ($matriz[dtInicial]) {
			$sqlDT=" AND $tb[ServicosPlanos].$tipoData >= '$dtInicial' ";
			$periodo="a partir de ".$matriz[dtInicial];
		} 
		elseif ($matriz[dtFinal])  {
			$sqlDT=" AND $tb[ServicosPlanos].$tipoData <= '$dtFinal' ";
			$periodo="até ".$matriz[dtFinal];
		}
				
		# Prepara as variáveis de ajuste
		$pp=0;
		$totalGeral=array();
		$total=array();
		
		$matLargura = array(    '40%',		'20%',				'30%',      '20%');
		$matCabecalho = array(  'Cliente',	'Data de Cadastro',	'Serviço',	'Valor');
		$matAlinhamento = array('left',		'center',			'left',		'right');
		$numCol=count($matCabecalho);
			
		$ttGeral= 0;
		$l=0;
		
		echo "<br>";
		while($matriz[pop][$pp]) {
			# nome do pop para exbição
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			$sqlPOP="$tb[POP].id = ".$matriz[pop][$pp];
//			# nome do serviço
//			$nomeServico = resultadoSQL( buscaServicos($servico[$pp], 'id', 'igual', 'nome'), 0, 'nome');
//			$sqlServico="$tb[Servicos].id = ".$servico[$pp];

			$sql="
				SELECT 
 					Servicos.nome servico, 
					Pessoas.nome cliente, 
					ServicosPlanos.dtCadastro, 
					Servicos.valor as valor, 
					ServicosPlanos.valor as valorEspecial, 
					PlanosPessoas.especial, 
					DescontosServicosPlanos.valor 

				FROM 
					Servicos INNER JOIN ServicosPlanos ON ( Servicos.id = ServicosPlanos.idServico ) 
					INNER JOIN StatusServicosPlanos ON ( ServicosPlanos.idStatus = StatusServicosPlanos.id ) 
					INNER JOIN PlanosPessoas ON ( ServicosPlanos.idPlano = PlanosPessoas.id ) 
					INNER JOIN PessoasTipos ON ( PlanosPessoas.idPessoaTipo = PessoasTipos.id ) 
					INNER JOIN Pessoas ON ( PessoasTipos.idPessoa = Pessoas.id ) 
					INNER JOIN Pop ON ( Pessoas.idPOP = Pop.id ) 
					LEFT JOIN DescontosServicosPlanos ON ( ServicosPlanos.id = DescontosServicosPlanos.idServicoPlano) 
				WHERE 
					$sqlPOP 
					$sqlServico 
					$sqlStatus 
					$sqlDT
				GROUP BY 
					Servicos.id, 
					Pessoas.id 
				ORDER BY 
					Servicos.Nome, 
					Pessoas.nome
			";
					
			$consulta=consultaSQL($sql, $conn);
//echo "SQL: $sql<BR>";
			$ttParcial = contaConsulta( $consulta );
			$ttGeral = $ttGeral + $ttParcial;
			if( $consulta && contaconsulta($consulta) ) {
				
				$matResultado=array();
				$l= 0;
				
				# se for consulta exibe o cabecalho
				if ($matriz[bntConfirmar]) {
					# Cabeçalho
					//echo "<br>";
					novaTabela($nomePop,"left", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
					$cor='tabfundo0';
					htmlAbreLinha($cor);
						for ($cc=0;$cc<count($matCabecalho);$cc++) {
							itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $matLargura[$cc], $corFundo, 0, $cor);
						}
					htmlFechaLinha();
				}
				else{
						$i=0;
						$matResultado[$matCabecalho[$i++]][$l]="<b>".$nomePop."</b>";
						$matResultado[$matCabecalho[$i++]][$l]="&nbsp;";
						$matResultado[$matCabecalho[$i++]][$l]="&nbsp;";
						$matResultado[$matCabecalho[$i++]][$l]="&nbsp;";
						$l++;
				}
				
				#inicia a varredura e joga 
				for($a=0;$a<contaConsulta($consulta);$a++) {
					
					$cc=0;
					$campos[$cc++]=resultadoSQL($consulta, $a, 'cliente');
					$campos[$cc++]=converteData( resultadoSQL($consulta, $a , 'dtCadastro'), 'banco', 'formdata' );
					$campos[$cc++]=resultadoSQL($consulta, $a, 'servico');
					$campos[$cc++]=formatarValoresForm(resultadoSQL($consulta, $a , 'especial') == 'S' ? resultadoSQL($consulta, $a , 'valorEspecial') : resultadoSQL($consulta, $a , 'valor') );
					
					# se for consulta exibe a linha detalhe
					if ($matriz[bntConfirmar]) {
						
						# detalhes
						htmlAbreLinha($corFundo);
							for ($cc=0; $cc<count($campos); $cc++) {
								itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, "normal9");
							}
						htmlFechaLinha();
						
					}
					else{
						# detalhe
						for ($cc=0; $cc<count($campos); $cc++) {
							$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
						}
						$l++;						
						
					}
				} # fim do for
				
				# se for consulta exibe o total
				if ($matriz[bntConfirmar]) {
					$zebra="tabfundo0";
					$cc=0;	
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL('<b>Total de Clientes</br>', 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, $zebra);
						$cc++;
						itemLinhaTMNOURL( $ttParcial, 'center', 'middle', $matAlinhamento[$cc++], $corFundo, 3, 'txtcheck');
					htmlFechaLinha();
					fechaTabela();

				}
				else{
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]="<b>Total de Clientes</b>";
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$matResultado[$matCabecalho[$c++]][$l]=$ttParcial;
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					
					$l++;
					
				}

			} # fim do if
			else {
				$vazio = 1;
			}
			if (! $vazio) {
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="QUANTIDADES DE CLIENTES POR SERVIÇOS";
				$matrizRelatorio[header][POP]=$nomePop;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=35; //25
				$matrizRelatorio[config][layout]='portrait';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
			
				$matrizGrupo[]=$matrizRelatorio;
			}
			$pp++;

		} # while
		
		# Rodapé
		if ( $matriz[bntConfirmar] ){
			novaTabela( "&nbsp;", "left", '100%', 0, 2, 0, $corFundo, $corBorda, 2);
				$cor='tabfundo0';
				htmlAbreLinha($corFundo);
						itemLinhaTMNOURL( _("Total Geral"), 'center', 'middle', '50%', $corFundo, 0, $cor );
						itemLinhaTMNOURL( $ttGeral, 'center', 'center', '50%', $corFundo, 0, $cor);
				htmlFechaLinha();
			fechaTabela();
//			if ( $matriz[pop_todos] ){
				//fechaTabela();
//			}
		}
		else{
			$c=0;
			$matResultado[$matCabecalho[$c++]][$l]="<b>Total Geral</b>";
			$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
			$matResultado[$matCabecalho[$c++]][$l]=$ttGeral;
			$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
			$l++;
			
			# Alimentar Matriz Geral
			$matrizRelatorio[detalhe]=$matResultado;
			
			# Alimentar Matriz de Header
			$matrizRelatorio[header][TITULO]="QUANTIDADES DE CLIENTES POR SERVIÇOS";
			$matrizRelatorio[header][POP]=$nomePop;
			$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
			
			# Configurações
			$matrizRelatorio[config][linhas]=35; //25
			$matrizRelatorio[config][layout]='portrait';
			$matrizRelatorio[config][marginleft]='1.0cm;';
			$matrizRelatorio[config][marginright]='1.0cm;';
			
			$matrizGrupo[]=$matrizRelatorio;
			
			#Se for escolhido Consulta nao gera o pdf
			if (! $matriz[bntConfirmar]) {
				# Converter para PDF:
				$nome= "cliente_quantidade_servico";
//				criaTemplates($nome,$matCabecalho,$matAlinhamento);
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html',$nome),$nome,$matrizRelatorio[config]);
				if ($arquivo) {
					echo "<br>";
					novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
						htmlAbreLinha($corfundo);
							itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Quantidades de Clientes por Serviço</a>",'pdf'), 'center', $corFundo, 1, 'txtaviso');
						htmlFechaLinha();
					fechaTabela();
				}
			}
		}
		return(0);
	}
	else {
		echo "<br>";
		$msg="Você esqueceu de selecionar o POP.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}
	
	echo "<script>location.href='#ancora';</script>";
}

?>