<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 24/06/2003
# Ultima altera��o: 17/03/2004
#    Altera��o No.: 008
#
# Fun��o:
#    Painel - Fun��es para cadastro de enderecos


# Fun��o de banco de dados - Pessoas
function dbEndereco($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir' || $tipo=='inserir') {
	
		$sql="INSERT INTO $tb[Enderecos] VALUES ($matriz[idPessoaTipo],
		'$matriz[tipoEndereco]',
		'$matriz[cidade]',
		'$matriz[endereco]',
		'$matriz[complemento]',
		'$matriz[bairro]',
		'$matriz[cep]',
		'$matriz[pais]',
		'$matriz[caixaPostal]',
		'$matriz[ddd_fone1]',
		'$matriz[fone1]',
		'$matriz[ddd_fone2]',
		'$matriz[fone2]',
		'$matriz[ddd_fax]',
		'$matriz[fax]',
		'$matriz[email]')";
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[Enderecos] WHERE idPessoaTipo=$matriz[idPessoaTipo] AND idTipo=$matriz[tipoEndereco]";
	}
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM $tb[Enderecos] WHERE idPessoaTipo=$matriz[id]";
	}
	elseif($tipo=='alterar') {
	
		$sql="UPDATE $tb[Enderecos] 
			SET
				idTipo='$matriz[tipoEndereco]',
				idCidade='$matriz[cidade]',
				endereco='$matriz[endereco]',
				complemento='$matriz[complemento]',
				bairro='$matriz[bairro]',
				cep='$matriz[cep]',
				pais='$matriz[pais]',
				caixa_postal='$matriz[caixaPostal]',
				ddd_fone1='$matriz[ddd_fone1]',
				fone1='$matriz[fone1]',
				ddd_fone2='$matriz[ddd_fone2]',
				fone2='$matriz[fone2]',
				ddd_fax='$matriz[ddd_fax]',
				fax='$matriz[fax]',
				email='$matriz[email]'
			WHERE
				idPessoaTipo=$matriz[idPessoaTipo]
				AND idTipo=$matriz[tipoEnderecoANT]";
	} #fecha inclusao
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


# fun��o de busca 
function buscaEnderecosPessoas($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Enderecos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Enderecos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Enderecos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Enderecos] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
	
} # fecha fun��o de busca


# Fun��o para listagem 
function enderecosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if( !$permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		verPessoas($modulo, $sub, $acao, $registro, $matriz);	
		echo "<br>";
	
		listarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	
}#fecha fun��o de listagem



