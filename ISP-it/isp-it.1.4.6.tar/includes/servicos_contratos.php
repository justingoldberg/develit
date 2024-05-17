<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 11/03/2004
# Ultima alteração: 11/03/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastro de contratos dos serviços

# Contatos dos Serviços
function servicosContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		if($acao=='contratos') listarServicosContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='contratosadicionar') adicionarServicosContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='contratosexcluir') excluirServicosContratos($modulo, $sub, $acao, $registro, $matriz);
	}
}


# função de busca 
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
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca



# Função de banco de dados - Contratos Servicos
function dbServicoContrato($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
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
	
	# Seleção de registros
	$consulta=buscaServicosContratos($registro, 'idServico','igual','idServico');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Contratos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contratosadicionar&registro=$registro>Adicionar</a>",'incluir');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
	
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há contratos cadastrados para este serviço!", 'left', $corFundo, 3, 'txtaviso');
	}
	else {
		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Contrato', 'center', '50%', 'tabfundo0');
			itemLinhaTabela('Validade', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
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

	# Seleção de registros
	$consulta=buscaServicos($registro, 'id', 'igual', 'id');
	
	if(!$consulta || contaConsulta($consulta)==0) {
		# Servidor não encontrado
		itemTabelaNOURL('Serviço não encontrado!', 'left', $corFundo, 3, 'txtaviso');
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
							echo "<b>Meses de Vigência: </b><br>
							<span class=normal10>Validade padrão do Contrato</span>";
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
					
				# Verificar inclusão de registro
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
				$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
				aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
			}
		}		
	} #fecha form
} # fecha funcao de inclusao de servicos




# Funcao para cadastro de servicos
function excluirServicosContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {

		# Seleção de registros
		$consulta=buscaServicosContratos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Contrato não encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			$idServico=resultadoSQL($consulta, 0, 'idServico');
			$idContrato=resultadoSQL($consulta, 0, 'idContrato');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$mesValidade=resultadoSQL($consulta, 0, 'mesValidade');
			
			# Mostrar Informações sobre Servidor
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
			
		# Verificar inclusão de registro
		if($grava) {
		
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);

			echo "<br>";
			listarServicosContratos($modulo, $sub, 'contratos',$matriz[servico], $matriz);
		}
		else {
			# Mensagem de aviso
			$msg="Erro ao excluir parâmetro!";
			$url="?modulo=$modulo&sub=$sub&acao=contratos&registro=$matriz[servico]";
			aviso("Aviso", $msg, $url, 400);
		}
	}
} # fecha funcao de excluir




# Função para montar campo de formulario
function formSelectServicosContratos($servico, $contrato, $campo, $tipo) {

	global $conn, $tb;
	
	if($tipo=='form') {
		# Buscar Serviços de servidor (ja cadastrados)
		$tmpConsulta=buscaServicosContratos($servico, 'idServico','igual','idServico');
		
		$consulta=buscaContratos($texto, $campo, 'todos', 'nome');
		
		$item="<select name=matriz[$campo] onChange=form.submit();>\n";
		
		# Listargem
		for($i=0;$i<contaConsulta($consulta);$i++) {
			# Zerar flag de registro já cadastrado
			$flag=0;
			
			# Valores dos campos
			$id=resultadoSQL($consulta, $i, 'id');
			$nome=resultadoSQL($consulta, $i, 'nome');
			$status=resultadoSQL($consulta, $i, 'status');
	
			# Verificar se serviço já está cadastrado
			for($x=0;$x<contaConsulta($tmpConsulta);$x++) {
				# Verificar
				$idTmp=resultadoSQL($tmpConsulta, $x, 'idContrato');
				
				if($idTmp == $id) {
					# Setar Flag de registro já cadastrado
					$flag=1;
					break;
				}
			}
	
			if(!$flag || $flag==0) {
				# Mostrar serviço		
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



# Função para buscar dados do plano
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
