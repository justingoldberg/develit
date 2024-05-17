<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 05/07/2004
# Ultima alteração: 05/07/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastramento de tipos de equiptoTipos


# abertura do modulo
function equiptoTipo($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Tipos de Equipamentos</b>";
	$subtitulo = "<br>Cadastro de Tipos de Equipamentos";
	
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
				equiptoTipoAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				equiptoTipoAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				equiptoTipoProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				equiptoTipoVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				equiptoTipoExcluir($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'incluirCaracteristicaTipo':
				equiptoTipoAdicionarCaracteristica($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluirCaracteristica':
				equiptoTipoExcluirCaracteristica($modulo, $sub, $acao, $registro, $matriz);
				break;
			default:
				equiptoTipoListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}


# Incluir Caracteristicas no Tipo
function equiptoTipoAdicionarCaracteristica($modulo, $sub, $acao, $registro, $matriz) {
	
	global $conn, $tb;
	
	$tabela=$tb[EquiptoTipoEquiptoCaracteristica];
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
	equiptoTipoVer($modulo, $sub, $acao, $registro, $matriz);
}


#Exclui caracteristica
function equiptoTipoExcluirCaracteristica($modulo, $sub, $acao, $registro, $matriz) {
	global $conn, $tb;
	$tabela=$tb[EquiptoTipoEquiptoCaracteristica];
	if ($registro) {
		$sql="delete from $tabela where id=$registro";
		$ok=consultaSQL($sql, $conn);
	}
	#volta a ver deste registro
	$matriz[id]=$registro;
	equiptoTipoVer($modulo, $sub, $acao, $matriz[idTipo], $matriz);
}

#
function equiptoTipoExcluir($modulo, $sub, $acao, $registro, $matriz) {
	$msg="Esta opção não está habilitada";
	avisoNOURL("Aviso: Exclusão", $msg, '400');
	echo "<br>";
	equiptoTipoVer($modulo, $sub, 'ver', $registro, $matriz);
}


#procurar
function equiptoTipoProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
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
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[EquiptoTipo]);
		equiptoTipoListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function equiptoTipoAdicionar($modulo, $sub, $acao, $registro, $matriz) {

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
				<input type=hidden name=registro value=$registro>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosequiptoTipo(0);
			
			equiptoTipoMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		
		$grava=dbEquiptoTipo($matriz, 'incluir');
		
		# Verificar inclusão de registro
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, '100%');
			echo "<br>";
			$matriz[naoInclui]=1;
			equiptoTipoListar($modulo, $sub, $acao, $registro, $matriz, 'ver');
		}
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, '60%');
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function equiptoTipoAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosequiptoTipo($registro, 'id', 'igual', 'id');
			
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
			
						
			equiptoTipoMostra($dados);
			
			#botao
			getBotao('matriz[bntConfirmar]', 'Alterar');
			
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbEquiptoTipo($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, '100%');
				echo "<br>";
				equiptoTipoVer($modulo, $sub, $acao, $registro, $matriz);
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


function equiptoTipoMostra($dados="") {
	global $conn, $tb;
	
	$tamanho=60;
	
	getCampo('text',   'Nome',       'matriz[nome]',     $dados[nome],     "", "", $tamanho);
	getCampo('area',   'Descrição',  'matriz[descricao]',$dados[descricao],"", "", $tamanho);
	getCampo('status', 'Status',     'matriz[status]',   $dados[status]);
	
}


# Função para buscar o NOVO ID da Pessoa
function equiptoTipoBuscaIDNovo() {

	global $conn, $tb;
	
	$tabela=$tb["EquiptoTipo"];
	
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
function equiptoTipoListar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Cabeçalho
		novaTabela("Lista de $titulo", "left", '100%', 0, 4, 1, $corFundo, $corBorda, 4);
		
			novaLinhaTabela($corFundo, '100%');
				# Opcoes Adicionais
				#menuOpcAdicional('EquiptoTipo', $sub, 'adicionar', $registro, $matriz, 4);
			fechaLinhaTabela();
				
			#monta uma lista de servicosPlanos para o sql
			$consulta=buscaRegistros($matriz[id], 'id',  'todos', 'nome', $tb[EquiptoTipo]);
			
			if ($consulta && contaConsulta($consulta)>0) {
				$largura             =array('25%',  '38%',       '10%',    '27%');
				$gravata[cabecalho]  =array('Nome', 'Descrição', 'Status', 'Opções');
				$gravata[alinhamento]=array('left', 'left',      'center', 'left');
				
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($largura); $i++)
						itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
				htmlFechaLinha();
				
				$qtd=contaConsulta($consulta);
				for($reg=0;$reg<$qtd;$reg++) {
					
					$id=resultadoSQL($consulta, $reg, 'id');
					$st=resultadoSQL($consulta, $reg, 'status');
	
					#opcoes
					$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
					$fnt="<font size='2'>";
					
					$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
					$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
					$opcoes.=htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>",'excluir');
					
					$i=0;
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
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', '100%', $corFundo, 4, 'txtaviso');
				fechaLinhaTabela();
			}
		fechaTabela();
	}
	
}

#busca

# função Exibição
function equiptoTipoVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosequiptoTipo($registro);
		
		if(is_array($objeto)) {
			# Motrar tabela de busca
			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				#menuOpcAdicional('equiptoTipo', $sub, 'alterar', $registro, $matriz);
				#itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				getCampo('', 'Nome', 'matriz[nome]', $objeto[nome]);
				getCampo('', 'Descrição', 'matriz[descricao]', $objeto[descricao]);
				getCampo('', 'Status', 'matriz[status]', getComboStatus($objeto[status], "", 'check'));
				getCampo('', 'Cadastrado', 'matriz[dtCadastro]', converteData($objeto[dtCadastro], 'banco', 'form'));
			fechaTabela();
			
			$matriz[idTipo]=$registro;
			echo "<br>";
			equiptoCaracteristicaTipoLista($modulo, $sub, $acao, $registro, $matriz);
			
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
function dadosEquiptoTipo($id) {

	global $tb;

	$tabela=$tb[EquiptoTipo];
	
	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tabela);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
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
....... form  - um combo com os nomes
....... multi - uma caixa de selecao com os nomes
Exclusoes = Array de valores que nao entram na lista
*/
function formSelectEquiptoTipo($item, $campo="", $retorno="", $tipo="", $exclusoes="") {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[EquiptoTipo];
	
	if($tipo=='check') {
		$campo=dadosequiptoTipo($item);
		$retorno=$campo[nome];
	} 
	else {
		$retorno = getSelectDados($item, $campo, $retorno, $tipo, $tabela, '', $exclusoes);
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
function dbEquiptoTipo($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[EquiptoTipo];

	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
		      VALUES ('',
					  '$matriz[nome]',
					  '$matriz[descricao]',
					  '$matriz[status]',
					  now()
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[nome]="nome='".$matriz[nome]."'";
		$campos[descricao]="descricao='".$matriz[descricao]."'";
		$campos[status]="status='".$matriz[status]."'";
		
		$sql="UPDATE $tabela SET
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