# Fun��o para listagem 
function verEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $conn;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if( !$permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
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
		
		# Visualizar endere�o informado
		$sqlEndereco="SELECT
			$tb[PessoasTipos].idPessoa idPessoa,
			$tb[Enderecos].idPessoaTipo idPessoaTipo,
			$tb[Enderecos].idTipo idTipo,
			$tb[Enderecos].endereco endereco,
			$tb[Enderecos].complemento complemento,
			$tb[Enderecos].bairro bairro,
			$tb[Enderecos].cep cep,
			$tb[Enderecos].pais pais,
			$tb[Enderecos].caixa_postal caixaPostal,
			$tb[Enderecos].ddd_fone1 ddd_fone1,
			$tb[Enderecos].fone1 fone1,
			$tb[Enderecos].ddd_fone2 ddd_fone2,
			$tb[Enderecos].fone2 fone2,
			$tb[Enderecos].ddd_fax ddd_fax,
			$tb[Enderecos].fax fax,
			$tb[Enderecos].email email,
			$tb[Cidades].nome cidade,
			$tb[Cidades].uf uf,
			$tb[TipoEnderecos].descricao tipoEndereco
		FROM
			$tb[Enderecos],
			$tb[Cidades],
			$tb[TipoEnderecos],
			$tb[PessoasTipos]
		WHERE
			idPessoaTipo=$idPessoaTipo
			AND $tb[Enderecos].idTipo=$idTipo
			AND $tb[Cidades].id = $tb[Enderecos].idCidade
			AND $tb[Enderecos].idTipo = $tb[TipoEnderecos].id
			AND $tb[PessoasTipos].id=$tb[Enderecos].idPessoaTipo
		";
		
		$consultaEndereco=consultaSQL($sqlEndereco, $conn);
		
		if($consultaEndereco && contaConsulta($consultaEndereco)>0) {
			# Mostrar Endere�o
			$idPessoa=resultadoSQL($consultaEndereco, 0, 'idPessoa');
			$endereco=resultadoSQL($consultaEndereco, 0, 'endereco');
			$complemento=resultadoSQL($consultaEndereco, 0, 'complemento');
			$bairro=resultadoSQL($consultaEndereco, 0, 'bairro');
			$cep=resultadoSQL($consultaEndereco, 0, 'cep');
			$pais=resultadoSQL($consultaEndereco, 0, 'pais');
			$caixaPostal=resultadoSQL($consultaEndereco, 0, 'caixaPostal');
			$ddd_fone1=resultadoSQL($consultaEndereco, 0, 'ddd_fone1');
			$fone1=resultadoSQL($consultaEndereco, 0, 'fone1');
			$ddd_fone2=resultadoSQL($consultaEndereco, 0, 'ddd_fone2');
			$fone2=resultadoSQL($consultaEndereco, 0, 'fone2');
			$ddd_fax=resultadoSQL($consultaEndereco, 0, 'ddd_fax');
			$fax=resultadoSQL($consultaEndereco, 0, 'fax');
			$email=resultadoSQL($consultaEndereco, 0, 'email');
			$cidade=resultadoSQL($consultaEndereco, 0, 'cidade');
			$uf=resultadoSQL($consultaEndereco, 0, 'uf');
			$tipoEndereco=resultadoSQL($consultaEndereco, 0, 'tipoEndereco');

			# Motrar tabela de busca
			novaTabela2("[Visualiza��o de Endere�o - $tipoEndereco]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$idPessoaTipo:$idPessoa");
				#fim das opcoes adicionais
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>Endere�o: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($endereco, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($complemento) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Complemento: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($complemento, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($bairro) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Bairro: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($bairro, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>CEP: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($cep, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>Cidade/UF: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm("$cidade/$uf", 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($pais) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Pais: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($pais, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($caixaPostal) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Caixa Postal: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($caixaPostal, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($fone1) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Fone: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$ddd_fone1 $fone1", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($fone2) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Fone: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$ddd_fone2 $fone2", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($fax) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Fax: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$ddd_fax $fax", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($email) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Email: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$email", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}

				
			fechaTabela();
			
		}
		
		# Listar demais endere�os
		echo "<br>";
	
		listarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);

	}
	
}#fecha fun��o de listagem



function listarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Quebrar Registro
	$matRegistro=explode(":", $registro);
	$idPessoaTipo=$matRegistro[0];
	$idTipo=$matRegistro[1];
	
	# Sele��o de registros
	$consulta=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
	
	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Endere�os]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecosadicionar&registro=$registro>Adicionar</a>",'incluir');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 5, 'tabfundo1');
	
	
	# Caso n�o hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# N�o h� registros
		itemTabelaNOURL("N�o h� endere�os cadastrados", 'left', $corFundo, 5, 'txtaviso');
	}
	else {

		# Cabe�alho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Endere�o', 'center', '35%', 'tabfundo0');
			itemLinhaTabela('Cidade/UF', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Fones', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Tipo', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Op��es', 'center', '20%', 'tabfundo0');
		fechaLinhaTabela();

		$i=0;
		
		while($i < contaConsulta($consulta)) {
			$endereco=resultadoSQL($consulta, $i, 'endereco');
			# Busca cidade/UF
			$idCidade=resultadoSQL($consulta, $i, 'idCidade');
			$fone1=resultadoSQL($consulta, $i, 'fone1');
			$ddd_fone1=resultadoSQL($consulta, $i, 'ddd_fone1');
			$fone2=resultadoSQL($consulta, $i, 'fone2');
			$ddd_fone2=resultadoSQL($consulta, $i, 'ddd_fone2');
			
			$telefone='';
			if($fone1) $telefone.="($ddd_fone1)&nbsp;$fone1";
			if($fone2) $telefone.="($ddd_fone2)&nbsp;$fone2";
				
			
			$cidade=checkCidade($idCidade);
			# Busca Tipo de Endere�o
			$idTipo=resultadoSQL($consulta, $i, 'idTipo');
			$tipoEndereco=checkTipoEndereco($idTipo);
			
			
			$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecosvisualizar&registro=$idPessoaTipo:$idTipo>Visualizar</a>",'ver');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecosalterar&registro=$idPessoaTipo:$idTipo>Alterar</a>",'alterar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecosexcluir&registro=$idPessoaTipo:$idTipo>Excluir</a>",'excluir');

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($endereco, 'center', '40%', 'normal10');
				itemLinhaTabela("$cidade[nome]/$cidade[uf]", 'center', '20%', 'normal10');
				itemLinhaTabela("$telefone", 'center', '20%', 'normal10');
				itemLinhaTabela($tipoEndereco, 'center', '20%', 'normal9');
				itemLinhaTabela($opcoes, 'center nowrap', '20%', 'normal10');
			fechaLinhaTabela();
			
			# Incrementar contador
			$i++;
		} #fecha laco de montagem de tabela
		
		fechaTabela();
	} #fecha servicos encontrados
}


# Fun��o para listagem 
function adicionarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $sessCadastro;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if( !$permissao[adicionar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		verPessoas($modulo, $sub, $acao, $registro, $matriz);	
	
		# Quebrar Registro
		$matRegistro=explode(":", $registro);
		$idPessoaTipo=$matRegistro[0];
		$idPessoa=$matRegistro[1];
		
		# Formul�rio para Adicionar endere�o
		if(!$matriz[bntConfirmar] || (!$matriz[endereco] || !$matriz[fone1] ) ) {
			# form
			echo "<br>";
				
			echo $texto;
			$matriz[acao]='adicionar';
			
			#limpar Vari�veis de endere�o
			$sessCadastro[endereco]='';
			$sessCadastro[complemento]='';
			$sessCadastro[cidade]='';
			$sessCadastro[tipoEndereco]='';
			$sessCadastro[uf]='';
			$sessCadastro[fone1]='';
			$sessCadastro[fone2]='';
			$sessCadastro[fax]='';
			$sessCadastro[email]='';
			$sessCadastro[bairro]='';
			$sessCadastro[cep]='';
			$sessCadastro[pais]='';
			$sessCadastro[caixaPostal]='';
		
			formAdicionarPessoaEndereco($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
		
			echo "<br>";
			
			$tipoEndereco=checkTipoEndereco($matriz[tipoEndereco]);
			
			# Gravar
			$gravaEndereco=dbEndereco($matriz, 'incluir');
			
			if($gravaEndereco) {
			
				# Verificar e incluir endere�o
				novaTabela2("[$tipoEndereco - Confirma��o de Cadastro]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
	
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Cidade/UF:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(formSelectCidade($matriz[cidade], $matriz[uf], '','check')."/$matriz[uf]", 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Endere�o:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($matriz[endereco], 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					if($matriz[complemento]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Complemento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($matriz[complemento], 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[bairro]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Bairro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($matriz[bairro], 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[cep]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>CEP:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($matriz[cep], 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[pais]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Pais:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($matriz[pais], 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[caixaPostal]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Caixa Postal:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($matriz[caixaPostal], 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[fone1]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>DDD/Fone:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm("$matriz[ddd_fone1] $matriz[fone1]", 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[fone2]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Fone2:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm("$matriz[ddd_fone2] $matriz[fone2]", 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[fax]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Fax:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm("$matriz[ddd_fax] $matriz[fax]", 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if($matriz[email]) {
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Email:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm("$matriz[email]", 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					
					#limpar Vari�veis de endere�o
					$sessCadastro[endereco]='';
					$sessCadastro[complemento]='';
					$sessCadastro[cidade]='';
					$sessCadastro[tipoEndereco]='';
					$sessCadastro[uf]='';
					$sessCadastro[ddd_fone1]='';
					$sessCadastro[fone1]='';
					$sessCadastro[ddd_fone2]='';
					$sessCadastro[fone2]='';
					$sessCadastro[ddd_fax]='';
					$sessCadastro[fax]='';
					$sessCadastro[email]='';
					$sessCadastro[bairro]='';
					$sessCadastro[cep]='';
					$sessCadastro[pais]='';
					$sessCadastro[caixaPostal]='';
					
					# Confirma��o de cadastro
					$msg="Cadastro efetuado!";
					$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
					aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
					
				fechaTabela();
			}
			else {
				# Erro
				$msg="ERRO na tentativa de altera��o de endere�o!";
				$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
				aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
			}
		}
	}
	
}#fecha fun��o 


# Fun��o para altera��o
function alterarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $sessCadastro, $conn;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if( !$permissao[alterar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		verPessoas($modulo, $sub, $acao, $registro, $matriz);	
	
		# Quebrar Registro
		$matRegistro=explode(":", $registro);
		$idPessoaTipo=$matRegistro[0];
		$idTipo=$matRegistro[1];
		
		# Buscar Informa��es sobre endere�o informado
		$sqlEndereco="SELECT
			$tb[Enderecos].idPessoaTipo idPessoaTipo,
			$tb[Enderecos].idTipo idTipo,
			$tb[Enderecos].endereco endereco,
			$tb[Enderecos].complemento complemento,
			$tb[Enderecos].bairro bairro,
			$tb[Enderecos].cep cep,
			$tb[Enderecos].pais pais,
			$tb[Enderecos].caixa_postal caixaPostal,
			$tb[Enderecos].ddd_fone1 ddd_fone1,
			$tb[Enderecos].fone1 fone1,
			$tb[Enderecos].ddd_fone2 ddd_fone2,
			$tb[Enderecos].fone2 fone2,
			$tb[Enderecos].ddd_fax ddd_fax,
			$tb[Enderecos].fax fax,
			$tb[Enderecos].email email,
			$tb[Cidades].nome cidade,
			$tb[Cidades].id idCidade,
			$tb[Cidades].uf uf,
			$tb[TipoEnderecos].descricao tipoEndereco
		FROM
			$tb[Enderecos],
			$tb[Cidades],
			$tb[TipoEnderecos]
		WHERE
			idPessoaTipo=$idPessoaTipo
			AND idTipo=$idTipo
			AND $tb[Cidades].id = $tb[Enderecos].idCidade
			AND $tb[Enderecos].idTipo = $tb[TipoEnderecos].id
		";
		$consultaEndereco=consultaSQL($sqlEndereco, $conn);

		if($consultaEndereco && contaConsulta($consultaEndereco)>0) {
		
			#atribuir valores para vari�veis
		
			# Formul�rio para Adicionar endere�o
			if(!$matriz[bntConfirmar] || (!$matriz[endereco] || !$matriz[fone1] ) ) {
			
				# Valores de vari�veis
				if(!$matriz[tipoEndereco]) {
					# Mostrar Endere�o
					$matriz[endereco]=resultadoSQL($consultaEndereco, 0, 'endereco');
					$matriz[complemento]=resultadoSQL($consultaEndereco, 0, 'complemento');
					$matriz[bairro]=resultadoSQL($consultaEndereco, 0, 'bairro');
					$matriz[cep]=resultadoSQL($consultaEndereco, 0, 'cep');
					$matriz[pais]=resultadoSQL($consultaEndereco, 0, 'pais');
					$matriz[caixaPostal]=resultadoSQL($consultaEndereco, 0, 'caixaPostal');
					$matriz[ddd_fone1]=resultadoSQL($consultaEndereco, 0, 'ddd_fone1');
					$matriz[fone1]=resultadoSQL($consultaEndereco, 0, 'fone1');
					$matriz[ddd_fone2]=resultadoSQL($consultaEndereco, 0, 'ddd_fone2');
					$matriz[fone2]=resultadoSQL($consultaEndereco, 0, 'fone2');
					$matriz[ddd_fax]=resultadoSQL($consultaEndereco, 0, 'ddd_fax');
					$matriz[fax]=resultadoSQL($consultaEndereco, 0, 'fax');
					$matriz[email]=resultadoSQL($consultaEndereco, 0, 'email');
					$matriz[cidade]=resultadoSQL($consultaEndereco, 0, 'idCidade');
					if(!$matriz[uf]) $matriz[uf]=resultadoSQL($consultaEndereco, 0, 'uf');
					$matriz[tipoEndereco]=resultadoSQL($consultaEndereco, 0, 'idTipo');
				}
				
				else {
					
					# Mostrar Endere�o
					$matriz[endereco]=resultadoSQL($consultaEndereco, 0, 'endereco');
					$matriz[complemento]=resultadoSQL($consultaEndereco, 0, 'complemento');
					$matriz[bairro]=resultadoSQL($consultaEndereco, 0, 'bairro');
					$matriz[cep]=resultadoSQL($consultaEndereco, 0, 'cep');
					$matriz[pais]=resultadoSQL($consultaEndereco, 0, 'pais');
					$matriz[caixaPostal]=resultadoSQL($consultaEndereco, 0, 'caixaPostal');
					$matriz[ddd_fone1]=resultadoSQL($consultaEndereco, 0, 'ddd_fone1');
					$matriz[fone1]=resultadoSQL($consultaEndereco, 0, 'fone1');
					$matriz[ddd_fone2]=resultadoSQL($consultaEndereco, 0, 'ddd_fone2');
					$matriz[fone2]=resultadoSQL($consultaEndereco, 0, 'fone2');
					$matriz[ddd_fax]=resultadoSQL($consultaEndereco, 0, 'ddd_fax');
					$matriz[fax]=resultadoSQL($consultaEndereco, 0, 'fax');
					$matriz[email]=resultadoSQL($consultaEndereco, 0, 'email');
					if(!$matriz[cidade]) $matriz[cidade]=resultadoSQL($consultaEndereco, 0, 'idCidade');
					if(!$matriz[uf]) $matriz[uf]=resultadoSQL($consultaEndereco, 0, 'uf');
				}
				
				# form
				echo "<br>";
					
				echo $texto;
				$matriz[acao]='alterar';
				formAdicionarPessoaEndereco($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
			
				echo "<br>";
				
				$tipoEndereco=checkTipoEndereco($matriz[tipoEndereco]);
				
				# Gravar
				$gravaEndereco=dbEndereco($matriz, 'alterar');
				
				if($gravaEndereco) {
				
					# Verificar e incluir endere�o
					novaTabela2("[$tipoEndereco - Confirma��o de Cadastro]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Cidade/UF:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm(formSelectCidade($matriz[cidade], $matriz[uf], '','check')."/$matriz[uf]", 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Endere�o:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($matriz[endereco], 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						if($matriz[complemento]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Complemento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($matriz[complemento], 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[bairro]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Bairro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($matriz[bairro], 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[cep]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>CEP:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($matriz[cep], 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[pais]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Pais:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($matriz[pais], 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[caixaPostal]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Caixa Postal:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($matriz[caixaPostal], 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[fone1]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>DDD/Fone1:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm("$matriz[ddd_fone1] $matriz[fone1]", 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[fone2]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>DDD/Fone2:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm("$matriz[ddd_fone2] $matriz[fone2]", 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[fax]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>DDD/Fax:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm("$matriz[ddd_fax] $matriz[fax]", 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if($matriz[email]) {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>E-mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm("$matriz[email]", 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						
						#limpar Vari�veis de endere�o
						$sessCadastro[endereco]='';
						$sessCadastro[complemento]='';
						$sessCadastro[cidade]='';
						$sessCadastro[tipoEndereco]='';
						$sessCadastro[uf]='';
						$sessCadastro[ddd_fone1]='';
						$sessCadastro[fone1]='';
						$sessCadastro[ddd_fone2]='';
						$sessCadastro[fone2]='';
						$sessCadastro[ddd_fax]='';
						$sessCadastro[fax]='';
						$sessCadastro[email]='';
						$sessCadastro[bairro]='';
						$sessCadastro[cep]='';
						$sessCadastro[pais]='';
						$sessCadastro[caixaPostal]='';
						
						# Confirma��o de cadastro
						$msg="Endere�o alterado com sucesso!";
						$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
						aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
						
					fechaTabela();
				}
				else {
					# Erro
					$msg="ERRO na tentativa de altera��o de endere�o!";
					$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
					aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
				}
			}
		}
	}
	
}#fecha fun��o 


# formul�rio de dados cadastrais
function formAdicionarPessoaEndereco($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Quebrar Registro
	$matRegistro=explode(":", $registro);
	$idPessoaTipo=$matRegistro[0];
	$idTipo=$matRegistro[1];
	
	$tipoEndereco=checkTipoEndereco($idTipo);
	
	$texto=formSelectTipoEnderecoPessoa($idPessoaTipo, $matriz[tipoEndereco], 'tipoEndereco',$matriz[acao]);
	
	if($texto) {

		# Pessoa f�sica
		novaTabela2("[Endere�o]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>&nbsp;
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
			<input type=hidden name=matriz[tipoEnderecoANT] value=$idTipo>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	
		if(!$matriz[tipoEndereco]) {
			# Selecionar tipo endere�o
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Endere�o:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectTipoEnderecoPessoa($idPessoaTipo, $matriz[tipoEndereco], 'tipoEndereco',$matriz[acao]).$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		else {
		
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Endere�o:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectTipoEnderecoPessoa($idPessoaTipo, $matriz[tipoEndereco], 'tipoEndereco',$matriz[acao]).$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		
			# Demais campos do endere�o
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>UF:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectUF($matriz[uf], 'uf','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			if($matriz[uf]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Cidade:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
					itemLinhaForm(formSelectCidade($matriz[cidade], $matriz[uf], 'cidade','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if($matriz[cidade]) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Logradouro (Rua, No.):</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[endereco] size=70 value='$matriz[endereco]'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Complemento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[complemento] size=70 value='$matriz[complemento]'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Bairro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[bairro] size=40 value='$matriz[bairro]'>";
						$texto.=" <b>Pa�s:</b> <input type=text name=matriz[pais] size=22 value='$matriz[pais]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>CEP:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						if($acao=='enderecosadicionar' || $acao=='enderecosalterar') $indiceEndereco=16;
						else $indiceEndereco=23;
						$texto="<input type=text name=matriz[cep] size=10 value='$matriz[cep]' onBlur=verificaCEP(this.value,$indiceEndereco)> ";
						$texto.=" <b>Caixa Postal:</b> <input type=text name=matriz[caixaPostal] size=10 value='$matriz[caixaPostal]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>DDD/Fone:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[ddd_fone1] size=3 value='$matriz[ddd_fone1]'> ";
						$texto.="<input type=text name=matriz[fone1] size=9 value='$matriz[fone1]'> ";
						$texto.="<b>DDD/Fone:</b> <input type=text name=matriz[ddd_fone2] size=3 value='$matriz[ddd_fone2]'> ";
						$texto.="<input type=text name=matriz[fone2] size=9 value='$matriz[fone2]'> ";
						$texto.=" <b>DDD/Fax:</b> <input type=text name=matriz[ddd_fax] size=3 value='$matriz[ddd_fax]'> ";
						$texto.="<input type=text name=matriz[fax] size=9 value='$matriz[fax]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>E-mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[email] size=60 value='$matriz[email]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					
					formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
					
				}
			}
			
		}
		
		fechaTabela();
	
	}
	else {
		# N�o h� mais tipos de endere�o dispon�veis para cadastramento
		$msg="N�o existem mais tipos de endere�os dispon�veis para cadastro nesta pessoa!";
		$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
		aviso("Aviso", $msg, $url, 600);
	}


}




# Fun��o para listagem 
function excluirEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $conn;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if( !$permissao[excluir]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
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
		
		# Visualizar endere�o informado
		$sqlEndereco="SELECT
			$tb[Enderecos].idPessoaTipo idPessoaTipo,
			$tb[Enderecos].idTipo idTipo,
			$tb[Enderecos].endereco endereco,
			$tb[Enderecos].complemento complemento,
			$tb[Enderecos].bairro bairro,
			$tb[Enderecos].cep cep,
			$tb[Enderecos].pais pais,
			$tb[Enderecos].caixa_postal caixaPostal,
			$tb[Enderecos].ddd_fone1 ddd_fone1,
			$tb[Enderecos].fone1 fone1,
			$tb[Enderecos].ddd_fone2 ddd_fone2,
			$tb[Enderecos].fone2 fone2,
			$tb[Enderecos].ddd_fax ddd_fax,
			$tb[Enderecos].fax fax,
			$tb[Enderecos].email email,
			$tb[Cidades].nome cidade,
			$tb[Cidades].uf uf,
			$tb[TipoEnderecos].descricao tipoEndereco
		FROM
			$tb[Enderecos],
			$tb[Cidades],
			$tb[TipoEnderecos]
		WHERE
			idPessoaTipo=$idPessoaTipo
			AND idTipo=$idTipo
			AND $tb[Cidades].id = $tb[Enderecos].idCidade
			AND $tb[Enderecos].idTipo = $tb[TipoEnderecos].id
		";
		
		$consultaEndereco=consultaSQL($sqlEndereco, $conn);
		
		if($consultaEndereco && contaConsulta($consultaEndereco)>0) {
			# Mostrar Endere�o
			$endereco=resultadoSQL($consultaEndereco, 0, 'endereco');
			$complemento=resultadoSQL($consultaEndereco, 0, 'complemento');
			$bairro=resultadoSQL($consultaEndereco, 0, 'bairro');
			$cep=resultadoSQL($consultaEndereco, 0, 'cep');
			$pais=resultadoSQL($consultaEndereco, 0, 'pais');
			$caixaPostal=resultadoSQL($consultaEndereco, 0, 'caixaPostal');
			$ddd_fone1=resultadoSQL($consultaEndereco, 0, 'ddd_fone1');
			$fone1=resultadoSQL($consultaEndereco, 0, 'fone1');
			$fone2=resultadoSQL($consultaEndereco, 0, 'ddd_fone2');
			$ddd_fone2=resultadoSQL($consultaEndereco, 0, 'fone2');
			$ddd_fax=resultadoSQL($consultaEndereco, 0, 'ddd_fax');
			$fax=resultadoSQL($consultaEndereco, 0, 'fax');
			$email=resultadoSQL($consultaEndereco, 0, 'email');
			$cidade=resultadoSQL($consultaEndereco, 0, 'cidade');
			$uf=resultadoSQL($consultaEndereco, 0, 'uf');
			$tipoEndereco=resultadoSQL($consultaEndereco, 0, 'tipoEndereco');
			$idTipoEndereco=resultadoSQL($consultaEndereco, 0, 'idTipo');
			
			# Motrar tabela de busca
			novaTabela2("[Excluir Endere�o - $tipoEndereco]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
					$texto="
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>&nbsp;
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
					<input type=hidden name=matriz[tipoEndereco] value=$idTipo>";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>Endere�o: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($endereco, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($complemento) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Complemento: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($complemento, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($bairro) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Bairro: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($bairro, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>CEP: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($cep, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<b class=bold10>Cidade/UF: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm("$cidade/$uf", 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($pais) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Pais: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($pais, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($caixaPostal) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Caixa Postal: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($caixaPostal, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($fone1) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>DDD/Fone1: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$ddd_fone1 $fone1", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($fone2) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>DDD/Fone2: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$ddd_fone2 $fone2", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($fax) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>Fax: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$ddd_fax $fax", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if($email) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("<b class=bold10>E-mail: </b>", 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm("$email", 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				
				
				if(!$matriz[bntConfirmar]) {
					
					# Bot�o de exclus�o
					novaLinhaTabela($corFundo, '100%');
						itemLinhaForm("<input type=submit name=matriz[bntConfirmar] value='Excluir' class=submit>", 'center', 'middle', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				}
				else {
					# excluir endere�o
					$gravaEndereco=dbEndereco($matriz, 'excluir');
					
					if($gravaEndereco) {
						# Confirma��o de cadastro
						$msg="Endere�o exclu�do com sucesso!";
						$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
						aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
					}
					else {
						# Aviso de erro
						$msg="ERRO ao excluir endere�o!";
						$url="?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro";
						aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');					
					}
				}
				
			fechaTabela();
			
		}
	}
	
}#fecha fun��o de listagem




# Fun��o para sele��o de dados de endere�o preferencial
function enderecoSelecionaPreferencial($consulta, $idTipoEndereco) {

	if($consulta && contaConsulta($consulta)>0) {
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$endereco[idTipo]=resultadoSQL($consulta, $a, 'idTipo');
			$endereco[idCidade]=resultadoSQL($consulta, $a, 'idCidade');
			$endereco[endereco]=resultadoSQL($consulta, $a, 'endereco');
			$endereco[complemento]=resultadoSQL($consulta, $a, 'complemento');
			$endereco[bairro]=resultadoSQL($consulta, $a, 'bairro');
			$endereco[cep]=cpfVerificaFormato(resultadoSQL($consulta, $a, 'cep'));
			$endereco[pais]=resultadoSQL($consulta, $a, 'pais');
			$endereco[caixaPostal]=resultadoSQL($consulta, $a, 'caixa_postal');
			$endereco[dddFone1]=resultadoSQL($consulta, $a, 'ddd_fone1');
			$endereco[fone1]=resultadoSQL($consulta, $a, 'fone1');
			$endereco[dddFone2]=resultadoSQL($consulta, $a, 'ddd_fone2');
			$endereco[fone2]=resultadoSQL($consulta, $a, 'fone2');
			$endereco[dddFax]=resultadoSQL($consulta, $a, 'ddd_fax');
			$endereco[fax]=resultadoSQL($consulta, $a, 'fax');
			$endereco[email]=resultadoSQL($consulta, $a, 'email');
			
			# Selecionar Cidade
			$cidade=buscaCidades($endereco[idCidade], 'id','igual','id');
			
			$endereco[cidade]=resultadoSQL($cidade, 0, 'nome');
			$endereco[uf]=resultadoSQL($cidade, 0, 'uf');
			
			# Quebrar CEP
			$endereco[cep_prefix]=substr($endereco[cep],0,5);
			$endereco[cep_sufix]=substr($endereco[cep],5,3);

			$consultaTipoEndereco=buscaTipoEnderecos($endereco[idTipo],'id','igual','id');
			if($consultaTipoEndereco && contaConsulta($consultaTipoEndereco)>0) 
				$tipoEndereco=resultadoSQL($consultaTipoEndereco, 0, 'valor');

			if($endereco[idTipo] == $idTipoEndereco) {
				$retorno=$endereco;
				$flagEndereco=1;
			}
			
			if($tipoEndereco == 'cor') $correspondencia=$endereco;
			elseif($tipoEndereco == 'cob') $cobranca=$endereco;
		}
		
		if(!$flagEndereco) {
			# retornar endereco secundario
			if(is_array($cobranca)) $retorno=$cobranca;
			elseif(is_array($correspondencia)) $retorno=$correspondencia;
		}
	}
	
	return($retorno);
}


# Fun��o para procura de servi�o
function procurarEnderecos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $sessCadastro;
	
	# Tipo de Pessoa
	novaTabela2("[Procurar Endere�os]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
		novaLinhaTabela($corFundo, '100%');
			$texto="
			<form method=post name=matriz action=\"index.php\">
			<input type=hidden name=modulo value=\"$modulo\">
			<input type=hidden name=sub value=\"$sub\">
			<input type=hidden name=acao value=procurar>
			<b>Procurar por:</b>&nbsp;<input type=text name=matriz[txtProcurarEnderecos] size=40>";
			
			$texto.="&nbsp;<input type=submit name=matriz[bntProcurarEnderecos] value=Procurar class=submit>";
			itemLinhaForm($texto, 'center','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso bot�o procurar seja pressionado
	if($matriz['txtProcurarEnderecos'] ) {

		$sql="
			SELECT
				$tb[Enderecos].endereco endereco, 
				$tb[Enderecos].idCidade idCidade, 
				$tb[Cidades].nome nomeCidade, 
				$tb[Cidades].uf uf, 
				$tb[PessoasTipos].idTipo, 
				$tb[Pessoas].id idPessoa, 
				$tb[Pessoas].idPOP idPOP, 
				$tb[Pessoas].nome, 
				$tb[PessoasTipos].id idPessoaTipo 
			FROM
				$tb[Pessoas], 
				$tb[PessoasTipos], 
				$tb[Enderecos],
				$tb[Cidades]
			WHERE
				$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
				AND $tb[PessoasTipos].id=$tb[Enderecos].idPessoaTipo 
				and $tb[Enderecos].idCidade = $tb[Cidades].id
				AND (
					$tb[Enderecos].endereco like '%$matriz[txtProcurarEnderecos]%'
					OR $tb[Enderecos].complemento like '%$matriz[txtProcurarEnderecos]%' 
					OR $tb[Enderecos].bairro like '%$matriz[txtProcurarEnderecos]%' 
					OR $tb[Enderecos].fone1 like '%$matriz[txtProcurarEnderecos]%' 
					OR $tb[Enderecos].fax like '%$matriz[txtProcurarEnderecos]%' 
					OR $tb[Enderecos].cep like '%$matriz[txtProcurarEnderecos]%' 
					)
		";
		
		$consulta=consultaSQL($sql, $conn);

		echo "<br>";
		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados endere�os cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Endere�os encontrados procurando por ('.$matriz[txtProcurarEnderecos].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&matriz[txtProcurarEnderecos]=".$matriz[txtProcurarEnderecos]."&matriz[bntProcurarEnderecos]=1";
			paginador($consulta, contaConsulta($consulta), $limite[lista][pessoas], $registro, 'normal', 5, $urlADD);

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Endereco', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('POP', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Cidade/UF', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '40%', 'tabfundo0');
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

			$limite=$i+$limite[lista][pessoas];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'idPessoa');
				$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
				$idPOP=resultadoSQL($consulta, $i, 'idPOP');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$endereco=resultadoSQL($consulta, $i, 'endereco');
				$idCidade=resultadoSQL($consulta, $i, 'idCidade');
				$cidade=resultadoSQL($consulta, $i, 'nomeCidade');
				$uf=resultadoSQL($consulta, $i, 'uf');
				$idTipo=resultadoSQL($consulta, $i, 'idTipo');
				$tmpCheckPessoa=checkIDTipoPessoa($idTipo);
				
				$opcoes=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=enderecos&registro=$idPessoaTipo:$id>Endere�os</a>",'endereco');
				if($tmpCheckPessoa[valor]=='cli') {
					$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$idPessoaTipo>Planos</a>",'planos');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$idPessoaTipo>Financeiro</a>",'financeiro');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=limites&registro=$idPessoaTipo>Administra��o</a>",'config');
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub&registro=$idPessoaTipo>Ocorr�ncias</a>",'ocorrencia');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=ver&registro=$idPessoaTipo:$id>Cadastro</a>",'ver');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=documentos&registro=$idPessoaTipo:$id>Documentos</a>",'documento');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&acao=listar&registro=$idPessoaTipo:$id>Contratos</a>",'contrato');
				}
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '20%', 'normal8');
					itemLinhaTabela($endereco, 'left', '20%', 'normal8');
					itemLinhaTabela(formSelectPOP($idPOP, '','check'), 'center', '10%', 'normal8');
					itemLinhaTabela("$cidade/$uf", 'center', '10%', 'normal8');
					itemLinhaTabela($opcoes, 'left', '40%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
		
		# Zerar pesquisa
		$sessCadastro[txtProcurar]='';
		$sessCadastro[bntProcurar]=0;
		fechaTabela();
	} # fecha bot�o procurar
} #fecha funcao de  procurar 



# Fun��o para buscar endere�os das Pessoas Tipos
function carregaEnderecoPessoaTipo($idPessoaTipo, $tipo) {

	global $conn, $tb;
	
	$sql="
		SELECT
			$tb[Enderecos].endereco, 
			$tb[Enderecos].complemento, 
			$tb[Enderecos].bairro, 
			$tb[Enderecos].cep, 
			$tb[Enderecos].pais, 
			$tb[Enderecos].caixa_postal, 
			$tb[Enderecos].ddd_fone1, 
			$tb[Enderecos].fone1, 
			$tb[Enderecos].ddd_fone2, 
			$tb[Enderecos].fone2, 
			$tb[Enderecos].ddd_fax, 
			$tb[Enderecos].fax, 
			$tb[Enderecos].email, 
			$tb[Cidades].nome cidade, 
			$tb[Cidades].uf 
		FROM
			$tb[Cidades], 
			$tb[Enderecos], 
			$tb[PessoasTipos], 
			$tb[TipoEnderecos] 
		WHERE
			$tb[Enderecos].idCidade = $tb[Cidades].id 
			AND $tb[PessoasTipos].id=$tb[Enderecos].idPessoaTipo 
			AND $tb[Enderecos].idTipo=$tb[TipoEnderecos].id 
			AND $tb[PessoasTipos].id=$idPessoaTipo
			AND $tb[TipoEnderecos].valor='$tipo'
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# Retornar dados de endere�o
		$retorno[endereco]=resultadoSQL($consulta, 0, 'endereco');
		$retorno[complemento]=resultadoSQL($consulta, 0, 'complemento');
		$retorno[bairro]=resultadoSQL($consulta, 0, 'bairro');
		$retorno[cep]=resultadoSQL($consulta, 0, 'cep');
		$retorno[pais]=resultadoSQL($consulta, 0, 'pais');
		$retorno[caixa_postal]=resultadoSQL($consulta, 0, 'caixa_postal');
		$retorno[ddd_fone1]=resultadoSQL($consulta, 0, 'ddd_fone1');
		$retorno[fone1]=resultadoSQL($consulta, 0, 'fone1');
		$retorno[ddd_fone2]=resultadoSQL($consulta, 0, 'ddd_fone2');
		$retorno[fone2]=resultadoSQL($consulta, 0, 'fone2');
		$retorno[ddd_fax]=resultadoSQL($consulta, 0, 'fax');
		$retorno[email]=resultadoSQL($consulta, 0, 'email');
		$retorno[cidade]=resultadoSQL($consulta, 0, 'cidade');
		$retorno[uf]=resultadoSQL($consulta, 0, 'uf');
		
	}
	
	return($retorno);
}


# Fun��o para busca de telefones do cadastro
function telefonesPessoasTipos($idPessoaTipo) {

	$consulta=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idPessoaTipo');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Retornvar lista de telefones formatado
		
		$retorno='';
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$ddd_fone1=resultadoSQL($consulta, $a, 'ddd_fone1');
			$fone1=resultadoSQL($consulta, $a, 'fone1');
			$ddd_fone2=resultadoSQL($consulta, $a, 'ddd_fone2');
			$fone2=resultadoSQL($consulta, $a, 'fone2');
			
			if($ddd_fone1 && $fone1) $retorno.=formatarFone($ddd_fone1.$fone1)."&nbsp;";
			if($ddd_fone2 && $fone2 && $fone1 != $fone2) $retorno.=formatarFone($ddd_fone2.$fone2);
		}
	}
	
	return($retorno);
}


/**
 * @return void
 * @param unknown $idPessoaTipo
 * @desc Retorna um array com os dados de enderecos.<BR>
 Alem dos campos da tabela, existem os seguintes campos extras:<BR>
enderecoCompleto: endereco+complemento<BR>
cidade   : cidade,<BR>
uf       : estado,<BR>
fone1DDD : ddd+fone1,<BR>
fone2DDD : ddd+fone2,<BR>
faxDDD   : ddd+fax,<BR>
telefones: fone1DDD / fone2DDD / FaxDDD
*/
function dadosEndereco($idPessoaTipo) {

	global $tb;
	
	$consulta=buscaRegistros($idPessoaTipo, 'idPessoaTipo', 'igual', 'idPessoaTipo', $tb[Enderecos]);
	
	if(contaConsulta($consulta)>0) {		
		//$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
		//$retorno[tipoEndereco]=resultadoSQL($consulta, 0, 'tipoEndereco');
		$retorno[idCidade]=resultadoSQL($consulta, 0, 'idCidade');
		$retorno[endereco]=resultadoSQL($consulta, 0, 'endereco');
		$retorno[complemento]=resultadoSQL($consulta, 0, 'complemento');
		$retorno[bairro]=resultadoSQL($consulta, 0, 'bairro');
		$retorno[cep]=resultadoSQL($consulta, 0, 'cep');
		$retorno[pais]=resultadoSQL($consulta, 0, 'pais');
		$retorno[caixaPostal]=resultadoSQL($consulta, 0, 'caixa_postal');
		$retorno[ddd_fone1]=resultadoSQL($consulta, 0, 'ddd_fone1');
		$retorno[fone1]=resultadoSQL($consulta, 0, 'fone1');
		$retorno[ddd_fone2]=resultadoSQL($consulta, 0, 'ddd_fone2');
		$retorno[fone2]=resultadoSQL($consulta, 0, 'fone2');
		$retorno[ddd_fax]=resultadoSQL($consulta, 0, 'ddd_fax');
		$retorno[fax]=resultadoSQL($consulta, 0, 'fax');
		$retorno[email]=resultadoSQL($consulta, 0, 'email');
		
		# Endereco
		$retorno[enderecoCompleto]=$retorno[endereco]." ";
		if($retorno[complemento]) 
			$retorno[enderecoCompleto].=$retorno[complemento];
			
		# Cidade / UF
		$cidade=checkCidade($retorno[idCidade]);
		$retorno[cidade]=$cidade[nome];
		$retorno[uf]=$cidade[uf];
		
		# Telefones
		$ddd  = resultadoSQL($consulta, 0, 'ddd_fone1');
		$fone = resultadoSQL($consulta, 0, 'fone1');
		if($ddd && $fone) $retorno[fone1DDD]="($ddd) ";
		$retorno[fone1DDD].=$fone;
		
		$ddd  = resultadoSQL($consulta, 0, 'ddd_fone2');
		$fone = resultadoSQL($consulta, 0, 'fone2');
		if($ddd && $fone) $retorno[fone2DDD]="($ddd) ";
		$retorno[fone2DDD].=$fone;
		
		$ddd  = resultadoSQL($consulta, 0, 'ddd_fax');
		$fone = resultadoSQL($consulta, 0, 'fax');
		if($ddd && $fone) $retorno[faxDDD]="($ddd) ";
		$retorno[faxDDD].=$fone;
		
		if($retorno[fone1DDD]) {
			$retorno[telefones]=$retorno[fone1DDD];
		}
		
		if($retorno[fone2DDD]) {
			if($retorno[telefones]) $retorno[telefones].=" / ";
			$retorno[telefones].=$retorno[fone2DDD];
		}
		
		if($retorno[faxDDD]) {
			if($retorno[telefones]) $retorno[telefones].=" : ";
			$retorno[telefones].=$retorno[faxDDD];
		}
		
		return($retorno);
	}
}

/**
 * @return void
 * @param unknown $idPessoaTipo
 * @desc Retorna um array com os dados de enderecos PARA COBRANCA.<BR>
 Alem dos campos da tabela, existem os seguintes campos extras:<BR>
enderecoCompleto: endereco+complemento<BR>
cidade   : cidade,<BR>
uf       : estado,<BR>
fone1DDD : ddd+fone1,<BR>
fone2DDD : ddd+fone2,<BR>
faxDDD   : ddd+fax,<BR>
telefones: fone1DDD / fone2DDD / FaxDDD
*/
function dadosEnderecoCustom($idPessoaTipo, $tipo='') {

	global $tb, $conn;
	
	if (!empty($tipo)) $sqlTipo = " AND $tb[Enderecos].idTipo = $tipo";
	$query= "SELECT * " .
			"FROM $tb[Enderecos] " .
			"WHERE  $tb[Enderecos].idPessoaTipo = $idPessoaTipo " .
			"$sqlTipo";
	$consulta = consultaSQL( $query, $conn); 
	//	$consulta=buscaRegistros($idPessoaTipo, 'idPessoaTipo', 'igual', 'idPessoaTipo', $tb[Enderecos]);
	
	if(contaConsulta($consulta)>0) {		
		//$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
		//$retorno[tipoEndereco]=resultadoSQL($consulta, 0, 'tipoEndereco');
		$retorno[idCidade]=resultadoSQL($consulta, 0, 'idCidade');
		$retorno[endereco]=resultadoSQL($consulta, 0, 'endereco');
		$retorno[complemento]=resultadoSQL($consulta, 0, 'complemento');
		$retorno[bairro]=resultadoSQL($consulta, 0, 'bairro');
		$retorno[cep]=resultadoSQL($consulta, 0, 'cep');
		$retorno[pais]=resultadoSQL($consulta, 0, 'pais');
		$retorno[caixaPostal]=resultadoSQL($consulta, 0, 'caixa_postal');
		$retorno[ddd_fone1]=resultadoSQL($consulta, 0, 'ddd_fone1');
		$retorno[fone1]=resultadoSQL($consulta, 0, 'fone1');
		$retorno[ddd_fone2]=resultadoSQL($consulta, 0, 'ddd_fone2');
		$retorno[fone2]=resultadoSQL($consulta, 0, 'fone2');
		$retorno[ddd_fax]=resultadoSQL($consulta, 0, 'ddd_fax');
		$retorno[fax]=resultadoSQL($consulta, 0, 'fax');
		$retorno[email]=resultadoSQL($consulta, 0, 'email');
		
		# Endereco
		$retorno[enderecoCompleto]=$retorno[endereco]." ";
		if($retorno[complemento]) 
			$retorno[enderecoCompleto].=$retorno[complemento];
			
		# Cidade / UF
		$cidade=checkCidade($retorno[idCidade]);
		$retorno[cidade]=$cidade[nome];
		$retorno[uf]=$cidade[uf];
		
		# Telefones
		$ddd  = resultadoSQL($consulta, 0, 'ddd_fone1');
		$fone = resultadoSQL($consulta, 0, 'fone1');
		if($ddd && $fone) $retorno[fone1DDD]="($ddd) ";
		$retorno[fone1DDD].=$fone;
		
		$ddd  = resultadoSQL($consulta, 0, 'ddd_fone2');
		$fone = resultadoSQL($consulta, 0, 'fone2');
		if($ddd && $fone) $retorno[fone2DDD]="($ddd) ";
		$retorno[fone2DDD].=$fone;
		
		$ddd  = resultadoSQL($consulta, 0, 'ddd_fax');
		$fone = resultadoSQL($consulta, 0, 'fax');
		if($ddd && $fone) $retorno[faxDDD]="($ddd) ";
		$retorno[faxDDD].=$fone;
		
		if($retorno[fone1DDD]) {
			$retorno[telefones]=$retorno[fone1DDD];
		}
		
		if($retorno[fone2DDD]) {
			if($retorno[telefones]) $retorno[telefones].=" / ";
			$retorno[telefones].=$retorno[fone2DDD];
		}
		
		if($retorno[faxDDD]) {
			if($retorno[telefones]) $retorno[telefones].=" : ";
			$retorno[telefones].=$retorno[faxDDD];
		}
		
		return($retorno);
	}
}
?>