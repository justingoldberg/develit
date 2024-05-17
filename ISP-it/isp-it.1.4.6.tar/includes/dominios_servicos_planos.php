<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 16/10/2003
# Ultima alteração: 22/03/2004
#    Alteração No.: 011
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# Função para busca de Contas por PessoaTipo
function buscaDominiosServicosPlanos($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[DominiosServicosPlanos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[DominiosServicosPlanos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[DominiosServicosPlanos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[DominiosServicosPlanos] WHERE $texto ORDER BY $ordem";
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



# Função para buscar dados do Dominio
function buscaDadosDominioServico($idDominio) {

	if($idDominio) {
		# Buscar dados
		$consulta=buscaDominiosServicosPlanos($idDominio, 'idDominio','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# dados
			$retorno[id]=resultadoSQL($consulta, 0, 'id');
			$retorno[idServicosPlanos]=resultadoSQL($consulta, 0, 'idServicosPlanos');
			$retorno[idDominio]=resultadoSQL($consulta, 0, 'idDominio');
			$retorno[idPessoasTipos]=resultadoSQL($consulta, 0, 'idPessoasTipos');
		}
	}
	
	
	return($retorno);
}


# Funcao para cadastro de usuarios
function adicionarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Procurar dominio
	$tmpNome=strtoupper($matriz[nome]);
	
	# Verificar se serviço existe
	$tmpBusca=buscaDominios("upper(nome)='$tmpNome'", $campo, 'custom', 'id');
	
	if(contaConsulta($tmpBusca)>0) {
		$msg="Domínio já existente!";
		avisoNOURL("Aviso", $msg, 400);
		echo "<br>";
	}
	
	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[nome] || !$matriz[descricao] || $msg) {
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
				<input type=hidden name=matriz[idPessoasTipos] value=$registro>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Domínio: </b><br>
					<span class=normal10>Nome do domínio</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=35 value='$matriz[nome]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Descricao: </b><br>
					<span class=normal10>Identificação detalhada do domínio</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=3 cols=35>$matriz[descricao]</textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Serviço: </b><br>
					<span class=normal10>Serviço a atribuir este domínio</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectServicoPlanoDominio($matriz[idPessoaTipo], $matriz[idServicosPlanos], 'idServicosPlanos','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do domínio</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusDominios('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[nome] && $matriz[descricao]) {
			# Cadastrar em banco de dados
			$matriz[id]=buscaIDNovoDominio();

			$matriz[padrao]='N';
			$grava=dbDominio($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {

				# Graver dominio servicos planos
				$matriz[idDominio]=$matriz[id];
				$grava2=dbDominiosServicosPlanos($matriz, 'incluir');
				
				if($grava2) {
					# Gravar parâmetros do dominio
					dbDominiosParametros($matriz, 'incluir');
				
					# Adicionar dominio ao manager
					$parametros=carregaParametrosConfig();
					if($parametros[manager_dominio_add]=='automatico') {
						$matriz[dominio]=$matriz[nome];
						$gravaManager=managerComando($matriz, 'dominioadicionar');
					}
				}
				else {
					# Apagar Dominio gravado
					$grava=dbDominio($matriz, 'excluir');
				}
				
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
				listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
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
} # fecha funcao de inclusao de grupos


# Função para listagem de contas  Radius por Pessoa Tipo
function listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $limite, $html;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	
	if($matriz[txtProcurar]) {
		$sqlADD=" AND ( 
			$tb[Dominios].nome like '%$matriz[txtProcurar]%'
			OR $tb[Dominios].descricao like '%$matriz[txtProcurar]%'
		)";
	}

	if($idPessoaTipo) {
		$sql="
			SELECT	
				$tb[DominiosServicosPlanos].id idDominiosServicosPlanos,
				$tb[DominiosServicosPlanos].idServicosPlanos idServicosPlanos,
				$tb[Dominios].id id,
				$tb[Dominios].nome nome,
				$tb[Dominios].dtCadastro dtCadastro,
				$tb[Dominios].dtAtivacao dtAtivacao,
				$tb[Dominios].status status
				
			FROM
				$tb[DominiosServicosPlanos],
				$tb[Dominios]
			WHERE
				$tb[DominiosServicosPlanos].idDominio = $tb[Dominios].id
				AND $tb[DominiosServicosPlanos].idPessoasTipos = $idPessoaTipo
				$sqlADD
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		novaTabela("Dominios", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 3, 'tabfundo1');
				novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
						$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=config>
						<input type=hidden name=registro value=$idPessoaTipo>
						<b>Procurar por:</b> <input type=text name=matriz[txtProcurar] size=25 value='$matriz[txtProcurar]'>
						<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
						itemLinhaForm($texto, 'center','middle', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();		
			htmlFechaColuna();
			fechaLinhaTabela();
		
			# Verificar contas configuradas para mostrar ou nao o link de "adicionar"
			$total=dominioTotalContas($idPessoaTipo);
			$totalEmUso=dominioTotalContasEmUso($idPessoaTipo);

			if($total > $totalEmUso) {
				$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=adicionar&registro=$idPessoaTipo>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=importar&registro=$idPessoaTipo>Importar Lista</a>",'importar');
				itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
			}
			
			if($consulta && contaConsulta($consulta)>0) {
			
				$matriz[registro]=$idPessoaTipo;
				paginador2($consulta, contaConsulta($consulta), $limite[lista][dominios], $matriz, 'normal8', 3, "");
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("Domínio", 'center', '40%', 'tabfundo0');
					itemLinhaTabela('Status', 'center', '15%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '45%', 'tabfundo0');
				fechaLinhaTabela();
				
					
				# Setar registro inicial
				if(!$matriz[pagina]) {
					$i=0;
				}
				elseif($matriz[pagina] && is_numeric($matriz[pagina]) ) {
					$i=$matriz[pagina];
				}
				else {
					$$i=0;
				}
				
				$limite=$i+$limite[lista][dominios];
				
				for($a=$i;$a<contaConsulta($consulta) && $a < $limite;$a++) {
				
					$id=resultadoSQL($consulta, $a, 'id');
					$idServicosPlanos=resultadoSQL($consulta, $a, 'idServicosPlanos');
					$nome=resultadoSQL($consulta, $a, 'nome');
					$dtCadastro=converteData(resultadoSQL($consulta, $a, 'dtCadastro'),'banco','formdata');
					$dtAtivacao=converteData(resultadoSQL($consulta, $a, 'dtAtivacao'),'banco','formdata');
					$status=resultadoSQL($consulta, $a, 'status');
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=alterar&registro=$idPessoaTipo:$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=excluir&registro=$idPessoaTipo:$id>Excluir</a>",'excluir');
					
					if($status=='A') {
						$class='txtok';
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=inativar&registro=$idPessoaTipo:$id>Desativar</a>",'desativar');
					}
					elseif($status=='I') {
						$class='txtaviso';
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=ativar&registro=$idPessoaTipo:$id>Ativar</a>",'ativar');
					}
					else {
						$class='bold10';
					}
					
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=parametros&registro=$idPessoaTipo:$id>Parâmetros</a>",'config');
					
					# Transferência de dominios entre serviços planos / pessoas tipos
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=transferir&registro=$idPessoaTipo:$id>Transferir</a>",'transferencia');
					
					# Sincronizar Contas de Email do Domínio
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=sincronizar&registro=$idPessoaTipo:$id>Re-sincronizar Contas</a>",'sincronizar');
					
					#visualizar servico/plano.
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>Visualizar Plano</a>", 'planos');
					
					novaLinhaTabela($corFundo, '100%');
						$nome="<img src=".$html[imagem][dominio]." border=0 align=left vspace=10>$nome<br><span class=normal8><b>Cadastro:</b> $dtCadastro<br><b>Ativação:</b> $dtAtivacao</span>";
						itemLinhaTMNOURL($nome, 'left','middle','40%',$corFundo,0,'normal10');
						itemLinhaTMNOURL(formSelectStatusDominios($status,'','check'), 'center','middle','15%',$corFundo,0,'normal8');
						itemLinhaTMNOURL($opcoes, 'left','middle','45%',$corFundo,0,'normal8');
					fechaLinhaTabela();
				}
			}
			else {
				$texto="<span class=txtaviso>Não existem domínios cadastrados!</span>";
				itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
			}
		fechaTabela();
	}
}



# Contar contas em uso
function dominioTotalContasEmUso($idPessoaTipo) {

	if($idPessoaTipo) {
		$consulta=buscaDominiosServicosPlanos($idPessoaTipo,'idPessoasTipos','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) $retorno=contaConsulta($consulta);
		else $retorno=0;
	}
	
	return($retorno);
}


# Contar contas em uso
function dominioTotalContasServicoEmUso($idPessoaTipo, $idServicoPlano) {

	if($idPessoaTipo) {
		$consulta=buscaDominiosServicosPlanos("idPessoasTipos=$idPessoaTipo AND idServicosPlanos=$idServicoPlano",'','custom','id');
		
		if($consulta && contaConsulta($consulta)>0) $retorno=contaConsulta($consulta);
		else $retorno=0;
	}
	
	return($retorno);
}



# Função para totalização de parametros
function dominioTotalContas($idPessoaTipo) {

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
			AND $tb[Modulos].modulo='dominio'
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
function dominioTotalContasServico($idPessoaTipo, $idServicoPlano) {

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
			AND $tb[Modulos].modulo='dominio'
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
function dbDominiosServicosPlanos($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[DominiosServicosPlanos] VALUES (
			0, 
			'$matriz[idServicosPlanos]', 
			'$matriz[idDominio]', 
			'$matriz[idPessoasTipos]'
		)";
	} #fecha inclusao
	
	# Excluir
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[DominiosServicosPlanos] WHERE id=$matriz[id]";
	}
	
	elseif($tipo=='excluirdominio') {
		$sql="DELETE FROM $tb[DominiosServicosPlanos] WHERE idDominio=$matriz[id]";
	}

	elseif($tipo=='transferir') {
		$sql="
			UPDATE 
				$tb[DominiosServicosPlanos] 
			SET
				idPessoasTipos='$matriz[idPessoaTipo]',
				idServicosPlanos='$matriz[idServicoPlano]'
			WHERE 
				idDominio=$matriz[id]";
	}
	

	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Funcao para cadastro de usuarios
function excluirDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}	
	else {
	
		if(!$matriz[bntExcluir]) {
			# Procurar registro
			$sql="
				SELECT
					$tb[Dominios].id idDominio,
					$tb[Dominios].nome nome,
					$tb[Dominios].descricao descricao,
					$tb[Dominios].dtCadastro dtCadastro,
					$tb[Dominios].status status,
					$tb[DominiosServicosPlanos].id idDominioServicosPlanos,
					$tb[DominiosServicosPlanos].idPessoasTipos idPessoasTipos,
					$tb[DominiosServicosPlanos].idServicosPlanos idServicosPlanos,
					$tb[Servicos].nome nomeServico,
					$tb[PlanosPessoas].id idPlano
				FROM 
					$tb[Dominios],
					$tb[DominiosServicosPlanos],
					$tb[Servicos],
					$tb[ServicosPlanos],
					$tb[PlanosPessoas]
				WHERE
					$tb[Dominios].id=$tb[DominiosServicosPlanos].idDominio
					AND $tb[Servicos].id=$tb[ServicosPlanos].idServico
					AND $tb[ServicosPlanos].id=$tb[DominiosServicosPlanos].idServicosPlanos
					AND $tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
					AND $tb[DominiosServicosPlanos].idDominio=$matriz[id]
			";
			
			$consulta=consultaSQL($sql, $conn);
			
			# Form de exclusao
			if($consulta && contaConsulta($consulta)>0) {
			
				$dominio=resultadoSQL($consulta, 0, 'nome');
				$descricao=resultadoSQL($consulta, 0, 'descricao');
				$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
				$status=resultadoSQL($consulta, 0, 'status');
				$idDominioServicosPlanos=resultadoSQL($consulta, 0, 'idDominioServicosPlanos');
				$idDominio=resultadoSQL($consulta, 0, 'idDominio');
				$idPessoasTipos=resultadoSQL($consulta, 0, 'idPessoasTipos');
				$nomeServico=resultadoSQL($consulta, 0, 'nomeServico');
				$idPlano=resultadoSQL($consulta, 0, 'idPlano');
				$idServicosPlanos=resultadoSQL($consulta, 0, 'idServicosPlanos');
				# Montar URL para acesso ao serviço
				$nomeServico="<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>
				<img src=".$html[imagem][planos]." align=right border=0 alt='Visualizar Serviço '></a>$nomeServico";
			
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
						<input type=hidden name=registro value=$registro:$idDominio>
						<input type=hidden name=matriz[id] value=$idDominio>
						<input type=hidden name=matriz[idPessoasTipos] value=$registro>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Domínio: </b>";
						htmlFechaColuna();
						itemLinhaForm($dominio, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Descricao: </b>";
						htmlFechaColuna();
						itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Serviço: </b>";
						htmlFechaColuna();
						itemLinhaForm($nomeServico, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Status: </b>";
						htmlFechaColuna();
						itemLinhaForm(formSelectStatusDominios($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					
					# Mostrar informações sobre Domínio
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
							listarDominiosParametros($modulo, $sub, $acao, $matriz[id], $matriz);
						htmlFechaColuna();
					fechaLinhaTabela();
					
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			}
		} #fecha form
		elseif($matriz[bntExcluir]) {
			# Excluir dominio servicos planos
			$dominio=dadosDominio($matriz[id]);
			$matriz[dominio]=$dominio[nome];
			$grava=dbDominio($matriz, 'excluir');
			
			if($grava) {
				# Graver dominio servicos planos
				$grava2=dbDominiosServicosPlanos($matriz, 'excluirdominio');
				
				# Excluir parametros
				$grava3=dbDominiosParametros($matriz, 'excluir');
				
				# Excluir contas de email
				$grava4=emailRemoverDominio($matriz[id]);
				
				# Excluir dominio do manager
				$parametros=carregaParametrosConfig();
				if($parametros[manager_dominio_del]=='automatico') {
					$gravaManager=managerComando($matriz, 'dominioexcluir');
				}
				
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
				$matriz[idModulo]=$matriz[idModulo];
				listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Apagar Dominio gravado
				$msg="Ocorreu um erro na tentativa de excluir o registro!";
				avisoNOURL("Aviso", $msg, 400);
			}
		}
		else {
			# Dominio nao encontrado
		}
	}
		
} # fecha funcao de inclusao de grupos



# Funcao para cadastro de usuarios
function alterarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}	
	else {
	
		if(!$matriz[bntAlterar]) {
			# Procurar registro
			$sql="
				SELECT
					$tb[Dominios].id idDominio,
					$tb[Dominios].nome nome,
					$tb[Dominios].descricao descricao,
					$tb[Dominios].dtCadastro dtCadastro,
					$tb[Dominios].dtAtivacao dtAtivacao,
					$tb[Dominios].status status,
					$tb[DominiosServicosPlanos].id idDominioServicosPlanos,
					$tb[DominiosServicosPlanos].idPessoasTipos idPessoasTipos,
					$tb[DominiosServicosPlanos].idServicosPlanos idServicosPlanos,
					$tb[Servicos].nome nomeServico,
					$tb[PlanosPessoas].id idPlano
				FROM 
					$tb[Dominios],
					$tb[DominiosServicosPlanos],
					$tb[Servicos],
					$tb[ServicosPlanos],
					$tb[PlanosPessoas]
				WHERE
					$tb[Dominios].id=$tb[DominiosServicosPlanos].idDominio
					AND $tb[Servicos].id=$tb[ServicosPlanos].idServico
					AND $tb[ServicosPlanos].id=$tb[DominiosServicosPlanos].idServicosPlanos
					AND $tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
					AND $tb[DominiosServicosPlanos].idDominio=$matriz[id]
			";
			
			$consulta=consultaSQL($sql, $conn);
			
			# Form de exclusao
			if($consulta && contaConsulta($consulta)>0) {
			
				$dominio=resultadoSQL($consulta, 0, 'nome');
				$descricao=resultadoSQL($consulta, 0, 'descricao');
				$dtCadastro=converteData(resultadoSQL($consulta, 0, 'dtCadastro'),'banco','formdata');
				$dtAtivacao=converteData(resultadoSQL($consulta, 0, 'dtAtivacao'),'banco','formdata');
				
				$status=resultadoSQL($consulta, 0, 'status');
				$idDominioServicosPlanos=resultadoSQL($consulta, 0, 'idDominioServicosPlanos');
				$idDominio=resultadoSQL($consulta, 0, 'idDominio');
				$idPessoasTipos=resultadoSQL($consulta, 0, 'idPessoasTipos');
				$nomeServico=resultadoSQL($consulta, 0, 'nomeServico');
				$idPlano=resultadoSQL($consulta, 0, 'idPlano');
				$idServicosPlanos=resultadoSQL($consulta, 0, 'idServicosPlanos');
				# Montar URL para acesso ao serviço
				$nomeServico="<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>
				<img src=".$html[imagem][planos]." align=right border=0 alt='Visualizar Serviço '></a>$nomeServico";
			
				novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					menuOpcAdicional($modulo, $sub, $acao, $registro);				
					#fim das opcoes adicionais
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro:$idDominio>
						<input type=hidden name=matriz[id] value=$idDominio>
						<input type=hidden name=matriz[idPessoasTipos] value=$registro>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Domínio: </b>";
						htmlFechaColuna();
						itemLinhaForm($dominio, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Descricao: </b>";
						htmlFechaColuna();
						itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Serviço: </b>";
						htmlFechaColuna();
						itemLinhaForm($nomeServico, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Status: </b>";
						htmlFechaColuna();
						itemLinhaForm(formSelectStatusDominios($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Cadastro: </b>";
						htmlFechaColuna();
						$texto="<input name=matriz[dtCadastro] value='$dtCadastro' size=10 onBlur=verificaData(this.value,6)>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Ativação: </b>";
						htmlFechaColuna();
						$texto="<input name=matriz[dtAtivacao] value='$dtAtivacao' size=10 onBlur=verificaData(this.value,7)>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			}
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# Excluir dominio servicos planos
			$dominio=dadosDominio($matriz[id]);
			$matriz[dominio]=$dominio[nome];
			
			$matriz[dtAtivacao]=converteData($matriz[dtAtivacao],'form','banco');
			$matriz[dtCadastro]=converteData($matriz[dtCadastro],'form','banco');

			$grava=dbDominio($matriz, 'alterar');

			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
				$matriz[idModulo]=$matriz[idModulo];
				listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Apagar Dominio gravado
				$msg="Ocorreu um erro na tentativa de excluir o registro!";
				avisoNOURL("Aviso", $msg, 400);
			}
		}
		else {
			# Dominio nao encontrado
		}
	}
		
} # fecha funcao de inclusao de grupos


# Funcao para cadastro de usuarios
function inativarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;

	if(!$matriz[bntInativar]) {
		# Procurar registro
		$sql="
			SELECT
				$tb[Dominios].id idDominio,
				$tb[Dominios].nome nome,
				$tb[Dominios].descricao descricao,
				$tb[Dominios].dtCadastro dtCadastro,
				$tb[Dominios].status status,
				$tb[DominiosServicosPlanos].id idDominioServicosPlanos,
				$tb[DominiosServicosPlanos].idPessoasTipos idPessoasTipos,
				$tb[DominiosServicosPlanos].idServicosPlanos idServicosPlanos,
				$tb[Servicos].nome nomeServico,
				$tb[PlanosPessoas].id idPlano
			FROM 
				$tb[Dominios],
				$tb[DominiosServicosPlanos],
				$tb[Servicos],
				$tb[ServicosPlanos],
				$tb[PlanosPessoas]
			WHERE
				$tb[Dominios].id=$tb[DominiosServicosPlanos].idDominio
				AND $tb[Servicos].id=$tb[ServicosPlanos].idServico
				AND $tb[ServicosPlanos].id=$tb[DominiosServicosPlanos].idServicosPlanos
				AND $tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
				AND $tb[DominiosServicosPlanos].idDominio=$matriz[id]
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		# Form de exclusao
		if($consulta && contaConsulta($consulta)>0) {
		
			$dominio=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			$idDominioServicosPlanos=resultadoSQL($consulta, 0, 'idDominioServicosPlanos');
			$idDominio=resultadoSQL($consulta, 0, 'idDominio');
			$idPessoasTipos=resultadoSQL($consulta, 0, 'idPessoasTipos');
			$nomeServico=resultadoSQL($consulta, 0, 'nomeServico');
			$idPlano=resultadoSQL($consulta, 0, 'idPlano');
			$idServicosPlanos=resultadoSQL($consulta, 0, 'idServicosPlanos');
			# Montar URL para acesso ao serviço
			$nomeServico="<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>
			<img src=".$html[imagem][planos]." align=right border=0 alt='Visualizar Serviço '></a>$nomeServico";
		
			novaTabela2("[Inativar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$idDominio>
					<input type=hidden name=matriz[id] value=$idDominio>
					<input type=hidden name=matriz[idPessoasTipos] value=$registro>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Domínio: </b>";
					htmlFechaColuna();
					itemLinhaForm($dominio, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descricao: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Serviço: </b>";
					htmlFechaColuna();
					itemLinhaForm($nomeServico, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusDominios($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntInativar] value=Inativar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
	} #fecha form
	elseif($matriz[bntInativar]) {
		# Excluir dominio servicos planos
		$dominio=dadosDominio($matriz[id]);
		$matriz[dominio]=$dominio[nome];
		$grava=dbDominio($matriz, 'inativar');
		
		if($grava) {
			# Excluir dominio do manager
			$gravaManager=managerComando($matriz, 'dominioinativar');
			
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
			$matriz[idModulo]=$matriz[idModulo];
			listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
			# Apagar Dominio gravado
			$msg="Ocorreu um erro na tentativa de inativar o registro!";
			avisoNOURL("Aviso", $msg, 400);
		}
	}
	else {
		# Dominio nao encontrado
	}
		
} # fecha funcao de inclusao de grupos




# Funcao para cadastro de usuarios
function ativarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;

	if(!$matriz[bntAtivar]) {
		# Procurar registro
		$sql="
			SELECT
				$tb[Dominios].id idDominio,
				$tb[Dominios].nome nome,
				$tb[Dominios].descricao descricao,
				$tb[Dominios].dtCadastro dtCadastro,
				$tb[Dominios].status status,
				$tb[DominiosServicosPlanos].id idDominioServicosPlanos,
				$tb[DominiosServicosPlanos].idPessoasTipos idPessoasTipos,
				$tb[DominiosServicosPlanos].idServicosPlanos idServicosPlanos,
				$tb[Servicos].nome nomeServico,
				$tb[PlanosPessoas].id idPlano
			FROM 
				$tb[Dominios],
				$tb[DominiosServicosPlanos],
				$tb[Servicos],
				$tb[ServicosPlanos],
				$tb[PlanosPessoas]
			WHERE
				$tb[Dominios].id=$tb[DominiosServicosPlanos].idDominio
				AND $tb[Servicos].id=$tb[ServicosPlanos].idServico
				AND $tb[ServicosPlanos].id=$tb[DominiosServicosPlanos].idServicosPlanos
				AND $tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
				AND $tb[DominiosServicosPlanos].idDominio=$matriz[id]
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		# Form de exclusao
		if($consulta && contaConsulta($consulta)>0) {
		
			$dominio=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			$idDominioServicosPlanos=resultadoSQL($consulta, 0, 'idDominioServicosPlanos');
			$idDominio=resultadoSQL($consulta, 0, 'idDominio');
			$idPessoasTipos=resultadoSQL($consulta, 0, 'idPessoasTipos');
			$nomeServico=resultadoSQL($consulta, 0, 'nomeServico');
			$idPlano=resultadoSQL($consulta, 0, 'idPlano');
			$idServicosPlanos=resultadoSQL($consulta, 0, 'idServicosPlanos');
			# Montar URL para acesso ao serviço
			$nomeServico="<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>
			<img src=".$html[imagem][planos]." align=right border=0 alt='Visualizar Serviço '></a>$nomeServico";
		
			novaTabela2("[Ativar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$idDominio>
					<input type=hidden name=matriz[id] value=$idDominio>
					<input type=hidden name=matriz[idPessoasTipos] value=$registro>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Domínio: </b>";
					htmlFechaColuna();
					itemLinhaForm($dominio, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descricao: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Serviço: </b>";
					htmlFechaColuna();
					itemLinhaForm($nomeServico, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusDominios($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAtivar] value=Ativar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
	} #fecha form
	elseif($matriz[bntAtivar]) {
		# Excluir dominio servicos planos
		$dominio=dadosDominio($matriz[id]);
		$matriz[dominio]=$dominio[nome];
		
		# Ativar todo o dominio
		$matriz[restricao]='liberar';
		$gravaManager=managerComando($matriz, 'configuracoes');
		
		$grava=dbDominio($matriz, 'ativar');
		
		if($grava) {
			# Selecionar todos os emails do dominio para ativação
			$consulta=buscaEmails($matriz[id],'idDominio','igual','id');
			
			if($consulta && contaConsulta($consulta)>0) {
				# aplicar configurações armazenadas por conta
				for($a=0;$a<contaConsulta($consulta);$a++) {
					$matriz[idEmail]=resultadoSQL($consulta, $a, 'id');
					$matriz[login]=resultadoSQL($consulta, $a, 'login');
					
					emailAplicaConfiguracaoEmail($matriz);
				}
			}
			
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
			$matriz[idModulo]=$matriz[idModulo];
			listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
			# Apagar Dominio gravado
			$msg="Ocorreu um erro na tentativa de inativar o registro!";
			avisoNOURL("Aviso", $msg, 400);
		}
	}
	else {
		# Dominio nao encontrado
	}
		
} # fecha funcao de inclusao de grupos



# Rotina para exclusão de dominios e configurações relacionadas ao um servico
function dominiosExcluirServico($idServicosPlanos) {

	$parametros=carregaParametrosConfig();
	
	# Buscar Dominios Servicos Planos
	$consultaDominiosServicos=buscaDominiosServicosPlanos($idServicosPlanos, 'idServicosPlanos','igual','id');
	
	if($consultaDominiosServicos && contaConsulta($consultaDominiosServicos)>0) {
		
		for($a=0;$a<contaConsulta($consultaDominiosServicos);$a++) {
			$matriz[id]=resultadoSQL($consultaDominiosServicos, $a, 'idDominio');
			
			$dominio=dadosDominio($matriz[id]);
			$matriz[dominio]=$dominio[nome];
			
			# Excluir dominio do manager
			if($parametros[manager_dominio_del]=='automatico') {
				$gravaManager=managerComando($matriz, 'dominioexcluir');
			}
			
			# Excluir Dominio
			dbDominio($matriz, 'excluir');
			
			# Excluir Parametros do dominio
			dbDominiosParametros($matriz, 'excluir');
			
			# Excluir Dominios Servicos Planos
			dbDominiosServicosPlanos($matriz,'excluirdominio');
		}
	}
}

/**
 * rotina para cancelamento de dominios gerados pelo cancelamento do servico, assim como lancamento para o manager
 * estar excluindo esta conta no servidor dns...
 * */

function dominiosCancelamentoServico($idServicosPlanos) {

	$parametros=carregaParametrosConfig();
	
	# Buscar Dominios Servicos Planos
	$consultaDominiosServicos=buscaDominiosServicosPlanos($idServicosPlanos, 'idServicosPlanos','igual','id');
	
	if($consultaDominiosServicos && contaConsulta($consultaDominiosServicos)>0) {
		
		for($a=0;$a<contaConsulta($consultaDominiosServicos);$a++) {
			$matriz[id]=resultadoSQL($consultaDominiosServicos, $a, 'idDominio');
			
			$dominio=dadosDominio($matriz[id]);
			$matriz[dominio]=$dominio[nome];
			
			# Excluir dominio do manager 
			if($parametros[manager_dominio_del]=='automatico') {
				$gravaManager=managerComando($matriz, 'dominioexcluir');
			}
			
			# Excluir Dominio
			dbDominio($matriz, 'cancelar');
			
//			# Excluir Dominios Servicos Planos
//			dbDominiosServicosPlanos($matriz,'excluirdominio');
		}
	}
}



# Funcao para cadastro de usuarios
function importarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Procurar dominio
	$tmpNome=strtoupper($matriz[nome]);
	
	# Verificar se serviço existe
	$tmpBusca=buscaDominios("upper(nome)='$tmpNome'", $campo, 'custom', 'id');
	
	if(contaConsulta($tmpBusca)>0) {
		$msg="Domínio já existente!";
		avisoNOURL("Aviso", $msg, 400);
		echo "<br>";
	}
	
	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[lista] || $msg) {
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
				<input type=hidden name=matriz[idPessoasTipos] value=$registro>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Listagem de Domínios: </b><br>
					<span class=normal10>Identificação detalhada do dominio</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[lista] rows=5 cols=40>$matriz[descricao]</textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do dominio</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusDominios('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[lista]) {
			# Cadastrar em banco de dados
			$matriz[id]=buscaIDNovoDominio();
			
			# Quebrar listagem e remover ',' caso informadas
			$matriz[lista]=str_replace(",","\n",$matriz[lista]);
			$matDominios=explode("\n",$matriz[lista]);
			
			# Alimentar matriz com valores validos
			for($a=0;$a<count($matDominios);$a++) {
				if(strlen(trim($matDominios[$a]))>0) {
					$matDominiosOK[]=$matDominios[$a];
				}
			}
			
			novaTabela("[Resultado da Importação]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL("Domínio", 'center', 'middle', '80%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL("Importado", 'center', 'middle', '20%', $corFundo, 0, 'tabfundo0');
			fechaLinhaTabela();
			
			if(count($matDominios)>0) {
			
				for($a=0;$a<count($matDominiosOK);$a++) {

					# Verificar quantidade ainda disponível
					$total=dominioTotalContas($matriz[idPessoasTipos]);
					$totalEmUso=dominioTotalContasEmUso($matriz[idPessoasTipos]);

					if( $totalEmUso < $total ) {
						$matriz[idServicosPlanos]=buscaIDServicoDominioNovo($matriz[idPessoasTipos]);
						
						if($matriz[idServicosPlanos]) {
	
							$matriz[id]=buscaIDNovoDominio();
							$matriz[nome]=$matDominiosOK[$a];
							$matriz[descricao]="Importação de Domínio: $matDominiosOK[$a]";
							$matriz[dtCadastro]=$data[dataSistema];
							$matriz[dtAtivacao]=$matriz[dtCadastro];
							$matriz[dtBloqueio]='';
							$matriz[dtCongelamento]='';
							$matriz[padrao]='N';
							
							$grava=dbDominio($matriz, 'incluir');
							
							# Verificar inclusão de registro
							if($grava) {
							
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL($matDominiosOK[$a], 'left', 'middle', '80%', $corFundo, 0, 'normal10');
									itemLinhaTMNOURL("Sim", 'center', 'middle', '20%', $corFundo, 0, 'txtok');
								fechaLinhaTabela();
								
								# Graver dominio servicos planos
								$matriz[idDominio]=$matriz[id];
								$grava2=dbDominiosServicosPlanos($matriz, 'incluir');
								
								if($grava2) {
									# Gravar parâmetros do dominio
									dbDominiosParametros($matriz, 'incluir');
									
								}
								else {
									# Apagar Dominio gravado
									$grava=dbDominio($matriz, 'excluir');
								}
								
							}
						}
						else {
							# erro
						}
					}
					else {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL($matDominiosOK[$a], 'left', 'middle', '80%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL("Não", 'center', 'middle', '20%', $corFundo, 0, 'txtaviso');
						fechaLinhaTabela();
					}
				}
				
				fechaTabela();
				echo "<br>";
				
				$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
				listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
				
			}
			else {
				# nao ha dominios para importação
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
} # fecha funcao de inclusao de grupos




# Formulário de seleção de Tipo de Endereço
function buscaIDServicoDominioNovo($idPessoasTipos) {

	global $conn, $tb;

	# Checar os endereços que a pessoa já possui
	$sql="
		SELECT 
			$tb[ServicosPlanos].id idServicoPlano, 
			$tb[Parametros].id idParametro, 
			$tb[Modulos].modulo modulo, 
			$tb[Parametros].parametro parametro,  
			$tb[ServicosParametros].valor valor,
			$tb[PlanosPessoas].nome nomePlano,
			$tb[Servicos].nome nomeServico
		FROM 
			$tb[StatusServicos], 
			$tb[Parametros], 
			$tb[Modulos], 
			$tb[ParametrosModulos], 
			$tb[Servicos], 
			$tb[ServicosParametros],
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[PessoasTipos] 
		WHERE 
			$tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano
			AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
			AND $tb[Servicos].id = $tb[ServicosParametros].idServico 
			AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
			AND $tb[Parametros].id=$tb[ParametrosModulos].idParametro
			AND $tb[ParametrosModulos].idModulo=$tb[Modulos].id
			AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
			AND ($tb[StatusServicos].status = 'A' OR $tb[StatusServicos].status = 'T' OR $tb[StatusServicos].status = 'N')
			AND $tb[PlanosPessoas].idPessoaTipo=$idPessoasTipos
			AND $tb[Modulos].modulo = 'dominio' 
		GROUP BY
			$tb[ServicosPlanos].id
	";
		
	$consulta=consultaSQL($sql, $conn);

	if($consulta && contaConsulta($consulta)>0) {

		for($a=0;$a<contaConsulta($consulta);$a++) {
		
			$idServicoPlano=resultadoSQL($consulta, $a, 'idServicoPlano');
			$idParametro=resultadoSQL($consulta, $a, 'idParametro');
			$modulo=resultadoSQL($consulta, $a, 'modulo');
			$parametro=resultadoSQL($consulta, $a, 'parametro');
			$valor=resultadoSQL($consulta, $a, 'valor');
			$nomePlano=resultadoSQL($consulta, $a, 'nomePlano');
			$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');
			
			# Contabilizar total e contas configuradas para este serviço
			$totalContasServico=dominioTotalContasServico($idPessoasTipos, $idServicoPlano);
			$totalContasServicoEmUso=dominioTotalContasServicoEmUso($idPessoasTipos, $idServicoPlano);
			
			if($totalContasServico > $totalContasServicoEmUso) return($idServicoPlano);
		}
	}
}



# Função para procura 
function procurarDominioServicosPlanos($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a variável de busca
	if(!$matriz) {
		$matriz[bntProcurarDominio]=1;
		//$matriz[txtProcurarDominio]=$textoProcurar;
	} #fim da atribuicao de variaveis
	
	# Motrar tabela de busca
	novaTabela2("[Procurar Domínios]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar><b>Procurar por:</b>
			<input type=text name=matriz[txtProcurarDominio] size=40>
			<input type=submit name=matriz[bntProcurarDominio] value=Procurar class=submit>";
			itemLinhaForm($texto, 'center','middle', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if($matriz[txtProcurarDominio] && $matriz[bntProcurarDominio]) {
		#buscar registros
		$sql="
			SELECT
				$tb[Dominios].id, 
				$tb[Dominios].nome, 
				$tb[Dominios].dtCadastro,
				$tb[Dominios].status, 
				$tb[Dominios].padrao, 
				$tb[DominiosServicosPlanos].idPessoasTipos idPessoaTipo 
			FROM
				$tb[Dominios], 
				$tb[DominiosServicosPlanos] 
			WHERE 
				$tb[Dominios].id = $tb[DominiosServicosPlanos].idDominio 
				AND
					($tb[Dominios].nome like '%$matriz[txtProcurarDominio]%' 
					OR $tb[Dominios].descricao like '%$matriz[txtProcurarDominio]%' )";
				
		$consulta=consultaSQL($sql, $conn);

		echo "<br>";

		novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 10);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 10, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (is_numeric($registro) || !$registro)) {	
		
			
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurarDominio].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 10, 'txtaviso');

			# Paginador
			$urlADD="&matriz[txtProcurarDominio]=".$matriz[txtProcurarDominio]."&matriz[bntProcurarDominio]=1";
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][dominios], $registro, 'normal', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Data de Cadastro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Padrão', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '25%', 'tabfundo0');
			fechaLinhaTabela();
			
			# Setar registro inicial
			if(!$registro) {
				$i=0;
			}
			elseif($registro && is_numeric($registro) ) {
				$i=$registro;
			}
			else {
				$i=0;
			}
			
			$limite=$i+$limite[lista][dominios];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				$status=resultadoSQL($consulta, $i, 'status');
				$padrao=resultadoSQL($consulta, $i, 'padrao');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dominio&acao=parametros&registro=$idPessoaTipo:$id>Detalhes</a>",'ver');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro,'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectStatusDominios($status,'','check'), 'center', '15%', 'normal10');
					itemLinhaTabela(formSelectSimNao($padrao,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
		fechaTabela();
	} # fecha botão procurar
} #fecha funcao de  procura



# Função para transferência de dominio entre servicos Planos
function transferirDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $conn, $tb;

	# Mostrar dominio
	verDominios($modulo, $sub, $acao, $matriz[id], $matriz);
	echo "<br>";
	
	# Procurar DominioServicosPlanos
	$consulta=buscaDominiosServicosPlanos($matriz[id], 'idDominio','igual','id');

	if($consulta && contaConsulta($consulta)>0) {
		
		$id=resultadoSQL($consulta, 0, 'id');
		$idServicosPlanos=resultadoSQL($consulta, 0, 'idServicosPlanos');
		$idPessoasTipos=resultadoSQL($consulta, 0, 'idPessoasTipos');
		
		# Mostrar form de pesquisa por clientes que possuam hospedagem de dominios
		# e dominios disponíveis para criação
		
		if(!$matriz[idPessoaTipoNovo] || !$matriz[idServicoPlanoNovo] || !$matriz[bntTransferir]) {
		
			novaTabela2("[Transferir Domínio]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value='$registro:$matriz[id]'>
					<input type=hidden name=matriz[idPessoasTipos] value=$registro>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Cliente: </b><br>
						<span class=normal10>Nome do cliente:</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=20 value='$matriz[nome]'>";
					$texto.="&nbsp;<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit3>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if($matriz[bntSelecionar] || $matriz[bntSelecionarCliente]) {
				
					# Procurar Cliente
					$tipoPessoa=checkTipoPessoa('cli');
					
					$consulta=buscaPessoas("
						(upper(nome) like '%$matriz[nome]%' 
							OR upper(razao) like '%$matriz[nome]%') 
						AND idTipo=$tipoPessoa[id]", $campo, 'custom','nome');
					
					if($consulta && contaConsulta($consulta)>0) {
						# Selecionar cliente
						novaLinhaTabela($corFundo, '100%');
							$texto=formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipoNovo', $matriz[idPessoaTipo]);
							$texto.="<br><input type=submit name=matriz[bntSelecionarCliente] value=Selecionar class=submit3>";
							itemLinhaTMNOURL('<b>Clientes:</b><br>
							<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					
					if($matriz[bntSelecionarCliente]) {
						$texto=formSelectServicoPlanoDominio($matriz[idPessoaTipoNovo], $matriz[idServicosPlanos], 'idServicoPlanoNovo','form');
						if($texto) {
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
									echo "<b class=bold10>Serviço: </b><br>
									<span class=normal10>Serviço a atribuir este domínio</span>";
								htmlFechaColuna();
								itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
									echo "<b class=bold10>Status: </b><br>
									<span class=normal10>Status do dominio</span>";
								htmlFechaColuna();
								itemLinhaForm(formSelectStatusDominios('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							//alterado por gustavo em 20050317 para exibir a opcao de exclusao de antigo servico
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
									echo "<b class=bold10>Serviços: </b><br>
									<span class=normal10>Remover Serviço, após transferencia.</span>";
								htmlFechaColuna();
								$texto = "<input type=checkbox name=matriz[remover] value=sim>";
								itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							//\\
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
									echo "&nbsp;";
								htmlFechaColuna();
								$texto="<input type=submit name=matriz[bntTransferir] value=Transferir class=submit>";
								itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
					}
				}
			fechaTabela();
		}
		else {
			# Atualizar banco de dados com novo idPessoaTipo e novo idServicoPlano
			$matriz[idDominio]=$matriz[id];
			$matriz[idPessoaTipo]=$matriz[idPessoaTipoNovo];
			/**Primeiramente resgata o idServicoPlanoAntigo caso a opcao remover servico esteja ativada*/
			if ($matriz[remover]=="sim") {
				$dadosDominio = buscaDadosDominioServico($matriz[idDominio]);
				$matriz[idServicoPlanoAntigo] = $dadosDominio[idServicosPlanos];
			}
			$matriz[idServicoPlano]=$matriz[idServicoPlanoNovo];
			
			$grava=dbDominiosServicosPlanos($matriz, 'transferir');
			
			# listar dominios
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, "100%");
				echo "<br>";
				
				# migrar contas do dominio para novo idPessoaTipo
				dbEmail($matriz, 'transferir');
				
				/*novamente verifica se deve remover o servico plano, caso sim executa a funcao apropriada*/
				if ($matriz[remover]=="sim") {
					$data=dataSistema();
					$matriz[dtCancelamento] = $data[dataNormalData];
					$consulta=buscaDominiosServicosPlanos($matriz[idServicoPlanoAntigo], "idServicosPlanos", "igual", "idServicosPlanos");
					if($matriz[idServicoPlano]==$matriz[idServicoPlanoAntigo]){
						$msg="O dominio já se encontra neste Serviço.";
						avisoNOURL("Aviso", $msg, "100%");
						echo "<br>";
					}
					elseif (contaConsulta($consulta) > 0) {
						$msg="Este serviço possui outros domínios. <br>Serviço não cancelado.";
						avisoNOURL("Aviso", $msg, "100%");
						echo "<br>";
					}
					elseif ($matriz[idServicoPlanoAntigo]) {
						$matriz[id] = $matriz[idServicoPlanoAntigo];
						cancelarServicosPlanosAutomatico($modulo, $sub, $acao, $matriz[idServicoPlanoAntigo], $matriz);
						$matriz[id] = $matriz[idDominio];
					}
				}
				
				echo "<br>";
				$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
				listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			
		}
	}
}




# Função para sincronizar contas de email ao ISP-IT
function sincronizarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $conn, $tb;

	# Mostrar dominio
	verDominios($modulo, $sub, $acao, $matriz[id], $matriz);
	echo "<br>";
	
	# Procurar DominioServicosPlanos
	$consulta=buscaDominiosServicosPlanos($matriz[id], 'idDominio','igual','id');

	if($consulta && contaConsulta($consulta)>0) {
		
		$id=resultadoSQL($consulta, 0, 'id');
		$idServicosPlanos=resultadoSQL($consulta, 0, 'idServicosPlanos');
		$idPessoasTipos=resultadoSQL($consulta, 0, 'idPessoasTipos');
		
		# Mostrar form de pesquisa por clientes que possuam hospedagem de dominios
		# e dominios disponíveis para criação
		
		if(!$matriz[idPessoaTipoNovo] || !$matriz[idServicoPlanoNovo] || !$matriz[bntTransferir]) {
		
			novaTabela2("[Re-sincronização de Contas do Domínio]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value='$registro:$matriz[id]'>
					<input type=hidden name=matriz[idPessoasTipos] value=$registro>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="&nbsp;<input type=submit name=matriz[bntSincronizar] value='Sincronizar Contas' class=submit2>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				# Listagem de contas não encontradas no ISP
				$dominio=dadosDominio($matriz[id]);
				$consultaContas=buscaEmails($matriz[id],'idDominio','igual','id');
				if($consultaContas && contaConsulta($consultaContas)>0) {
					# Monstagem de SQL adicional para comparação de contas
					$sqlADDContas=" IN (";
					
					for($i=0;$i<contaConsulta($consultaContas);$i++) {
						$login=resultadoSQL($consultaContas, $i, 'login');
						$sqlADDContas.="'$login'";
						
						if(($i+1) < contaConsulta($consultaContas)) $sqlADDContas.=",";
					}
					
					$sqlADDContas.=")";
					
					$consultaContasEmail=vpopmailBuscaUsuario($dominio[nome], "pw_name NOT $sqlADDContas", '','custom','pw_name');
					
				} # fecha SQL adicional de busca de contas do dominio (cadastradas no ISP)
				else {
					$consultaContasEmail=vpopmailBuscaUsuario($dominio[nome], '', '','todos','pw_name');					
				}
				
				# Cabeçalho
				# Motrar tabela de busca
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('20%', 'right', $corFundo, 2, 'tabfundo1');
					novaTabela("[Contas Não Sincronizadas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					
					if(! $matriz[bntSincronizar]) {
						# Cabeçalho
						novaLinhaTabela($corFundo, '100%');
							itemLinhaForm('Conta', 'center', 'middle', $corFundo, 2, 'tabfundo0');
						fechaLinhaTabela();
					}
					
					if($consultaContasEmail && contaConsulta($consultaContasEmail)>0) {
						# mostrar contas de email disponíveis
						for($b=0;$b<contaConsulta($consultaContasEmail);$b++) {

							$login=resultadoSQL($consultaContasEmail, $b, 'pw_name');
							$senha_texto=resultadoSQL($consultaContasEmail, $b, 'pw_clear_passwd');
							
							if($matriz[bntSincronizar]) {
								# Gravar conta de email no dominio
								# Cadastrar em banco de dados
								$matriz[idEmail]=buscaIDNovoEmail();
								$matriz[login]=$login;
								$matriz[idPessoaTipo]=$idPessoasTipos;
								$matriz[idDominio]=$matriz[id];
								$matriz[senha_conta]=$senha_texto;
								$matriz[status]=$dominio[status];
								
								dbEmail($matriz, 'incluir');
								
								emailAdicionaConfiguracao($matriz[idEmail], $matriz[idDominio]);
								
								emailAplicaConfiguracaoEmail($matriz);
							}
							else {
								novaLinhaTabela($corFundo, '100%');
									itemLinhaForm("$login@$dominio[nome]", 'center', 'middle', $corFundo, 2, 'normal10');
								fechaLinhaTabela();
							}
						}
						
						if( $matriz[bntSincronizar]) {
							# Cabeçalho
							novaLinhaTabela($corFundo, '100%');
								itemLinhaForm('Contas sincronizadas com sucesso!', 'left', 'middle', $corFundo, 2, 'txtaviso');
							fechaLinhaTabela();
						}
					}
					else {
						# Não há registros
						itemTabelaNOURL('Não há contas contas não sincronizadas ao sistema', 'left', $corFundo, 6, 'txtaviso');
					}
					
					
					fechaTabela();
					htmlFechaColuna();
				fechaLinhaTabela();
				
				if($matriz[bntSelecionar] || $matriz[bntSelecionarCliente]) {
				
					# Procurar Cliente
					$tipoPessoa=checkTipoPessoa('cli');
					
					$consulta=buscaPessoas("
						(upper(nome) like '%$matriz[nome]%' 
							OR upper(razao) like '%$matriz[nome]%') 
						AND idTipo=$tipoPessoa[id]", $campo, 'custom','nome');
					
					if($consulta && contaConsulta($consulta)>0) {
						# Selecionar cliente
						novaLinhaTabela($corFundo, '100%');
							$texto=formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipoNovo', $matriz[idPessoaTipo]);
							$texto.="<br><input type=submit name=matriz[bntSelecionarCliente] value=Selecionar class=submit3>";
							itemLinhaTMNOURL('<b>Clientes:</b><br>
							<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					
					if($matriz[bntSelecionarCliente]) {
						$texto=formSelectServicoPlanoDominio($matriz[idPessoaTipoNovo], $matriz[idServicosPlanos], 'idServicoPlanoNovo','form');
						if($texto) {
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
									echo "<b class=bold10>Serviço: </b><br>
									<span class=normal10>Serviço a atribuir este domínio</span>";
								htmlFechaColuna();
								itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
									echo "<b class=bold10>Status: </b><br>
									<span class=normal10>Status do dominio</span>";
								htmlFechaColuna();
								itemLinhaForm(formSelectStatusDominios('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
									echo "&nbsp;";
								htmlFechaColuna();
								$texto="<input type=submit name=matriz[bntTransferir] value=Transferir class=submit>";
								itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
					}
				}
			fechaTabela();
		}
		else {
			# Atualizar banco de dados com novo idPessoaTipo e novo idServicoPlano
			$matriz[idDominio]=$matriz[id];
			$matriz[idPessoaTipo]=$matriz[idPessoaTipoNovo];
			$matriz[idServicoPlano]=$matriz[idServicoPlanoNovo];
			
			$grava=dbDominiosServicosPlanos($matriz, 'transferir');
			
			# listar dominios
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# migrar contas do dominio para novo idPessoaTipo
				dbEmail($matriz, 'transferir');
				
				echo "<br>";
				$matriz[idPessoaTipo]=$matriz[idPessoasTipos];
				listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			
		}
	}
}

function buscarNomesDominiosServicosPlanos ($idServicosPlanos) {
	global $tb, $conn ;
		if ($idServicosPlanos) {

		$sql = "
Select
  nome 
FROM 
  $tb[Dominios]
INNER JOIN 
  $tb[DominiosServicosPlanos]
  On($tb[Dominios].id = $tb[DominiosServicosPlanos].idDominio)
where $tb[DominiosServicosPlanos].idServicosPlanos = $idServicosPlanos";

		$consultaDominio = consultaSQL($sql, $conn);
		if(contaConsulta($consultaDominio) > 0){
			$dominio = " <span class=normal9> <br> [ ";
			for ($i = 0; $i < contaConsulta($consultaDominio); $i++) 
				$dominio .= resultadoSQL($consultaDominio, $i, "nome") . " ";
			$dominio .= "] </span>";
		}	
	}
	return ($dominio);
}


?>
