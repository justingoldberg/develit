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
function formRelatorioSimulacaoFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar] ) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
		$data=dataSistema();
		# Motrar tabela de busca
		novaTabela2("[Simulação de Faturamento por Grupos de Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			
			getDetalharCliente($matriz);
						
			//Periodo
			getPeriodo(9, 10, $matriz);
			
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			// Botoes
			getBotoesConsRel();
		
			htmlFechaLinha();
		fechaTabela();
	}
	
}



#
# Função para consulta de Simulação de Faturamento
function consultaSimulacaoFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml;
	$vazio= 0;
	
	if ($matriz[pop] || $matriz[pop_todos]) {
	
		# Faz o between das datas
		$dtInicial = substr( $matriz['dtInicial'], 3, 4 )."-".substr( $matriz['dtInicial'], 0, 2)."-01";
		$dtFinal = substr( $matriz['dtFinal'], 3, 4 )."-".substr( $matriz['dtFinal'], 0, 2)."-".dataDiasMes(substr($matriz[dtFinal],0,2));

		$sqlDT = " AND ContasAReceber.dtVencimento BETWEEN '".$dtInicial." 00:00:00' AND '".$dtFinal." 23:59:59' ";
		
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
	
		
		// Se forem todos os pops gera a lista na matriz
		if($matriz[pop_todos]) {
			$consultaPop=buscaPOP('','','todos', 'id');
			if( $consultaPop && contaconsulta($consultaPop) ) {
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					$matriz[pop][$a]=resultadoSQL($consultaPop, $a, 'id');
				}
			}
		}
		
		
		//Se forem todos os grupos gera a lista na matriz
		if($matriz[grupos_todos] || count ($matriz[idGrupos]) == 0) {
			$consultaGrupo=buscaGruposServicos('','','todos', 'id');
			if( $consultaGrupo && contaconsulta($consultaGrupo) ) {
				for($a=0;$a<contaConsulta($consultaGrupo);$a++) {
					$matriz[idGrupos][$a]=resultadoSQL($consultaGrupo, $a, 'id');
				}
			}
		}
		
		 
		#Faz o in para os grupos
		$sqlGRUPO=" AND $tb[ServicosGrupos].idGrupos in (".implode(",",$matriz[idGrupos]).") ";
		
		$pp=0;
		$totalGeral=array();
		$total=array();
		if ($matriz[detalhar]) {
			$largura=array(       '30', '30'  , '8%',     '8%',      '8%',      '8%',     '8%');
			$matCabecalho=array(  "Grupo", "Cliente", "Referência", "Valor", "Adicional", "Desconto", "Total");
			$matAlinhamento=array("left",  "left", "center",    "right",    "right",    "right",  "right");			
		}
		else {
			$largura=array(       '40%',  '10%', '15%',      '10%',      '10%',     '15%');
			$matCabecalho=array(  "Grupo", "Referência", "Valor", "Adicionais", "Desconto", "Total");
			$matAlinhamento=array("left",  "right", "right",    "right",    "right",  "right");			
		}
		
		
		//zera as variaveis que serao usados para relatorio
		$matResultado=array();
		$matrizRelatorio= array();
		$periodoDatas = preparaPeriodo( $dtInicial, $dtFinal );
		
		// consulta pop a pop, grupo a grupo os servicos calculando os totais e exibindo-os
		while($matriz[pop][$pp]) {
			$l=0; //nova pagina do rel, comecando da linha 0 ;)
			
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			
			if ($matriz[bntConfirmar]) {
				# Cabeçalho
				echo "<br>";
				novaTabela($nomePop." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
				$cor='tabfundo0';
				htmlAbreLinha($cor);
				for ($cc=0;$cc<count($matCabecalho);$cc++) 
					itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);					
				htmlFechaLinha();
			}
			
			$popVal[$pp] = 0;
			$popAdic[$pp] = 0;
			$popDesc[$pp] = 0;
			
			foreach($matriz[idGrupos] as $grupo){
				$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
				
				$sqlPOP=" AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];
				
				$sqlGRUPO = " AND $tb[ServicosGrupos].idGrupos = " . $grupo;
				
				$sql="SELECT $tb[Pessoas].nome cliente,
					$tb[GruposServicos].nome grupo, 
					$tb[PlanosPessoas].status statusPlanosPessoas, 
				  	$tb[PlanosPessoas].especial especial, 
				  	$tb[Vencimentos].id idVencimento, 
				  	$tb[ServicosPlanos].id idServicosPlanos, 
					$tb[ServicosPlanos].dtCadastro dtCadastroServicosPlanos, 
					$tb[ServicosPlanos].dtAtivacao dtAtivacaoServicosPlanos, 
					$tb[ServicosPlanos].diasTrial diasTrial, 
					Sum($tb[ServicosPlanos].valor) valorServicosPlanos, 
				  	$tb[StatusServicos].cobranca cobranca, 
				  	Sum($tb[Servicos].valor) valorServicos, 
				  	$tb[Servicos].nome nomeServico,  
				  	$tb[TipoCobranca].proporcional proporcional, 
					$tb[TipoCobranca].forma formaCobranca,
					$tb[TipoCobranca].tipo tipoCobranca
				  FROM $tb[Pessoas] INNER JOIN 
					$tb[PessoasTipos]            ON ( $tb[PessoasTipos].idPessoa = $tb[Pessoas].id )                         INNER JOIN 
					$tb[PlanosPessoas]           ON ( $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id )               INNER JOIN 
					$tb[Vencimentos]             ON ( $tb[Vencimentos].id= $tb[PlanosPessoas].idVencimento )                 INNER JOIN 
					$tb[ServicosPlanos]          ON ( $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id)                   INNER JOIN 
					$tb[StatusServicos]          ON ( $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus)           INNER JOIN 
					$tb[Servicos]                ON ( $tb[Servicos].id = $tb[ServicosPlanos].idServico)                      INNER JOIN 
					$tb[ServicosGrupos]          ON ( $tb[Servicos].id = $tb[ServicosGrupos].idServico )                     INNER JOIN 
					$tb[GruposServicos]          ON ( $tb[ServicosGrupos].idGrupos = $tb[GruposServicos].id )                INNER JOIN 
					$tb[TipoCobranca]            ON ( $tb[TipoCobranca].id = $tb[Servicos].idTipoCobranca) 
				  WHERE $tb[StatusServicos].status='A' AND $tb[PlanosPessoas].status= 'A' 
					$sqlPOP 
					$sqlGRUPO
				  GROUP BY $tb[Pessoas].id, $tb[PlanosPessoas].id, $tb[ServicosPlanos].id
				  ORDER BY $tb[GruposServicos].id, $tb[Pessoas].nome";
							
				$consultaPop=consultaSQL($sql, $conn);
	
				if($consultaPop && contaconsulta($consultaPop)) {
					
					for ($z = 0; $z < count( $periodoDatas ); $z++ ){
						$grupoVal["$grupo"] = 0;
						$grupoAdic["$grupo"] = 0;
						$grupoDesc["$grupo"] = 0;
				
						#inicia a varredura e joga 
						for($a=0;$a<contaConsulta($consultaPop);$a++) {
							//variaveis
							$nomeGrupo=resultadoSQL( $consultaPop, $a, 'grupo');
							$idVencimento= resultadoSQL( $consultaPop, $a, 'idVencimento' );
							$idServicoPlano= resultadoSQL( $consultaPop, $a, 'idServicosPlanos' );
							$proporcional= resultadoSQL( $consultaPop, $a, 'proporcional' );
							$cliente= resultadoSQL( $consultaPop, $a, 'cliente' );
							$especial= resultadoSQL( $consultaPop, $a, 'especial' );
							$formaCobranca= resultadoSQL( $consultaPop, $a, 'formaCobranca' );
							$tipoCobranca= resultadoSQL( $consultaPop, $a, 'tipoCobranca' );
							$cobranca= resultadoSQL( $consultaPop, $a, 'cobranca' );
							$diasTrial= resultadoSQL( $consultaPop, $a, 'diasTrial' );
							$nomeServico = resultadoSQL( $consultaPop, $a, 'nomeServico' );
							
							$dtCadastro=resultadoSQL($consultaPop, $a, 'dtCadastroServicosPlanos');
							$dtAtivacao=resultadoSQL($consultaPop, $a, 'dtAtivacaoServicosPlanos');
							
							$vencimento= dadosVencimento( $idVencimento );
														
														
							// calcula o dia da fatura para chamar a funcao q calcula o valor do plano
							if ( $vencimento[diaFaturamento] == '25' )
								$dtVencimento= mktime( 0, 0, 0, ( substr( $periodoDatas[$z], 0, strpos($periodoDatas[$z], '/') ) + 1 ), $vencimento[diaVencimento], substr( $periodoDatas[$z], strpos($periodoDatas[$z], '/')+1,6 ));
							else
								$dtVencimento= mktime( 0, 0, 0,substr( $periodoDatas[$z], 0, strpos($periodoDatas[$z], '/') ), $vencimento[diaVencimento], substr( $periodoDatas[$z], strpos($periodoDatas[$z], '/')+1,6 ));						
//						$pos = strpos($periodoDatas[$z], '/');

						//echo "<br>" . substr( $periodoDatas[$z], 0, $pos) . "#" . substr( $periodoDatas[$z], $pos+1,6 );
							if($especial=='S')
								 $valorServico= resultadoSQL($consultaPop, $a, 'valorServicosPlanos');
							else
								$valorServico= resultadoSQL($consultaPop, $a, 'valorServicos');
						
							if(formatarData($dtAtivacao)<=0) $dtAtivacao=$dtCadastro;
							
							if($formaCobranca=='mensal') {
								if($cobranca=='S') {
									# Verificar se serviço tem valor Proporcional
									if($proporcional=='S') {
										# Calcular dias e valor proporcional
										# Data de Vencimento com dia de Faturamento não de Vencimento
										# Serviço tem calculo baseado em data de Ativação e data de Faturamento
										# par proporcionalidade
										$arrayValor= calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $dtFinal, $tipoCobranca);
										$valor= $arrayValor['valor'];
										$valorAdicional= calculaServicosAdicionais( $idServicoPlano, $dtVencimento );
										$valorDesconto= calculaDescontos( $idServicoPlano, $dtVencimento );
									}
									else {
										# Verificar se servico nao esta em período trial
										$arrayValor= calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $dtFinal);
										$valor= $arrayValor['valor'];
										$valorAdicional= calculaServicosAdicionais( $idServicoPlano, $dtVencimento );
										$valorDesconto= calculaDescontos( $idServicoPlano, $dtVencimento );
									}
								}	
							}
							elseif($formaCobranca=='anual') {
								if($cobranca=='S') {
									# Cobrar servico - verificando anualidade
									$arrayValor= calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valorServico, $formaCobranca);
									$valor= $arrayValor['valor'];
									$valorAdicional= calculaServicosAdicionais( $idServicoPlano, $dtVencimento );
									$valorDesconto= calculaDescontos( $idServicoPlano, $dtVencimento );
								}
							}
							//echo "<br> valores: $valor ## $valorAdicional ## $valorDesconto ## $idServicoPlano ##" . date ("d-m-Y", $dtVencimento);
							$grupoVal[$grupo] += $valor;
							$grupoAdic[$grupo]+= $valorAdicional;
							$grupoDesc[$grupo]+= $valorDesconto;	
							
							//caso tenha sido "checado"  a opcao detalhar, exibe o registro.
							if ($matriz[detalhar]){
								if ($matriz[bntConfirmar]) {
									$zebra="tabfundo1";
									$cc=0;
									htmlAbreLinha($corFundo);
										itemLinhaTMNOURL($nomeGrupo . " / " . $nomeServico, $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
										itemLinhaTMNOURL($cliente, $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
										itemLinhaTMNOURL($periodoDatas[$z], $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
										itemLinhaTMNOURL(formatarValoresForm( $valor), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
										itemLinhaTMNOURL(formatarValoresForm($valorAdicional) , $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
										itemLinhaTMNOURL(formatarValoresForm( $valorDesconto ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
										#linha abaixo incluida
										itemLinhaTMNOURL(formatarValoresForm( ( ($valor + $valorAdicional) - $valorDesconto) ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
									htmlFechaLinha();
								
								}//senao gera o rel pdf ;)
								elseif($matriz[bntRelatorio]) {
									$c=0;
									$matResultado[$matCabecalho[$c++]][$l] = $nomeGrupo . " / " . $nomeServico;
									$matResultado[$matCabecalho[$c++]][$l] = $cliente;
									$matResultado[$matCabecalho[$c++]][$l] = $periodoDatas[$z];
									$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $valor);
									$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $valorAdicional);
									$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $valorDesconto);
									$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( ( ( $valor + $valorAdicional) - $valorDesconto) );
									$l++;
								}
							}
							//
							
								
						///////////////////////////////				
						}// fim da consulta
//						if ($z != 0)	$nomeGrupo = "&nbsp;";
						if ($matriz[bntConfirmar]) {
							$zebra="tabfundo1";
							$cc=0;
							htmlAbreLinha($corFundo);
								if ($matriz[detalhar]) {
									itemLinhaTMNOURL('Total de '.$nomeGrupo, 'right', 'middle',$largura[$cc++], $corFundo, 2, $zebra);
									$cc++;
								} 
								else {
									itemLinhaTMNOURL($nomeGrupo, 'left', 'middle',$largura[$cc++], $corFundo, 0, $zebra);
								}
								itemLinhaTMNOURL($periodoDatas[$z], $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL(formatarValoresForm( $grupoVal["$grupo"] ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL(formatarValoresForm( $grupoAdic["$grupo"] ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
								itemLinhaTMNOURL(formatarValoresForm( $grupoDesc["$grupo"] ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
								#linha abaixo incluida
								itemLinhaTMNOURL(formatarValoresForm( ( ( $grupoVal["$grupo"] + $grupoAdic["$grupo"] ) - $grupoDesc["$grupo"] ) ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
							htmlFechaLinha();
						
						}//senao gera o rel pdf ;)
						elseif($matriz[bntRelatorio]) {
							$c=0;
							$matResultado[$matCabecalho[$c++]][$l] = $nomeGrupo;
							$matResultado[$matCabecalho[$c++]][$l] = $periodoDatas[$z];
							$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $grupoVal["$grupo"]);
							$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $grupoAdic["$grupo"]);
							$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $grupoDesc["$grupo"]);
							$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( ( ( $grupoVal["$grupo"] + $grupoAdic["$grupo"] ) - $grupoDesc["$grupo"] ) );
							$l++;
						}
						$popVal[$pp] += $grupoVal["$grupo"];
						$popAdic[$pp] += $grupoAdic["$grupo"];
						$popDesc[$pp] += $grupoDesc["$grupo"];
					
							
					}  // fim do periodo 
					
				}//verifica resultado da consulta
			
			} //fim foreach grupos
			//total do pop
			if ($matriz[bntConfirmar]) {
				$zebra="tabfundo1";
				$cor="tabfundo1";
				$cc=0;
				htmlAbreLinha($cor);
				if ($matriz[detalhar])
					itemLinhaTMNOURL("<b>Total do Pop:</b>", 'right', 'middle',$largura[$cc++], $corFundo, 3, $zebra);
				else	
					itemLinhaTMNOURL("<b>Total do Pop:</b>", 'right', 'middle',$largura[$cc++], $corFundo, 2, $zebra);
					
				itemLinhaTMNOURL(formatarValoresForm( $popVal[$pp] ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
				itemLinhaTMNOURL(formatarValoresForm( $popAdic[$pp] ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
				itemLinhaTMNOURL(formatarValoresForm( $popDesc[$pp] ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
				#linha abaixo incluida
				itemLinhaTMNOURL(formatarValoresForm( ( ( $popVal[$pp] + $popAdic[$pp] ) - $popDesc[$pp] ) ), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $zebra);
				htmlFechaLinha();
				fechaTabela();		
			}
			elseif($matriz[bntRelatorio]) {
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l] = "<b>Total do Pop:</b>";
				$matResultado[$matCabecalho[$c++]][$l] = "&nbsp";
				$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $popVal[$pp]);
				$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $popAdic[$pp]);
				$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( $popDesc[$pp]);
				$matResultado[$matCabecalho[$c++]][$l] = formatarValoresForm( ( ( $popVal[$pp] + $popAdic[$pp] ) - $popDesc[$pp] ) );
				$l++;
			}
			$pp++;
			if($matriz[bntRelatorio] && count($matResultado)){		
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="SIMULAÇÃO DE FATURAMENTO POR GRUPOS DE SERVIÇOS";
				$matrizRelatorio[header][POP]=$nomePop.'<br>'.$periodo;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
					
				# Configurações
				$matrizRelatorio[config][linhas]=26;
				$matrizRelatorio[config][layout]='landscape';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
				$matrizGrupo[]=$matrizRelatorio;
			}
			
		}//fim da consulta pop
		
		if(count($matrizGrupo)>0){
			if(!$matriz[detalhar])
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','simulacao_faturamento_grservicos'),'simulacao_faturamento_grupos_servicos',$matrizRelatorio[config]);
			else
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','simulacao_faturamento_grservicos_det'),'simulacao_faturamento_grupos_servicos',$matrizRelatorio[config]);
			
			if ($arquivo) {
				echo "<br>";
				novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
					htmlAbreLinha($corfundo);
						itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Baixa por Serviço</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
					htmlFechaLinha();
				fechaTabela();
			}
		}
					
	}//fim do teste  se existe pop
	// exibir que nao foi selecionado nenhum pop

} //fim da funcao de relatorio




function preparaPeriodo($inicio, $fim){
	$mesIni = intval(substr( $inicio, 5, 2 ));
	$mesFim = intval(substr( $fim, 5, 2 ));
	$anoIni = intval(substr( $inicio, 0, 4 ));
	$anoFim = intval(substr( $fim, 0, 4 ));
	
	$cc = 0;
	for ( $i = $anoIni; $i <= $anoFim; $i++ ){
		if ( $anoIni == $anoFim ){
			//print "Entrou no if";
			for ($j = $mesIni; $j <= $mesFim; $j++ ){
				$mtrPeriodo[$cc++] = $j."/".$anoFim;
			}
		}
		else{
			if ( $i == $anoIni){
				//print "Entrou no if do else";
				for ($j = $mesIni; $j <= 12; $j++ ){
					$mtrPeriodo[$cc++] = $j."/".$i;
				}					
			}
			if ( $i != $anoIni && $i < $anoFim ){
				//print "Entrou no if do else";
				for ($j = 1; $j <= 12; $j++ ){
					$mtrPeriodo[$cc++] = $j."/".$i;
				}				
			}
			elseif ( $i == $anoFim ){
				//print "Entrou no else do else";
				for ($j = 1; $j <= $mesFim; $j++ ){
					$mtrPeriodo[$cc++] = $j."/".$i;
				}			
			}
		}
	}

	return $mtrPeriodo;
}

?>