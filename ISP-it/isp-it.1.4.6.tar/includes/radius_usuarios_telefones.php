<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 19/02/2004
# Ultima alteração: 19/02/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para controle de serviço de radius (telefones)
#
# Função de configurações

function adicionarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	$total=radiusTotalContas($registro);
	$totalEmUso=radiusTotalContasEmUso($registro);

	$matriz[telefone]=formatarFoneNumeros($matriz[telefone]);
	
	# Form de inclusao
	if(!$matriz[telefone] || !$matriz[status] ) {
		# Visualizar conta
		administracaoRadiusVerConta($modulo, $sub, $acao, $registro, $matriz);
		
		# Motrar tabela de busca
		echo "<br>";
		novaTabela2("[Adicionar Telefone Autorizado]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Validações
			if($matriz[senha] != $matriz[confirma_senha]) {
				$validacao="<span class=txtaviso>Senha e Confirmação de senha não conferem!</span><br><br>";
			}
			
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro:$matriz[id]>
				&nbsp;<br>$validacao";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Telefone: </b><br>
					<span class=normal10>Numero do telefone autorizado</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[telefone] size=30 value='$matriz[telefone]'>";
				$texto.=" <span class=txtaviso>(Formato: 1433241128)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status inicial para usuario</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusRadiusTelefones('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[telefone] && $matriz[status]) {
		
			# Cadastrar em banco de dados
			$matriz[idRadiusUsuarioPessoaTipo]=$matriz[id];
			$grava=dbRadiusUsuarioTelefone($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
			
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				echo "<br>";
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				listarRadiusTelefones($modulo, $sub, 'telefones', $registro, $matriz);
			}
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			
			echo "<center>";
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
			echo "</center>";
		}
	}
}




