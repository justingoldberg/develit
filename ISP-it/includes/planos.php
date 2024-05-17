<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 09/07/2003
# Ultima altera��o: 25/02/2004
#    Altera��o No.: 040
#
# Fun��o:
#    Painel - Fun��es para planos


# Fun��o de banco de dados - Pessoas
function dbPlano($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;

	$data=dataSistema();

	# Sql de inclus�o
	if($tipo=='incluir') {

		$sql="INSERT INTO $tb[PlanosPessoas] VALUES (0,
		$matriz[idPessoaTipo],
		'$matriz[vencimento]',
		'$matriz[forma_cobranca]',
		'$matriz[dtCadastro]',
		'',
		'$matriz[nome]',
		'$matriz[especial]',
		'$matriz[desconto]',
		'$matriz[status]'
		)";

	} #fecha inclusao
	if($tipo=='incluirplano') {

		$sql="INSERT INTO $tb[PlanosPessoas] VALUES (
		$matriz[idPlano],
		$matriz[idPessoaTipo],
		'$matriz[vencimento]',
		'$matriz[forma_cobranca]',
		'$matriz[dtCadastro]',
		'',
		'$matriz[nome]',
		'$matriz[especial]',
		'$matriz[desconto]',
		'$matriz[status]'
		)";

	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[PlanosPessoas] where id=$matriz[id]";
	}
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM $tb[PlanosPessoas] where idPessoaTipo=$matriz[id]";
	}
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[PlanosPessoas]
			SET 
				idVencimento='$matriz[vencimento]', 
				idFormaCobranca='$matriz[forma_cobranca]', 
				nome='$matriz[nome]',
				especial='$matriz[especial]',
				desconto='$matriz[desconto]'
			WHERE id=$matriz[id]";
	}
	elseif($tipo=='cancelar') {
		$sql="UPDATE $tb[PlanosPessoas]
			SET 
				status='C'
			WHERE id=$matriz[id]";
	}
	elseif($tipo=='ativar') {
		$sql="UPDATE $tb[PlanosPessoas]
			SET 
				status='A'
			WHERE id=$matriz[id]";
	}
	elseif($tipo=='desativar') {
		$sql="UPDATE $tb[PlanosPessoas]
			SET 
				status='I'
			WHERE id=$matriz[id]";
	}

	elseif ($tipo == 'ativarPlano') {
		$sql = "UPDATE $tb[ServicosPlanos]
			SET
				status = '4'
			WHERE id = $matriz[id]";
	}

	elseif ($tipo == 'inativarPlano') {
		$sql = "UPDATE $tb[ServicosPlanos]
			SET
				status = '6'
			WHERE id = $matriz[id]";
	}

	if($sql) {
		$retorno=consultaSQL($sql, $conn);
		return($retorno);
	}
}




# Fun��o para listagem
function listarPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessLogin, $sessPlanos, $conn, $tb;

	$data=dataSistema();

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');

	if( ! $permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {

		if($registro) {
			# Gravar Session de Planos - Informa��es da Pessoa
			$sessPlanos[idPessoaTipo]=$registro;

			# Mostrar Informa��es sobre Servidor
			verPessoas($modulo, $sub, $acao, $registro, $matriz);
			echo "<br>";

			if($acao=='listar') $consulta=buscaPlanos("idPessoaTipo=$registro AND (status='A' OR status='I' OR status='N' OR status='T')", '','custom','dtCadastro');
			elseif($acao=='listartodos') $consulta=buscaPlanos($registro, 'idPessoatipo','igual','status, dtCadastro');

			# Cabe�alho
			# Motrar tabela de busca
			novaTabela("[Planos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 7);
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar&registro=$registro>Adicionar Plano</a>",'incluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$registro>Listar Ativos</a>",'listar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos&registro=$registro>Listar Todos</a>",'listar');
			itemTabelaNOURL($opcoes, 'right', $corFundo, 7, 'tabfundo1');


			# Caso n�o hajam servicos para o servidor
			if(!$consulta || contaConsulta($consulta)==0) {
				# N�o h� registros
				itemTabelaNOURL('N�o h� planos cadastrados', 'left', $corFundo, 7, 'txtaviso');
			}
			else {

				# Cabe�alho
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome do Plano', 'center', '33%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Tipo', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Vencimento', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Servi�os', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '22%', 'tabfundo0');
				fechaLinhaTabela();

				for($i=0;$i<contaConsulta($consulta);$i++) {
					# Mostrar registro
					$id=resultadoSQL($consulta, $i, 'id');
					$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
					$idVencimento=resultadoSQL($consulta, $i, 'idVencimento');
					$idFormaCobranca=resultadoSQL($consulta, $i, 'idFormaCobranca');
					$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
					$nome=resultadoSQL($consulta, $i, 'nome');
					$especial=resultadoSQL($consulta, $i, 'especial');
					$status=resultadoSQL($consulta, $i, 'status');

					# Somar valor total do plano
					$valor=valorPlano($id, $especial, $idVencimento, '', $data[mes], $data[ano], $parametro);
					$valorFormatado=formatarValoresForm($valor);

					$sqlServicos="
						SELECT 
							COUNT($tb[ServicosPlanos].id) qtde, 
							$tb[StatusServicos].status 
						FROM 
							$tb[ServicosPlanos], 
							$tb[StatusServicos]
						WHERE 
							$tb[ServicosPlanos].idStatus=$tb[StatusServicos].id 
							AND $tb[ServicosPlanos].idPlano=$id 
						GROUP BY $tb[StatusServicos].status";

					$consultaServicosPlanos=consultaSQL($sqlServicos, $conn);
					$qtdeServicos='';
					if($consultaServicosPlanos && contaConsulta($consultaServicosPlanos)>0) {
						for($a=0;$a<contaConsulta($consultaServicosPlanos);$a++) {
							$tmpQtde=resultadoSQL($consultaServicosPlanos, $a, 'qtde');
							$tmpStatus=resultadoSQL($consultaServicosPlanos, $a, 'status');

							$tmpStatus=formSelectStatus($tmpStatus, '','check');
							$qtdeServicos.="$tmpStatus&nbsp;<b>($tmpQtde)</b><br>";
						}
					}
					else $qtdeServicos="<span class=txtaviso>Nenhum</span>";

					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ver&registro=$id>Ver</a>",'ver');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$id>Abrir</a>",'abrir');

					if($status!='C') {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
					}

					if($status=='A') {
						$opcoes.="<br>";
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$id>Cancelar</a>",'cancelar');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativar&registro=$id>Desativar</a>",'ativar');
					}
					if($status=='N' || $status=='T') {
						$opcoes.="<br>";
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$id>Cancelar</a>",'cancelar');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativar&registro=$id>Ativar</a>",'desativar');
					}
					if($status=='I') {
						$opcoes.="<br>";
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativar&registro=$id>Ativar</a>",'desativar');
					}


					# Somat�rio para total
					if($status=='A') {
						$totalAtivo+=$valor;
						if($valor) $valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=calculo&registro=$id>$valorFormatado</a>",'lancamento');
					}
					else {
						$totalInativo+=$valor;
						if($valor) $valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=calculo&registro=$id>$valorFormatado</a>",'lancamento');
					}

					novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '33%', 'normal10');
					itemLinhaTabela(formSelectStatus($status, '','check'), 'center', '5%', 'normal10');
					itemLinhaTabela($valorFormatado, 'center', '15%', 'normal10');
					itemLinhaTabela(checkTipoPlano($especial), 'center', '5%', 'normal10');
					itemLinhaTabela(formSelectVencimento($idVencimento,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($qtdeServicos, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'left', '22%', 'normal8');
					fechaLinhaTabela();


				} #fecha laco de montagem de tabela

				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Valor Total dos Planos Inativos', 'right', 'middle', '40%', $corFundo, 2, 'bold10');
				itemLinhaTMNOURL(formatarValoresForm($totalInativo), 'center', 'middle', '15%', $corFundo, 0, 'txtaviso');
				itemLinhaTMNOURL('&nbsp;', 'center', 'middle', '45%', $corFundo, 4, 'tabfundo0');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Valor Total dos Planos Ativos', 'right', 'middle', '40%', $corFundo, 2, 'bold10');
				itemLinhaTMNOURL(formatarValoresForm($totalAtivo), 'center', 'middle', '15%', $corFundo, 0, 'txtok');
				itemLinhaTMNOURL('&nbsp;', 'center', 'middle', '45%', $corFundo, 4, 'tabfundo0');
				fechaLinhaTabela();

			} #fecha servicos encontrados
		}
		else {
			# Aviso
			$msg="Par�metro necess�rio n�o recebido! - Tipo de Pessoa! ";
			$url="?modulo=$modulo&sub=$sub";
			aviso("Aviso", $msg, $url, 760);
		}

		fechaTabela();

	}#fecha permissoes
}#fecha fun��o de listagem



# fun��o de busca
function buscaPlanos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;

	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[PlanosPessoas] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[PlanosPessoas] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[PlanosPessoas] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[PlanosPessoas] WHERE $texto ORDER BY $ordem";
	}

	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
} # fecha fun��o de busca




