<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/07/2003
# Ultima alteração: 26/01/2004
#    Alteração No.: 005
#
# Função:
#    Painel - Funções para cadastro de parametros de servicos 


# Função de banco de dados - Pessoas
function dbParametroServico($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[ServicosParametros] VALUES (
		$matriz[servico],
		'$matriz[parametro]',
		'$matriz[valor]')";
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM 
					$tb[ServicosParametros] 
				WHERE 
					idServico=$matriz[servico] 
					AND idParametro=$matriz[parametro]";
	}
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[ServicosParametros] 
			SET
				valor='$matriz[valor]'
			WHERE
				idServico=$matriz[idServico] 
				AND idParametro=$matriz[idParametro]
				AND valor='$matriz[valorANT]'";
	} #fecha inclusao
	elseif($tipo == 'retornaValor'){
		 $sql = "SELECT $tb[ServicosParametros].valor " .
		 		"FROM $tb[ServicosParametros] " .
		 		"INNER JOIN $tb[Servicos] " .
		 		"ON ($tb[ServicosParametros].idServico = $tb[Servicos].id) " .
		 		"INNER JOIN $tb[ServicosPlanos] " .
		 		"ON ($tb[Servicos].id = $tb[ServicosPlanos].idServico) " .
		 		"INNER JOIN $tb[PlanosPessoas] ON ($tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id) " .
		 		"INNER JOIN $tb[PessoasTipos] ON ($tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id) " .
		 		"WHERE PessoasTipos.id = $matriz[idPessoasTipos] AND ServicosPlanos.id = $matriz[idServicoPlano]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


# função de busca 
function buscaParametrosServico($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ServicosParametros] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ServicosParametros] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ServicosParametros] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ServicosParametros] WHERE $texto ORDER BY $ordem";
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
	
} # fecha função de busca


# Função para listagem 
function parametrosServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		verServico($modulo, $sub, $acao, $registro, $matriz);	
		itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');	
		listarParametrosServicos($modulo, $sub, $acao, $registro, $matriz);
	}
	
}#fecha função de listagem



function listarParametrosServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Seleção de registros
	$consulta=buscaParametrosServico($registro, 'idServico','igual','idServico');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Parâmetros]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametrosadicionar&registro=$registro>Adicionar</a>",'incluir');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
	
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há parâmetros cadastrados", 'left', $corFundo, 3, 'txtaviso');
	}
	else {


		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há parâmetros configurados para este módulo', 'left', $corFundo, 3, 'txtaviso');
		}
		else {

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Parametro', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '20%', 'tabfundo0');
			fechaLinhaTabela();

			$i=0;
			
			while($i < contaConsulta($consulta)) {
				# Mostrar registro
				$idParametro=resultadoSQL($consulta, $i, 'idParametro');
				$valor=resultadoSQL($consulta, $i, 'valor');
				
				# Buscar parametro
				$consultaParametro=buscaParametros($idParametro, 'id','igual','id');

				$descricao=resultadoSQL($consultaParametro, 0, 'descricao');
				$tipo=resultadoSQL($consultaParametro, 0, 'tipo');
				$unidade=resultadoSQL($consultaParametro, 0, 'idUnidade');
				$parametro=resultadoSQL($consultaParametro, 0, 'parametro');
				
				if($tipo=='sn') $textoValor=formSelectSimNao($valor, '','check');
				elseif($tipo=='nr') $textoValor="<b>$valor</b> <span class=txtaviso>".formSelectUnidades($unidade,'','check')."</span>";
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".excluir."&registro=$registro:$idParametro>Excluir</a>",'excluir');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'center', '50%', 'normal10');
					itemLinhaTabela($textoValor, 'center', '30%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
			
			fechaTabela();
		} #fecha servicos encontrados
	} #fecha listagem
}



