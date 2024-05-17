<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 16/10/2003
# Ultima alteração: 31/03/2004
#    Alteração No.: 006
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# Função para busca de Contas por PessoaTipo
function radiusBuscaUsuariosPessoasTipos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[RadiusUsuariosPessoasTipos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[RadiusUsuariosPessoasTipos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[RadiusUsuariosPessoasTipos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[RadiusUsuariosPessoasTipos] WHERE $texto ORDER BY $ordem";
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




# Função para listagem de contas  Radius por Pessoa Tipo
function radiusListarUsuariosPessoasTipos($idPessoaTipo, $idModulo) {

	global $conn, $tb, $corFundo, $corBorda;

	if($idPessoaTipo) {
		$sql="
			SELECT	
				$tb[RadiusUsuariosPessoasTipos].id id,
				$tb[RadiusUsuariosPessoasTipos].idPessoasTipos idPessoasTipos,
				$tb[RadiusUsuarios].login login,
				$tb[RadiusUsuarios].idGrupo idGrupo,
				$tb[RadiusUsuarios].dtCadastro dtCadastro,
				$tb[RadiusUsuarios].status status
				
			FROM
				$tb[RadiusUsuariosPessoasTipos],
				$tb[RadiusUsuarios]
			WHERE
				$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios = $tb[RadiusUsuarios].id
				AND $tb[RadiusUsuariosPessoasTipos].idPessoasTipos = $idPessoaTipo
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		novaTabela("Contas de Acesso Dial-UP", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			# Verificar contas configuradas para mostrar ou nao o link de "adicionar"
			$total=radiusTotalContas($idPessoaTipo);
			$totalEmUso=radiusTotalContasEmUso($idPessoaTipo);

			if($total > $totalEmUso) {
				$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=adicionarusuario&registro=$idPessoaTipo>Adicionar</a>",'incluir');
				itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
			}
	
			if($consulta && contaConsulta($consulta)>0) {
			
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("Login", 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '60%', 'tabfundo0');
				fechaLinhaTabela();
			
				for($a=0;$a<contaConsulta($consulta);$a++) {
				
					$id=resultadoSQL($consulta, $a, 'id');
					$idGrupo=resultadoSQL($consulta, $a, 'idGrupo');
					$idPessoasTipos=resultadoSQL($consulta, $a, 'idPessoasTipos');
					$login=resultadoSQL($consulta, $a, 'login');
					$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
					$status=resultadoSQL($consulta, $a, 'status');
					
					if($status=='A') $class='txtok';
					elseif($status=='I') $class='txtaviso';
					elseif($status=='T') $class='txttrial';
					else $class='bold10';
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=alterar&registro=$idPessoaTipo:$id>Senha</a>",'senha');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=excluir&registro=$idPessoaTipo:$id>Excluir</a>",'excluir');
					if($status=='A') {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=inativar&registro=$idPessoaTipo:$id>Inativar</a>",'ativar');
					}
					if($status=='I' || $status=='T') {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=ativar&registro=$idPessoaTipo:$id>Ativar</a>",'desativar');
					}
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=extrato&registro=$idPessoaTipo:$id>Extrato</a>",'extrato');
					
					# Checar se há telefones configurados
					//$consultaTelefones=buscaRadiusTelefones($id, 'idRadiusUsuarioPessoaTipo','igual','id');
					if($consultaTelefones && contaConsulta($consultaTelefones)>0) {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=telefones&registro=$idPessoaTipo:$id><b>Telefones</b></a>",'fone');
					}
					else {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=telefones&registro=$idPessoaTipo:$id>Telefones</a>",'fone');
					}
						
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($login, 'center', '30%', 'normal10');
						itemLinhaTabela(formSelectStatusRadius($status,'','check'), 'center', '10%', 'normal10');
						itemLinhaTabela($opcoes, 'center nowrap', '60%', 'normal8');
					fechaLinhaTabela();
				}
			}
			else {
				$texto="<span class=txtaviso>Não existem contas configuradas!</span>";
				itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
			}
			
		fechaTabela();
	}
}



# Contar contas em uso
function radiusTotalContasEmUso($idPessoaTipo) {

	if($idPessoaTipo) {
		$consulta=radiusBuscaUsuariosPessoasTipos($idPessoaTipo,'idPessoasTipos','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) $retorno=contaConsulta($consulta);
		else $retorno=0;
	}
	
	return($retorno);
}


# Contar contas em uso
function radiusTotalContasServicoEmUso($idPessoaTipo, $idServicoPlano) {

	if($idPessoaTipo) {
		$consulta=radiusBuscaUsuariosPessoasTipos("idPessoasTipos=$idPessoaTipo AND idServicosPlanos=$idServicoPlano",'','custom','id');
		
		if($consulta && contaConsulta($consulta)>0) $retorno=contaConsulta($consulta);
		else $retorno=0;
	}
	
	return($retorno);
}



# Função para totalização de parametros
function radiusTotalContas($idPessoaTipo) {

	global $conn, $tb;

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
			AND $tb[Modulos].modulo='dial'
			AND ($tb[StatusServicos].status='A' OR $tb[StatusServicos].status='I' OR $tb[StatusServicos].status='T')
			AND $tb[Parametros].parametro='qtde'
			AND $tb[PessoasTipos].id=$idPessoaTipo
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
				$retorno+=resultadoSQL($consulta, $a, 'valor');
			}
		}
	}
	return($retorno);
}



# Função para totalização de parametros
function radiusTotalContasServico($idPessoaTipo, $idServicoPlano) {

	global $conn, $tb;

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
			AND $tb[Modulos].modulo='dial'
			AND $tb[Parametros].parametro='qtde'
			AND $tb[PessoasTipos].id=$idPessoaTipo
			AND $tb[ServicosPlanos].id=$idServicoPlano
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
				$retorno+=resultadoSQL($consulta, $a, 'valor');
			}
		}
	}
	return($retorno);
}