# fun��o para adicionar pessoa
function adicionarPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	if(!$permissao[adicionar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {

		# Verificar se ID de pessoa Tipo foi recebido
		if($registro) {

			if(!$matriz[bntConfirmar]) {

				verPessoas($modulo, $sub, $acao, $registro, $matriz);
				echo "<br>";

				# Motrar tabela de busca
				novaTabela2("[Planos - Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				//menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=registro value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$registro>
						<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Nome do Plano:</b><br>
						<span class=normal10>Informe o nome do plano</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=texto name=matriz[nome] size=60 value='$matriz[nome]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Vencimento:</b><br>
						<span class=normal10>Selecione o Vencimento</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectVencimento($matriz[vencimento],'vencimento','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Forma de Cobran�a:</b><br>
						<span class=normal10>Selecione a Forma de Cobran�a</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectFormaCobranca($matriz[forma_cobranca],'forma_cobranca','formOnChange').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if($matriz[forma_cobranca]) {
				
					$formaCobranca = dadosFormaCobranca($matriz[forma_cobranca]);
					$banco = dadosBanco($formaCobranca[idBanco]);
					$tipoCartira = dadosTipoCarteira($formaCobranca[idTipoCarteira]);
					if ($tipoCartira[valor] == 'S' ||
					   ($tipoCartira[valor] == 'M' && $banco[numero] == 341) || 
					   ($tipoCartira[valor] == 'M' && $banco[numero] == 341)) {
						novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Descontos:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("<input type=checkbox name=matriz[desconto] value=1>
								<span class=normal10>Gerar descontos no faturamento</span>", 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					
					$checked = $matriz[especial] == 'S' ? "checked" : "";
					novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Plano Especial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm("<input type=checkbox name=matriz[especial] value=S $checked>
							<span class=normal10>Selecionar esta op��o quando os servi�os obtiverem valores diferenciados</span>", 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Status:</b><br>
							<span class=normal10>Selecione o Status Inicial do Plano</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectStatus($matriz[status],'status', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
					formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	
					htmlFechaLinha();
					fechaTabela();
				}
			}
			else {
				if($matriz[nome]) {

					# Gravar
					$data=dataSistema();

					# Incluir Pessoa
					$matriz[dtCadastro]=$data[dataBanco];
					$gravaPlano=dbPlano($matriz, 'incluir');

					if(!$gravaPlano) {
						# Excluir pessoa
						$msg="Ocorreu um erro na tentativa de gravar o Plano";
						$url="?modulo=$modulo&sub=$sub&acao=$acao";
						aviso("Aviso", $msg, $url, '100%');
					}
					else {
						# Selecionar Servi�os do Plano
						$msg="Plano gravado com sucesso!";
						avisoNOURL("Aviso", $msg, '600');

						echo "<br>";

						listarPlanos($modulo, $sub, 'listar', $matriz[idPessoaTipo], $matriz);
					}
				}
				else {
					# Falta de par�metros
					# Mensagem de aviso
					$msg="Campos obrigat�rios n�o preenchidos!<br> Preencha todos os campos antes de prosseguir com o cadastro! ";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					aviso("Aviso", $msg, $url, 760);
				}


				fechaTabela();
			}
		}
		else {
			# Aviso
			$msg="Par�metro necess�rio n�o recebido! - Tipo de Pessoa! ";
			$url="?modulo=$modulo&sub=$sub";
			aviso("Aviso", $msg, $url, 760);
		}
	}
}




# fun��o para adicionar pessoa
function alterarPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');

	if(!$permissao[alterar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Selecionar Plano
		if(!$matriz[bntConfirmar]) {
			
			$registro = ($matriz[id] ? $matriz[id] : $registro); 
			$consulta=buscaPlanos($registro, 'id','igual','id');

			if($consulta && contaConsulta($consulta)>0) {

				# Dados do plano
				if ($matriz[id]){
					$idPessoaTipo = $matriz[idPessoaTipo];
					$idVencimento = $matriz[vencimento];
					$idFormaCobranca = $matriz[forma_cobranca];
					$nome = $matriz[nome];
					$especial = $matriz[especial];
					if($especial=='S') $opcCheckEspecial='checked';
					$status = $matriz[status];
					$desconto = $matriz[desconto];
				}else {
					$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
					$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
					$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
					$nome=resultadoSQL($consulta, 0, 'nome');
					$especial=resultadoSQL($consulta, 0, 'especial');
					if($especial=='S') $opcCheckEspecial='checked';
					$status=resultadoSQL($consulta, 0, 'status');
					$desconto = resultadoSQL($consulta, 0, 'desconto');
				}
				

				verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
				echo "<br>";

				# Motrar tabela de busca
				novaTabela2("[Planos - Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=matriz[id] value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						<input type=hidden name=registro value=$idPessoaTipo>
						<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Nome do Plano:</b><br>
						<span class=normal10>Informe o nome do plano</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=texto name=matriz[nome] size=60 value='$nome'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Vencimento:</b><br>
						<span class=normal10>Selecione o Vencimento</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectVencimento($idVencimento,'vencimento','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Forma de Cobran�a:</b><br>
						<span class=normal10>Selecione a Forma de Cobran�a</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectFormaCobranca($idFormaCobranca,'forma_cobranca','formOnChange'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				$formaCobranca = dadosFormaCobranca($idFormaCobranca);
				$banco = dadosBanco($formaCobranca[idBanco]);
				$tipoCartira = dadosTipoCarteira($formaCobranca[idTipoCarteira]);
				if ($tipoCartira[valor] == 'S' || 
				   ($tipoCartira[valor] == 'M' && $banco[numero] == 341) || 
				   ($tipoCartira[valor] == 'R' && $banco[numero] == 341)) {
					$checked = $desconto ? "checked" : "";
					novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Descontos:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm("<input type=checkbox name=matriz[desconto] value=1 $checked>
							<span class=normal10>Gerar descontos no faturamento</span>", 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Plano Especial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm("<input type=checkbox name=matriz[especial] value=S $opcCheckEspecial>
						<span class=normal10>Selecionar apenas de valores do plano possuem valores diferenciados </span>", 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				# Nao deixar alterar status
				/*
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b><br>
				<span class=normal10>Selecione o Status Inicial do Plano</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectStatus($status, 'status', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				*/
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);

				htmlFechaLinha();
				fechaTabela();
			}
			else {
				# Registro n�o encontrado!
				# Mensagem de aviso
				$msg="Plano n�o encontrado! ";
				$url="?modulo=cadastros&sub=clientes&acao=procurar";
				aviso("Aviso", $msg, $url, 760);
			}

		}
		else {
			if($matriz[nome]) {

				# Gravar
				$data=dataSistema();

				# Incluir Pessoa
				$gravaPlano=dbPlano($matriz, 'alterar');

				if(!$gravaPlano) {
					# Excluir pessoa
					$msg="Ocorreu um erro na tentativa de gravar o Plano";
					$url="?modulo=$modulo&sub=$sub&acao=$acao";
					aviso("Aviso de Erro", $msg, $url, '400');
				}
				else {

					# Alterar Vencimento:
					# - Servi�os Adicionais
					# - Descontos
					$dtInicio="$data[ano]-$data[mes]";
					atualizarDataDesconto($matriz[id], $dtInicio, $matriz[vencimento]);
					atualizarDataServicoAdicional($matriz[id], $dtInicio, $matriz[vencimento]);

					# Selecionar Servi�os do Plano
					$msg="Plano gravado com sucesso!";
					avisoNOURL("Confirma��o de Altera��o", $msg, '400');
					echo "<br>";

					listarPlanos($modulo, $sub, 'listar', $matriz[idPessoaTipo], $matriz);

				}
			}
			else {
				# Falta de par�metros
				# Mensagem de aviso
				$msg="Campos obrigat�rios n�o preenchidos!<br> Preencha todos os campos antes de prosseguir com o cadastro! ";
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
				aviso("Aviso", $msg, $url, 760);
			}
			fechaTabela();
		}
	}//permissoes
}




# fun��o para adicionar pessoa
function cancelarPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	if(!$permissao[excluir]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {

		# Selecionar Plano
		if(!$matriz[bntConfirmar]) {

			$consulta=buscaPlanos($registro, 'id','igual','id');

			if($consulta && contaConsulta($consulta) > 0) {

				# Dados do plano
				$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
				$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
				$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$especial=resultadoSQL($consulta, 0, 'especial');
				if($especial=='S') $opcCheckEspecial='checked';
				$status=resultadoSQL($consulta, 0, 'status');

				verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
				echo "<br>";

				verPlanos($modulo, $sub, $acao, $registro, $matriz);
				echo "<br>";

				# Motrar tabela de busca
				novaTabela2("[Planos - Cancelar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				//menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=matriz[id] value=$registro>
						<input type=hidden name=matriz[idVencimento] value=$idVencimento>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						<input type=hidden name=registro value=$idPessoaTipo>
						<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status Atual do Plano:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectStatus($status, 'status', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status de Cancelamento:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectStatusServicoAtivos('C', 'statusServico', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);

				htmlFechaLinha();
				fechaTabela();
			}
			else {
				# Registro n�o encontrado!
				# Mensagem de aviso
				$msg="Plano n�o encontrado! ";
				$url="?modulo=cadastros&sub=clientes&acao=procurar";
				aviso("Aviso", $msg, $url, 760);
			}

		}
		else {
			# Gravar
			$data = dataSistema();

			# Incluir Pessoa
			$gravaPlano = dbPlano($matriz, 'cancelar');

			if(!$gravaPlano) {
				# Excluir pessoa
				$msg="Ocorreu um erro na tentativa de gravar o Plano";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso de Erro", $msg, $url, '400');
			}
			else {

				# calcular servi�o adicional do cancelamento


				# cancelar servi�os
				cancelarTodosServicosPlanos($matriz, 'cancelar');

				# Selecionar Servi�os do Plano
				$msg="Plano cancelado com sucesso!";
				avisoNOURL("Confirma��o de Cancelamento", $msg, '400');
				echo "<br>";

				listarPlanos($modulo, $sub, 'listar', $matriz[idPessoaTipo], $matriz);

			}
		}
	}//permissoes
}




# fun��o para adicionar pessoa
function desativarPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	if(!$permissao[alterar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {

		# Selecionar Plano
		if(!$matriz[bntConfirmar]) {

			$consulta=buscaPlanos($registro, 'id','igual','id');

			if($consulta && contaConsulta($consulta)>0) {

				# Dados do plano
				$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
				$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
				$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$especial=resultadoSQL($consulta, 0, 'especial');
				if($especial=='S') $opcCheckEspecial='checked';
				$status=resultadoSQL($consulta, 0, 'status');

				verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
				echo "<br>";

				verPlanos($modulo, $sub, $acao, $registro, $matriz);
				echo "<br>";

				# Motrar tabela de busca
				novaTabela2("[Planos - Desativar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				//menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=matriz[id] value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						<input type=hidden name=registro value=$idPessoaTipo>
						<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectStatus($status, 'status', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);

				htmlFechaLinha();
				fechaTabela();
			}
			else {
				# Registro n�o encontrado!
				# Mensagem de aviso
				$msg="Plano n�o encontrado! ";
				$url="?modulo=cadastros&sub=clientes&acao=procurar";
				aviso("Aviso", $msg, $url, 760);
			}

		}
		else {
			# Gravar
			$data=dataSistema();

			# Incluir Pessoa
			$gravaPlano=dbPlano($matriz, 'desativar');

			if(!$gravaPlano) {
				# Excluir pessoa
				$msg="Ocorreu um erro na tentativa de gravar o Plano";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso de Erro", $msg, $url, '400');
			}
			else {

				# Selecionar Servi�os do Plano
				$msg="Plano desativado com sucesso!";
				avisoNOURL("Confirma��o de Inativa��o", $msg, '400');
				echo "<br>";

				listarPlanos($modulo, $sub, 'listar', $matriz[idPessoaTipo], $matriz);

			}
		}
	}//permissoes
}




# fun��o para adicionar pessoa
function ativarPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	if(!$permissao[alterar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {

		# Selecionar Plano
		if(!$matriz[bntConfirmar]) {

			$consulta=buscaPlanos($registro, 'id','igual','id');

			if($consulta && contaConsulta($consulta)>0) {

				# Dados do plano
				$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
				$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
				$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$especial=resultadoSQL($consulta, 0, 'especial');
				if($especial=='S') $opcCheckEspecial='checked';
				$status=resultadoSQL($consulta, 0, 'status');

				verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
				echo "<br>";

				verPlanos($modulo, $sub, $acao, $registro, $matriz);
				echo "<br>";

				# Motrar tabela de busca
				novaTabela2("[Planos - Ativar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				//menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=matriz[id] value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						<input type=hidden name=registro value=$idPessoaTipo>
						<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectStatus($status, 'status', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);

				htmlFechaLinha();
				fechaTabela();
			}
			else {
				# Registro n�o encontrado!
				# Mensagem de aviso
				$msg="Plano n�o encontrado! ";
				$url="?modulo=cadastros&sub=clientes&acao=procurar";
				aviso("Aviso", $msg, $url, 760);
			}

		}
		else {
			# Gravar
			$data=dataSistema();

			# Incluir Pessoa
			$gravaPlano=dbPlano($matriz, 'ativar');

			if(!$gravaPlano) {
				# Excluir pessoa
				$msg="Ocorreu um erro na tentativa de gravar o Plano";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso de Erro", $msg, $url, '400');
			}
			else {

				# Selecionar Servi�os do Plano
				$msg="Plano ativado com sucesso!";
				avisoNOURL("Confirma��o de Cancelamento", $msg, '400');
				echo "<br>";

				listarPlanos($modulo, $sub, 'listar', $matriz[idPessoaTipo], $matriz);

			}
		}
	}//permissoes
}



# fun��o para checagem de tipo de plano
function checkTipoPlano($tipo) {
	if($tipo && $tipo=='S') {
		$retorno="<span class=txtaviso>Especial</span>";
	}
	else {
		$retorno="<span class=txtok>Normal</span>";
	}

	return($retorno);
}



# fun��o para adicionar pessoa
function verPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessLogin, $tb;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');

	if(!$permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {

		# Procurar dados
		$consulta=buscaPlanos($registro, "id",'igual','id');

		if($consulta && contaConsulta($consulta)>0) {

			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
			$vencimento=dadosVencimento($idVencimento);
			$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$especial=resultadoSQL($consulta, 0, 'especial');
			$status=resultadoSQL($consulta, 0, 'status');
			$desconto=resultadoSQL($consulta, 0, 'desconto');

			# Motrar tabela de busca
			novaTabela2("[Informa��es sobre o Plano]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, 'abrir', $registro);
			#fim das opcoes adicionais
			itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Nome:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Vencimento:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm("$vencimento[descricao] [$vencimento[diaVencimento]] - Faturamento: $vencimento[diaFaturamento]", 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			$descDesconto = $desconto ? "<span class=txtcheck8> (com desconto)</span>" : "";
			
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Forma de Cobran�a:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectFormaCobranca($idFormaCobranca, '','check').$descDesconto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(converteData($dtCadastro, 'banco','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Tipo do Plano:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(checkTipoPlano($especial), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectStatus($status, '','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();

			fechaTabela();

		}
	}
}




# fun��o para adicionar pessoa
function abrirPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPlanos;

	# Recebe ID do Plano - Procurar por ID da Pessoa
	$consulta=buscaPlanos($registro, 'id','igual','id');

	if($consulta && contaConsulta($consulta)>0) {
		# prosseguir e mostrar pessoa e plano
		$idPessoa=resultadoSQL($consulta, 0, 'idPessoaTipo');

		# Ver dados da pessoa
		verPessoas('cadastros', 'clientes', 'ver', $idPessoa, $matriz);
		echo "<br>";

		# Ver dados do Plano
		verPlanos($modulo, $sub, $acao, $registro, $matriz);

		# Listar Servi�os do Plano
		listarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);

	}
	else {
		# Erro
		$msg="ERRO ao selecionar o Plano do Cliente!";
		$url="?modulo=cadastros&sub=clientes";
		aviso("Aviso", $msg, $url, 760);
	}
}



# Funcao para verifica��o de plano especial
function checkPlanoEspecial($idPlano) {
	$consulta=buscaPlanos($idPlano, 'id','igual','id');

	if($consulta && contaConsulta($consulta)>0) {
		$retorno=resultadoSQL($consulta, 0, 'especial');
	}

	return($retorno);
}



# Funcao para verifica��o de plano especial
function checkStatusPlano($idPlano) {

	$consulta=buscaPlanos($idPlano, 'id','igual','id');

	if($consulta && contaConsulta($consulta)>0) {
		$retorno=resultadoSQL($consulta, 0, 'status');
	}

	return($retorno);

}



# Fun��o para visualizar o plano  e mostrar novamente alista
function visualizarPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Buscar informa��es sobre pessoa do plano
	$consultaPlano=buscaPlanos($registro, 'id','igual','id');

	if($consultaPlano && contaConsulta($consultaPlano)>0) {
		$idPessoaTipo=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');

		listarPlanos('lancamentos', 'planos', 'listar', $idPessoaTipo, $matriz);

		echo "<br>";

		# Mostrar dados do plano
		verPlanos($modulo, $sub, $acao, $registro, $matriz);

	}


}



# Fun��o para totaliza��o de valores dos planos
function valorPlano($idPlano, $especial, $idVencimento, $sqlADD, $mes, $ano, $parametro) {
	global $tb, $conn;


	$data=dataSistema();
	$dtAtivacaoMinima="$ano-$mes-31";
	$valida_desconto=$parametro[valida_desconto];
	$colunas=$parametro[colunas];

	# Verificar o tipo de opera��o
	# Verificar se plano � especial
	# Totalizar Plano Especial
	$sql="
		SELECT
			$tb[ServicosPlanos].id id, 
			$tb[ServicosPlanos].idServico idServico, 
			$tb[ServicosPlanos].valor valor, 
			$tb[ServicosPlanos].dtCadastro dtCadastro, 
			$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
			$tb[ServicosPlanos].diasTrial diasTrial, 
			$tb[StatusServicos].cobranca cobranca, 
			$tb[Servicos].valor valorServico, 
			$tb[Servicos].nome nomeServico,
			$tb[TipoCobranca].proporcional proporcional, 
			$tb[TipoCobranca].forma formaCobranca, 
			$tb[TipoCobranca].tipo tipoCobranca 
		FROM
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[TipoCobranca], 
			$tb[StatusServicos]
		WHERE
			$tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
			AND $tb[ServicosPlanos].idServico = $tb[Servicos].id 
			AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
 			AND $tb[ServicosPlanos].idPlano=$idPlano
			$sqlADD";

	if($sql) $consulta=consultaSQL($sql, $conn);

	if($consulta && contaConsulta($consulta)>0) {

		$vencimento=dadosVencimento($idVencimento);
		$vencimento[mes]=$mes;
		$vencimento[ano]=$ano;

		if($consulta && contaConsulta($consulta)>0) {

			# Zerar vari�veis de totais
			$retorno=0;

			# checar todos os servi�os e somar
			for($a=0;$a<contaConsulta($consulta);$a++) {
				# Verificar se servi�os s�o proporcionais
				$idServicoPlano=resultadoSQL($consulta, $a, 'id');
				$idServico=resultadoSQL($consulta, $a, 'idServico');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');

				if($especial=='S') $valorServico=resultadoSQL($consulta, $a, 'valor');
				else $valorServico=resultadoSQL($consulta, $a, 'valorServico');

				$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
				$dtAtivacao=resultadoSQL($consulta, $a, 'dtAtivacao');

				if(formatarData($dtAtivacao)<=0) $dtAtivacao=$dtCadastro;

				$diasTrial=resultadoSQL($consulta, $a, 'diasTrial');
				$cobranca=resultadoSQL($consulta, $a, 'cobranca');
				$proporcional=resultadoSQL($consulta, $a, 'proporcional');
				$formaCobranca=resultadoSQL($consulta, $a, 'formaCobranca');
				$tipoCobranca=resultadoSQL($consulta, $a, 'tipoCobranca');


				if($formaCobranca=='mensal') {
					if($cobranca=='S') {
						# Verificar se servi�o tem valor Proporcional
						if($proporcional=='S') {
							# Calcular dias e valor proporcional
							# Data de Vencimento com dia de Faturamento n�o de Vencimento
							# Servi�o tem calculo baseado em data de Ativa��o e data de Faturamento
							# par proporcionalidade
							$tmpValor=calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $tipoCobranca);
							$retorno+=$tmpValor[valor];
						}
						else {
							# Verificar se servico nao esta em per�odo trial
							$tmpValor=calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $tipoCobranca);
							$retorno+=$tmpValor[valor];
						}
					}
				}
				//				elseif($formaCobranca=='anual') {
				elseif($formaCobranca=='anual' || $formaCobranca=='semestral' || $formaCobranca=='trimestral') {
					if($cobranca=='S') {
						# Cobrar servico - verificando anualidade
						$tmpValor=calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valorServico, $formaCobranca);
						$retorno+=$tmpValor[valor];
					}
				}

				# Informa��es sobre data de vencimento do cliente
				$anoAtivacao=substr($dtAtivacao, 0, 4);
				$mesAtivacao=substr($dtAtivacao, 5, 2);
				$diaAtivacao=substr($dtAtivacao, 8, 2);

				# Verificar Servicos Adicionais
				# Data de Vencimento com dia de Vencimento n�o de faturamento
				$dtVencimento=calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $mes, $ano);
				$retorno+=calculaServicosAdicionais($idServicoPlano, $dtVencimento);

				# Verificar Descontos
				# Data de Vencimento com dia de Vencimento n�o de faturamento
				$desconto=calculaDescontos($idServicoPlano, $dtVencimento);
				if($retorno > 0 && $tmpValor[valor] >= $desconto ) {
					$retorno-=$desconto;
				}
				else {
					# Verificar se existe desconto
					if($desconto) {
						# Verificar se desconto � maior do que o valor do servi�o
						if($tmpValor[valor] < $desconto) {
							if($valida_desconto=='S') {
								htmlAbreLinha($corFundo);
								itemLinhaTMNOURL("<span class=txtaviso>ATEN��O:</span> <span class=bold10>Desconto superior ao valor do servi�o:</b> <a href=?modulo=lancamentos&sub=planos&acao=descontosservico&registro=$idServicoPlano>$nomeServico</a><br>", 'left', 'middle', '100%', $corFundo, $colunas, 'tabfundo1');
								fechaLinhaTabela();
							}
						}
					}
				}
			}
		}
	}

	return(round($retorno,2));
}




# Fun��o para totaliza��o de valores dos planos - sem contar proporcionalidade
function valorPlanoBruto($idPlano, $especial, $idVencimento, $sqlADD, $mes, $ano, $parametro) {
	global $tb, $conn;


	$data=dataSistema();
	$dtAtivacaoMinima="$ano-$mes-31";

	# Verificar o tipo de opera��o
	# Verificar se plano � especial
	# Totalizar Plano Especial
	$sql="
		SELECT
			$tb[ServicosPlanos].id id, 
			$tb[ServicosPlanos].idServico idServico, 
			$tb[ServicosPlanos].valor valor, 
			$tb[ServicosPlanos].dtCadastro dtCadastro, 
			$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
			$tb[ServicosPlanos].diasTrial diasTrial, 
			$tb[StatusServicos].cobranca cobranca, 
			$tb[Servicos].valor valorServico, 
			$tb[Servicos].nome nomeServico,
			$tb[TipoCobranca].proporcional proporcional, 
			$tb[TipoCobranca].forma formaCobranca, 
			$tb[TipoCobranca].tipo tipoCobranca 
		FROM
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[TipoCobranca], 
			$tb[StatusServicos]
		WHERE
			$tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
			AND $tb[ServicosPlanos].idServico = $tb[Servicos].id 
			AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
 			AND $tb[ServicosPlanos].idPlano=$idPlano
			$sqlADD";

	if($sql) $consulta=consultaSQL($sql, $conn);

	if($consulta && contaConsulta($consulta)>0) {

		$vencimento=dadosVencimento($idVencimento);
		$vencimento[mes]=$mes;
		$vencimento[ano]=$ano;

		if($consulta && contaConsulta($consulta)>0) {

			# Zerar vari�veis de totais
			$retorno=0;

			# checar todos os servi�os e somar
			for($a=0;$a<contaConsulta($consulta);$a++) {
				# Verificar se servi�os s�o proporcionais
				$idServicoPlano=resultadoSQL($consulta, $a, 'id');
				$idServico=resultadoSQL($consulta, $a, 'idServico');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');

				if($especial=='S') $valorServico=resultadoSQL($consulta, $a, 'valor');
				else $valorServico=resultadoSQL($consulta, $a, 'valorServico');

				$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
				$dtAtivacao=resultadoSQL($consulta, $a, 'dtAtivacao');
				if(formatarData($dtAtivacao)<=0) $dtAtivacao=$dtCadastro;
				$diasTrial=resultadoSQL($consulta, $a, 'diasTrial');
				$cobranca=resultadoSQL($consulta, $a, 'cobranca');
				$proporcional=resultadoSQL($consulta, $a, 'proporcional');
				$formaCobranca=resultadoSQL($consulta, $a, 'formaCobranca');
				$tipoCobranca=resultadoSQL($consulta, $a, 'tipoCobranca');


				if($formaCobranca=='mensal') {
					if($cobranca=='S') {
						$tmpValor=calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico);
						$retorno+=$tmpValor[valor];
					}
				}
				//				elseif($formaCobranca=='anual') {
				elseif($formaCobranca=='anual' || $formaCobranca=='semestral' || $formaCobranca=='trimestral') {
					if($cobranca=='S') {
						# Cobrar servico - verificando anualidade
						$tmpValor=calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valorServico, $formaCobranca);
						$retorno+=$tmpValor[valor];
					}
				}

				# Informa��es sobre data de vencimento do cliente
				$anoAtivacao=substr($dtAtivacao, 0, 4);
				$mesAtivacao=substr($dtAtivacao, 5, 2);
				$diaAtivacao=substr($dtAtivacao, 8, 2);

				# Verificar Servicos Adicionais
				# Data de Vencimento com dia de Vencimento n�o de faturamento

				$dtVencimento=calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $mes, $ano);
				$retorno+=calculaServicosAdicionais($idServicoPlano, $dtVencimento);

				# Verificar Descontos
				# Data de Vencimento com dia de Vencimento n�o de faturamento
				//$desconto=calculaDescontos($idServicoPlano, $dtVencimento);
				if($retorno > 0 && $retorno >= $desconto ) {
					$retorno-=$desconto;
				}
			}
		}


	}
	return(round($retorno,2));
}

# Fun��o para totaliza��o de valores dos planos - sem contar proporcionalidade e sem contar servicos adicionais
# obs. nome da funcao em homenagem ao nosso grande companheiro Danilo .
function valorPlanoGrosso($idPlano, $especial, $idVencimento, $sqlADD, $mes, $ano, $parametro, $formaCob) {
	global $tb, $conn;


	$data=dataSistema();
	$dtAtivacaoMinima="$ano-$mes-31";

	# Verificar o tipo de opera��o
	# Verificar se plano � especial
	# Totalizar Plano Especial
	$sql="
		SELECT
			$tb[ServicosPlanos].id id, 
			$tb[ServicosPlanos].idServico idServico, 
			$tb[ServicosPlanos].valor valor, 
			$tb[ServicosPlanos].dtCadastro dtCadastro, 
			$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
			$tb[ServicosPlanos].diasTrial diasTrial, 
			$tb[StatusServicos].cobranca cobranca, 
			$tb[Servicos].valor valorServico, 
			$tb[Servicos].nome nomeServico,
			$tb[TipoCobranca].proporcional proporcional, 
			$tb[TipoCobranca].forma formaCobranca, 
			$tb[TipoCobranca].tipo tipoCobranca 
		FROM
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[TipoCobranca], 
			$tb[StatusServicos]
		WHERE
			$tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
			AND $tb[ServicosPlanos].idServico = $tb[Servicos].id 
			AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
 			AND $tb[ServicosPlanos].idPlano=$idPlano
			$sqlADD";

	if($sql) $consulta=consultaSQL($sql, $conn);

	if($consulta && contaConsulta($consulta)>0) {

		$vencimento=dadosVencimento($idVencimento);
		$vencimento[mes]=$mes;
		$vencimento[ano]=$ano;

		if($consulta && contaConsulta($consulta)>0) {

			# Zerar vari�veis de totais
			$retorno=0;

			# checar todos os servi�os e somar
			for($a=0;$a<contaConsulta($consulta);$a++) {
				# Verificar se servi�os s�o proporcionais
				$idServicoPlano=resultadoSQL($consulta, $a, 'id');
				$idServico=resultadoSQL($consulta, $a, 'idServico');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');

				if($especial=='S') $valorServico=resultadoSQL($consulta, $a, 'valor');
				else $valorServico=resultadoSQL($consulta, $a, 'valorServico');

				$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
				$dtAtivacao=resultadoSQL($consulta, $a, 'dtAtivacao');
				if(formatarData($dtAtivacao)<=0) $dtAtivacao=$dtCadastro;
				$diasTrial=resultadoSQL($consulta, $a, 'diasTrial');
				$cobranca=resultadoSQL($consulta, $a, 'cobranca');
				$proporcional=resultadoSQL($consulta, $a, 'proporcional');
				$formaCobranca=resultadoSQL($consulta, $a, 'formaCobranca');
				$tipoCobranca=resultadoSQL($consulta, $a, 'tipoCobranca');


				if($formaCobranca=='mensal' && $formaCob == 'mensal' ) {
					if($cobranca=='S') {
						$tmpValor=calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico);
						$retorno+=$tmpValor[valor];
					}
				}
				elseif(($formaCobranca=='anual' && $formaCob == 'anual')
				|| ($formaCobranca=='semestral' && $formaCob == 'semestral')
				|| ($formaCobranca=='trimestral' && $formaCob == 'trimestral')  ) {
					if($cobranca=='S') {
						# Cobrar servico - verificando anualidade
						$tmpValor=calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valorServico, $formaCobranca);
						$retorno+=$tmpValor[valor];
					}
				}

				# Informa��es sobre data de vencimento do cliente
				$anoAtivacao=substr($dtAtivacao, 0, 4);
				$mesAtivacao=substr($dtAtivacao, 5, 2);
				$diaAtivacao=substr($dtAtivacao, 8, 2);
			}
		}


	}
	return(round($retorno,2));
}


function valorPlanoCanceladoInativo($idPlano, $especial, $idVencimento, $sqlADD, $mes, $ano, $parametro) {
	global $tb, $conn;


	$data=dataSistema();
	$dtAtivacaoMinima="$ano-$mes-31";

	# Verificar o tipo de opera��o
	# Verificar se plano � especial
	# Totalizar Plano Especial
	$sql="
		SELECT
			$tb[ServicosPlanos].id id, 
			$tb[ServicosPlanos].idServico idServico, 
			$tb[ServicosPlanos].valor valor, 
			$tb[ServicosPlanos].dtCadastro dtCadastro, 
			$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
			$tb[ServicosPlanos].diasTrial diasTrial, 
			$tb[StatusServicos].cobranca cobranca, 
			$tb[Servicos].valor valorServico, 
			$tb[Servicos].nome nomeServico,
			$tb[TipoCobranca].proporcional proporcional, 
			$tb[TipoCobranca].forma formaCobranca, 
			$tb[TipoCobranca].tipo tipoCobranca 
		FROM
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[TipoCobranca], 
			$tb[StatusServicos]
		WHERE
			$tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
			AND $tb[ServicosPlanos].idServico = $tb[Servicos].id 
			AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
			AND $tb[ServicosPlanos].idPlano=$idPlano
			$sqlADD";

	if($sql) $consulta=consultaSQL($sql, $conn);

	if($consulta && contaConsulta($consulta)>0) {

		$vencimento=dadosVencimento($idVencimento);
		$vencimento[mes]=$mes;
		$vencimento[ano]=$ano;

		if($consulta && contaConsulta($consulta)>0) {

			# Zerar vari�veis de totais
			$retorno=0;

			# checar todos os servi�os e somar
			for($a=0;$a<contaConsulta($consulta);$a++) {
				# Verificar se servi�os s�o proporcionais
				$idServicoPlano=resultadoSQL($consulta, $a, 'id');
				$idServico=resultadoSQL($consulta, $a, 'idServico');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');

				# Verificar Servicos Adicionais
				# Data de Vencimento com dia de Vencimento n�o de faturamento
				$dtVencimentoCobranca=calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $mes, $ano);
				$dtVencimento=mktime(0,0,0,$vencimento[mes],$vencimento[diaVencimento],$vencimento[ano]);
				$dtVencimento=date('Y-m-d',$dtVencimento);

				$retorno+=calculaServicosAdicionais($idServicoPlano, $dtVencimentoCobranca);

				//echo "DEBUG: $dtVencimento/$dtVencimentoCobranca - $retorno - $nomeServico<br>";

				# Verificar Descontos
				# Data de Vencimento com dia de Vencimento n�o de faturamento
				$desconto=calculaDescontos($idServicoPlano, $dtVencimento);
				if($retorno > 0 && $retorno >= $desconto ) {
					$retorno-=$desconto;
				}
			}
		}
	}
	return(round($retorno,2));
}



# Fun��o para totaliza��o de valores dos planos - Adicionar Servi�o em ServicosPlanosDocumentosGerados
function valorPlanoDocumentosGerados($idPlano, $especial, $idVencimento, $sqlADD, $mes, $ano, $matriz) {
	global $tb, $conn;


	$data=dataSistema();
	$dtAtivacaoMinima="$ano-$mes-31";

	# Verificar o tipo de opera��o
	# Verificar se plano � especial
	# Totalizar Plano Especial
	$sql="
		SELECT
			$tb[ServicosPlanos].id id, 
			$tb[ServicosPlanos].idServico idServico, 
			$tb[ServicosPlanos].valor valor, 
			$tb[ServicosPlanos].dtCadastro dtCadastro, 
			$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
			$tb[ServicosPlanos].diasTrial diasTrial, 
			$tb[StatusServicos].cobranca cobranca, 
			$tb[Servicos].valor valorServico, 
			$tb[Servicos].nome nomeServico,
			$tb[TipoCobranca].proporcional proporcional, 
			$tb[TipoCobranca].forma formaCobranca, 
			$tb[TipoCobranca].tipo tipoCobranca 
		FROM
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[TipoCobranca], 
			$tb[StatusServicos]
		WHERE
			$tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
			AND $tb[ServicosPlanos].idServico = $tb[Servicos].id 
			AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
			AND $tb[ServicosPlanos].idPlano=$idPlano
			$sqlADD";

	if($sql) $consulta=consultaSQL($sql, $conn);

	if($consulta && contaConsulta($consulta)>0) {

		$vencimento=dadosVencimento($idVencimento);
		$vencimento[mes]=$mes;
		$vencimento[ano]=$ano;

		if($consulta && contaConsulta($consulta)>0) {

			# Zerar vari�veis de totais
			$retorno=0;

			# checar todos os servi�os e somar
			for($a=0;$a<contaConsulta($consulta);$a++) {
				# Verificar se servi�os s�o proporcionais
				$idServicoPlano=resultadoSQL($consulta, $a, 'id');
				$idServico=resultadoSQL($consulta, $a, 'idServico');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');

				if($especial=='S') $valorServico=resultadoSQL($consulta, $a, 'valor');
				else $valorServico=resultadoSQL($consulta, $a, 'valorServico');

				$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
				$dtAtivacao=resultadoSQL($consulta, $a, 'dtAtivacao');
				if(formatarData($dtAtivacao)<=0) $dtAtivacao=$dtCadastro;
				$diasTrial=resultadoSQL($consulta, $a, 'diasTrial');
				$cobranca=resultadoSQL($consulta, $a, 'cobranca');
				$proporcional=resultadoSQL($consulta, $a, 'proporcional');
				$formaCobranca=resultadoSQL($consulta, $a, 'formaCobranca');
				$tipoCobranca=resultadoSQL($consulta, $a, 'tipoCobranca');
				$retorno=0; //??????????????????????

				if($formaCobranca=='mensal') {
					if($cobranca=='S') {
						# Verificar se servi�o tem valor Proporcional
						if($proporcional=='S') {
							# Calcular dias e valor proporcional
							# Data de Vencimento com dia de Faturamento n�o de Vencimento
							# Servi�o tem calculo baseado em data de Ativa��o e data de Faturamento
							# par proporcionalidade
							$tmpValor=calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $tipoCobranca);
							$retorno+=$tmpValor[valor];
						}
						else {
							# Verificar se servico nao esta em per�odo trial
							$tmpValor=calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico);
							$retorno+=$tmpValor[valor];
						}
					}
				}
				//				elseif($formaCobranca=='anual') {
				elseif($formaCobranca=='anual' || $formaCobranca=='semestral' || $formaCobranca=='trimestral') {
					if($cobranca=='S') {
						# Cobrar servico - verificando anualidade
						$tmpValor=calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valorServico, $formaCobranca);
						$retorno+=$tmpValor[valor];
					}
				}

				# Informa��es sobre data de vencimento do cliente
				$anoAtivacao=substr($dtAtivacao, 0, 4);
				$mesAtivacao=substr($dtAtivacao, 5, 2);
				$diaAtivacao=substr($dtAtivacao, 8, 2);

				# Verificar Servicos Adicionais
				# Data de Vencimento com dia de Vencimento n�o de faturamento
				$dtVencimento=calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $mes, $ano);
				//echo "Servico Adicional: " . date('d/m/Y', $dtVencimento) . "<br>";
				$retorno+=calculaServicosAdicionais($idServicoPlano, $dtVencimento);

				# Verificar Descontos
				# Data de Vencimento com dia de Vencimento n�o de faturamento
				$desconto=calculaDescontos($idServicoPlano, $dtVencimento);
				if($retorno > 0 && $tmpValor[valor] >= $desconto ) {
					$retorno-=$desconto;
				}

				# Verificar se valor do servi�o � valido (maior do que valor minimo de faturamento)
				if($retorno) {
					$matriz[idServicoPlano]=$idServicoPlano;
					//$matriz[valor]=($tmpValor[valor] + $servicoAdicional - $desconto);
					$matriz[valor]=$retorno;
					//					echo "<BR> VALOR - $matriz[valor]<BR> RETORNO - $retorno";
					$gravaServicosPlanosDocumentosGerados=dbServicoPlanoDocumentoGerado($matriz, 'incluir');
				}
			}
		}
	}

	return($retorno);
}


# Mostrar calculos do plano
function calculosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Buscar informa��es sobre pessoa do plano
	$consultaPlano=buscaPlanos($registro, 'id','igual','id');

	if($consultaPlano && contaConsulta($consultaPlano)>0) {
		$idPessoaTipo=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');

		verPessoas('cadastros', 'clientes', 'ver' , $idPessoaTipo, $matriz);
		//listarPlanos('lancamentos', 'planos', 'listar', $idPessoaTipo, $matriz);
		echo "<br>";

		# Mostrar dados do plano
		verPlanos($modulo, $sub, $acao, $registro, $matriz);
		echo "<br>";

		# Informa��es de calculos do plano
		verCalculoPlano($modulo, $sub, $acao, $registro, $matriz);

	}

}



# Visualiza��o de calculos
function verCalculoPlano($modulo, $sub, $acao, $registro, $matriz) {

	global $tb, $conn, $corFundo, $corBorda;

	# Verificar dados do plano
	$consultaPlano=buscaPlanos($registro, 'id','igual','id');

	$data=dataSistema();

	if($consultaPlano && contaConsulta($consultaPlano)>0) {

		$idVencimento=resultadoSQL($consultaPlano, 0, 'idVencimento');
		$especial=resultadoSQL($consultaPlano, 0, 'especial');
		$vencimento=dadosVencimento($idVencimento);
		if(!$matriz[ano] || !$matriz[mes]) {
			$vencimento[ano]=$data[ano];
			$vencimento[mes]=$data[mes];
		}
		else {
			$vencimento[ano]=$matriz[ano];
			$vencimento[mes]=$matriz[mes];
		}

		# Totalizar Plano Especial
		$sql="
			SELECT
				$tb[ServicosPlanos].id id, 
				$tb[ServicosPlanos].idServico idServico, 
				$tb[ServicosPlanos].valor valor, 
				$tb[ServicosPlanos].dtCadastro dtCadastro, 
				$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
				$tb[ServicosPlanos].diasTrial diasTrial, 
				$tb[StatusServicos].cobranca cobranca, 
				$tb[Servicos].valor valorServico, 
				$tb[Servicos].nome nomeServico,
				$tb[TipoCobranca].proporcional proporcional, 
				$tb[TipoCobranca].forma formaCobranca, 
				$tb[TipoCobranca].tipo tipoCobranca 
			FROM
				$tb[Servicos], 
				$tb[ServicosPlanos], 
				$tb[TipoCobranca], 
				$tb[StatusServicos]
			WHERE
				$tb[ServicosPlanos].idServico = $tb[Servicos].id 
				AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id 
				AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
				AND $tb[ServicosPlanos].idPlano=$registro";

		$consulta=consultaSQL($sql, $conn);

		if($consulta && contaConsulta($consulta)>0) {

			# Cabe�alho
			# Motrar tabela de busca
			novaTabela("[Valores obtidos para Faturamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 7);
			# Zerar Totais
			$totalServico=0;
			$totalServicosAdicionais=0;
			$totalDescontos=0;
			$totalTotal=0;

			# checar todos os servi�os e somar
			for($a=0;$a<contaConsulta($consulta);$a++) {
				# Verificar se servi�os s�o proporcionais
				$idServicoPlano=resultadoSQL($consulta, $a, 'id');
				$idServico=resultadoSQL($consulta, $a, 'idServico');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');
				$nomeServico=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$idServicoPlano>$nomeServico</a>",'servicos');

				$valorServico=0;
				if($especial=='S') {
					$valorServico=resultadoSQL($consulta, $a, 'valor');
				}
				else {
					$valorServico=resultadoSQL($consulta, $a, 'valorServico');
				}

				$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
				$dtAtivacao=resultadoSQL($consulta, $a, 'dtAtivacao');
				if(formatarData($dtAtivacao)<=0) $dtAtivacao=$dtCadastro;
				$diasTrial=resultadoSQL($consulta, $a, 'diasTrial');
				$cobranca=resultadoSQL($consulta, $a, 'cobranca');
				$proporcional=resultadoSQL($consulta, $a, 'proporcional');
				$formaCobranca=resultadoSQL($consulta, $a, 'formaCobranca');
				$tipoCobranca=resultadoSQL($consulta, $a, 'tipoCobranca');

				if($formaCobranca=='mensal') {
					if($cobranca=='S') {
						# Verificar se servi�o tem valor Proporcional
						if($proporcional=='S') {
							# Calcular dias e valor proporcional
							$valor=calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $tipoCobranca);
						}
						else {
							$valor=calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico);
						}

						# Total servi�o
						$totalServicoBruto+=$valorServico;
						$totalServico+=$valor[valor];
					}
				}
				//					elseif($formaCobranca=='anual') {
				elseif( $formaCobranca=='anual' ||
				$formaCobranca=='semestral' ||
				$formaCobranca=='trimestral') {

					if($cobranca=='S') {
						# Cobrar servico - verificando anualidade
						$valor=calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valorServico, $formaCobranca);

						# Total servi�o
						$totalServicoBruto+=$valorServico;
						$totalServico+=$valor[valor];
					}
				}

				# Verificar Servicos Adicionais
				$dtVencimento=mktime(0,0,0, $data[mes], $data[dia], $data[ano]);
				$dtVencimentoServico=mktime(0,0,0, $vencimento[mes], $vencimento[diaVencimento], $vencimento[ano]);

				# Verificar dias de uso do servi�o
				if($proporcional=='S') {
					# Informa��es sobre data de vencimento do cliente
					$anoAtivacao=substr($dtAtivacao, 0, 4);
					$mesAtivacao=substr($dtAtivacao, 5, 2);
					$diaAtivacao=substr($dtAtivacao, 8, 2);

					$dtInicioCliente=mktime(0,0,0,$mesAtivacao, $diaAtivacao+$diasTrial, $anoAtivacao);
					$qtdeDias=$valor[dias];
				}

				# Servi�os Adicionais
				$dtVencimentoServico=calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
				$servicosAdicionais=calculaServicosAdicionais($idServicoPlano, $dtVencimentoServico);
				$totalServicosAdicionais+=$servicosAdicionais;

				# Verificar Descontos
				$descontos=calculaDescontos($idServicoPlano, $dtVencimentoServico);
				$totalDescontos+=$descontos;

				# Sub Total do servi�o
				if($cobranca=='S') $total=( ( $valor[valor] + $servicosAdicionais ) - $descontos );
				else $total=( $servicosAdicionais - $descontos );

				$totalTotal+=$total;

				# Cabe�alho
				if($cobranca=='S' && $total) {
					novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('Servi�o', 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Utiliza��o', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Valor do Servi�o', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Valor Calculado', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Servi�os Adicionais', 'center', '15%', 'tabfundo0');
					itemLinhaTabela('Descontos', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Total', 'center', '15%', 'tabfundo0');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("<b>$nomeServico</b>", 'left', '30%', 'tabfundo1');
					itemLinhaTabela("<span class=txtaviso>$valor[descricao]</span>", 'center', '10%', 'tabfundo1');
					itemLinhaTabela("<span class=bold10>".formatarValoresForm($valorServico)."</span>", 'center', '10%', 'tabfundo1');
					itemLinhaTabela("<span class=txtcheck>".formatarValoresForm($valor[valor])."</span>", 'center', '10%', 'tabfundo1');
					itemLinhaTabela("<span class=txtok>".formatarValoresForm($servicosAdicionais)."</span>", 'center', '15%', 'tabfundo1');
					itemLinhaTabela("<span class=txtaviso>".formatarValoresForm($descontos)."</span>", 'center', '10%', 'tabfundo1');
					itemLinhaTabela("<span class=txtok>".formatarValoresForm($total)."</span>", 'center', '15%', 'tabfundo1');
					fechaLinhaTabela();
				}

				if($servicosAdicionais > 0 || $descontos > 0) {

					if($cobranca=='N') {
						novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela('Servi�o', 'center', '30%', 'tabfundo0');
						itemLinhaTabela('Utiliza��o', 'center', '10%', 'tabfundo0');
						itemLinhaTabela('Valor do Servi�o', 'center', '10%', 'tabfundo0');
						itemLinhaTabela('Valor Calculado', 'center', '10%', 'tabfundo0');
						itemLinhaTabela('Servi�os Adicionais', 'center', '15%', 'tabfundo0');
						itemLinhaTabela('Descontos', 'center', '10%', 'tabfundo0');
						itemLinhaTabela('Total', 'center', '15%', 'tabfundo0');
						fechaLinhaTabela();
					}
					novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'left', $corFundo, 7, 'normal10');
					# Listar os servicos Adicionais
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);

					# Verificar se existem servi�os Adicionais e listar
					if($servicosAdicionais>0) {
						listarServicosAdicionaisVencimento($idServicoPlano, $dtVencimentoServico);
					}

					# Verificar se existem Descontos e Listar
					if($descontos>0) {
						# listar descontos
						listarDescontosVencimento($idServicoPlano, $dtVencimentoServico);
					}

					fechaTabela();

					htmlFechaColuna();
					fechaLinhaTabela();
				}

			}

			# Totalizar
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Total:</b>', 'right', 'middle', '40%', $corFundo, 2, 'tabfundo0');
			itemLinhaTabela("<span class=bold10>".formatarValoresForm($totalServicoBruto)."</span>", 'center', '10%', 'tabfundo0');
			itemLinhaTabela("<span class=txtcheck>".formatarValoresForm($totalServico)."</span>", 'center', '10%', 'tabfundo0');
			itemLinhaTabela("<span class=txtok>".formatarValoresForm($totalServicosAdicionais)."</span>", 'center', '15%', 'tabfundo0');
			itemLinhaTabela("<span class=txtaviso>".formatarValoresForm($totalDescontos)."</span>", 'center', '10%', 'tabfundo0');
			itemLinhaTabela("<span class=txtok>".formatarValoresForm($totalTotal)."</span>", 'center', '15%', 'tabfundo0');
			fechaLinhaTabela();

			fechaTabela();
		}
	}


}