# Funcao para cadastro de servicos
function adicionarParametrosServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAdicionar]) {

		# Seleção de registros
		$consulta=buscaServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Serviço não encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informações sobre Servidor
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
						echo "<b>Parâmetro: </b><br>
						<span class=normal10>Selecione o parâmetro do módulo</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectParametrosServicos($registro, $matriz[parametro], 'parametro', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if(!$matriz[parametro]) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				else {
					# Verificar Tipo de Parametro para caixa de preenchimento de valor
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Valor: </b><br>
							<span class=normal10>Informe o valor do parâmetro</span>";
						htmlFechaColuna();
						itemLinhaForm(formInputValorParametro($matriz[parametro], $valor, 'valor', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			fechaTabela();
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[parametro] && $matriz[valor]) {
			# Cadastrar em banco de dados
			$grava=dbParametroServico($matriz, 'incluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
				avisoNOURL("Aviso", $msg, 760);
				
				echo "<br>";
				listarParametrosServicos($modulo, $sub, 'parametros', $matriz[servico], $matriz);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de inclusao de servicos




# Funcao para cadastro de servicos
function excluirParametrosServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Quebrar registro
	$matRegistro=explode(":", $registro);
	
	$matriz[servico]=$matRegistro[0];
	$matriz[parametro]=$matRegistro[1];
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {

		# Seleção de registros
		$consulta=buscaParametrosServico("idServico=$matriz[servico] AND idParametro=$matriz[parametro]", '', 'custom', 'idServico');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Parâmetro não encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informações sobre Servidor
			verServico($modulo, $sub, $acao, $matriz[servico], $matriz);
			
			echo "<br>";
			
			# Selecionar dados do Parametro
			$valor=resultadoSQL($consulta, 0, 'valor');
	
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $matriz[servico]);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[servico] value=$matriz[servico]>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Parâmetro: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectParametrosServicos($matriz[servico], $matriz[parametro], '', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				# Verificar Tipo de Parametro para caixa de preenchimento de valor
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
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntExcluir]) {
		# Conferir campos
		if($matriz[parametro] && $matriz[servico]) {
			# Cadastrar em banco de dados
			$grava=dbParametroServico($matriz, 'excluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=parametros&registro=$matriz[servico]";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao excluir parâmetro!";
				$url="?modulo=$modulo&sub=$sub&acao=parametros&registro=$matriz[servico]";
				aviso("Aviso", $msg, $url, 760);
			}
			
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente.";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de excluir



# Função para montar campo de formulario
function formSelectParametrosServicos($servico, $parametro, $campo, $tipo) {

	global $conn, $tb;
	
	if($tipo=='form') {
		# Buscar Serviços de servidor (ja cadastrados)
		$tmpConsulta=buscaParametrosServico($servico, 'idServico','igual','idServico');
		
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
				$item.= "<option value=$id $opcSelect>$descricao - $tipo";
				
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



# Função para montar campo de formulario
function formSelectServicosGruposRadius($gruporadius, $servico, $campo, $tipo) {

	global $conn, $tb;
	
	if($tipo=='form') {
		# Buscar Serviços de servidor (ja cadastrados)
		$tmpConsulta=radiusBuscaGruposServicos($gruporadius, 'idRadiusGrupos','igual','idRadiusGrupos');
		
		$consulta=buscaServicos($texto, $campo, 'todos', 'nome');
		
		$item="<select name=matriz[$campo] onChange=form.submit();>\n";
		
		# Listargem
		for($i=0;$i<contaConsulta($consulta);$i++) {
			# Zerar flag de registro já cadastrado
			$flag=0;
			
			# Valores dos campos
			$id=resultadoSQL($consulta, $i, 'id');
			$nome=resultadoSQL($consulta, $i, 'nome');
			$valor=resultadoSQL($consulta, $i, 'valor');
	
			# Verificar se serviço já está cadastrado
			for($x=0;$x<contaConsulta($tmpConsulta);$x++) {
				# Verificar
				$idTmp=resultadoSQL($tmpConsulta, $x, 'idServicos');
				
				if($idTmp == $id) {
					# Setar Flag de registro já cadastrado
					$flag=1;
					break;
				}
			}
	
			if(!$flag || $flag==0) {
				# Mostrar serviço		
				if($servico==$id) $opcSelect='selected';
				else $opcSelect='';
				$item.= "<option value=$id $opcSelect>$nome";
			}
		}
		
		$item.="</select>";
		
		return($item);
		
	}
	elseif($tipo=='check') {
		# Selecionar Parametro
		$consulta=buscaServicos($servico, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# Retornar nome do parametro
			$retorno=resultadoSQL($consulta, 0, 'descricao');
		}
		return($retorno);
	}
} #fecha funcao de montagem de campo de form


# Função para totalização de parametros
function totalParametrosServicoModulo($idPessoaTipo, $idModulo, $idServico, $idParametro) {

	global $conn, $tb;

	$retorno=0;
	
	# Totalizar parametro
	$sql="
		select 
			$tb[PlanosPessoas].id idPlano, 
			$tb[PlanosPessoas].nome nomePlano,
			$tb[ServicosPlanos].id idServico, 
			$tb[Servicos].nome nomeServico,
			$tb[Modulos].id idModulo, 
			$tb[Modulos].modulo, 
			$tb[Parametros].descricao nomeParametro,
			$tb[Parametros].parametro, 
			$tb[Unidades].unidade, 
			$tb[ServicosParametros].valor 
		FROM
			$tb[Modulos], 
			$tb[Parametros], 
			$tb[ParametrosModulos], 
			$tb[ServicosParametros], 
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[StatusServicos], 
			$tb[PlanosPessoas], 
			$tb[PessoasTipos], 
			$tb[Pessoas], 
			$tb[Unidades] 
		WHERE
			$tb[Modulos].id=$tb[ParametrosModulos].idModulo 
			AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
			AND $tb[Parametros].idUnidade = $tb[Unidades].id 
			AND $tb[Parametros].id = $tb[ServicosParametros].idParametro 
			AND $tb[ServicosParametros].idServico  = $tb[Servicos].id 
			AND $tb[Servicos].id = $tb[ServicosPlanos].idServico 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
			AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
			AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND ($tb[StatusServicos].status = 'A' OR $tb[StatusServicos].status = 'T' OR $tb[StatusServicos].status='N')
			AND $tb[PessoasTipos].id=$idPessoaTipo
			AND $tb[Modulos].id=$idModulo
			AND $tb[Parametros].id=$idParametro

		ORDER BY
			idServico";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		if(contaConsulta($consulta)==1) {
			# retornar resultado
			$retorno=resultadoSQL($consulta, 0, 'valor');
		}
		else {
			# Contabilizar tudo
			for($a=0;$a<contaConsulta($consulta);$a++) {
				if(resultadoSQL($consulta,$a,'parametro') != 'quota' || $retorno==0) $retorno+=resultadoSQL($consulta, $a, 'valor');
			}
		}
	}
	return($retorno);
}


?>