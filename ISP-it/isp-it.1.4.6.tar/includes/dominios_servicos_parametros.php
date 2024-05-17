<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 27/10/2003
# Ultima alteração: 07/01/2004
#    Alteração No.: 007
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# Função para manutenção de banco de dados
function dbDominiosParametros($matriz, $tipo) {

	global $tb, $conn;

	if($tipo=='incluir') {
	
		# Selecionar os parametros do modulo informado e incluir em DominiosParametros
		$sql="
			select 
				$tb[Parametros].id idParametro, 
				$tb[Parametros].parametro, 
				$tb[Modulos].id idModulo, 
				$tb[Modulos].modulo, 
				$tb[ServicosParametros].valor 
			from 
				$tb[ServicosParametros], 
				$tb[ServicosPlanos], 
				$tb[Modulos], 
				$tb[Parametros], 
				$tb[ParametrosModulos]
			where 
				$tb[Parametros].id = $tb[ParametrosModulos].idParametro  
				AND $tb[ParametrosModulos].idModulo = $tb[Modulos].id 
				AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				AND $tb[ServicosParametros].idServico = $tb[ServicosPlanos].idServico 
				AND $tb[ServicosPlanos].id = $matriz[idServicosPlanos]
				AND ($tb[Modulos].modulo = 'mail' OR $tb[Modulos].modulo = 'web')
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			# Incluir parametros ao dominio
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$idModulo=resultadoSQL($consulta, $a, 'idModulo');
				$modulo=resultadoSQL($consulta, $a, 'modulo');
				$valor=resultadoSQL($consulta, $a, 'valor');
				
				# Verificar valores e parametros para fazer divisão entre dominios
				# Buscar o numero de registros do dominio
				if($parametro=='quotaweb' || ($modulo=='mail' && $parametro=='qtde') ) {
					# dividir igualmente entre dominios
					$sql="
						select 
							$tb[Parametros].id idParametro, 
							$tb[Parametros].parametro, 
							$tb[Modulos].id idModulo, 
							$tb[Modulos].modulo, 
							$tb[ServicosParametros].valor 
						from 
							$tb[ServicosParametros], 
							$tb[ServicosPlanos], 
							$tb[Modulos], 
							$tb[Parametros], 
							$tb[ParametrosModulos]
						where 
							$tb[Parametros].id = $tb[ParametrosModulos].idParametro  
							AND $tb[ParametrosModulos].idModulo = $tb[Modulos].id 
							AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
							AND $tb[ServicosParametros].idServico = $tb[ServicosPlanos].idServico 
							AND $tb[ServicosPlanos].id = $matriz[idServicosPlanos]
							AND $tb[Modulos].modulo = 'dominio'
							AND $tb[Parametros].parametro = 'qtde'
					";
					
					$consultaDominio=consultaSQL($sql, $conn);
					
					if($consultaDominio) $valor=($valor/resultadoSQL($consultaDominio, 0, 'valor'));
					
				}
				
				$sql="
					INSERT INTO
						$tb[DominiosParametros]
					VALUES (
						0,
						$matriz[idDominio],
						$idModulo,
						$idParametro,
						'$valor'
					)
				";
				
				$grava=consultaSQL($sql, $conn);
			}
		}
	}
	
	elseif($tipo=='excluir') {
	
		$sql="DELETE FROM $tb[DominiosParametros] where idDominio='$matriz[id]'";
		
		$consulta=consultaSQL($sql, $conn);
	}
	
	elseif($tipo=='excluirparametro') {
	
		$sql="DELETE FROM $tb[DominiosParametros] where id='$matriz[idDominiosParametros]'";
		
		$consulta=consultaSQL($sql, $conn);
	}
	
	elseif($tipo=='alterarparametro') {
	
		$sql="
			UPDATE 
				$tb[DominiosParametros] 
			SET
				valor='$matriz[valor]'
			WHERE
				id='$matriz[id]'
		";
		
		$grava=consultaSQL($sql, $conn);
	}
	
	elseif($tipo=='incluirparametro') {
		$sql="
			INSERT INTO
				$tb[DominiosParametros]
			VALUES (
				0,
				$matriz[idDominio],
				$matriz[idModulo],
				$matriz[parametro],
				'$matriz[valor]'
			)
		";
		
		$grava=consultaSQL($sql, $conn);
	}
	
	return($grava);
}




