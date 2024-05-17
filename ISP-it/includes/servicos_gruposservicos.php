<?
################################################################################
#       Criado por: Hugo Ribeiro - hugo@devel-it.com.br
#  Data de criação: 24/03/2004
# Ultima alteração: 24/03/2004
#    Alteração No.: 001
#
# Função:
/**
	Administra os serviços para os Grupos de Serviços
*/


# Função de banco de dados
function dbServicoGruposServicos($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	$tabela="ServicosGrupos";
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[$tabela] VALUES (
		$matriz[idGrupos],
		'$matriz[idServico]')";
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM 
					$tb[$tabela] 
				WHERE 
					idGrupos=$matriz[idGrupos] 
					AND idServico=$matriz[idServico]";
	}
	if($sql) { 
		//echo "SQL: $sql<br>";
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


# função de busca 
function buscaServicoGruposServicos($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	$sql="SELECT Servicos.id as id, Servicos.nome as nome, Servicos.descricao as descricao 
							FROM $tb[ServicosGrupos], $tb[GruposServicos], $tb[Servicos] 
							WHERE GruposServicos.id=ServicosGrupos.idGrupos 
							and ServicosGrupos.idServico=Servicos.id ";
	if($tipo=='todos') {
		#all
	}
	elseif($tipo=='contem') {
		$sql.="and $campo LIKE '%$texto%' ";
	}
	elseif($tipo=='igual') {
		$sql.="and $campo='$texto' ";
	}
	elseif($tipo=='custom') {
		$sql.="and  $texto ";
	}
	else {
		$sql="";
	}
	
	# Verifica consulta
	if($sql){
		#adiciona o campo order by
		$sql.="ORDER BY $ordem";
		//echo "SQL bsgs: $sql";
		$consulta=consultaSQL($sql, $conn);
		# Retornar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca



function servicoGruposServicos($modulo, $sub, $acao, $registro, $matriz) {

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
		verGruposServicos($modulo, $sub, $acao, $registro, $matriz);	
		itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');	
		listarServicoGruposServicos($modulo, $sub, $acao, $registro, $matriz);
	}
	
}


# Função para listagem 
function listarServicoGruposServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Seleção de registros
	$consulta=buscaServicoGruposServicos($registro, 'idGrupos','igual','nome');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionar&registro=$registro>Adicionar</a>",'incluir');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 4, 'tabfundo1');
	
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há serviços cadastrados", 'left', $corFundo, 4, 'txtaviso');
	}
	else {


		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há serviços configurados para este módulo', 'left', $corFundo, 4, 'txtaviso');
		}
		else {

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Serviço', 'left', '25%', 'tabfundo0');
				itemLinhaTabela('Descrição', 'left', '50%', 'tabfundo0');
				itemLinhaTabela('Opções', 'left', '20%', 'tabfundo0');
			fechaLinhaTabela();

			$i=0;
			
			while($i < contaConsulta($consulta)) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".excluir."&registro=$registro:$id>Excluir</a>",'excluir');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '25%', 'normal10');
					itemLinhaTabela($descricao, 'left', '50%', 'normal10');
					itemLinhaTabela($opcoes, 'left', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
			
			fechaTabela();
		} #fecha servicos encontrados
	} #fecha listagem
}