/*
# Visualiza��o de calculos
function calculoTotalPlano($registro, $mes , $ano) {

global $tb, $conn, $corFundo, $corBorda;

# Verificar dados do plano
$consultaPlano=buscaPlanos($registro, 'id','igual','id');

if($consultaPlano && contaConsulta($consultaPlano)>0) {

$idVencimento=resultadoSQL($consultaPlano, 0, 'idVencimento');
$especial=resultadoSQL($consultaPlano, 0, 'especial');
$vencimento=dadosVencimento($idVencimento);


# Totalizar Plano Especial
$sql="
SELECT
$tb[ServicosPlanos].id id,
$tb[ServicosPlanos].idServico idServico,
$tb[ServicosPlanos].valor valor,
$tb[ServicosPlanos].dtCadastro dtCadastro,
$tb[ServicosPlanos].dtAtivacao dtAtivacao,
$tb[ServicosPlanos].diasTrial diasTrial,
$tb[StatusServicos].cobranca cobranca,
$tb[Servicos].valor valorServico,
$tb[Servicos].nome nomeServico,
$tb[TipoCobranca].proporcional proporcional,
$tb[TipoCobranca].forma formaCobranca,
$tb[TipoCobranca].tipo tipoCobranca
FROM
$tb[Servicos],
$tb[ServicosPlanos],
$tb[TipoCobranca],
$tb[StatusServicos]
WHERE
$tb[ServicosPlanos].idServico = $tb[Servicos].id
AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id
AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id
AND cobranca='S'
AND $tb[ServicosPlanos].idPlano=$registro";

$consulta=consultaSQL($sql, $conn);

if($consulta && contaConsulta($consulta)>0) {

# checar todos os servi�os e somar
for($a=0;$a<contaConsulta($consulta);$a++) {
# Verificar se servi�os s�o proporcionais
$idServicoPlano=resultadoSQL($consulta, $a, 'id');
$idServico=resultadoSQL($consulta, $a, 'idServico');

if($especial=='S') $valorServico=resultadoSQL($consulta, $a, 'valor');
else $valorServico=resultadoSQL($consulta, $a, 'valorServico');

$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
$dtAtivacao=resultadoSQL($consulta, $a, 'dtAtivacao');
$diasTrial=resultadoSQL($consulta, $a, 'diasTrial');
$cobranca=resultadoSQL($consulta, $a, 'cobranca');
$proporcional=resultadoSQL($consulta, $a, 'proporcional');
$formaCobranca=resultadoSQL($consulta, $a, 'formaCobranca');
$tipoCobranca=resultadoSQL($consulta, $a, 'tipoCobranca');

# Verificar Servicos Adicionais
$dtVencimento=mktime(0,0,0, $mes, $vencimento[diaFaturamento], $ano);

# Verificar se servi�o tem valor Proporcional
if($proporcional=='S') {
# Calcular dias e valor proporcional
$valor=calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $mes, $ano);

# Informa��es sobre data de vencimento do cliente
$anoAtivacao=substr($dtAtivacao, 0, 4);
$mesAtivacao=substr($dtAtivacao, 5, 2);
$diaAtivacao=substr($dtAtivacao, 8, 2);

$dtInicioCliente=mktime(0,0,0,$mesAtivacao, $diaAtivacao+$diasTrial, $anoAtivacao);
$qtdeDias=($dtVencimento - $dtInicioCliente)/60/60/24;

# Total servi�o
$totalServicoBruto+=$valorServico;
$totalServico+=$valor[valor];

}
else {
# Total servi�o
$totalServicoBruto+=$valorServico;
$totalServico+=$valorServico;

$qtdeDias='<span class=txtaviso>total</span>';

}

# Servi�os Adicionais
$servicosAdicionais=calculaServicosAdicionais($idServicoPlano, $dtVencimento);
$totalServicosAdicionais+=$servicosAdicionais;

# Verificar Descontos
$descontos=calculaDescontos($idServicoPlano, $dtVencimento);
$totalDescontos+=$descontos;

# Sub Total do servi�o
$total=( ( $totalServico + $servicosAdicionais ) - $descontos );

$totalTotal+=$total;
}

}

# Totais
$retorno[totalGeral]=$totalTotal;
$retorno[totalDescontos]=$totalDescontos;
$retorno[totalServicosAdicionais]=$totalServicosAdicionais;

}
return($retorno);
}
*/