# Função para gravação em banco de dados
/**
 * @return unknown
 * @param unknown $matriz
 * @param unknown $tipo
 * @desc Função de manipulacao da Tabela RadiusUsuariosPessoaTipo
$matriz = matriz de elemnentos necessários nas operacoes da função
$tipo = incluir || excluir || excluirtodos || alterarservico;
*/
function radiusDBUsuarioPessoaTipo($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tb[RadiusUsuariosPessoasTipos] VALUES (
			0, 
			'$matriz[idPessoaTipo]', 
			'$matriz[idRadiusUsuarios]', 
			'$matriz[idServicoPlano]'
		)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[RadiusUsuariosPessoasTipos] WHERE idRadiusUsuarios=$matriz[idRadiusUsuarios]";
	}
	
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM $tb[RadiusUsuariosPessoasTipos] WHERE idPessoasTipos=$matriz[id]";
	}
	
	elseif ( $tipo== 'alterarservico'){
		$sql= "update $tb[RadiusUsuariosPessoasTipos] SET idServicosPlanos='".$matriz[idServicosPlanos]."' WHERE id='".$matriz[idRadius]."'";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
} # fecha função de gravação em banco de dados


# Alterar status de contas radius de um serviço
function radiusStatusContaServico($idServicoPlano, $status) {

	global $conn, $tb;

	$sql="
		SELECT
			$tb[RadiusUsuariosPessoasTipos].id id, 
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios idRadiusUsuarios, 
			$tb[RadiusUsuarios].login login, 
			$tb[RadiusUsuarios].status 
		FROM
			$tb[RadiusUsuarios], 
			$tb[RadiusUsuariosPessoasTipos] 
		WHERE 
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios = $tb[RadiusUsuarios].id 
			AND $tb[RadiusUsuariosPessoasTipos].idServicosPlanos=$idServicoPlano;
	";

	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		for($a=0;$a<contaConsulta($consulta);$a++) {
		
			# dados da conta radius
			$id=resultadoSQL($consulta, $a, 'id');
			$login=resultadoSQL($consulta, $a, 'login');
			$idRadiusUsuarios=resultadoSQL($consulta, $a, 'idRadiusUsuarios');
			
			# Alterar status de radiusUsuarios
			$matriz[idRadiusUsuarios]=$idRadiusUsuarios;
			if($status=='I') $radiusUsuarios=radiusDBUsuario($matriz, 'inativar');
			elseif($status=='A') $radiusUsuarios=radiusDBUsuario($matriz, 'ativar');
			
			# Alterar status da conta no serviço radius
			$radiusStatus=radiusStatusConta($login, $status);
			
		}
	}
}



