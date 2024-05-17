<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 02/07/2004
# Ultima alteração: 02/07/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastramento de equipamentos


# abertura do modulo
function equipamento($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Equipamentos</b>";
	$subtitulo = "<br>Cadastro de equipamentos";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Topo da tabela - Informações e menu principal do Cadastro
		$itens=Array('Adicionar', 'Listar', 'Procurar');
		getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
		
		# case das acoes
		echo "<br>";
		switch ($acao) {
			case "adicionar":		
				equipamentoAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				equipamentoAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				equipamentoProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				equipamentoVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				equipamentoExcluir($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'incluirCaracteristicaTipo':
				equipamentoAdicionarCaracteristica($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluirCaracteristica':
				equipamentoExcluirCaracteristica($modulo, $sub, $acao, $registro, $matriz);
				break;
			default:
				equipamentoListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}



# Incluir Caracteristicas no Tipo
function equipamentoAdicionarCaracteristica($modulo, $sub, $acao, $registro, $matriz) {
	
	global $conn, $tb;
	
	$tabela=$tb[EquipamentoEquiptoCaracteristica];
	if ($registro && $matriz[idCaracteristica]) {
		#associa o valor a caracteristicaTipo
		$sql="insert into $tabela 
			       values('', 
						  '$registro', 
						  '$matriz[idCaracteristica]', 
						  '$matriz[valor]')";
		$ok=consultaSQL($sql, $conn);
	}
	#volta a ver deste registro
	$matriz[id]=$registro;
	equipamentoVer($modulo, $sub, $acao, $registro, $matriz);
}


#Exclui caracteristica
function equipamentoExcluirCaracteristica($modulo, $sub, $acao, $registro, $matriz) {
	global $conn, $tb;
	$tabela=$tb[EquipamentoEquiptoCaracteristica];
	if ($registro) {
		$sql="delete from $tabela where id=$registro";
		$ok=consultaSQL($sql, $conn);
	}
	#volta a ver deste registro
	$matriz[id]=$registro;
	equipamentoVer($modulo, $sub, $acao, $matriz[idEquipamento], $matriz);
}



function equipamentoExcluir($modulo, $sub, $acao, $registro, $matriz) {
	$msg="Esta opção não está habilitada";
	avisoNOURL("Aviso: Exclusão", $msg, 400);
	echo "<br>";
	equipamentoVer($modulo, $sub, 'ver', $registro, $matriz);
}

#procurar
function equipamentoProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( !$matriz[bntProcurar] ) {

		novaTabela2("[Procurar $titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			#nome
			getCampo('text', "Nome", 'matriz[nome]');
			
			#botao
			getBotao('matriz[bntProcurar]', 'Procurar');
			
		fechaTabela();
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[Equipamento]);
		equipamentoListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function equipamentoAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if(!$matriz[bntAdicionar]) {
		
		# Motrar tabela de busca
		novaTabela2("[Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosequipamento(0);
			
			equipamentoMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	elseif ($matriz[nome] && $matriz[descricao]) {
		
		$grava=dbEquipamento($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, '100%');
			echo "<br>";
			$matriz[naoInclui]=1;
			equipamentoListar($modulo, $sub, $acao, $registro, $matriz, 'ver');
		}
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, '60%');
		}
	}
	else{
		# Mensagem de aviso
		$msg="Campos obrigatórios não preenchidos!<br> Preencha todos os campos antes de prosseguir com o cadastro! ";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function equipamentoAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosequipamento($registro, 'id', 'igual', 'id');
			
		# Motrar tabela de busca
		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			# Opcoes Adicionais
			#menuOpcAdicional('servicoIVR', $sub, 'ver', $registro, $matriz);
				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=subacao value=alterar>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			equipamentoMostra($dados);
			
			#botao
			getBotao('matriz[bntConfirmar]', 'Alterar');
			
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbequipamento($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, '100%');
				echo "<br>";
				equipamentoVer($modulo, $sub, $acao, $registro, $matriz);
			} else {
				echo "<br>";
				$msg="Ocorreram erros durante a gravação.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, '100%');
			}
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
}


function equipamentoMostra($dados="") {
	global $conn, $tb;
	
	$tamanho=60;
	getCampo('text',   'Nome',       'matriz[nome]',     $dados[nome],     "", "", $tamanho);
	getCampo('combo',  'Tipo',       'matriz[idTipo]',   formSelectEquiptoTipo('id', 'nome', 'matriz[idTipo]', 'form'));
	getCampo('text',   'Descrição',  'matriz[descricao]',$dados[descricao],"", "", $tamanho);
	getCampo('status', 'Status',     'matriz[status]',   $dados[status]);
	
}


# Função para buscar o NOVO ID da Pessoa
function equipamentoBuscaIDNovo() {

	global $conn, $tb;
	
	$tabela=$b["Equipamento"];
	
	$sql="SELECT count(id) qtde from $tabela";
	$consulta=consultaSQL($sql, $conn);
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tabela";
		$consulta=consultaSQL($sql, $conn);
		if($consulta && contaConsulta($consulta)>0) {
			$retorno=resultadoSQL($consulta, 0, 'id');
			if(!is_numeric($retorno)) $retorno=1;
		}
		else $retorno=1;
	}
	else {
		$retorno=resultadoSQL($consulta, 0, 'qtde')+1;
	}
	return($retorno);
}


#Lista todas OS do cliente selecionado
function equipamentoListar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		$numCol=5;
		# Cabeçalho
		novaTabela("Lista de $titulo", "left", '100%', 0, $numCol, 1, $corFundo, $corBorda, $numCol);
			
			novaLinhaTabela($corFundo, '100%');
				# Opcoes Adicionais
				#menuOpcAdicional('equipamento', $sub, 'adicionar', $registro, $matriz, $numCol);
			fechaLinhaTabela();
			
			#monta uma lista de servicosPlanos para o sql
			$consulta=buscaRegistros($matriz[id], 'id',  'todos', 'nome', $tb[Equipamento]);
			
			if ($consulta && contaConsulta($consulta)>0) {
				$largura             =array('15%',  '25%',  '25%',       '10%',    '25%');
				$gravata[cabecalho]  =array('Tipo', 'Nome', 'Descrição', 'Status', 'Opções');
				$gravata[alinhamento]=array('left', 'left', 'left',      'center', 'left');
				
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($largura); $i++)
						itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
				htmlFechaLinha();
				
				$qtd=contaConsulta($consulta);
				for($reg=0;$reg<$qtd;$reg++) {
					
					$id=resultadoSQL($consulta, $reg, 'id');
					$st=resultadoSQL($consulta, $reg, 'status');
					$idTipo=resultadoSQL($consulta, $reg, 'idTipo');
					
					#opcoes
					$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
					$fnt="<font size='2'>";
					
					$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
					$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
					$opcoes.=htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>",'excluir');
					
					$i=0;
					$campo[$i++]=formSelectEquiptoTipo($idTipo, "", "", 'check');
					$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
					$campo[$i++]=resultadoSQL($consulta, $reg, 'descricao');
					$campo[$i++]=getComboStatus($st, "", "check");
					$campo[$i++]=$opcoes;
					
					htmlAbreLinha($corFundo);
						for($i=0;$i<count($campo); $i++)
							itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i], $corFundo, 0, 'normal10');
					htmlFechaLinha();
				}
			}
			else {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', '100%', $corFundo, $numCol, 'txtaviso');
				fechaLinhaTabela();
			}
		fechaTabela();
	}
}

