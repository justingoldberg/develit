<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Descontos Concedidos



# função para form de seleção de filtros de consulta geral de cliente
function formDescontosConcedidos($modulo, $sub, $acao, $registro, $matriz) {

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
	
		# Motrar tabela de busca
		novaTabela2("[Consulta de de Descontos Concedidos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Serviço:</b><br>
				<span class=normal10>Selecione o Tipo de Serviço</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[servico_todos]) $opcServico='checked';
				$texto="<input type=checkbox name=matriz[servico_todos] value=S $opcServico><b>Todos</b>";
				itemLinhaForm(formSelectServicos($matriz[servico],'servico','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>POP:</b><br>
				<span class=normal10>Selecione o POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[pop_todos]) $opcPOP='checked';
				$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
				itemLinhaForm(formSelectPOP($matriz[pop],'pop','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Vencimento:</b><br>
				<span class=normal10>Selecione o Vencimento</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[vencimento_todos]) $opcVencimento='checked';
				$texto="<input type=checkbox name=matriz[vencimento_todos] value=S $opcVencimento><b>Todos</b>";
				itemLinhaForm(formSelectVencimento($matriz[vencimento],'vencimento','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano de Referência:</b><br>
				<span class=normal10>Selecione o mês de referência</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectMes($matriz[mes],'mes','form').formSelectAno($matriz[ano],'ano','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
			
		fechaTabela();
	}
}


# Função para consultar de Simulação de Faturamento
function consultaDescontosConcedidos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb;
	
	# Calcular a data inicial para consulta
	$tmpData=mktime(0,0,0,$matriz[mes],31,$matriz[ano]);
	$dtCadastroPlano=date('Y-m-d',$tmpData);
	
	if(strlen($matriz[mes]) < 2) $matriz[mes]="0".$matriz[mes];
	
	# 1-Consultar planos ativos
	if(!$matriz[pop_todos]) $sqlADD.=" AND $tb[Pessoas].idPOP = '$matriz[pop]' ";
	if(!$matriz[servico_todos]) {
		$sqlADD.=" AND $tb[ServicosPlanos].idServico = '$matriz[servico]' ";
	}
	if(!$matriz[vencimento_todos]) {
		$sqlADD.=" AND $tb[PlanosPessoas].idVencimento = '$matriz[vencimento]' ";
		$sqlADD.=" AND LEFT($tb[DescontosServicosPlanos].dtDesconto,7) = '$matriz[ano]-$matriz[mes]' ";
	}
	else {
		$sqlADD.=" AND LEFT($tb[DescontosServicosPlanos].dtDesconto,7) = '$matriz[ano]-$matriz[mes]' ";
	}

	$sql="
		SELECT
			$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
			$tb[PlanosPessoas].id idPlano, 
			$tb[PlanosPessoas].nome nomePlano, 
			$tb[PlanosPessoas].dtCadastro dtCadastro, 
			$tb[PlanosPessoas].idVencimento idVencimento, 
			$tb[PlanosPessoas].idFormaCobranca idFormaCobranca, 
			$tb[PlanosPessoas].especial especial, 
			$tb[PlanosPessoas].status status,
			$tb[DescontosServicosPlanos].id id,
			$tb[DescontosServicosPlanos].descricao descricaoDesconto,
			$tb[DescontosServicosPlanos].status statusDesconto,
			$tb[DescontosServicosPlanos].valor valorDesconto,
			$tb[DescontosServicosPlanos].dtDesconto dtDesconto,
			$tb[Pessoas].nome nome,
			$tb[Pessoas].idPOP idPOP
		FROM 
			$tb[PlanosPessoas], 
			$tb[PessoasTipos],
			$tb[DescontosServicosPlanos],
			$tb[Pessoas],
			$tb[ServicosPlanos]
		WHERE
			$tb[Pessoas].id = $tb[PessoasTipos].idPessoa
			AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano
			AND $tb[DescontosServicosPlanos].idPlano = $tb[PlanosPessoas].id
			AND $tb[DescontosServicosPlanos].idServicoPlano = $tb[ServicosPlanos].id
			AND $tb[DescontosServicosPlanos].status = 'A'
			$sqlADD
		ORDER BY
			$tb[Pessoas].nome";
	
	if($sql) $consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta) ) {
		
		# Cabeçalho
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		novaTabela2("[Descontos Concedidos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
			# Listagem de Planos com servicos e totais por serviço
			$anterior = ''; 
			for($a=0;$a<contaConsulta($consulta);$a++) {
				
				# Consultar Planos da pessoa
				$id=resultadoSQL($consulta, $a, 'id');
				$idPlano=resultadoSQL($consulta, $a, 'idPlano');
				$especial=resultadoSQL($consulta, $a, 'especial');
				if($especial=='S') $tipoPlano='<span class=txtaviso>Especial</span>';
				else $tipoPlano='<span class=txtok>Normal</span>';
				$descricao=resultadoSQL($consulta, $a, 'descricaoDesconto');
				$valor=resultadoSQL($consulta, $a, 'valorDesconto');
				$dtDesconto=resultadoSQL($consulta, $a, 'dtDesconto');
				$idPessoaTipo=resultadoSQL($consulta, $a, 'idPessoaTipo');
				$nomePessoa=resultadoSQL($consulta, $a, 'nome');
				$idVencimento=resultadoSQL($consulta, $a, 'idVencimento');
				$idFormaCobranca=resultadoSQL($consulta, $a, 'idFormaCobranca');
				$vencimento=dadosVencimento($idVencimento);
				$idPOP=resultadoSQL($consulta, $a, 'idPOP');
				# Mostrar Cliente
				
				if ($anterior != $nomePessoa ){
					$totalCliente=0;
				htmlAbreLinha($corFundo);
					htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
					echo "<br>";
					
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL('Detalhamento', 'center', 'middle', '60%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('POP de Acesso', 'center', 'middle', '40%', $corFundo, 0, 'tabfundo0');
						htmlFechaLinha();
											
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL("<b>$nomePessoa</b>", 'center', 'middle', '60%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL(formSelectPOP($idPOP, '','check'), 'center', 'middle', '40%', $corFundo, 0, 'bold10');
						htmlFechaLinha();
							
						htmlAbreLinha($corFundo);
							htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
							novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
								# Zerar total do cliente
															
								# Mostrar resultado
				}//fim de um novo cliente
				$totalCliente+=$valor;
				
				# Formatar valor com URL de calculo do plano
				$valorFormatado=formatarValoresForm($valor);
				$valorFormatado=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=calculo&registro=$idPlano&matriz[mes]=$matriz[mes]&matriz[ano]=$matriz[ano]>$valorFormatado</a>",'lancamento');
								
				//registro de dedetalhe.
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL($descricao, 'left', 'middle', '35%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL("Venc.: ".$vencimento[descricao], 'center', 'middle', '15%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL(formSelectFormaCobranca($idFormaCobranca,'','check'), 'center', 'middle', '20%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL($tipoPlano, 'center', 'middle', '15%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL($valorFormatado, 'center', 'middle', '15%', $corFundo, 0, 'normal10');
				htmlFechaLinha();
				//fim do registro de detalhe.
				
				//rodape
				$anterior = $nomePessoa;
				if ($a+1 < contaConsulta($consulta))
					$proximo = resultadoSQL($consulta, $a+1, 'nome');
				else
					$proximo='';
					
				if ($anterior != $proximo ){
								# Totalizar Cliente
								htmlAbreLinha($corFundo);
									itemLinhaTMNOURL("<b>Total de Descontos do Cliente</b>", 'right', 'middle', '75%', $corFundo, 4, 'tabfundo1');
									itemLinhaTMNOURL(formatarValoresForm($totalCliente), 'center', 'middle', '15%', $corFundo, 0, 'txtok');
								htmlFechaLinha();
								
								# Contabilizar Total geral	
								$totalGeral+=$totalCliente;
								
								fechaTabela();
								
								htmlFechaColuna();
							htmlFechaLinha();
							
					fechaTabela();
					
					htmlFechaColuna();
				htmlFechaLinha();
				
				}
				
				
			}
			
		# Rodapé com totais
		htmlAbreLinha($corFundo);
			htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');		
				novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL("<b>Total Geral Descontado</b>", 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						itemLinhaTMNOURL(formatarValoresForm($totalGeral), 'center', 'middle', '40%', $corFundo, 0, 'txtok');
					htmlFechaLinha();
				fechaTabela();
			htmlFechaColuna();
		fechaLinhaTabela();
		
	}
	else {
		# Verificar faturamento dos clientes
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		novaTabela("[Descontos Concedidos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			# Mensagem de alerta de faturamento não encontrado
			$msg="<span class=txtaviso>Não foram encontrados registros para processamento!</span>";
			itemTabelaNOURL($msg, 'left', $corFundo, 4, 'normal10');
		fechaTabela();
	}
	return(0);
}

?>