# Função para busca de Contas por PessoaTipo
function buscaDominiosParametros($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[DominiosParametros] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[DominiosParametros] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[DominiosParametros] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[DominiosParametros] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca de grupos




# Listar parametros do domínio
function listarDominiosParametros($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;

	# Dados do dominio
	$dominio=dadosDominio($matriz[id]);

	if($acao=='parametros') {
		verDominios($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";
	}
	
	
	if($dominio[padrao]=='S') {
		
		$sql="
			SELECT
				$tb[Parametros].descricao nomeParametro,
				$tb[Parametros].parametro parametro,
				$tb[Parametros].id idParametro,
				$tb[DominiosParametros].id id,
				$tb[DominiosParametros].idDominio idDominio,
				$tb[DominiosParametros].idModulo idModulo,
				$tb[DominiosParametros].idParametro idParametro,
				$tb[DominiosParametros].valor valor
			FROM
				$tb[Parametros],
				$tb[DominiosParametros]
			WHERE
				$tb[Parametros].id = $tb[DominiosParametros].idParametro
				AND $tb[DominiosParametros].idDominio = $matriz[id]
			ORDER BY
				$tb[Parametros].descricao ASC
		";
	}
	else  {
	
		$sql="
			SELECT
				$tb[Parametros].descricao nomeParametro,
				$tb[Parametros].parametro parametro,
				$tb[Parametros].id idParametro,
				$tb[DominiosParametros].id id,
				$tb[DominiosServicosPlanos].idPessoasTipos idPessoasTipos,
				$tb[DominiosParametros].idDominio idDominio,
				$tb[DominiosParametros].idModulo idModulo,
				$tb[DominiosParametros].idParametro idParametro,
				$tb[DominiosParametros].valor valor
			FROM
				$tb[Parametros],
				$tb[DominiosParametros],
				$tb[DominiosServicosPlanos]
			WHERE
				$tb[Parametros].id = $tb[DominiosParametros].idParametro
				AND $tb[DominiosServicosPlanos].idDominio = $tb[DominiosParametros].idDominio
				AND $tb[DominiosParametros].idDominio = $matriz[id]
			ORDER BY
				$tb[Parametros].descricao ASC
		";
	}
	
	$consulta=consultaSQL($sql, $conn);
	
	# Selecionar parametros do dominio
	novaTabela("Parâmetros do Domínio", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
		if($acao=='parametros' || $acao=='listar') {
			if($dominio[padrao] == 'S') {
				$opcAdicionar=htmlMontaOpcao("<a href=?modulo=configuracoes&sub=dominios&acao=parametrosadicionar&registro=$matriz[id]>Adicionar Parâmetro</a>",'incluir');
			}
			else {
				$opcAdicionar=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=parametrosadicionar&registro=$dominio[idPessoasTipos]:$matriz[id]>Adicionar Parâmetro</a>",'incluir');
				if($matriz[idPessoaTipo]) $opcAdicionar.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=config&registro=$dominio[idPessoasTipos]>Listar Domínios</a>",'dominio');
			}
			itemTabelaNOURL($opcAdicionar, 'right', $corFundo, 3, 'tabfundo1');
		}
		
		if($consulta && contaConsulta($consulta)>0) {
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela("Parâmetro", 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
			fechaLinhaTabela();
		
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$id=resultadoSQL($consulta, $a, 'id');
				$idDominio=resultadoSQL($consulta, $a, 'idDominio');
				$idModulo=resultadoSQL($consulta, $a, 'idModulo');
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$nomeParametro=resultadoSQL($consulta, $a, 'nomeParametro');
				$valor=resultadoSQL($consulta, $a, 'valor');
				
				
				if($dominio[padrao]=='S') {
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominios&acao=parametrosalterar&registro=$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("&nbsp;<a href=?modulo=administracao&sub=dominios&acao=parametrosexcluir&registro=$id>Excluir</a>",'excluir');
				}
				else {
					$idPessoasTipos=resultadoSQL($consulta, $a, 'idPessoasTipos');
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=parametrosalterar&registro=$idPessoasTipos:$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("&nbsp;<a href=?modulo=administracao&sub=dominio&acao=parametrosexcluir&registro=$matriz[idPessoaTipo]:$id>Excluir</a>",'excluir');
				}
				
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nomeParametro, 'left', '50%', 'normal10');
					itemLinhaTabela($valor, 'center', '20%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal8');
				fechaLinhaTabela();
			}
		}
		else {
			$texto="<span class=txtaviso>Não parâmetros para este domínio!</span>";
			itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
		}
	fechaTabela();
}




# Listar parametros do domínio
function adicionarDominiosParametros($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;

	verDominios($modulo, $sub, $acao, $matriz[id], $matriz);
	echo "<br>";

	if(!$matriz[bntAdicionar] || !$matriz[parametro] || !$matriz[valor]) {
		# Motrar tabela de busca
		novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, "$registro:$matriz[id]");	
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=registro value=$registro:$matriz[id]>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[idPessoasTipos] value=$matriz[idPessoaTipo]>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Parâmetro: </b><br>
					<span class=normal10>Selecione o parâmetro</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectParametrosDominios($matriz[id], $matriz[parametro], 'parametro', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			# Verificar Tipo de Parametro para caixa de preenchimento de valor
			if($matriz[parametro]) {
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Valor: </b><br>
						<span class=normal10>Informe o valor do parâmetro</span>";
					htmlFechaColuna();
					itemLinhaForm(formInputValorParametro($matriz[parametro], $valor, 'valor', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			
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
		if($matriz[parametro] && $matriz[valor]) {
		
			# Graver dominio servicos planos
			$matriz[idDominio]=$matriz[id];
			
			# Buscar ID do modulo para parametro
			$consultaModulo=buscaParametrosModulos($matriz[parametro],'idParametro','igual','idParametro');
			if($consultaModulo && contaConsulta($consultaModulo)>0) {
			
				$matriz[idModulo]=resultadoSQL($consultaModulo, 0, 'idModulo');
			
				$grava=dbDominiosParametros($matriz, 'incluirparametro');
				
				if($grava) {
					# acusar falta de parametros
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);				
				}
				else {
					# acusar falta de parametros
					# Mensagem de aviso
					$msg="Erro ao gravar registro!";
					avisoNOURL("Aviso", $msg, 400);				
				}
				
				echo "<br>";
				listarDominiosParametros($modulo, $sub, 'listar', $registro, $matriz);
			}
			else {
				$msg="Erro ao incluir registro";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, $url, 400);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, $url, 400);
		}
	}
}



# Listar parametros do domínio
function excluirDominiosParametros($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;
	
	
	# Buscar informações sobre dominio
	$consulta=buscaDominiosParametros($matriz[id], 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$id=resultadoSQL($consulta, 0, 'id');
		$idDominio=resultadoSQL($consulta, 0, 'idDominio');
		$idParametro=resultadoSQL($consulta, 0, 'idParametro');
		$idModulo=resultadoSQL($consulta, 0, 'idModulo');
		$valor=resultadoSQL($consulta, 0, 'valor');
	
		verDominios($modulo, $sub, $acao, $idDominio, $matriz);
		echo "<br>";
	
		if(!$matriz[bntExcluir]) {
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$registro:$idDominio");	
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idDominios] value=$idDominio>
					<input type=hidden name=matriz[id] value=$idDominio>
					<input type=hidden name=matriz[idPessoasTipos] value=$matriz[idPessoaTipo]>
					<input type=hidden name=matriz[idDominiosParametros] value=$id>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Parâmetro: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectParametrosDominios($idDominio, $idParametro, 'parametro', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Valor: </b>";
					htmlFechaColuna();
					itemLinhaForm($valor, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		elseif($matriz[bntExcluir]) {
			# Conferir campos
			# Graver dominio servicos planos
			$matriz[idDominio]=$matriz[id];
			
			$grava=dbDominiosParametros($matriz, 'excluirparametro');
			
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);				
			}
			
			$registro="$matriz[idPessoasTipos]:$matriz[idDominios]";
			$matriz[id]=$matriz[idDominios];
			listarDominiosParametros($modulo, $sub, 'parametros', $registro, $matriz);
		}
	}
	else {
		# acusar falta de parametros
		# Mensagem de aviso
		$msg="Registro não encontrado!";
		avisoNOURL("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}



# Listar parametros do domínio
function alterarDominiosParametros($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;
	
	
	# Buscar informações sobre dominio
	$consulta=buscaDominiosParametros($matriz[id], 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$id=resultadoSQL($consulta, 0, 'id');
		$idDominio=resultadoSQL($consulta, 0, 'idDominio');
		$idParametro=resultadoSQL($consulta, 0, 'idParametro');
		$idModulo=resultadoSQL($consulta, 0, 'idModulo');
		$valor=resultadoSQL($consulta, 0, 'valor');
	
		verDominios($modulo, $sub, $acao, $idDominio, $matriz);
		echo "<br>";
		
		$dadosParametro=checkParametro($idParametro);
	
		if(!$matriz[bntAlterar] || !$matriz[valor]) {
			# Motrar tabela de busca
			novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$registro:$idDominio");	
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idDominios] value=$idDominio>
					<input type=hidden name=matriz[id] value=$idDominio>
					<input type=hidden name=matriz[idPessoasTipos] value=$matriz[idPessoaTipo]>
					<input type=hidden name=matriz[idDominiosParametros] value=$id>
					<input type=hidden name=matriz[parametro] value=$dadosParametro[parametro]>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Parâmetro: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectParametrosDominios($idDominio, $idParametro, 'parametro', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Valor: </b>";
					htmlFechaColuna();
					itemLinhaForm(formInputValorParametro($idParametro, $valor, 'valor', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(checkParametroModulo($idParametro, 'mail')) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Aplicar ao domínio: </b>";
						htmlFechaColuna();
						$texto="<input type=checkbox name=matriz[aplicar_dominio] value='S'><span class=txtaviso> (Aplicar a todas as contas)</span>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# Conferir campos
			# Graver dominio servicos planos
			$matriz[idDominio]=$matriz[id];
			
			$grava=dbDominiosParametros($matriz, 'alterarparametro');
			
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);				
			}
			
			# Aplicação de configurações alteradas para contas de email do dominio
			if($matriz[aplicar_dominio]=='S') {
				$dominio=dadosDominio($matriz[idDominios]);
				
				# Aplicar configuração no Manager
				# Aplicar alterações para emails do dominio
				$matriz[idDominio]=$dominio[id];
				$matriz[dominio]=$dominio[nome];
				
				$matriz[$matriz[parametro]]=$matriz[valor];
				
				emailAplicaConfiguracaoDominio($matriz);
			}
			
			$registro="$matriz[idPessoasTipos]:$matriz[idDominios]";
			$matriz[id]=$matriz[idDominios];
			echo "<br>";
			listarDominiosParametros($modulo, $sub, 'parametros', $registro, $matriz);
		}
	}
	else {
		# acusar falta de parametros
		# Mensagem de aviso
		$msg="Registro não encontrado!";
		avisoNOURL("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}

# Função para montar campo de formulario
function formSelectParametrosDominios($dominio, $parametro, $campo, $tipo) {

	global $conn, $tb;
	
	if($tipo=='form') {
		# Buscar Serviços de servidor (ja cadastrados)
		$tmpConsulta=buscaDominiosParametros($dominio, 'idDominio','igual','idDominio');
		
		$consulta=buscaParametros($texto, $campo, 'todos', 'descricao');
		
		$item="<select name=matriz[$campo] onChange=form.submit();>\n";
		
		# Listargem
		for($i=0;$i<contaConsulta($consulta);$i++) {
			# Zerar flag de registro já cadastrado
			$flag=0;
			
			# Valores dos campos
			$id=resultadoSQL($consulta, $i, 'id');
			$descricao=resultadoSQL($consulta, $i, 'descricao');
			$idUnidade=resultadoSQL($consulta, $i, 'idUnidade');
			$unidade=formSelectUnidades($idUnidade, '','check');
			$idTipo=resultadoSQL($consulta, $i, 'tipo');
			$tipo=formSelectTipoParametro($idTipo, '','check');
	
			# Verificar se serviço já está cadastrado
			for($x=0;$x<contaConsulta($tmpConsulta);$x++) {
				# Verificar
				$idTmp=resultadoSQL($tmpConsulta, $x, 'idParametro');
				
				if($idTmp == $id) {
					# Setar Flag de registro já cadastrado
					$flag=1;
					break;
				}
			}
	
			if(!$flag || $flag==0) {
				# Mostrar serviço		
				if($parametro==$id) $opcSelect='selected';
				else $opcSelect='';
				$item.= "<option value=$id $opcSelect>$descricao";
				
				if($idUnidade)  $item.=" - $unidade";
			}
		}
		
		$item.="</select>";
		
		return($item);
		
	}
	elseif($tipo=='check') {
		# Selecionar Parametro
		$consulta=buscaParametros($parametro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# Retornar nome do parametro
			$retorno=resultadoSQL($consulta, 0, 'descricao');
			
		}
		
		
		return($retorno);
	}
} #fecha funcao de montagem de campo de form



# Função para carregar parametros do domino
function carregaParametrosDominio($idDominio, $modulo) {

	global $conn, $tb;

	if($idDominio && $modulo) {
		# consultar
		
		$sql="
			select 
				$tb[DominiosParametros].idDominio, 
				$tb[DominiosParametros].valor, 
				$tb[Modulos].id idModulo, 
				$tb[Modulos].modulo, 
				$tb[Parametros].id idParametro,
				$tb[Parametros].parametro 
			FROM
				$tb[DominiosParametros], 
				$tb[Modulos], 
				$tb[Parametros]
			WHERE
				$tb[DominiosParametros].idModulo=$tb[Modulos].id 
				AND $tb[DominiosParametros].idParametro = $tb[Parametros].id 
				AND $tb[DominiosParametros].idDominio = $idDominio
				AND $tb[Modulos].modulo='$modulo'
			GROUP BY
				$tb[Parametros].id
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
		
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$idDominio=resultadoSQL($consulta, $a, 'idDominio');
				$valor=resultadoSQL($consulta, $a, 'valor');
					
				if($modulo=='mail') {
					if($parametro != 'qtde') $retorno[$idParametro]=$valor;
				}
			}
		}
	}
	
	
	return($retorno);
}

?>