# Função para exclusão de conta de radius relacionado do serviço
function radiusExcluirContaServico($idServicoPlano) {

	global $conn, $tb;
	
	
	/*$sql="
		SELECT
			$tb[RadiusUsuariosPessoasTipos].id id, 
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios idRadiusUsuarios, 
			$tb[RadiusUsuarios].login login, 
			$tb[RadiusUsuarios].status 
		FROM
			$tb[RadiusUsuarios], 
			$tb[RadiusUsuariosPessoasTipos] 
		WHERE 
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios = $tb[RadiusUsuarios].id 
			AND $tb[RadiusUsuariosPessoasTipos].idServicosPlanos=$idServicoPlano
	";*/
	# Otimizei a consulta para resolver os conflitos de SQL ANSI nas versões mais recentes do 
	# MySQL - por Felipe Assis - 03/09/2008
	
	$sql = "SELECT $tb[RadiusUsuariosPessoasTipos].id as id, 
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios as idRadiusUsuarios, 
			$tb[RadiusUsuarios].login as login, 
			$tb[RadiusUsuarios].status 
			FROM $tb[RadiusUsuarios] INNER JOIN $tb[RadiusUsuariosPessoasTipos] 
			ON ($tb[RadiusUsuarios].id = $tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios) 
			WHERE $tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios = $tb[RadiusUsuarios].id 
			AND $tb[RadiusUsuariosPessoasTipos].idServicosPlanos = $idServicoPlano";
//	echo "DEBUG: Consulta de Usuários do Radius - " . $sql . "<br>";

	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta) > 0) {
	
		for($a=0;$a<contaConsulta($consulta);$a++) {
		
			# dados da conta radius
			$id=resultadoSQL($consulta, $a, 'id');
			$login=resultadoSQL($consulta, $a, 'login');
			$idRadiusUsuarios=resultadoSQL($consulta, $a, 'idRadiusUsuarios');
			
			# Excluir RadiusUsuariosPessoasTipos
			$matriz[idRadiusUsuarios]=$idRadiusUsuarios;
			$radiusUsuariosPessoasTipos=radiusDBUsuarioPessoaTipo($matriz, 'excluir');
			
			# Excluir RadiusUsuarios
			$radiusUsuarios=radiusDBUsuario($matriz, 'excluir');			
			
			# Excluir Conta
			$radiusStatus=radiusExcluirConta($login, 'S');
			
		}
	}

}