#busca

# função Exibição
function equipamentoVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosEquipamento($registro);
		
		if(is_array($objeto)) {
			# Motrar tabela de busca
			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
				# Opcoes Adicionais
				#menuOpcAdicional('equipamento', $sub, 'alterar', $registro, $matriz);
				#itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				getCampo('', 'Tipo', 'matriz[idTipo]',  formSelectEquiptoTipo($objeto[idTipo], '', '', 'check'));
				getCampo('', 'Nome', 'matriz[nome]', $objeto[nome]);
				getCampo('', 'Descrição', 'matriz[descricao]', $objeto[descricao]);
				getCampo('', 'Status', 'matriz[status]', getComboStatus($objeto[status], "", 'check'));
				getCampo('', 'Cadastrado', 'matriz[dtCadastro]', converteData($objeto[dtCadastro], 'banco', 'form'));
			fechaTabela();
			
			echo "<br>";
			equiptoCaracteristicaEquipamentoLista($modulo, $sub, $acao, $objeto[id], $matriz);
		}
	}
}


# Função para Dados
/**
 * @return array
 * @param int $id
 * @desc Retorna um array com os dados
 extra
 dados[base] = dados de base
*/
function dadosequipamento($id) {

	global $tb;

	$tabela=$tb[Equipamento];
	
	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tabela);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idTipo]=resultadoSQL($consulta, 0, 'idTipo');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
	}
	
	return($retorno);
	
}

/**
 * @return unknown
 * @param unknown $item
 * @param unknown $campo
 * @param unknown $retorno
 * @param unknown $tipo
 * @desc Retorna 1 ou varios itens do objeto.
 item  = id 
 campo = campo da matriz
 retorno = campo de retorno (nome do componente)
 tipo = check - retorna o nome
        form  - um combo com os nomes
        multi - uma caixa de selecao com os nomes
*/
function formSelectEquipamento($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[Equipamento];
	
	if($tipo=='check') {
		$campo=dadosequipamento($item);
		$retorno=$campo[nome];
	} else {
		$retorno = getSelecDados($item, $campo, $retorno, $tipo, $tabela);
	}
	return($retorno);
}


#Função de banco de dados
/**
 * @return unknown
 * @param unknown $matriz
 * @param unknown $tipo
 * @desc Tratamento da sentenca sql
*/
function dbEquipamento($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[Equipamento];

	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
		      VALUES ('',
					  '$matriz[idTipo]',
					  '$matriz[nome]',
					  '$matriz[descricao]',
					  '$matriz[status]',
					  now()
					 )";
	}
	# Alterar
	elseif($tipo=='alterar') {
		/* 
			Cria uma matriz com os campos ja formatados para o SQL 
		*/
		$campos[id]="id=$matriz[id]";
		$campos[idTipo]="idTipo=$matriz[idTipo]";
		$campos[nome]="nome='".$matriz[nome]."'";
		$campos[descricao]="descricao='".$matriz[descricao]."'";
		$campos[status]="status='".$matriz[status]."'";
		
		$sql="UPDATE $tabela SET
					$campos[idTipo],
					$campos[nome],
					$campos[descricao],
					$campos[status]
				WHERE
					$campos[id]";
	}
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tabela WHERE $campos[id]";
	}
	
	if($sql) { 
		#echo $sql;
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}

?>