<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 24/06/2003
# Ultima alteração: 17/03/2004
#    Alteração No.: 008
#
# Função:
#    Painel - Funções para cadastro de enderecos


# Função de banco de dados - Pessoas
function dbDocumento($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;

	# Sql de inclusão
	if($tipo=='incluir' || $tipo=='inserir') {
	
		# Verificar os documentos Informados
		if($matriz[rg] && $matriz[pessoaTipo]=='F') {
			$tipoDocumento=checkTipoDocumento('rg');
	
			$sql="INSERT INTO $tb[Documentos] VALUES ($matriz[id],
			'$tipoDocumento[id]',
			'$matriz[rg]',
			'$matriz[dtCadastro]')";
			
			$consulta=consultaSQL($sql, $conn);
			
			if(!$consulta) {
				return(0);
			}
			else $retorno=1;
		}
		if($matriz[cpf] && $matriz[pessoaTipo]=='F') {
			$tipoDocumento=checkTipoDocumento('cpf');
	
			$sql="INSERT INTO $tb[Documentos] VALUES ($matriz[id],
			'$tipoDocumento[id]',
			'$matriz[cpf]',
			'$matriz[dtCadastro]')";
			
			$consulta=consultaSQL($sql, $conn);
			
			if(!$consulta) {
				return(0);
			}
			else $retorno=1;
		}
		if($matriz[cnpj] && $matriz[pessoaTipo]=='J') {
			$tipoDocumento=checkTipoDocumento('cnpj');
			$sql="INSERT INTO $tb[Documentos] VALUES ($matriz[id],
			'$tipoDocumento[id]',
			'$matriz[cnpj]',
			'$matriz[dtCadastro]')";
			
			$consulta=consultaSQL($sql, $conn);
			
			if(!$consulta) {
				return(0);
			}
			else $retorno=1;
		}
		if($matriz[ie] && $matriz[pessoaTipo]=='J') {
			$tipoDocumento=checkTipoDocumento('ie');
	
			$sql="INSERT INTO $tb[Documentos] VALUES ($matriz[id],
			'$tipoDocumento[id]',
			'$matriz[ie]',
			'$matriz[dtCadastro]')";
			
			$consulta=consultaSQL($sql, $conn);
			
			if(!$consulta) {
				return(0);
			}
			else $retorno=1;
		}
		
		return($retorno);
	} #fecha inclusao
	
	# Sql de inclusão
	elseif($tipo=='incluirdoc') {
	
		$data=dataSistema();
		
		$sql="INSERT INTO $tb[Documentos] VALUES ($matriz[idPessoa],
		'$matriz[tipoDocumento]',
		'$matriz[documento]',
		'$data[dataBanco]')";
		
	}
	
	# Exclusão
	elseif($tipo=='excluir') {
		$sql="DELETE FROM 
				$tb[Documentos] 
			WHERE 
				idPessoa='$matriz[idPessoa]' 
				AND idTipo='$matriz[tipoDocumento]'
				AND dtCadastro='$matriz[dtCadastro]'";
	}
	
	# Exclusão
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM 
				$tb[Documentos] 
			WHERE 
				idPessoa='$matriz[id]'";
	}
	
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


