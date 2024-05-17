<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Consulta de Clientes por POP

/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para filtros de Faturamento por POP
*/
function formRelatorioClientesPOP($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	
	$data=dataSistema();
	
	# Motrar tabela de busca
	novaTabela2("[Consulta de Clientes por POP]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>POP:</b><br>
			<span class=normal10>Selecione o POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if($matriz[pop_todos]) $opcPOP='checked';
			$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
			itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Detalhar Cliente:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=checkbox name=matriz[detalhar] value='S'>&nbsp;<span class=txtaviso>(Detalhamento de Planos e Serviços do Cliente)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		//Periodo ##
			//formPeriodoMesAno($matriz, $opcDe, $opcAte);
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
		//###
		novaLinhaTabela($corFundo, '100%');
			//$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
			//$texto.="&nbsp;<input type=submit name=matriz[bntRelatorio] value='Gerar Relatório' class=submit2>";
			$texto="<input type=submit name=matriz[bntRelatorio] value='Gerar Relatório' class=submit2>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	
}

/*
# Função para consultar de Simulação de Faturamento
function relatorioClientesPOP($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb;
	# Procedimentos
	# 1-Consultar todos os planos Ativos 
	# 2--> Consultar Servicos cadastrados/ativos com dtInicial>=mes/ano informados
	# 2--> Consultar Servicos ativos no plano
	# 3---> Consultar Servicos Adicionais do Serviço do Plano (ativos)
	# 4---> Consultar Descontos do Serviço do Plano (ativos)
	
	# Formatar Datas
	$matriz[dtInicial]=formatarData($matriz[dtInicial]);
	$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/01 00:00:00';
	
	# Consultar POPs
	if(!$matriz[pop_todos] && $matriz[pop]) {
		
		$sqlADDPOP='';
		$i=0;
		while($matriz[pop][$i]) {
			$sqlADDPOP.=$matriz[pop][$i];
			
			if($i+1 < count($matriz[pop])) $sqlADDPOP.=",";
			
			$i++;
		}
		
		$sqlPOP="
			SELECT
				$tb[POP].id,
				$tb[POP].nome
			FROM
				$tb[POP]
			WHERE 
				$tb[POP].id IN ($sqlADDPOP)
			ORDER BY
				$tb[POP].nome
		";
		
		$consultaPOP=consultaSQL($sqlPOP, $conn);
	}
	else {
		$consultaPOP=buscaPOP('','','todos','nome');
	}	
	
	if($consultaPOP && contaConsulta($consultaPOP)>0) {
			
		for($a=0;$a<contaConsulta($consultaPOP);$a++) {
			
			$idPOP=resultadoSQL($consultaPOP, $a, 'id');
			$nomePOP=resultadoSQL($consultaPOP, $a, 'nome');

			# SQL para consulta de emails por dominios do cliente informado
			$sql="
				SELECT
					$tb[POP].nome, 
					$tb[Pessoas].id idPessoa, 
					$tb[Pessoas].idPOP idPOP, 
					$tb[Pessoas].dtCadastro dtCadastro, 
					$tb[Pessoas].nome nomePessoa, 
					$tb[Pessoas].razao razaoSocial, 
					$tb[Pessoas].tipoPessoa tipoPessoa, 
					$tb[PessoasTipos].id idPessoaTipo
				FROM 
					$tb[Pessoas], 
					$tb[PessoasTipos], 
					$tb[POP],
					$tb[TipoPessoas]
				WHERE 
					$tb[PessoasTipos].idTipo = $tb[TipoPessoas].id
					AND $tb[TipoPessoas].valor='cli'
					AND $tb[POP].id = $tb[Pessoas].idPOP
					AND $tb[Pessoas].id = $tb[PessoasTipos].idPessoa
					AND $tb[POP].id = '$idPOP'
				ORDER BY
					$tb[Pessoas].nome
			";
			
			$consulta=consultaSQL($sql, $conn);		

			
			if($consulta && contaConsulta($consulta)>0) {
				# Totalizar
				for($i=0;$i<contaConsulta($consulta);$i++) {
					$nome=resultadoSQL($consulta, $i, 'nome');
					$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
					$idPOP=resultadoSQL($consulta, $i, 'idPOP');
					$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
					$nomePessoa=resultadoSQL($consulta, $i, 'nomePessoa');
					$razaoSocial=resultadoSQL($consulta, $i, 'razaoSocial');
					$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
					$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
				
					$c=0;
					
					$matResultado[$matCabecalho[$c++]][$l]=$nomePessoa;
					$matResultado[$matCabecalho[$c++]][$l]=$telefone;
					$matResultado[$matCabecalho[$c++]][$l]=converteData($dtVencimento,'banco','formdata');
					$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalPendente);
					
					$l++;
					
					$totalGeral[pendente]+=$totalPendente;
				}
				
			}
	
			
			if(is_array($total)) {
				$keys=array_keys($total);
				# Cabeçalho
				$matResultado=array();
				$matCabecalho=array("POP", $grvMes, "Faturado", "Recebido", "Pendente", "Juros", "Descontos");
				
				for($a=0;$a<count($keys);$a++) {
					
					$ano=substr($keys[$a],0,4);
					$mesFormatado=$configMeses[intval(substr($keys[$a], 5, 2))]."/".$ano;
					
					$totalValor=$total[$keys[$a]][valor];
					$totalRecebido=$total[$keys[$a]][recebido];
					$totalPendente=$total[$keys[$a]][pendente];
					$totalJuros=$total[$keys[$a]][juros];
					$totalDesconto=$total[$keys[$a]][desconto];
					
					$c=0;
					$matResultado[$matCabecalho[$c++]][$a]=$nomePOP;
					$matResultado[$matCabecalho[$c++]][$a]=$mesFormatado;
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalValor);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalRecebido);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalPendente);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalJuros);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalDesconto);
					
					$totalGeral[faturado]+=$totalValor;
					$totalGeral[recebido]+=$totalRecebido;
					$totalGeral[pendente]+=$totalPendente;
					$totalGeral[juros]+=$totalJuros;
					$totalGeral[desconto]+=$totalDesconto;
					
					
				} #fecha laco de montagem de tabela
				
				# Alimentar Array de Detalhe com mais um campo - totais
				$c=0;
				$matResultado[$matCabecalho[$c++]][$a]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$a]='<b>Total</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[recebido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[pendente]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[juros]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[desconto]).'</b>';
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="TOTAL DE FATURAMENTO DO POP$ttBase<br>".converteData($dtInicial,'banco','formdata')." até ".converteData($dtFinal,'banco','formdata');;
				$matrizRelatorio[header][POP]=$nomePOP;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=20;
				$matrizRelatorio[config][layout]='landscape';
								
				$matrizGrupo[]=$matrizRelatorio;
				
			}
			else {
				# Não há registros
				itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'left', $corFundo, 7, 'txtaviso');
			}
		}
		
		if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
			# Converter para PDF:
			$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_pop'),'faturamento_pop',$matrizRelatorio[config]);
			itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório Total de Faturamento por POP$ttBase</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
		}
		else {
			# Não há registros
			itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');	
		}
				
		if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
			# Converter para PDF:
			$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_pop'),'faturamento_pop',$matrizRelatorio[config]);
			itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório Total de Faturamento por POP$ttBase</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
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
*/
?>