# Alteração
function alterarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			RadiusUsuariosPessoasTipos.idPessoasTipos idPessoaTipo, 
			RadiusUsuariosTelefones.id id, 
			RadiusUsuariosTelefones.telefone telefone, 
			RadiusUsuariosTelefones.dtCadastro dtCadastro, 
			RadiusUsuariosTelefones.status status,
			RadiusUsuarios.id idRadiusUsuario
		FROM
			RadiusUsuariosPessoasTipos, 
			RadiusUsuariosTelefones,
			RadiusUsuarios
		WHERE
			RadiusUsuariosPessoasTipos.id = RadiusUsuariosTelefones.idRadiusUsuarioPessoaTipo 
			AND RadiusUsuariosPessoasTipos.idRadiusUsuarios = RadiusUsuarios.id
			AND RadiusUsuariosTelefones.id=$matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		$matriz[telefone]=formatarFoneNumeros($matriz[telefone]);
		
		# Form de inclusao
		if(!$matriz[telefone]) {

			$id=resultadoSQL($consulta, 0, 'id');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idRadiusUsuario=resultadoSQL($consulta, 0, 'idRadiusUsuario');
			$telefone=resultadoSQL($consulta, 0, 'telefone');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Motrar tabela de busca
			novaTabela2("[Alteração de Telefone Autorizado]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				#fim das opcoes adicionais
				
				novaLinhaTabela($corFundo, '100%');
				$texto="
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuario] value=$idRadiusUsuario>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Telefone: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[telefone] size=30 value='$telefone'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadiusTelefones($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# Cadastrar em banco de dados
			$grava=dbRadiusUsuarioTelefone($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
			
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				$matriz[id]=$matriz[idRadiusUsuario];
				listarRadiusTelefones($modulo, $sub, 'telefones', $registro, $matriz);
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<br><center>";
				$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
				echo "</center>";
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro não encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config&registro=registro";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}



# Exclusão
function excluirRadiusTelefone($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			RadiusUsuariosPessoasTipos.idPessoasTipos idPessoaTipo, 
			RadiusUsuariosTelefones.id id, 
			RadiusUsuariosTelefones.telefone telefone, 
			RadiusUsuariosTelefones.dtCadastro dtCadastro, 
			RadiusUsuariosTelefones.status status,
			RadiusUsuarios.id idRadiusUsuario
		FROM
			RadiusUsuariosPessoasTipos, 
			RadiusUsuariosTelefones,
			RadiusUsuarios
		WHERE
			RadiusUsuariosPessoasTipos.id = RadiusUsuariosTelefones.idRadiusUsuarioPessoaTipo 
			AND RadiusUsuariosPessoasTipos.idRadiusUsuarios = RadiusUsuarios.id
			AND RadiusUsuariosTelefones.id=$matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		# Form de inclusao
		if(!$matriz[bntExcluir]) {

			$id=resultadoSQL($consulta, 0, 'id');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idRadiusUsuario=resultadoSQL($consulta, 0, 'idRadiusUsuario');
			$telefone=resultadoSQL($consulta, 0, 'telefone');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Motrar tabela de busca
			novaTabela2("[Exclusão de Telefone Autorizado]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				#fim das opcoes adicionais
				
				novaLinhaTabela($corFundo, '100%');
				$texto="
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuario] value=$idRadiusUsuario>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Telefone: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[telefone] size=30 value='$telefone'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadiusTelefones($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit2>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntExcluir]) {
			# Cadastrar em banco de dados
			$grava=dbRadiusUsuarioTelefone($matriz, 'excluir');
			
			# Verificar inclusão de registro
			if($grava) {
			
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				$matriz[id]=$matriz[idRadiusUsuario];
				listarRadiusTelefones($modulo, $sub, 'telefones', $registro, $matriz);
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<br><center>";
				$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
				echo "</center>";
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro não encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config&registro=registro";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}



# Ativação
function ativarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			RadiusUsuariosPessoasTipos.idPessoasTipos idPessoaTipo, 
			RadiusUsuariosTelefones.id id, 
			RadiusUsuariosTelefones.telefone telefone, 
			RadiusUsuariosTelefones.dtCadastro dtCadastro, 
			RadiusUsuariosTelefones.status status,
			RadiusUsuarios.id idRadiusUsuario
		FROM
			RadiusUsuariosPessoasTipos, 
			RadiusUsuariosTelefones,
			RadiusUsuarios
		WHERE
			RadiusUsuariosPessoasTipos.id = RadiusUsuariosTelefones.idRadiusUsuarioPessoaTipo 
			AND RadiusUsuariosPessoasTipos.idRadiusUsuarios = RadiusUsuarios.id
			AND RadiusUsuariosTelefones.id=$matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		# Form de inclusao
		if(!$matriz[bntAtivar]) {

			$id=resultadoSQL($consulta, 0, 'id');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idRadiusUsuario=resultadoSQL($consulta, 0, 'idRadiusUsuario');
			$telefone=resultadoSQL($consulta, 0, 'telefone');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Motrar tabela de busca
			novaTabela2("[Ativação de Telefone Autorizado]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				#fim das opcoes adicionais
				
				novaLinhaTabela($corFundo, '100%');
				$texto="
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuario] value=$idRadiusUsuario>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Telefone: </b>";
					htmlFechaColuna();
					itemLinhaForm($telefone, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadiusTelefones($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAtivar] value=Ativar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAtivar]) {
			# Cadastrar em banco de dados
			$matriz[status]='A';
			$grava=dbRadiusUsuarioTelefone($matriz, 'status');
			
			# Verificar inclusão de registro
			if($grava) {
			
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				$matriz[id]=$matriz[idRadiusUsuario];
				listarRadiusTelefones($modulo, $sub, 'telefones', $registro, $matriz);
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<br><center>";
				$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
				echo "</center>";
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro não encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config&registro=registro";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}



# Inativação
function inativarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	echo "<br>";
	
	$sql="
		SELECT
			RadiusUsuariosPessoasTipos.idPessoasTipos idPessoaTipo, 
			RadiusUsuariosTelefones.id id, 
			RadiusUsuariosTelefones.telefone telefone, 
			RadiusUsuariosTelefones.dtCadastro dtCadastro, 
			RadiusUsuariosTelefones.status status,
			RadiusUsuarios.id idRadiusUsuario
		FROM
			RadiusUsuariosPessoasTipos, 
			RadiusUsuariosTelefones,
			RadiusUsuarios
		WHERE
			RadiusUsuariosPessoasTipos.id = RadiusUsuariosTelefones.idRadiusUsuarioPessoaTipo 
			AND RadiusUsuariosPessoasTipos.idRadiusUsuarios = RadiusUsuarios.id
			AND RadiusUsuariosTelefones.id=$matriz[id]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		# Form de inclusao
		if(!$matriz[bntInativar]) {

			$id=resultadoSQL($consulta, 0, 'id');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idRadiusUsuario=resultadoSQL($consulta, 0, 'idRadiusUsuario');
			$telefone=resultadoSQL($consulta, 0, 'telefone');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Motrar tabela de busca
			novaTabela2("[Inativação de Telefone Autorizado]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				#fim das opcoes adicionais
				
				novaLinhaTabela($corFundo, '100%');
				$texto="
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[id]>
					<input type=hidden name=matriz[idRadiusUsuario] value=$idRadiusUsuario>
					&nbsp;<br>$validacao";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Telefone: </b>";
					htmlFechaColuna();
					itemLinhaForm($telefone, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadiusTelefones($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntInativar] value=Inativar class=submit2>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntInativar]) {
			# Cadastrar em banco de dados
			$matriz[status]='I';
			$grava=dbRadiusUsuarioTelefone($matriz, 'status');
			
			# Verificar inclusão de registro
			if($grava) {
			
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<center>";
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "</center>";
				
				$matriz[id]=$matriz[idRadiusUsuario];
				listarRadiusTelefones($modulo, $sub, 'telefones', $registro, $matriz);
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				echo "<br><center>";
				$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
				echo "</center>";
			}
		}
	}
	else {
		# registro nao encontrado
		$msg="Registro não encontrado!!!";
		$url="?modulo=$modulo&sub=$sub&acao=config&registro=registro";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}



# Alteração de conta
function listarRadiusTelefones($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	# Visualizar conta
	administracaoRadiusVerConta($modulo, $sub, $acao, $registro, $matriz);
	
	# Motrar tabela de busca
	echo "<br>";
	
	novaTabela("[Telefones Autorizados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	
		$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=${acao}adicionar&registro=$registro:$matriz[id]>Adicionar</a>",'incluir');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 4, 'tabfundo1');

		$consulta=buscaRadiusTelefones($matriz[id], 'idRadiusUsuarioPessoaTipo','igual','id');
	
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
		}
		else {
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Telefone', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Data de Cadastro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '25%', 'tabfundo0');
			fechaLinhaTabela();
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$id=resultadoSQL($consulta, $a, 'id');
				$idRadiusUsuarioPessoaTipo=resultadoSQL($consulta, $a, 'idRadiusUsuarioPessoaTipo');
				$telefone=resultadoSQL($consulta, $a, 'telefone');
				$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
				$status=resultadoSQL($consulta, $a, 'status');
			
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=telefonesalterar&registro=$registro:$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=telefonesexcluir&registro=$registro:$id>Excluir</a>",'excluir');
				if($status=='A') 
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=telefonesinativar&registro=$registro:$id>Inativar</a>",'ativar');
				else
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=telefonesativar&registro=$registro:$id>Ativar</a>",'desativar');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($telefone, 'left', '30%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro,'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectStatusRadiusTelefones($status,'','check'), 'center', '15%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();

}


# função de busca de grupos
function buscaRadiusTelefones($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[RadiusUsuariosTelefones] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[RadiusUsuariosTelefones] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[RadiusUsuariosTelefones] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[RadiusUsuariosTelefones] WHERE $texto ORDER BY $ordem";
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







# Função para gravação em banco de dados
function dbRadiusUsuarioTelefone($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
	
		$sql="INSERT INTO $tb[RadiusUsuariosTelefones] VALUES (0,
			'$matriz[idRadiusUsuarioPessoaTipo]',
			'$matriz[telefone]',
			'$data[dataBanco]',
			'$matriz[status]'
		)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[RadiusUsuariosTelefones] WHERE id=$matriz[id]";
	}
	
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM $tb[RadiusUsuariosTelefones] WHERE idRadiusUsuarioPessoaTipo=$matriz[id]";
	}
	
	elseif($tipo=='excluirconta') {
		# Selecionar todos os telefones para idRadiusUsuarioPessoaTipo
		$sql="DELETE FROM $tb[RadiusUsuariosTelefones] WHERE idRadiusUsuarioPessoaTipo=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[RadiusUsuariosTelefones] 
			SET 
				telefone='$matriz[telefone]'
		WHERE id=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='status') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[RadiusUsuariosTelefones] 
			SET 
				status='$matriz[status]'
		WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Função para carregamento de telefones de conta radius
function carregarRadiusTelefones($login) {

	global $conn, $tb;
	
	$sql="
		SELECT
			$tb[RadiusUsuariosTelefones].telefone 
		FROM
			$tb[RadiusUsuarios], 
			$tb[RadiusUsuariosPessoasTipos], 
			$tb[RadiusUsuariosTelefones]
		WHERE
			$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios = $tb[RadiusUsuarios].id 
			AND $tb[RadiusUsuariosPessoasTipos].id = $tb[RadiusUsuariosTelefones].idRadiusUsuarioPessoaTipo 
			AND $tb[RadiusUsuariosTelefones].status='A'
			AND $tb[RadiusUsuarios].login='$login'
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$retorno[]=resultadoSQL($consulta, $a, 'telefone');
		}
	}
	
	return($retorno);

}

?>