# função de busca 
function buscaDocumentosPessoas($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Documentos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Documentos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Documentos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Documentos] WHERE $texto ORDER BY $ordem";
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
function documentosPessoas($modulo, $sub, $acao, $registro, $matriz) {

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
		if($sub=='pop') {
			verPOP($modulo, $sub, $acao, $matriz[idPop], $matriz);
		}
		else {
			verPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		echo "<br>";
	
		listarDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	
}#fecha função de listagem



function listarDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Quebrar Registro
	$matRegistro=explode(":", $registro);
	$idPessoaTipo=$matRegistro[0];
	$idPessoa=$matRegistro[1];
	
	# Seleção de registros
	$consulta=buscaDocumentosPessoas($idPessoa, 'idPessoa','igual','idTipo');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Documentos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=${acao}adicionar&registro=$registro>Adicionar</a>",'incluir');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 4, 'tabfundo1');
	
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há documentos cadastrados", 'left', $corFundo, 4, 'txtaviso');
	}
	else {

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Documento', 'center', '30%', 'tabfundo0');
			itemLinhaTabela('Tipo', 'center', '40%', 'tabfundo0');
			itemLinhaTabela('Data Cadastro', 'center nowrap', '10%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '20%', 'tabfundo0');
		fechaLinhaTabela();

		$i=0;
		
		while($i < contaConsulta($consulta)) {
			$idTipo=resultadoSQL($consulta, $i, 'idTipo');
			$tipoDocumento=checkIDTipoDocumento($idTipo);
			$documento=resultadoSQL($consulta, $i, 'documento');
			$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
			
			//$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentosalterar&registro=$idPessoaTipo:$idTipo>Alterar</a>",'alterar');
			$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentosexcluir&registro=$idPessoaTipo:$idTipo>Excluir</a>",'excluir');

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($documento, 'center', '30%', 'normal10');
				itemLinhaTabela("$tipoDocumento[descricao]", 'center', '40%', 'normal10');
				itemLinhaTabela(converteData($dtCadastro, 'banco','formdata'), 'center', '10%', 'normal10');
				itemLinhaTabela($opcoes, 'center nowrap', '20%', 'normal10');
			fechaLinhaTabela();
			
			# Incrementar contador
			$i++;
		} #fecha laco de montagem de tabela
		
		fechaTabela();
	} #fecha servicos encontrados
}




