<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Serviços por Cliente


# função para form de seleção de filtros de faturamento
function formServicosClientes($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Consulta de Serviços por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			itemLinhaTMNOURL('<b>Busca por Serviço:</b><br>
			<span class=normal10>Informe nome ou descrição do serviço</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[txtProcurar] size=60 value='$matriz[txtProcurar]'> <input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if($matriz[txtProcurar]) {
			# Procurar Cliente
			$tipoPessoa=checkTipoPessoa('cli');
			$consulta=buscaServicos("upper(nome) like '%$matriz[txtProcurar]%' 
				OR upper(descricao) like '%$matriz[txtProcurar]%'", $campo, 'custom','nome');
			
			if($consulta && contaConsulta($consulta)>0) {
				# Selecionar cliente
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Serviços encontrados:</b><br>
					<span class=normal10>Selecione o Serviço</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectConsulta($consulta, 'nome', 'id', 'idServico', $matriz[idServico]), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
			}
		}
		htmlFechaLinha();
	fechaTabela();
}



# Função para consultar de Simulação de Faturamento
function consultaServicosClientes($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb;
	# Procedimentos
	# 1-Consultar todos os planos Ativos do Cliente
	# 2--> Consultar Servicos cadastrados/ativos com dtInicial>=mes/ano informados
	# 2--> Consultar Servicos ativos no plano
	# 3---> Consultar Servicos Adicionais do Serviço do Plano (ativos)
	# 4---> Consultar Descontos do Serviço do Plano (ativos)
	
	# Calcular a data inicial para consulta
	$tmpData=mktime(0,0,0,$matriz[mes],dataDiasMes($matriz[mes]),$matriz[ano]);
	$dtCadastroPlano=date('Y-m-d',$tmpData);
	
	# 1-Consultar planos ativos
	$sql="
		SELECT
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[PessoasTipos].idPessoa idPessoa, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[PessoasTipos].dtCadastro dtCadastro, 
			$tb[Pessoas].idPOP idPOP, 
			$tb[POP].nome nomePOP, 
			$tb[Pessoas].tipoPessoa tipoPessoa, 
			$tb[PlanosPessoas].nome, 
			$tb[Servicos].nome 
		FROM
			$tb[POP], 
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[PlanosPessoas], 
			$tb[ServicosPlanos], 
			$tb[Servicos] 
		WHERE
			$tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			and $tb[Pessoas].idPOP = $tb[POP].id 
			and $tb[PlanosPessoas].idPessoaTipo=$tb[PessoasTipos].id 
			and $tb[ServicosPlanos].idPlano=$tb[PlanosPessoas].id 
			and $tb[ServicosPlanos].idServico=$tb[Servicos].id 
			and $tb[ServicosPlanos].idServico='$matriz[idServico]'
		GROUP BY
			$tb[PessoasTipos].id
		ORDER BY
			$tb[Pessoas].nome";
	
	if($sql) $consultaPlanosAtivos=consultaSQL($sql, $conn);
	
	if($consultaPlanosAtivos && contaconsulta($consultaPlanosAtivos) ) {
		
		# Cabeçalho
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		# Mostrar Cliente
		htmlAbreLinha($corFundo);
			htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
				novaTabela("[Listagem de Clientes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL("<b>Cliente</b>", 'center', 'middle', '35%', $corFundo, 0, 'tabfundo1');
						itemLinhaTMNOURL("<b>POP de Acesso</b>", 'center', 'middle', '25%', $corFundo, 0, 'tabfundo1');
						itemLinhaTMNOURL("<b>Data Cadastro</b>", 'center', 'middle', '15%', $corFundo, 0, 'tabfundo1');
						itemLinhaTMNOURL("<b>Opções</b>", 'center', 'middle', '25%', $corFundo, 0, 'tabfundo1');
					htmlFechaLinha();
				
					# Listagem de Planos com servicos e totais por serviço
					for($a=0;$a<contaConsulta($consultaPlanosAtivos);$a++) {
						
						# Consultar Planos da pessoa
						$idPessoaTipo=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoaTipo');
						$nomePessoa=resultadoSQL($consultaPlanosAtivos, $a, 'nomePessoa');
						$fone=dadosPessoasTipos($idPessoaTipo);
						$nomePOP=resultadoSQL($consultaPlanosAtivos, $a, 'nomePOP');
						$dtCadastro=resultadoSQL($consultaPlanosAtivos, $a, 'dtCadastro');
						$txtProcurar=substr($nomePessoa,0,2);
						
						$opcoes=htmlMontaOpcao("<a href=?modulo=consultas&sub=clientes&matriz[txtProcurar]='$txtProcurar'&matriz[idPessoaTipo]=$idPessoaTipo&matriz[bntConfirmar]=1&matriz[detalhar]='S'>Detalhamento</a>",'servicos');
						
						# Mostrar resultado
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL($nomePessoa."<br>".$fone[endereco][telefones], 'left', 'middle', '35%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($nomePOP, 'center', 'middle', '25%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL(converteData($dtCadastro, 'banco','formdata'), 'center', 'middle', '15%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($opcoes, 'center', 'middle', '25%', $corFundo, 0, 'normal10');
						htmlFechaLinha();						
			
					}
				fechaTabela();
			htmlFechaColuna();
		htmlFechaLinha();

		# Rodapé com totais
		fechaTabela();
	
	}
	else {
		# Verificar faturamento dos clientes
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		novaTabela("[Servicos por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			# Mensagem de alerta de faturamento não encontrado
			$msg="<span class=txtaviso>Não foram encontrados registros para processamento!</span>";
			itemTabelaNOURL($msg, 'left', $corFundo, 4, 'normal10');
		fechaTabela();
	}
	return(0);
}

/**
 * Seleciona todos os servicos do cliente a partir do idPessoaTipo informado,
 * é possível também adicionar mais condicoes pelo parâmetro where.
 *
 * @author João Petrelli
 * @since 18-02-2009
 * 
 * @param int $idPessoaTipo
 * @param String $where
 * @return consultaSQL()
 */
function selecionaServicosClientes($idPessoaTipo, $where = '') {
	global $conn;
	
	if ($where) {
		$where = "AND $where";
	}
	
	$sql = 		"SELECT 
					ServicosPlanos.id, 
					Servicos.nome, 
					Servicos.valor valorServico,
					ServicosPlanos.valor,
					StatusServicosPlanos.status,
					PlanosPessoas.especial
				FROM 
					Servicos, 
					StatusServicosPlanos, 
					ServicosPlanos, 
					PlanosPessoas
				WHERE 
					PlanosPessoas.idPessoaTipo = '".$idPessoaTipo."' 
					AND PlanosPessoas.id = ServicosPlanos.idPlano 
					AND ServicosPlanos.idStatus = StatusServicosPlanos.id 
					AND ServicosPlanos.idServico = Servicos.id
					$where
				ORDER BY
					Servicos.nome";
	
	return consultaSQL($sql, $conn);
}
?>