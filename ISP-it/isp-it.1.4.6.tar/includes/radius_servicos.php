<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 20/10/2003
# Ultima altera��o: 20/10/2003
#    Altera��o No.: 002
#
# Fun��o:
#    Painel - Fun��es para controle de servi�o de radius (grupos)


# fun��o de busca de grupos
function radiusBuscaGruposServicos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ServicosRadiusGrupos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ServicosRadiusGrupos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ServicosRadiusGrupos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ServicosRadiusGrupos] WHERE $texto ORDER BY $ordem";
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
	
} # fecha fun��o de busca de grupos


# Listar grupos
function radiusServicosGrupo($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;


	radiusVerGrupo($modulo, $sub, $acao, $registro, $matriz);
	echo "<br>";

	# Cabe�alho
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	
		$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionar&registro=$registro>Adicionar</a>",'incluir');
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar Grupos</a>",'listar');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
	
		# Sele��o de registros
		$consulta=radiusBuscaGruposServicos($registro, 'idRadiusGrupos', 'igual','id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Grupo', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Servi�os', 'center', '35%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '25%', 'tabfundo0');
			fechaLinhaTabela();
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idRadiusGrupos=resultadoSQL($consulta, $i, 'idRadiusGrupos');
				$idServicos=resultadoSQL($consulta, $i, 'idServicos');
				
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosexcluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela(formRadiusSelectGrupo($idRadiusGrupos, '','check'), 'center', '15%', 'normal10');
					itemLinhaTabela(formSelectServicos($idServicos, '', 'check'), 'left', '35%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();

} # fecha fun��o de listagem



# Funcao para cadastro de usuarios
function radiusAdicionarServicosGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	radiusVerGrupo($modulo, $sub, $acao, $registro, $matriz);
	echo "<br>";
	
	# Form de inclusao
	if(!$matriz[bntAdicionar]) {
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
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[idRadiusGrupos] value=$registro>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Servi�o: </b><br>
					<span class=normal10>Nome do Servi�o</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectServicosGruposRadius($registro, $matriz[idServicos], 'idServicos','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[idServicos]) {
			# Cadastrar em banco de dados
			$grava=radiusDBServicoGrupo($matriz, 'incluir');
			
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				
				radiusServicosGrupo($modulo, $sub, 'servicos', $registro, $matriz);
			}
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de inclusao de grupos



# Funcao para cadastro de usuarios
function radiusExcluirServicosGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {
		# Buscar dados de ServicosRadiusGrupos
		$consulta=radiusBuscaGruposServicos($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
	
			$id=resultadoSQL($consulta, 0, 'id');
			$idRadiusGrupos=resultadoSQL($consulta, 0, 'idRadiusGrupos');
			$idServicos=resultadoSQL($consulta, 0, 'idServicos');
		
			radiusVerGrupo($modulo, $sub, $acao, $idRadiusGrupos, $matriz);
			echo "<br>";	
	
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$idRadiusGrupos>
					<input type=hidden name=matriz[id] value=$id>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Servi�o: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicos($idServicos, '','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		else {
			# registro n�o encontrado
			$msg="Registro n�o encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}		
	}
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=radiusDBServicoGrupo($matriz, 'excluir');
		
		# Verificar inclus�o de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Excl�do com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			
			radiusServicosGrupo($modulo, $sub, 'servicos', $registro, $matriz);
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}

	
} # fecha funcao de inclusao de grupos




# Fun��o para grava��o em banco de dados
function radiusDBServicoGrupo($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[ServicosRadiusGrupos] VALUES (
			0, 
			'$matriz[idRadiusGrupos]', 
			'$matriz[idServicos]'
		)";
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[ServicosRadiusGrupos] WHERE id=$matriz[id]";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados




# Fun�ao para busca de informa��es do vencimento
function radiusDadosServicosGrupos($valor, $campo) {

	$consulta=radiusBuscaGruposServicos($valor, $campo,'igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# dados do vencimento
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idRadiusGrupos]=resultadoSQL($consulta, 0, 'idRadiusGrupos');
		$retorno[idServicos]=resultadoSQL($consulta, 0, 'idServicos');
	}
	
	return($retorno);
}


# Buscar ID do Grupo do Radius
function buscaIDGrupoServicoPlanoRadius($idServicoPlano) {

	$consultaPlano=buscaServicosPlanos($idServicoPlano, 'id','igual','id');
	
	if($consultaPlano && contaConsulta($consultaPlano)>0) {
		$idServico=resultadoSQL($consultaPlano, 0, 'idServico');
		
		$consulta=radiusBuscaGruposServicos($idServico, 'idServicos','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			$idRadiusGrupos=resultadoSQL($consulta, 0, 'idRadiusGrupos');
		}
	}
	
	return($idRadiusGrupos);

}


?>