# Função para listagem 
function adicionarDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $sessCadastro;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		verPessoas($modulo, $sub, $acao, $registro, $matriz);	
	
		# Quebrar Registro
		$matRegistro=explode(":", $registro);
		$idPessoaTipo=$matRegistro[0];
		$idPessoa=$matRegistro[1];
		
		# Formulário para Adicionar endereço
		if(!$matriz[bntConfirmar] || (!$matriz[documento] || !$matriz[tipoDocumento]) ) {
			# form
			echo "<br>";
				
			echo $texto;
			$matriz[acao]='adicionar';
			
			#limpar Variáveis de endereço
			$sessCadastro[documento]='';
			$sessCadastro[tipoDocumento]='';
		
			formAdicionarDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
		
			echo "<br>";
			
			$tipoDocumento=checkIDTipoDocumento($matriz[tipoDocumento]);
			
			# Gravar
			$gravaDocumento=dbDocumento($matriz, 'incluirdoc');
			
			if($gravaDocumento) {
			
				# Verificar e incluir endereço
				novaTabela2("[$tipoDocumento[descricao] - Confirmação de Cadastro]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
	
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Tipo de Documento: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($tipoDocumento[descricao], 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($matriz[documento], 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('&nbsp;', 'right', 'middle', '100%', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					
					#limpar Variáveis de endereço
					
					$sessCadastro[tipoDocumento]='';
					$sessCadastro[documento]='';
					
					# Confirmação de cadastro
					$msg="Cadastro efetuado!";
					$url="?modulo=$modulo&sub=$sub&acao=documentos&registro=:$matriz[idPessoaTipo]:$matriz[idPessoa]";
					aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
					
				fechaTabela();
			}
			else {
				# Erro
				$msg="ERRO na tentativa de alteração de endereço!";
				$url="?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro";
				aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
			}
		}
	}
	
}#fecha função 

# formulário de dados cadastrais
/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Formulario para adicionar documentos pessoais para o cadastro
*/
function formAdicionarDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Quebrar Registro
	$matRegistro=explode(":", $registro);
	$idPessoaTipo=$matRegistro[0];
	$idPessoa=$matRegistro[1];
	
	$texto=formSelectTipoDocumentoPessoa($idPessoaTipo, $matriz[tipoDocumento], 'tipoDocumento',$matriz[acao]);
	
	if($texto) {

		# Pessoa física
		novaTabela2("[Documento]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>&nbsp;
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idPessoa] value=$idPessoa>
			<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
			<input type=hidden name=matriz[tipoDocumentoANT] value=$idTipo>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	
		if(!$matriz[tipoDocumento]) {
			# Selecionar tipo endereço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectTipoDocumentoPessoa($idPessoa, $matriz[tipoDocumento], 'tipoDocumento',$matriz[acao]).$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		else {
		
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectTipoDocumentoPessoa($idPessoaTipo, $matriz[tipoDocumento], 'tipoDocumento',$matriz[acao]).$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			# Verificar o tipo de documento Selecionado
		
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[documento] size=30 value='$matriz[documento]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
			
		}
		
		fechaTabela();
	
	}
	else {
		# Não há mais tipos de endereço disponíveis para cadastramento
		$msg="Não existem mais tipos de documentos disponíveis para cadastro nesta pessoa!";
		$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
		aviso("Aviso", $msg, $url, 600);
	}


}



function excluirDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $conn, $sessCadastro;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[excluir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		# Quebrar Registro
		$matRegistro=explode(":", $registro);
		$idPessoaTipo=$matRegistro[0];
		$idTipo=$matRegistro[1];
		
		# Visualizar dados da pessoa
		verPessoas($modulo, $sub, $acao, $registro, $matriz);	
		echo "<br>";
		
		# Procurar o ID da Pessoa - mostrar o endereço da pessoa (nao ligado a PessoaTipo)
		$consultaPessoaTipo=buscaPessoas($idPessoaTipo,"$tb[PessoasTipos].id",'igual','idPessoaTipo');
		if($consultaPessoaTipo) {
			$idPessoa=resultadoSQL($consultaPessoaTipo, 0, 'idPessoa');
		}
		
		$consulta=buscaDocumentosPessoas("idPessoa=$idPessoa AND idTipo=$idTipo", '', 'custom', 'idPessoa');
		
		if($consulta && contaConsulta($consulta)>0) {
			# Mostrar Endereço
			$idTipo=resultadoSQL($consulta, 0, 'idTipo');
			$documento=resultadoSQL($consulta, 0, 'documento');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			
			$tipo=checkIDTipoDocumento($idTipo);
			
			# Motrar tabela de busca
			novaTabela2("[Excluir Documento - $tipo[descricao]]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
					$texto="
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>&nbsp;
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[idPessoa] value=$idPessoa>
					<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
					<input type=hidden name=matriz[tipoDocumento] value=$idTipo>
					<input type=hidden name=matriz[dtCadastro] value='$dtCadastro'>";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>Tipo de Documento: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($tipo[descricao], 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>Documento: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($documento, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if(!$matriz[bntConfirmar]) {
					
					# Botão de exclusão
					novaLinhaTabela($corFundo, '100%');
						itemLinhaForm("<input type=submit name=matriz[bntConfirmar] value='Excluir' class=submit>", 'center', 'middle', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				}
				else {
					# excluir endereço
					$grava=dbDocumento($matriz, 'excluir');
					
					if($grava) {
					
						$sessCadastro[tipoDocumento]='';
						$sessCadastro[documento]='';
						
						# Confirmação de cadastro
						$msg="Documento excluído com sucesso!";
						$url="?modulo=$modulo&sub=$sub&acao=documentos&registro=$matriz[idPessoaTipo]:$matriz[idPessoa]";
						aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
					}
					else {
						# Aviso de erro
						$msg="ERRO ao excluir endereço!";
						$url="?modulo=$modulo&sub=$sub&acao=documentos&registro=:$matriz[idPessoaTipo]:$matriz[idPessoa]";
						aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');					
					}
				}
				
			fechaTabela();
			
		}
	}
	
}#fecha função 



# Função para seleção de documento preferencial
function documentoSelecionaPreferencial($idPessoa, $tipoPessoa) {

	global $conn, $tb;
	
	$sql="
		SELECT
			$tb[Documentos].documento, 
			$tb[TipoDocumentos].valor 
		from 
			$tb[Documentos], 
			$tb[TipoDocumentos]
		where 
			$tb[Documentos].idTipo=$tb[TipoDocumentos].id 
			and $tb[Documentos].idPessoa=$idPessoa";	
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		if($tipoPessoa=='F') {
			for($a=0;$a<contaconsulta($consulta);$a++) {
				$valor=resultadoSQL($consulta, $a, 'valor');
				$documento=resultadoSQL($consulta, $a, 'documento');
				
				if($valor=='cpf') {
					$flagRG=0;
					$flagOutro=0;
					
					$flagCPF=1;
					$cpf=$documento;
				}
				elseif($retorno[valor]=='rg') {
					$flagOutro=0;

					$flagRG=1;
					$rg=$documento;
				}
				else {
					$flagOutro=1;
					$outro=$documento;
				}
			}
			
			if($flagCPF==1) $retorno=$cpf;
			elseif(!$flagCPF && $flagRG==1) $retorno=$rg;
			elseif(!$flagCPF && !$flagRG && $flagOutro==1) $retorno=$outro;
		}
		elseif($tipoPessoa=='J') {
			for($a=0;$a<contaconsulta($consulta);$a++) {
				$valor=resultadoSQL($consulta, $a, 'valor');
				$documento=resultadoSQL($consulta, $a, 'documento');
				
				if($valor=='cpf') {
					$flagRG=0;
					$flagOutro=0;
					
					$flagCPF=1;
					$cpf=$documento;
				}
				elseif($retorno[valor]=='rg') {
					$flagOutro=0;

					$flagRG=1;
					$rg=$documento;
				}
				else {
					$flagOutro=1;
					$outro=$documento;
				}
			}
			
			if($flagCPF==1) $retorno=$cpf;
			elseif(!$flagCPF && $flagRG==1) $retorno=$rg;
			elseif(!$flagCPF && !$flagRG && $flagOutro==1) $retorno=$outro;
		}
	}
	
	
	return($retorno);
}


