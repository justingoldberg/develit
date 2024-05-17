<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 11/03/2004
# Ultima altera��o: 11/03/2004
#    Altera��o No.: 001
#
# Fun��o:
#    Painel - Fun��es para cadastro de contratos dos servi�os

# Contatos dos Servi�os
function servicosContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin;
	
	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		if($acao=='contratos') listarServicosContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='contratosadicionar') adicionarServicosContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='contratosexcluir') excluirServicosContratos($modulo, $sub, $acao, $registro, $matriz);
	}
}


# fun��o de busca 
function buscaServicosContratos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ServicosContratos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ServicosContratos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ServicosContratos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ServicosContratos] WHERE $texto ORDER BY $ordem";
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



# Fun��o de banco de dados - Contratos Servicos
function dbServicoContrato($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="
			INSERT INTO 
				$tb[ServicosContratos] 
			VALUES (
				0,
				$matriz[servico],
				'$matriz[contrato]',
				'$data[dataBanco]',
				'$matriz[validade]'
		)";
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM 
					$tb[ServicosContratos] 
				WHERE 
					id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}

# Listar Servicos Contratos
function listarServicosContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	verServico($modulo, $sub, $acao, $registro, $matriz);	
	echo "<br>";
	
	# Sele��o de registros
	$consulta=buscaServicosContratos($registro, 'idServico','igual','idServico');
	
	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Contratos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contratosadicionar&registro=$registro>Adicionar</a>",'incluir');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
	
	
	# Caso n�o hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# N�o h� registros
		itemTabelaNOURL("N�o h� contratos cadastrados para este servi�o!", 'left', $corFundo, 3, 'txtaviso');
	}
	else {
		# Cabe�alho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Contrato', 'center', '50%', 'tabfundo0');
			itemLinhaTabela('Validade', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Op��es', 'center', '30%', 'tabfundo0');
		fechaLinhaTabela();

		$i=0;
		
		while($i < contaConsulta($consulta)) {
			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idServico=resultadoSQL($consulta, $i, 'idServico');
			$idContrato=resultadoSQL($consulta, $i, 'idContrato');
			$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
			$mesValidade=resultadoSQL($consulta, $i, 'mesValidade');
			
			$opcoes="";
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".excluir."&registro=$id>Excluir</a>",'excluir');

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela(formSelectContratos($idContrato,'','check'), 'left', '50%', 'normal10');
				itemLinhaTabela("$mesValidade meses", 'center', '20%', 'normal10');
				itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
			fechaLinhaTabela();
			
			# Incrementar contador
			$i++;
		} #fecha laco de montagem de tabela
			
		fechaTabela();
	} #fecha listagem
}



# Funcao para cadastro de servicos
function adicionarServicosContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Sele��o de registros
	$consulta=buscaServicos($registro, 'id', 'igual', 'id');
	
	if(!$consulta || contaConsulta($consulta)==0) {
		# Servidor n�o encontrado
		itemTabelaNOURL('Servi�o n�o encontrado!', 'left', $corFundo, 3, 'txtaviso');
	}
	else {
	
		if(!$matriz[bntAdicionar] || !$matriz[validade] || !is_numeric($matriz[validade]) || !$matriz[contrato]) {

			verServico($modulo, $sub, $acao, $registro, $matriz);	
			echo "<br>";

			# Motrar tabela de busca
			novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[servico] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Contrato: </b><br>
						<span class=normal10>Selecione o contrato</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicosContratos($registro, $matriz[contrato], 'contrato', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($matriz[bntSelecionar] || $matriz[contrato]) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Meses de Vig�ncia: </b><br>
							<span class=normal10>Validade padr�o do Contrato</span>";
						htmlFechaColuna();
						$texto="<input type=text name=matriz[validade] size=3>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				else {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			fechaTabela();
		} # fecha servidor informado para cadastro
		else {
			# Conferir campos
			if($matriz[contrato]) {
				# Cadastrar em banco de dados
				$grava=dbServicoContrato($matriz, 'incluir');
					
				# Verificar inclus�o de registro
				if($grava) {
					# acusar falta de parametros
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, 400);
					
					echo "<br>";
					listarServicosContratos($modulo, $sub, 'contratos', $matriz[servico], $matriz);
				}
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
				aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
			}
		}		
	} #fecha form
} # fecha funcao de inclusao de servicos




