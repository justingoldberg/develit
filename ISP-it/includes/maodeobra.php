<?
################################################################################
#       Criado por: Hugo Ribeiro
#  Data de criação: 29/04/2004
# Ultima alteração: 29/04/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para gerenciamento de maodeobra


function maodeobra($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "Mão de Obra";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função!";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		//$modulo= 'maodeobra';
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[$titulo]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>$tipoPessoa[descricao]</b>
					<br><span class=bold>$titulo</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		if($acao=="adicionar") {
			echo "<br>";
			maodeobraAdicionar($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=="alterar") {
			echo "<br>";
			maodeobraAlterar($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='procurar') {
			echo "<br>";
			maodeobraProcurar($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='ver') {
			echo "<br>";
			maodeobraVer($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='excluir') {
			echo "<br>";
			maodeobraExcluir($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='listar') {
			echo "<br>";
			maodeobraListar($modulo, $sub, $acao, $registro, $matriz);
		}
		/*
		elseif($acao=='imprimir') {
			echo "<br>";
			relatorioOrdemServico($modulo, 'ordemservico', $acao, $registro, $matriz);
		}
		*/
	}
	echo "<script>location.href='#ancora';</script>";
}



/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Funcao de Exclusao de Mao de Obra de Ordem de Servico
*/
function maodeobraExcluir($modulo, $sub, $acao, $registro, $matriz) {
	/*$msg="Esta opção não está habilitada";
	avisoNOURL("Aviso: Exclusão", $msg, 400);
	echo "<br>";
	maodeobraVer($modulo, $sub, 'ver', $registro, $matriz);	*/
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro, $sessLogin, $titulo;
	
	if($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$dados=dadosMaodeobra($registro, 'id', 'igual', 'id');
			
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
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			//================================================================================
			//maodeobraMostra($dados);
			$bgLabel='tabfundo1';
			$bgCampo='tabfundo1';
			
			#descricao
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
				itemLinhaTMNOURL($dados[descricao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
			fechaLinhaTabela();
							
			#Usuario
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Usuário: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
				itemLinhaTMNOURL($dados[login], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
			fechaLinhaTabela();
			
			#dtCriacao
			if ($dados[dtCriacao]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data da Criação: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL($dados[dtCriacao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
			}
			//=================================================================================
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntExcluir] value='Excluir' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	
	# Exclusão - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Conferir campos
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbmaodeobra($matriz, 'excluir');
			
			# Verificar exclusão de registro
			if($grava) {
				# OK
				# Visualizar Pessoa
				$msg="Registro Excluído!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				maodeobraVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
			} else {
				echo "<br>";
				$msg="Ocorreram erros durante a gravação.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
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
	} #fecha bntExcluir
} // fim da funcao de exclusao de Mao de Obra


function maodeobraProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( !$matriz[bntProcurar] ) {

		novaTabela2("[Procurar $titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL("<b>Descrição: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[nome] size=60 value=''>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntProcurar] value='Procurar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
		fechaTabela();
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[nome], 'descricao', 'contem', 'descricao', $tb[MaodeObra]);
		maodeobraListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}

# função para adicionar
function maodeobraAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if(!$matriz[bntAdicionar]) {
		
		# Motrar tabela de busca
		novaTabela2("[Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosMaodeobra(0);
			
			maodeobraMostra($dados);
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAdicionar] value='Adicionar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();	
	}
	else {
		
		//$matriz[id]=buscaNovoID($tb[MaodeObra]);
		$grava=dbmaodeobra($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			maodeobraListar($modulo, $sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function maodeobraAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosMaodeobra($registro, 'id', 'igual', 'id');
			
		# Motrar tabela de busca
		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			maodeobraMostra($dados);
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntConfirmar] value='Alterar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbmaodeobra($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar Pessoa
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				maodeobraVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
			} else {
				echo "<br>";
				$msg="Ocorreram erros durante a gravação.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
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


/**
 * @return void
 * @param unknown $dados
 * @desc Enter description here...
*/
function maodeobraMostra($dados) {

	#descricao
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[descricao] size=40 value='$dados[descricao]'>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();

	
}


# Função para buscar o NOVO ID da Pessoa
function maodeobraBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from $tb[MaodeObra]";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tb[MaodeObra]";
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
function maodeobraListar($modulo, $sub, $acao, $lista, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# Cabeçalho
		novaTabela("Lista de ".$titulo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $cliente);
		fechaTabela();
		
		
		novaTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 6);
		
		if (!$lista)
			$consulta=buscaRegistros('status <> "I"', 'status', 'custom', 'descricao', $tb[MaodeObra]);
		else 
			$consulta=$lista;

		if ($consulta && contaConsulta($consulta)>0) {
			
			$largura             =array('10%', '50%',       '15%',  '25%');
			$gravata[cabecalho]  =array('ID',  'Descrição', 'Data', 'Opções');
			$gravata[alinhamento]=array('left','left',      'left', 'left');
			
			$cor='tabfundo0';
			htmlAbreLinha($corFundo);
				for($i=0;$i<count($largura); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				$usuario=resultadoSQL($consulta, $reg, 'idUsuario');
				if ($usuario) $usuario=buscaLoginUsuario($usuario,'id','igual', 'id');
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
				$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
				$opcoes.=htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>",'excluir');
							
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'id');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'descricao');
				$campo[$i++]=converteData(resultadoSQL($consulta, $reg, 'dtCadastro'), 'banco', 'formdata');
				$campo[$i++]=$opcoes;
				
				$cor='normal10';
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($largura); $i++)
						itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i].' '.$cor, 'middle', $largura[$i], $corFundo, 0, $cor);
				htmlFechaLinha();
			}
		}
		else {
			fechaTabela();
			novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				$cor='normal10';
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', $largura[0], $corFundo, 0, 'txtaviso');
				htmlFechaLinha();
		}
		fechaTabela();
	}

}

# função Exibição
function maodeobraVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# Procurar dados3
		$objeto=dadosMaodeobra($registro);
		
		if(is_array($objeto)) {

			# Motrar tabela de busca
			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, 'ver', $registro);
				
				$bgLabel='tabfundo1';
				$bgCampo='tabfundo1';
				
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				
				#descricao
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL($objeto[descricao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
								
				#Usuario
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Usuário: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL($objeto[login], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
				
				#dtCriacao
				if ($objeto[dtCriacao]) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Data da Criação: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
						itemLinhaTMNOURL($objeto[dtCriacao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
					fechaLinhaTabela();
				}
				
			fechaTabela();
			
		}
	}
}



# Função para Dados
function dadosMaodeobra($id) {

	global $tb;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[MaodeObra]);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idUsuario]=resultadoSQL($consulta, 0, 'idUsuario');
		$retorno[dtCriacao]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		
		
		#extras
		$usuario='';
		if($retorno[idUsuario])	$usuario=buscaLoginUsuario($retorno[idUsuario], 'id', 'igual', 'id');
		$retorno[login]=$usuario;
	}
	
	return($retorno);
}



# função de forma para seleção
function formSelectMaodeObra($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[MaodeObra];
	
	if($tipo=='check') {
	
		$campo=dadosMaodeobra($item);
		/*
		$id=$campo[id];
		$idTipoCobranca=resultadoSQL($consulta, 0, 'idTipoCobranca');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$valor=resultadoSQL($consulta, 0, 'valor');
		*/
		$retorno=$campo[descricao];
	
	}
	elseif(($tipo=='form') || $tipo=='formnochange') {
	
		$consulta=buscaRegistros('status="A"','status','custom','descricao', $tabela);
		
		if($consulta && contaConsulta($consulta)>0) {
			
			if ($tipo=='formnochange')
				$retorno="<select name=$retorno>";
			else 
				$retorno="<select name=$retorno onChange=javascript:submit();>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				
				if($item==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$descricao";
			}
			$retorno.="</select>";
		}
	}
	
	elseif($tipo=='multi') {
	
		$consulta=buscaRegistros('','','todos','descricao', $tabela);
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select multiple size=6 name=matriz[$campo][]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				
				if($item==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$descricao";
			}
			$retorno.="</select>";
		}
	}
	return($retorno);
}


#Função de banco de dados
function dbMaodeObra($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[MaodeObra];
	$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login', 'igual', 'id');
	
	/* Cria uma matriz com os campos ja formatados para o SQL */
	$campos[id]="id=$matriz[id]";
	$campos[descricao]="descricao='$matriz[descricao]'";
	$campos[idUsuario]="id=$matriz[idUsuario]";
	
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$campos[id]='id='.buscaNovoID($tabela);
		
		$sql="INSERT INTO $tabela 
				   VALUES (0,
						   '$matriz[descricao]',
						   now(),
						   '$matriz[idUsuario]',
							'A'
						)";
		
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		
		$sql="
			UPDATE $tabela 
			SET
				 $campos[descricao]
			WHERE
				$campos[id]";
	}
	
	elseif($tipo=='excluir') {
		$sql="UPDATE $tabela SET status='I' WHERE $campos[id]";
	}
	
	#echo "SQL: $sql";
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}

?>