# função para dados de documentos
function dadosDocumentosPessoas($id) {

	global $conn, $tb;
	
	$sql="
		SELECT
			$tb[TipoDocumentos].valor tipo, 
			$tb[Documentos].documento documento
		FROM
			$tb[TipoDocumentos], 
			$tb[Documentos], 
			$tb[Pessoas]
		WHERE
			$tb[Pessoas].id=$tb[Documentos].idPessoa 
			AND $tb[Documentos].idTipo = $tb[TipoDocumentos].id 
			AND $tb[Pessoas].id=$id
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$tipo=resultadoSQL($consulta, $a, 'tipo');
			$documento=resultadoSQL($consulta, $a, 'documento');
			
			$retorno[$tipo]=$documento;
		}
	}
	
	return($retorno);
}



# Funçao para carregar documento
function carregaDocumentosPessoaTipo($idPessoaTipo) {

	global $conn, $tb;
	
	$sql="
		SELECT
			$tb[TipoDocumentos].valor tipo, 
			$tb[Documentos].documento 
		FROM
			$tb[Documentos], 
			$tb[TipoDocumentos], 
			$tb[Pessoas], 
			$tb[PessoasTipos] 
		WHERE
			$tb[Documentos].idTipo=$tb[TipoDocumentos].id 
			AND $tb[Pessoas].id = $tb[Documentos].idPessoa 
			AND $tb[Pessoas].id = $tb[PessoasTipos].idPessoa 
			AND $tb[PessoasTipos].id = $idPessoaTipo
		GROUP BY $tb[TipoDocumentos].id
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		for($a=0;$a<contaConsulta($consulta);$a++) {
		
			$tipo=resultadoSQL($consulta, $a, 'tipo');
			$documento=resultadoSQL($consulta, $a, 'documento');
			
			$retorno[$tipo]=$documento;
		}
	}
	
	return($retorno);
	
}

?>