# Funcao para cadastro de servicos
function excluirServicosContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {

		# Sele��o de registros
		$consulta=buscaServicosContratos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor n�o encontrado
			itemTabelaNOURL('Contrato n�o encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			$idServico=resultadoSQL($consulta, 0, 'idServico');
			$idContrato=resultadoSQL($consulta, 0, 'idContrato');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$mesValidade=resultadoSQL($consulta, 0, 'mesValidade');
			
			# Mostrar Informa��es sobre Servidor
			verServico($modulo, $sub, $acao, $idServico, $matriz);
			
			echo "<br>";
			
			# Motrar tabela de busca
			novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $matriz[servico]);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[servico] value=$idServico>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Contrato: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectContratos($idContrato, '', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				# Verificar Tipo de Parametro para caixa de preenchimento de valor
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntExcluir]) {
		# Conferir campos
		# Cadastrar em banco de dados
		$matriz[id]=$registro;
		$grava=dbServicoContrato($matriz, 'excluir');
			
		# Verificar inclus�o de registro
		if($grava) {
		
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);

			echo "<br>";
			listarServicosContratos($modulo, $sub, 'contratos',$matriz[servico], $matriz);
		}
		else {
			# Mensagem de aviso
			$msg="Erro ao excluir par�metro!";
			$url="?modulo=$modulo&sub=$sub&acao=contratos&registro=$matriz[servico]";
			aviso("Aviso", $msg, $url, 400);
		}
	}
} # fecha funcao de excluir




# Fun��o para montar campo de formulario
function formSelectServicosContratos($servico, $contrato, $campo, $tipo) {

	global $conn, $tb;
	
	if($tipo=='form') {
		# Buscar Servi�os de servidor (ja cadastrados)
		$tmpConsulta=buscaServicosContratos($servico, 'idServico','igual','idServico');
		
		$consulta=buscaContratos($texto, $campo, 'todos', 'nome');
		
		$item="<select name=matriz[$campo] onChange=form.submit();>\n";
		
		# Listargem
		for($i=0;$i<contaConsulta($consulta);$i++) {
			# Zerar flag de registro j� cadastrado
			$flag=0;
			
			# Valores dos campos
			$id=resultadoSQL($consulta, $i, 'id');
			$nome=resultadoSQL($consulta, $i, 'nome');
			$status=resultadoSQL($consulta, $i, 'status');
	
			# Verificar se servi�o j� est� cadastrado
			for($x=0;$x<contaConsulta($tmpConsulta);$x++) {
				# Verificar
				$idTmp=resultadoSQL($tmpConsulta, $x, 'idContrato');
				
				if($idTmp == $id) {
					# Setar Flag de registro j� cadastrado
					$flag=1;
					break;
				}
			}
	
			if(!$flag || $flag==0) {
				# Mostrar servi�o		
				if($contrato==$id) $opcSelect='selected';
				else $opcSelect='';
				$item.= "<option value=$id $opcSelect>$nome";
				
			}
		}
		
		$item.="</select>";
		
		return($item);
		
	}
	elseif($tipo=='check') {
		# Selecionar Parametro
		$consulta=buscaContratos($contrato, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# Retornar nome do parametro
			$retorno=resultadoSQL($consulta, 0, 'descricao');
			
		}
		
		
		return($retorno);
	}
} #fecha funcao de montagem de campo de form



# Fun��o para buscar dados do plano
function dadosServicosContratos($id) {

	$consulta=buscaServicosContratos($id, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idServico]=resultadoSQL($consulta, 0, 'idServico');
		$retorno[idContrato]=resultadoSQL($consulta, 0, 'idContrato');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[mesValidade]=resultadoSQL($consulta, 0, 'mesValidade');
	}
	
	return($retorno);
}


?>