# Funcao para cadastro de servicos
function adicionarServicoGruposServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	verGruposServicos($modulo, $sub, $acao, $registro, $matriz);	
	
	# Form de inclusao
	if(!$matriz[bntAdicionar]) {
		# Seleção de registros
		$consulta=buscaServicos($registro, 'id', 'todos', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Serviço não encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informações sobre o Grupo escolhido
			
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
					<input type=hidden name=matriz[idGrupos] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp";
					
			    	itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Serviço: </b><br>
						<span class=normal10>Selecione o serviço para o grupo</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicoGruposServicos($registro, $matriz[ids], 'ids', 'multi'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();

				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
			fechaTabela();
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[ids]) {
			# Cadastrar em banco de dados
			$i=0;
			while ($matriz[ids][$i]) {
				$matriz[idServico]=$matriz[ids][$i];
				$grava=dbServicoGruposServicos($matriz, 'incluir');
				$i++;
			}
			# Verificar inclusão de registro
			if($grava) {
				# OK
				echo "<br>";
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				listarServicoGruposServicos($modulo, $sub, 'servicos', $registro, $matriz);
			} else {
				# Mensagem de aviso - nao gravou
				$msg="Erro na gravação.";
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
				aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
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




# Funcao para excluir servicos
function excluirServicoGruposServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Quebrar registro
	$matRegistro=explode(":", $registro);
	
	$matriz[idGrupos]=$matRegistro[0];
	$matriz[idServico]=$matRegistro[1];

	# Mostrar Informações sobre Grupo
	verGruposServicos($modulo, $sub, $acao, $matriz[idGrupos], $matriz);

	# Form de inclusao
	if(!$matriz[bntExcluir]) {

		# Seleção de registros
		$consulta=buscaServicoGruposServicos("idServico=$matriz[idServico] AND idGrupos=$matriz[idGrupos]", '', 'custom', 'idServico');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Serviço não encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			echo "<br>";
	
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
					<input type=hidden name=matriz[idServico] value=$matriz[idServico]>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Serviço: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectServicoGruposServicos($matriz[idServico], $matriz[idGrupos], '', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[idGrupos] && $matriz[idServico]) {
			# Cadastrar em banco de dados
			$grava=dbServicoGruposServicos($matriz, 'excluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# Mensagem de aviso
				echo "<br>";
				$msg="Serviço excluído!";
				$url="?modulo=$modulo&sub=$sub&acao=servicos_gruposservicos&registro=$matriz[idServico]:$matriz[idGrupos]";
				avisoNOURL("Aviso", $msg, 400);
				echo "<br>";
				
				listarServicoGruposServicos($modulo, $sub, 'servicos', $matriz[idGrupos],$matriz);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao excluir o serviço!";
				$url="?modulo=$modulo&sub=$sub&acao=servicos_gruposservicos&registro=$matriz[idServico]:$matriz[idGrupos]";
				aviso("Aviso", $msg, $url, 400);
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
} # fecha funcao de excluir



# Função para montar campo de formulario
function formSelectServicoGruposServicos($servico, $parametro, $campo, $tipo) {

	if($tipo=='check') {
	
		$consulta=buscaServicos($servico,'id','igual','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');

			$retorno=$nome;
		}
	
	}
	elseif($tipo=='form') {
	
		$consulta=buscaServicos('','','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select name=matriz[$campo]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				
				if($servico==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$nome";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	elseif($tipo=='multi') {
		#busca os que ja existem na tabela
		$jatem=buscaServicoGruposServicos($servico, 'idGrupos', 'igual', 'idServico');

		$consulta=buscaServicos('','','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select multiple size=6 name=matriz[$campo][]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
				$ok=1;
				$id=resultadoSQL($consulta, $i, 'id');
				if($jatem && contaConsulta($jatem)>0) {
					#verifica se o id ja esta na lista
					for ($p=0;$p<contaConsulta($jatem);$p++){
						$id2=resultadoSQL($jatem, $p, 'id');	
						if($id == $id2) $ok=0;
					}
				}
				if ($ok) { #se ja existir na lista ele negativa a inclusao
					$nome=resultadoSQL($consulta, $i, 'nome');
					$descricao=resultadoSQL($consulta, $i, 'descricao');
					
					if($parametro==$id) $opcSelect='selected';
					else $opcSelect='';
					
					$retorno.="\n<option value=$id $opcSelect>$nome";
				}
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
	
} #fecha funcao de montagem de campo de form



# Função para totalização de parametros
function totalServicoGruposServicosModulo($idPessoaTipo, $idModulo, $idServico, $idParametro) {

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