# Média de utilização dos usuarios
function radiusExtratoForm($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $conn, $radius, $sessLogin;
	
	$data=dataSistema();
	
	# Buscar informações sobre usuario
	$dadosUsuariosRadius=radiusDadosUsuariosPessoaTipo($matriz[id]);
	
	$matriz[loginRadius]=$dadosUsuariosRadius[login];
	echo "<br>";
	radiusVerUsuario($modulo, $sub, $acao, $dadosUsuariosRadius[idRadiusUsuarios], $matriz);
	echo "<br>";

	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela2("[Extrato de Horas Utilizadas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	# solicitar dados		
	novaLinhaTabela($corFundo, '100%');
	$texto="			
		<form method=post name=matriz action=index.php>
		<input type=hidden name=modulo value=$modulo>
		<input type=hidden name=sub value=$sub>
		<input type=hidden name=acao value=$acao>
		<input type=hidden name=registro value=$registro:$matriz[id]>
		<input type=hidden name=matriz[id] value=$matriz[id]>
		&nbsp;";
		itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
	fechaLinhaTabela();
	novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Data Inicial: </b><br>
			<span class=normal10>Informa a data inicial da pesquisa</span>";
		htmlFechaColuna();
		$texto="<input type=text name=matriz[dtInicial] size=10 value='$matriz[dtInicial]' onBlur=verificaData(this.value,5)>
		<span class=txtaviso>Formato: (15/01/2003)</span>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Data Final: </b><br>
			<span class=normal10>Informe data final da pesquisa</span>";
		htmlFechaColuna();
		$texto="<input type=text name=matriz[dtFinal] size=10 value='$matriz[dtFinal]' onBlur=verificaData(this.value,6)>
		<span class=txtaviso>Formato: (15/01/2003)</span>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "&nbsp;";
		htmlFechaColuna();
		$texto="<input type=submit name=matriz[bntConsultar] value=Consultar class=submit>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();

		
	# verificar confirmação de postagem de valores e realizar consulta
	if($matriz[dtInicial] && $matriz[dtFinal]) {
		
		# Mostrar relação e medias
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
			# Cabeçalho		
			# Motrar tabela de busca
			novaTabela("[Informações de Utilização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
			# Formulário de Seleção de dados	
	
			# Converter Datas
			$matriz[dtInicial]=converteData($matriz[dtInicial],'form','bancodata');
			$matriz[dtFinal]=converteData($matriz[dtFinal],'form','bancodata');
			
			# Extrato do usuario
			radiusListarExtrato($modulo, $sub, $acao, $registro, $matriz);
			
		fechaTabela();
		htmlFechaColuna();
		fechaLinhaTabela();
		
	}
	# Caso não seja informada uma data, mostrar ultima semana
	elseif(!$matriz[bntConsultar] || !$matriz[dtInicial] || !$matriz[dtFinal]) {
	
		# Mostrar relação e medias
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
			# Cabeçalho		
			# Motrar tabela de busca
			novaTabela("[Informações de Utilização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
			# Formulário de Seleção de dados	
	
			$matriz[dtInicial]=date('Y-m-d',mktime(0,0,0,$data[mes],$data[dia],$data[ano]));
			$matriz[dtFinal]=$data[ano].'-'.$data[mes].'-'.$data[dia];
			radiusListarExtrato($modulo, $sub, $acao, $registro, $matriz);
			
		fechaTabela();
		htmlFechaColuna();
		fechaLinhaTabela();
	
	}

	fechaTabela();

}



# Função para listagem de horas do usuario
function radiusListarExtrato($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $radius, $limite;

	$connRadius=conectaRadius();
	
	# consulta horas de acesso
	$sql="
		SELECT
			UserName login, 
			AcctStartTime start, 
			AcctStopTime stop, 
			AcctSessionTime time, 
			AcctInputOctets input, 
			AcctOutputOctets output, 
			FramedIPAddress ip, 
			CallingStationID fone 
		FROM
			$radius[db].radacct 
		WHERE 
			username='$matriz[loginRadius]' 
			AND AcctStartTime 
			BETWEEN
				'$matriz[dtInicial]' AND '$matriz[dtFinal] 23:59:59'
		ORDER BY
			AcctStartTime DESC";
				
	$consultaAcesso=consultaSQL($sql, $connRadius);
	
	$total=radiusTotalAcesso($consultaAcesso);
	
	if( !$horas  || (intval($total[horas]/3600) >= $horas )) { 
		
		if($consultaAcesso && contaConsulta($consultaAcesso)>0) {
		
			
			# Paginador
			$matriz[registro]="$registro:$matriz[id]";
			$matriz[dtInicial]=converteData($matriz[dtInicial],'banco','formdata');
			$matriz[dtFinal]=converteData($matriz[dtFinal],'banco','formdata');
			
			$urlADD="&matriz[dtInicial]=$matriz[dtInicial]&matriz[dtFinal]=$matriz[dtFinal]";
			paginador2($consulta, contaConsulta($consultaAcesso), $limite[lista][extrato], $matriz, 'normal', 6, $urlADD);
			
			# Cabeçalho
			$login=$usuario;
			itemTabelaNOURL("Usuário: $matriz[loginRadius]", 'center', $corFundo, 6, 'tabfundo0');
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Informações sobre Conexão', 'center', 'middle', '47%', $corFundo, 3, 'tabfundo0');
				itemLinhaTMNOURL('Bytes Transferidos', 'center', 'middle', '45%', $corFundo, 2, 'tabfundo0');
				itemLinhaTMNOURL('Telefone', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Início', 'center', 'middle', '20%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL('Término', 'center', 'middle', '20%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL('Tempo de Conexão', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL('Download', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL('Upload', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL('Origem', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
			fechaLinhaTabela();
			
			
			# Setar registro inicial
			if(!$matriz[pagina]) {
				$i=0;
			}
			elseif($matriz[pagina] && is_numeric($matriz[pagina]) ) {
				$i=$matriz[pagina];
			}
			else {
				$i=0;
			}
			
			$limite=$i+$limite[lista][extrato];
			
			while($i < contaConsulta($consultaAcesso) && $i < $limite) {
			
				$start=resultadoSQL($consultaAcesso, $i, 'start');
				$stop=resultadoSQL($consultaAcesso, $i, 'stop');
				$time=resultadoSQL($consultaAcesso, $i, 'time');
				$input=resultadoSQL($consultaAcesso, $i, 'input');
				$output=resultadoSQL($consultaAcesso, $i, 'output');
				$ip=resultadoSQL($consultaAcesso, $i, 'ip');
				$fone=resultadoSQL($consultaAcesso, $i, 'fone');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL(converteData($start,'banco','form'), 'center', 'middle', '20%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL(converteData($stop,'banco','form'), 'center', 'middle', '20%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL(radiusExtratoAcesso($time), 'center', 'middle', '15%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL(formatarBytes($output)." KBytes", 'right', 'middle', '15%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL(formatarBytes($input)." KBytes", 'right', 'middle', '15%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL(formatarFone($fone), 'center', 'middle', '15%', $corFundo, 0, 'normal10');
				fechaLinhaTabela();
				
				$subTotal[time]+=$time;
				$subTotal[input]+=$input;
				$subTotal[output]+=$output;
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabe
 
			# Mostrar totais
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Sub-Total (esta página):', 'right', 'middle', '40%', $corFundo, 2, 'tabfundo0');
				itemLinhaTMNOURL(radiusExtratoAcesso($subTotal[time]), 'center', 'middle', '15%', $corFundo, 0, 'bold8');
				itemLinhaTMNOURL(formatarBytes($subTotal[input])." KBytes", 'right', 'middle', '15%', $corFundo, 0, 'bold8');
				itemLinhaTMNOURL(formatarBytes($subTotal[output])." KBytes", 'right', 'middle', '15%', $corFundo, 0, 'bold8');
				itemLinhaTMNOURL('&nbsp;', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
			fechaLinhaTabela();

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Total Geral:', 'right', 'middle', '40%', $corFundo, 2, 'tabfundo0');
				itemLinhaTMNOURL(radiusExtratoAcesso($total[horas]), 'center', 'middle', '15%', $corFundo, 0, 'bold8');
				itemLinhaTMNOURL(formatarBytes($total[input])." KBytes", 'right', 'middle', '15%', $corFundo, 0, 'bold8');
				itemLinhaTMNOURL(formatarBytes($total[output])." KBytes", 'right', 'middle', '15%', $corFundo, 0, 'bold8');
				itemLinhaTMNOURL('&nbsp;', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
			fechaLinhaTabela();
		}
		else {
			# Mostrar linha de aviso
			$login=$usuario;
			itemTabelaNOURL("&nbsp;", 'center', $corFundo, 6, 'tabfundo1');
			itemTabelaNOURL("<span class=txtaviso>Não foram encontrados registros de acesso</span>", 'center', $corFundo, 6, 'tabfundo1');
		}
	}
}



# Dados do usuario radius pessoas tipos
function radiusDadosUsuariosPessoaTipo($id) {

	global $tb, $conn;
	
	$sql="
		SELECT
			$tb[RadiusUsuariosPessoasTipos].id idRadiusUsuariosPessoasTipos, 
			$tb[RadiusUsuariosPessoasTipos].idPessoasTipos idPessoaTipo, 
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios idRadiusUsuarios, 
			$tb[RadiusUsuariosPessoasTipos].idServicosPlanos idServicoPlano, 
			$tb[RadiusUsuarios].idGrupo idGrupo, 
			$tb[RadiusUsuarios].login login, 
			$tb[RadiusUsuarios].senha_texto senha_texto, 
			$tb[RadiusUsuarios].dtCadastro dtCadastro, 
			$tb[RadiusUsuarios].dtAtivacao dtAtivacao, 
			$tb[RadiusUsuarios].dtInativacao dtInativacao, 
			$tb[RadiusUsuarios].dtCancelamento dtCancelamento, 
			$tb[RadiusUsuarios].status status 
		FROM
			$tb[RadiusUsuarios], 
			$tb[RadiusUsuariosPessoasTipos] 
		WHERE
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios = $tb[RadiusUsuarios].id
			AND $tb[RadiusUsuariosPessoasTipos].id=$id
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[idRadiusUsuariosPessoasTipos]=resultadoSQL($consulta, 0, 'idRadiusUsuariosPessoasTipos');
		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$retorno[idRadiusUsuarios]=resultadoSQL($consulta, 0, 'idRadiusUsuarios');
		$retorno[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
		$retorno[idGrupo]=resultadoSQL($consulta, 0, 'idGrupo');
		$retorno[login]=resultadoSQL($consulta, 0, 'login');
		$retorno[senha_texto]=resultadoSQL($consulta, 0, 'senha_texto');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
		$retorno[dtInativacao]=resultadoSQL($consulta, 0, 'dtInativacao');
		$retorno[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
	}
	
	return($retorno);

}




?>