# Fun��o para buscar dados do plano
function dadosPlanos($idPlano) {

	$consulta=buscaPlanos($idPlano, 'id','igual','id');

	if($consulta && contaConsulta($consulta)>0) {
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[especial]=resultadoSQL($consulta, 0, 'especial');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$retorno[idVencimento]=resultadoSQL($consulta, 0, 'idVencimento');
		$retorno[idFormaCobranca]=resultadoSQL($consulta, 0, 'idFormaCobranca');
		$retorno[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
		$retorno[desconto]=resultadoSQL($consulta, 0, 'desconto');
	}

	return($retorno);
}

function formSelectPlanos ($tipo, $campo, $valor, $filtro) {

	if($tipo=='form') {

		if ($filtro) 	$consulta = buscaPlanos($filtro. " AND status != 'C' " , '', 'custom', 'nome');
		else			$consulta=buscaPlanos('', '', 'todos', 'nome');

		if($consulta && contaConsulta($consulta)>0) {

			$retorno="<select name=matriz[$campo] class=normal8>\n";
			$linha=0;
			for($a=0;$a<contaConsulta($consulta);$a++) {

				$idPlano=resultadoSQL($consulta, $a, 'id');
				$nomePlano=resultadoSQL($consulta, $a, 'nome');

				if($id==$valor) $opcSelect='selected';
				else $opcSelect='';

				$retorno.="<option value=$idPlano $opcSelect>Plano: $nomePlano";

			}

			$retorno.="</select>";
		}
		else $retorno = "Este cliente n�o possui nenhum plano ativo";
	}

	return ($retorno);
}

# Fun��o adicional para montar um select listando os nomes dos Planos de acordo
# com o registro da Nota Fiscal
function formSelectPlanosNF($tipo, $campo, $valor, $registro){

	global $tb, $conn;

	if($tipo == "form"){
		//Consultando Planos do Cliente
		$sql = "SELECT $tb[PlanosPessoas].id, $tb[PlanosPessoas].nome FROM $tb[PlanosPessoas]
					 INNER JOIN $tb[PessoasTipos] 
					 ON ($tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id) 
					 INNER JOIN $tb[NotaFiscal] 
					 ON ($tb[NotaFiscal].idPessoaTipo = $tb[PessoasTipos].id) 
					 WHERE $tb[NotaFiscal].id = $registro AND $tb[PlanosPessoas].status <> 'C'";
		$resultado = consultaSQL($sql, $conn);

		if(mysql_num_rows($resultado) > 0){
			$retorno = "<select name='matriz[$campo]' class=normal8>\n";
			$linha = 0;
			for($i = 0; $i < mysql_num_rows($resultado); $i ++){
				$dadosPlanos = mysql_fetch_array($resultado);
				$idPlano = $dadosPlanos['id'];
				$nomePlano = $dadosPlanos['nome'];

				if($registro == $valor){
					$opcSelect = 'selected';
				}
				else{
					$opcSelect = '';
				}
				$retorno .= "<option value=$idPlano $opcSelect>Plano: $nomePlano</option>\n";
			}
			$retorno .= "</select>";
		}
		else{
			echo "Este cliente n�o possui nenhum plano ativo";
		}
	}
	return $retorno;
}

function buscaServicoMaiorValor($idPlano, $n=0){
	global $tb, $conn;

	$plano = dadosPlanos($idPlano);

	if ($plano['especial'] == 'S')
	$valor = $tb[ServicosPlanos].'.valor';
	else
	$valor = $tb[Servicos].'.valor';

	$idStatus = implode(', ', statusServicosCobradosId() );

	$sql = "Select " .
	"	$tb[ServicosPlanos].id, " .
	"	$tb[Servicos].nome, " .
	"	$valor  " .
	" From " .
	"	$tb[ServicosPlanos] " .
	" INNER JOIN $tb[Servicos] " .
	"	ON	($tb[ServicosPlanos].idServico = $tb[Servicos].id) " .
	" WHERE " .
	"	$tb[ServicosPlanos].idPlano='$idPlano '" .
	"	AND $tb[ServicosPlanos].idStatus in ($idStatus)" .
	" ORDER BY " .
	"	valor desc " .
	" limit $n, 1";

	$cons = consultaSQL($sql, $conn);

	if (contaConsulta($cons)){
		$retorno['idServicoPlano']	= resultadoSQL($cons, 0, 'id');
		$retorno['nome']			= resultadoSQL($cons, 0, 'nome');
		$retorno['valor']			= resultadoSQL($cons, 0, 'valor');
	}

	return $retorno;
}

/**
 *    Ativa / Inativa os servicos do cliente de acordo com o selecionado
 * pelo usu�rio. Caso o servico seja IVR, e exista o relacionamento com 
 * o WT, � enviado uma String para o servidor e o cliente � Ativado / 
 * Inativado no WT. E caso o servico esteja relacionado com um dom�nio o
 * mesmo insere um comando na fila do manager para Ativar / Inativar o 
 * acesso do cliente.
 *
 * @param String $modulo
 * @param String $sub
 * @param String $acao
 * @param int 	 $registro
 * @param array  $matriz
 */
function ativarInativarClienteServico($modulo, $sub, $acao, $registro, $matriz) {
	/* Vari�veis globais de configuracao */
	global $corFundo, $corBorda, $conn, $k, $x;

	/* Seleciona os Servicos de IVR do Cliente */
	$consultaServicosIVR = selecionaServicosIVR($registro);

	/* Seleciona os Servicos do cliente */
	$consultaServicos = selecionaServicosClientes($registro);

	/* Caso o bot�o Confirmar ainda n�o tenha sido precionado */
	if (!$matriz[btnConfirmarAtualizacao]) {
		if (contaConsulta($consultaServicos) > 0 || contaConsulta($consultaServicosIVR) > 0) {
			/* Form */
			abreFormulario("formAtivarInativarServico", "index.php", "POST");
			inputType("hidden", "modulo", $modulo);
			inputType("hidden", "sub", $sub);
			inputType("hidden", "acao", "ativarInativarClienteServico");
			inputType("hidden", "registro", "$registro");

			/* Tabela para exibir os servicos e status */
			$campos['nome'] 		= array("Status do Servi�o", "Servidor", "Servi�os", "Valor - R$", "Status Servidor", "Status");
			$campos['alinhamento']	= array("center", "center", "center", "center", "center", "center");
			$campos['tamanho']		= array("16%", "10%", "36%", "10%", "14%", "14%");
			$campos['classeCSS']	= array("tabfundo0", "tabfundo0", "tabfundo0", "tabfundo0", "tabfundo0", "tabfundo0");
			montaTabela("[Servi�os dos Clientes]", $campos, "center", "100%", 0, 2, 1, $corFundo, $corBorda, 6, "100%");

			/* Consulta os Servicos IVR do Cliente */
			$servicoIVR = isServicoIVR($consultaServicos, $consultaServicosIVR);

			/* Consulta os servicos de dominios do Cliente retornando um array */
			$servicoDominio = preparaArrayDominio(selecionaDominioCliente($registro));

			/* Contador para os dominios */
			$x = 0;

			/* Contador para os servicos */
			$j = 0;

			/* Contador global para o checkbox IVR */
			$k = 0;

			for ($i = 0; $i < contaConsulta($consultaServicos); $i++) {
				$status = resultadoSQL($consultaServicos, $i, "status");
				$idServicoPlano = resultadoSQL($consultaServicos, $i, 'id');
				$nomeServico = resultadoSQL($consultaServicos, $i, "nome");
				$valorServico = resultadoSQL($consultaServicos, $i, "valor");
				$especial = resultadoSQL($consultaServicos, $i, "especial");

				/* Status do servico */
				if ($status == "A") {
					$statusServico = "<font color=darkgreen>Ativo</font>";
					$acaoServico = "<input type=checkbox name='" . $j++ . "' value='inativar:$idServicoPlano'> Inativar";
					$botaConfirmar = true;
				} elseif ($status == "I") {
					$statusServico = "<font color=brwon>Inativo";
					$acaoServico = "<input type=checkbox name='" . $j++ . "' value='ativar:$idServicoPlano'> Ativar";
					$botaConfirmar = true;
				} elseif ($status == "C") {
					$statusServico = "Cancelado";
					$acaoServico = "";
					$acaoWT = "";
				} else {
					$statusServico = "Aguardando";
					$acaoServico = "<input type=checkbox name='" . $j++ . "' value='ativar:$idServicoPlano'> Ativar";
					$botaConfirmar = true;
				}
				
				/* N�o faz sentido Ativar / Inativar uma taxa de cobranca */
				if ($nomeServico == "Cobran�a de Instala��o") {
					$acaoServico = "";
					$acaoWT = "";
				}

				/* Status e checkbox do WT */
				if ($servicoIVR) {
					foreach ($servicoIVR as $ivr) {
						if ($ivr["id"] == $idServicoPlano) {
							$acaoWT = montaCheckBoxServicoIVR($idServicoPlano, $ivr["status"]);
							$statusWT = montaStatusServicoIVR($ivr["status"]);
							$botaConfirmar = true;
						}
					}
				}

				/* Recupera o status do dominio e monta o checkbox e status */
				if ($servicoDominio) {
					foreach ($servicoDominio as $dominio) {
						if ($dominio['idServicosPlanos'] == $idServicoPlano) {
							$nomeServico = $nomeServico . " ($dominio[nome])";
							$acaoWT = montaCheckBoxDominio($dominio["idDominio"], $dominio["status"]);
							$statusWT = montaStatusDominio($dominio["status"]);
							$botaConfirmar = true;
						}
					}
				}

				/* Verifica se o servico n�o � especial */
				if ($especial != "S") {
					$valorServico = resultadoSQL($consultaServicos, $i, "valorServico");
				}

				/* Tabela de exibic�o dos Servicos */
				novaLinhaTabela($corFundo, "100%");
				itemLinhaTabela($acaoServico, "left", "16%", "normal10");
				itemLinhaTabela($acaoWT, "left", "10%", "normal10");
				itemLinhaTabela($nomeServico, "center", "36%", "normal10");
				itemLinhaTabela($valorServico, "right", "10%", "normal10");
				itemLinhaTabela($statusWT, "center", "14%", "txtaviso");
				itemLinhaTabela($statusServico, "center", "14%", "txtaviso");
				fechaLinhaTabela();

				/* Limpando as vari�veis */
				$acaoWT = "";
				$statusWT = "";
			}
			fechaTabela();
			inputType("hidden", "limiteCampoServico", $i);
			inputType("hidden", "limiteCampoServicoIVR", $k);
			inputType("hidden", "limiteCampoDominio", $x);

			/* Opc�es extras */
			$campoFaturamentoAvulso	= "<input type='checkbox' name='gerarFaturamentoAvulso' value='true'> Gerar Faturamento Avulso";
//			$campoBoletoAvulso		= "<input type='checkbox' name='gerarBoletoAvulso' value='true'> Gerar Boleto Avulso";
			$campos['nome'] 		= array("Op��o", "Descri��o");
			$campos['alinhamento']	= array("center", "center");
			$campos['tamanho']		= array("30%", "70%");
			$campos['classeCSS']	= array("tabfundo0", "tabfundo0");
			montaTabela("[Op��es extras]", $campos, "center", "100%", "0", "2", "1", $corFundo, $corBorda, 2, "100%");
			novaLinhaTabela($corFundo, "100%");
			itemLinhaTabela($campoFaturamentoAvulso, "left", "30%", "normal10");
			itemLinhaTabela("Gera um faturamento avulso para os servi�os a serem ativados.", "left", "70%", "normal10");
			fechaLinhaTabela();
			
//			novaLinhaTabela($corFundo, "100%")			;
//			itemLinhaTabela($campoBoletoAvulso, "left", "30%", "normal10");
//			itemLinhaTabela("Gera um boleto avulso para os servi�os a serem ativados.", "left", "70%", "normal10");
//			fechaLinhaTabela();
			fechaTabela();

			/* Bot�o confirmar */
			if ($botaConfirmar) {
				botaoConfirmar('submit', 'matriz[btnConfirmarAtualizacao]', 'Confirmar', 'submit');
			}
			fechaFormulario();
		} else {
			/* Mensagem de aviso de nenhum servico encontrado */
			echo "<br>";
			$msg="N�o foi encontrado nenhum servi�o para este cliente.";
			$url="javascript:history.back();";
			aviso("Aviso de Erro", $msg, $url, '400');
			echo "<br>";
		}

	} else {
		/* Caso o bot�o confirmar tenha sido precionado */
		$limiteCampoServico = $_REQUEST['limiteCampoServico'];
		$limiteCampoServicoIVR = $_REQUEST['limiteCampoServicoIVR'];
		$limiteCampoDominio = $_REQUEST['limiteCampoDominio'];

		/* Entra nesse if caso tenha algum Servico para ativar/inativar */
		if ($limiteCampoServico > 0 || $limiteCampoServicoIVR > 0 || $limiteCampoDominio > 0) {
			/* Ativar/Inativar ServicoIVR no WT */
			if ($limiteCampoServicoIVR > 0) {
				for ($i = 0; $i < $limiteCampoServicoIVR; $i++) {
					$request = explode(":",$_REQUEST["ivr:$i"]);
					$acao = $request[0];
					$idServicoPlano = $request[1];
					$matriz["idServicoIVR"] = resultadoSQL($consultaServicosIVR, 0, "id");
					
					if ($acao == "ativar") {
						$clienteWT = ativarClienteWT($matriz);
					} elseif ($acao == "inativar") {
						$clienteWT = inativarClienteWT($matriz);
					}
				}
			}

			$servicoErro			= true;
			$dominioErro			= false;
			$faturamentoIncluido	= false;
			/**
			 *  1 - Ativar/Inativar ServicoIVR no ISP 
			 *  2 - Gerar Faturamento Avulso
			 * 
			 * $clienteWT = 0 -> Retorno de ok do WT
			*/

			$data = dataSistema();
			/* Comentado, pois estava gerando problemas na hora de 
			   somar o valor total do plano */
//			$matriz["dtAtivacao"] = $data['dataBanco'];
			$idStatusAtivar 	  = resultadoSQL(consultaSQL("SELECT * FROM StatusServicosPlanos WHERE cobranca = 'S' AND status = 'A'", $conn), 0, 'id');
			$idStatusInativar 	  = resultadoSQL(consultaSQL("SELECT * FROM StatusServicosPlanos WHERE cobranca = 'N' AND status = 'I'", $conn), 0, 'id');

			/* IMPLEMENTAR OPC�O DE N�O GERAR UM FATURAMENTO AVULSO */
			if ($_REQUEST["gerarFaturamentoAvulso"]) {
				/* Gerar Faturamento Avulso */
				/* Populando os atributos da tabela Faturamentos */
				$dadosFaturamento 		  = dadosFaturamento(buscaIdFaturamento($data));
				$matriz["idFaturamento"]  = $dadosFaturamento["id"];
				$matriz["descricao"]      = "Faturamento avulso gerado referente � data $data[dia]/$data[mes]/$data[ano]";
				$matriz["dataBanco"]	  = $data["dataBanco"];
				$matriz["servico"]		  = 0; //resultadoSQL(consultaSQL("SELECT idServico FROM ServicosPlanos WHERE id = '$matriz[id]'", $conn), 0, 'idServico');
				$matriz["pop"]			  = resultadoSQL(consultaSQL("SELECT Pop.id FROM Pessoas, PessoasTipos, Pop WHERE PessoasTipos.id = '" . $registro . "' AND Pessoas.id = PessoasTipos.idPessoa AND Pop.id = Pessoas.idPop", $conn), 0, 'id');
				/* ARRUMAR ESSE SELECT PARA PEGAR id DA Duplicata Simples QUANDO FOR PARA GERAR BOLETO, E id DA Duplicata Registrada QUANDO FOR PARA GERAR ARQUIVO REMESSA */
				/* SELECT FormaCobranca.id FROM TipoCarteira, FormaCobranca WHERE TipoCarteira.nome = 'Duplicata Simples' AND TipoCarteira.id = FormaCobranca.idTipoCarteira; */
				/* SELECT FormaCobranca.id FROM TipoCarteira, FormaCobranca WHERE TipoCarteira.nome = 'Duplicata Registrada' AND TipoCarteira.id = FormaCobranca.idTipoCarteira; */
//				$matriz["forma_cobranca"] = resultadoSQL(consultaSQL("SELECT idFormaCobranca FROM PlanosPessoas WHERE PlanosPessoas.idPessoaTipo = '" . $registro . "'", $conn), 0, 'idFormaCobranca');
				$matriz["forma_cobranca"] = resultadoSQL(consultaSQL("SELECT FormaCobranca.id FROM TipoCarteira, FormaCobranca WHERE TipoCarteira.nome = 'Duplicata Registrada' AND TipoCarteira.id = FormaCobranca.idTipoCarteira", $conn), 0, "id");
				$matriz["vencimento"]	  = resultadoSQL(consultaSQL("SELECT idVencimento FROM PlanosPessoas WHERE PlanosPessoas.idPessoaTipo = '" . $registro . "'", $conn), 0, 'idVencimento');
				$matriz["mes"]			  = $data["mes"];
				$matriz["ano"]			  = $data["ano"];
			}
			/** Ativa/Inativa cada servico selecionado pelo usu�rio e insere nas tabelas:
				 *  - DocumentosGerados
				 *  - PlanosDocumentosGerados
				 *  - ServicosPlanosDocumentosGerados
				 */
			for ($i = 0; $i < $limiteCampoServico; $i++) {
				/* Retira acao e id da tabela ServicosPlanos */
				$request = explode(":",$_REQUEST[$i]);
				/* Acao: ativar/inativar */
				$acao = $request[0];
				/* id da tabela ServicosPlanos */
				$matriz["id"] = $request[1];
				$matriz["idServicoPlano"] = $request[1];

				if ($acao == "ativar") {

					$matriz["idStatus"] = $idStatusAtivar;
					$ativarServico = dbServicosPlano($matriz, 'ativarCliente', $registro);

					if ($ativarServico) {
						$servicoErro = false;

						/* Atributo para a tabela PlanosDocumentosGerados e para recuperar o valor do servico */
						$planosDocumentosGerados["idPlanoDocumentoGerado"] = novoIDPlanoDocumentoGerado();
						$planosDocumentosGerados["idPlano"] = resultadoSQL(consultaSQL("SELECT idPlano FROM ServicosPlanos WHERE id = '". $matriz["idServicoPlano"] ."'", $conn), 0, "idPlano");
						
						/* Atributos para a tabela ServicosPlanosDocumentosGerados */
						$servicosPlanosDocumentosGerados["idPlanoDocumentoGerado"] = $planosDocumentosGerados["idPlanoDocumentoGerado"];
						$servicosPlanosDocumentosGerados["idServicoPlano"] = $matriz["idServicoPlano"];
						$especial = resultadoSQL(consultaSQL("SELECT especial FROM  PlanosPessoas,ServicosPlanos WHERE ServicosPlanos.id = '$servicosPlanosDocumentosGerados[idServicoPlano]' AND PlanosPessoas.id = idPlano", $conn), 0, "especial");
						$valorServicoPlano = calculaValorServicosPlanos($planosDocumentosGerados["idPlano"], $especial, $matriz["vencimento"], "AND ServicosPlanos.id = '". $matriz['idServicoPlano'] ."'", $matriz["mes"], $matriz["ano"], $servicosPlanosDocumentosGerados);
						$servicosPlanosDocumentosGerados["valor"] = $valorServicoPlano;
							
						/* Caso esteja selecionado o checkbox para gerar faturamento avulso */
						if ($_REQUEST["gerarFaturamentoAvulso"] && $servicosPlanosDocumentosGerados["valor"] > 0) {
							/* Inclui dados na tabela Faturamentos caso o pop, a forma de cobranca e o vencimento seja diferente do ultimo faturamento avulso lancado */
							if ($matriz["pop"] != $dadosFaturamento["idPOP"] || $matriz["forma_cobranca"] != $dadosFaturamento["idFormaCobranca"] || $matriz["vencimento"] != $dadosFaturamento["idVencimento"]) {
								/* Para n�o incluir o faturamento mais de uma vez */
								if (!$faturamentoIncluido) {
									$matriz["idFaturamento"] = novoIDFaturamento();
									$incluirFaturamento = dbFaturamento($matriz, 'incluir', 'A');
									$faturamentoIncluido = true;
								}
							}
							
							/* Atributos para a tabela DocumentosGerados */
							$documentosGerados["idDocumentoGerado"] = novoIDDocumentoGerado();
							$documentosGerados["idFaturamento"] = $matriz["idFaturamento"];
							$documentosGerados["idPessoaTipo"] = $registro;
							$documentosGerados["status"] = "I";

							/* Inserir dados na tabela DocumentosGerados */
							$gravaDocumentosGerados = dbDocumentoGerado($documentosGerados, "incluirAvulso");

							/* Atributos para a tabela PlanosDocumentosGeradas. */
							$planosDocumentosGerados["idDocumentoGerado"] = $documentosGerados["idDocumentoGerado"];
							$planosDocumentosGerados["idFormaCobranca"] = $matriz["forma_cobranca"];
							$planosDocumentosGerados["idVencimento"] = $matriz["vencimento"];
							$dadosVencimento = dadosVencimento($planosDocumentosGerados["idVencimento"]);

							if($dadosVencimento["diaVencimento"] <= $dadosVencimento["diaFaturamento"]) {
								$dtVencimentoDocumento=mktime(0, 0, 0, ($matriz["mes"]+1), $dadosVencimento["diaVencimento"], $matriz["ano"]);
								$dtVencimentoDocumento=date('Y-m-d', $dtVencimentoDocumento);
							}
							else {
								$dtVencimentoDocumento=mktime(0, 0, 0, ($matriz["mes"]), $dadosVencimento["diaVencimento"], $matriz["ano"]);
								$dtVencimentoDocumento=date('Y-m-d', $dtVencimentoDocumento);
							}

							$planosDocumentosGerados["dtVencimentoPlanoDocumentoGerado"] = $dtVencimentoDocumento;

							/* Inserir dados na tabela PlanosDocumentosGerados */
							$gravaPlanosDocumentosGerados = dbPlanoDocumentoGerado($planosDocumentosGerados, 'incluir');

							/* Insere dados na tabela ServicosPlanosDocumentosGerados */
							$gravaServicosPlanosDocumentosGerados = dbServicoPlanoDocumentoGerado($servicosPlanosDocumentosGerados, "incluir");
							
							/* Atributos para a tabela ContasAReceber */
							$contasAReceber["idDocumentosGerados"] 	= $documentosGerados["idDocumentoGerado"];
							$contasAReceber["valor"] 				= $valorServicoPlano;
							$contasAReceber["dtVencimento"] 		= $dtVencimentoDocumento;
							$contasAReceber["obs"]					= "Registro referente ao faturamento avulso lancado em $data[dataNormalData]";
							
							/* Insere dados na tabela ContasAReceber */
							$gravaContasAReceber = dbContasReceber($contasAReceber, "incluir");
						}// Fim do if(GerarFaturamentoAvulso)
					}// Fim do if(ativarServico)

				} elseif ($acao == "inativar") {
					$matriz["idStatus"] 	  = $idStatusInativar;
					$matriz["dtInativacao"] = $data['dataBanco'];
					$inativarServico 	  = dbServicosPlano($matriz, 'inativar', $registro);

					if ($inativarServico) {
						$servicoErro = false;
					}
				}// Fim Ativar/Inativar Servico
			}// Fim do for


			/* Ativa/Inativa um dom�nio de acordo com o informado pelo usu�rio */
			if ($limiteCampoDominio) {
				/* Implementar aqui ativar/inativar dominio */
				for ($i = 0; $i < $limiteCampoDominio; $i++) {
					$request 			= explode(":", $_REQUEST["dominio:$i"]);
					$acaoDominio 		= $request[0];
					$idDominio	 		= $request[1];
					$dominio 			= dadosDominio($idDominio);
					$matriz				= $dominio;
					$matriz["dominio"] 	= $dominio["nome"];
					$matriz["id"]		= $idDominio;

					if ($acaoDominio == "ativar") {
						$matriz["restricao"]= 'liberar';
						$insereManager 		= managerComando($matriz, 'configuracoes');
						$ativarDominio 		= dbDominio($matriz, 'ativar');

						if ($ativarDominio) {
							$consulta=buscaEmails($matriz["id"],'idDominio','igual','id');

							if($consulta && contaConsulta($consulta) > 0) {
								/* Aplicar as configurac�es armazenadas em todas as contas */
								for($a = 0; $a < contaConsulta($consulta); $a++) {
									$matriz["idEmail"]	=resultadoSQL($consulta, $a, 'id');
									$matriz["login"]	=resultadoSQL($consulta, $a, 'login');

									emailAplicaConfiguracaoEmail($matriz);
								}
							}
						} else {
							$dominioErro = true;
						}

					} elseif ($acaoDominio == "inativar") {
						$inativarDominio = dbDominio($matriz, "inativar");
						$isereManager = managerComando($matriz, "dominioinativar");

						if (!$isereManager) {
							$dominioErro = true;
						}
					}

				}
			}

			/* Mensagens de erro */
			if ((!$gravaContasAReceber || !$gravaDocumentosGerados || !$gravaPlanosDocumentosGerados || !$gravaServicosPlanosDocumentosGerados) && $_REQUEST["gerarFaturamentoAvulso"]) {
				$msg .= "<p>Ocorreu algum problema ao tentar gerar um faturamento avulso";
			} elseif ($_REQUEST["gerarFaturamentoAvulso"] && ($gravaContasAReceber || $gravaDocumentosGerados || $gravaPlanosDocumentosGerados || $gravaServicosPlanosDocumentosGerados)) {
				/* Mensagem de sucesso */
				$msgSucesso .= "<center><p>Faturamento avulso gerado com sucesso!";
			}
			if ($servicoErro || $dominioErro) {
				$msg .= "<p>N�o foi poss�vel Ativar/Inativar algum do(s) servi�o(s) para este cliente.";
			}
			if ($clienteWT != "0" && $clienteWT != "") {
				$msg .= "<p>Erro Retornado: $clienteWT";
			}
			if ($clienteWT != "0" && $clienteWT != "" && !$servicoErro && !$dominioErro) {
				$msg .= "<p>Por�m o(s) servi�os foram Ativado(s)/Inativado(s) com sucesso no ISP.";
			}
			if ($servicoErro || $dominioErro || $clienteWT != "0" && $clienteWT != "") {
				echo "<br>";
				$url="javascript:history.back();";
				aviso("Aviso de Erro", $msg, $url, '400');
				echo "<br>";
			}
			/* Mensagem de sucesso */
			else {
				echo "<br>";
				$msgSucesso .= "<p>Servi�o(s) Ativado(s)/Inativado(s) com sucesso!";
				avisoNOURL("Confirma��o de Ativa��o/Inativa��o", $msgSucesso, '400');
				echo "<br>";
			}
		}// Fim do if para caso exista algum servico para Ativar/Inatvar
	}// Fim do else para caso o bot�o confirmar tenha sido precionado
}// Fim da func�o inativarAtivarClienteServico()

/**
 * 	Monta um checkbox com o id ServicoPlano do cliente para
 * os servicos que possuem relacionamento com o servico IVR
 * 
 * @author Jo�o Petrelli
 * @since 10-02-2009
 *
 * @param array $idServicoPlano
 * @param String $status
 * @return String
 */
function montaCheckBoxServicoIVR($idServicoPlano, $status) {
	global $k;
	$acaoWT = "";

	if ($status == "A") {
		$acaoWT = "<input type='checkbox' name='ivr:" . $k++ . "' value='inativar:$idServicoPlano'> Inativar";
	} elseif ($status == "I") {
		$acaoWT = "<input type='checkbox' name='ivr:" . $k++ . "' value='ativar:$idServicoPlano'> Ativar";
	}

	return $acaoWT;
}

/**
 * 	Monta o status do wt na listagem somente para os campos
 * que possuem relacionamento com o servico IVR
 * 
 * @author Jo�o Petrelli
 * @since 10-02-2009
 *
 * @param String $status
 * @return String
 */
function montaStatusServicoIVR ($status) {
	$statusWT = "";

	if ($status == "A") {
		$statusWT = "<font color=darkgreen>Ativo</font>";
	}
	elseif ($status == "I") {
		$statusWT = "<font color=brwon>Inativo";
	} else {
		$statusWT = "Aguardando";
	}

	return $statusWT;
}

/**
 * Monta um checkbox de acordo com o status informado.
 * 
 * @author Jo�o Petrelli
 * @since 11-02-2009
 *
 * @param int $idDominio
 * @param String $status
 * @return String
 */
function montaCheckBoxDominio($idDominio, $status) {
	global $x;

	$checkbox = "";

	if ($status == "A") {
		$checkbox = "<input type='checkbox' name='dominio:" . $x++ . "' value='inativar:$idDominio'> Inativar";
	} else {
		$checkbox = "<input type='checkbox' name='dominio:" . $x++ . "' value='ativar:$idDominio'> Ativar";
	}
	return  $checkbox;
}

/**
 * Define o status do dominio de acordo com o par�metro passado.
 * 
 * @author Jo�o Petrelli
 * @since 11-02-2009
 *
 * @param String $status
 * @return String
 */
function montaStatusDominio($status) {
	$statusWT = "";
	if ($status == "A") {
		$statusWT = "<font color=darkgreen>Ativo</font>";
	} elseif ($status == "I") {
		$statusWT = "<font color=brwon>Inativo";
	} else {
		$statusWT = "Aguardando";
	}
	return $statusWT;
}
?>