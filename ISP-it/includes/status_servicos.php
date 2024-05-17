<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 10/07/2003
# Ultima altera��o: 01/08/2003
#    Altera��o No.: 004
#
# Fun��o:
#    Painel - Fun��es para cadastro de status de servi�os


# Fun��o para cadastro
function statusServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informa��es e menu principal do Cadastro
		novaTabela2("[Cadastro de Status de Servi�os]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][status]." border=0 align=left><b class=bold>Status de Servi�os</b>
					<br><span class=normal10>Cadastro de <b>status de servi�os</b>, utilizados para o manuten��o de servi�os
					de planos.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		# Inclus�o
		if($acao=="adicionar") {
			echo "<br>";
			adicionarStatusServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Altera��o
		elseif($acao=="alterar") {
			echo "<br>";
			alterarStatusServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Exclus�o
		elseif($acao=="excluir") {
			echo "<br>";
			excluirStatusServicos($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar") {
			echo "<br>";
			procurarStatusServicos($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
		
		# Listar
		elseif($acao=="listar" || !$acao) {
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			listarStatusServicos($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
	}


} #fecha menu principal 


# fun��o de busca 
function buscaStatusServicos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[StatusServicos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[StatusServicos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[StatusServicos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[StatusServicos] WHERE $texto ORDER BY $ordem";
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




# Funcao para cadastro 
function adicionarStatusServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Form de inclusao
	if(!$matriz[bntConfirmar]) {
		# Motrar tabela de busca
		
		formStatusServicos($modulo, $sub, $acao, $registro, $matriz);

	} #fecha form
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[descricao] && $matriz[cobranca] && $matriz[status]) {
			# Buscar por prioridade
			$grava=dbStatusServico($matriz, 'incluir');
			
			# Verificar inclus�o de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				avisoNOURL("Confirma��o de Grava��o", $msg, 400);
				
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'normal10');
				
				
				adicionarStatusServicos($modulo, $sub, $acao, $registro, '');
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao gravar registro!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso de erro", $msg, $url, 400);
			}
				
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}

} # fecha funcao de inclusao




# Fun��o para grava��o em banco de dados
function dbStatusServico($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[StatusServicos] VALUES (0,
		'$matriz[descricao]',
		'$matriz[cobranca]',
		'$matriz[status]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaStatusServicos($matriz[id], 'id', 'igual', 'id');
		
		# Registro j� existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro n�o existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[StatusServicos] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="UPDATE 
					$tb[StatusServicos] 
				SET 
					descricao='$matriz[descricao]', 
					cobranca='$matriz[cobranca]',
					status='$matriz[status]'
				WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados



# Listar 
function listarStatusServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
		# Sele��o de registros
		$consulta=buscaStatusServicos($texto, $campo, 'todos','descricao');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][status_servicos], $registro, 'normal10', 4, $urlADD);
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Descri��o', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Cobran�a', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '30%', 'tabfundo0');
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

			$limite=$i+$limite[lista][status_servicos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$cobranca=resultadoSQL($consulta, $i, 'cobranca');
				$status=resultadoSQL($consulta, $i, 'status');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '50%', 'normal10');
					itemLinhaTabela(formSelectStatus($status,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela(formSelectCobranca($cobranca,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha fun��o de listagem


# Fun��o para procura de servi�o
function procurarStatusServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a vari�vel de busca
	if($textoProcurar) {
		$matriz[bntProcurar]=1;
		$matriz[txtProcurar]=$textoProcurar;
	} #fim da atribuicao de variaveis
	
	# Motrar tabela de busca
	novaTabela2("[Procurar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b>Procurar por:</b>";
			htmlFechaColuna();
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<input type=text name=matriz[txtProcurar] size=40 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	echo "</form>";


	# Caso bot�o procurar seja pressionado
	if($matriz[txtProcurar] && $matriz[bntProcurar]) {
		#buscar registros
		$consulta=buscaStatusServicos("upper(descricao) like '%$matriz[txtProcurar]%'",$campo, 'custom','descricao');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][status_servicos], $registro, 'normal', 4, $urlADD);

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Descricao', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Cobranca', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '30%', 'tabfundo0');
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

			$limite=$i+$limite[lista][status_servicos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$cobranca=resultadoSQL($consulta, $i, 'cobranca');
				$status=resultadoSQL($consulta, $i, 'status');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '50%', 'normal10');
					itemLinhaTabela(formSelectStatus($status,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela(formSelectCobranca($cobranca,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
} #fecha funcao de  procurar 



# Funcao para altera��o
function alterarStatusServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif(!$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$consulta=buscaStatusServicos($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$matriz[descricao]=resultadoSQL($consulta, 0, 'descricao');
			$matriz[cobranca]=resultadoSQL($consulta, 0, 'cobranca');
			$matriz[status]=resultadoSQL($consulta, 0, 'status');
			
			formStatusServicos($modulo, $sub, $acao, $registro, $matriz);
			
		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Altera��o - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[descricao]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbStatusServico($matriz, 'alterar');
			
			# Verificar inclus�o de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				avisoNOURL("Confirma��o de Grava��o", $msg, 400);
				echo "<br>";
				
				listarStatusServicos($modulo, $sub, 'listar', '', '');
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
	
} # fecha funcao de altera��o


# Exclus�o de servicos
function excluirStatusServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro n�o foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaStatusServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$id=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$cobranca=resultadoSQL($consulta, 0, 'cobranca');
			$status=resultadoSQL($consulta, 0, 'status');
			
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
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Descri��o: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Cobranca: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectCobranca($cobranca, '','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatus($status, '','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
	
				# Bot�o de confirma��o
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Altera��o - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbStatusServico($matriz, 'excluir');
				
		# Verificar inclus�o de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro exclu�do com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			avisoNOURL("Confirma��o de Grava��o", $msg, 400);
			echo "<br>";
			
			listarStatusServicos($modulo, $sub, 'listar', '', '');
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# fun��o de forma para sele��o de tipo de pessoas
function formSelectStatusServico($status, $campo, $tipo) {

	if($tipo=='check') {
	
		$consulta=buscaStatusServicos($status,'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$cobranca=resultadoSQL($consulta, 0, 'cobranca');
			
			if($cobranca=='S') $opcRetorno='Sim';
			elseif($cobranca=='N') $opcRetorno='N�o';

			$retorno[descricao]=$descricao;
			$retorno[cobranca]=$cobranca;
			$retorno[detalhe]="$descricao / Gera Cobran�a: $opcRetorno";
		}
	
	}
	elseif($tipo=='form') {
	
		$consulta=buscaStatusServicos('','','todos','descricao');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select name=matriz[$campo]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$cobranca=resultadoSQL($consulta, $i, 'cobranca');
				
				if($cobranca=='S') $opcRetorno='Sim';
				elseif($cobranca=='N') $opcRetorno='N�o';
			
				if($status==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$descricao - Gera Cobran�a: $opcRetorno";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
}



# fun��o de forma para sele��o de tipo de pessoas
function formSelectStatusServicoAtivos($status, $campo, $tipo) {


	$consulta=buscaStatusServicos("status='$status' || status='N'",'','custom','descricao');
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$retorno="<select name=matriz[$campo]>";
		
		for($i=0;$i<contaConsulta($consulta);$i++) {
		
			$id=resultadoSQL($consulta, $i, 'id');
			$descricao=resultadoSQL($consulta, $i, 'descricao');
			$cobranca=resultadoSQL($consulta, $i, 'cobranca');
			
			if($cobranca=='S') $opcRetorno='Sim';
			elseif($cobranca=='N') $opcRetorno='N�o';
		
			if($status==$id) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="\n<option value=$id $opcSelect>$descricao - Gera Cobran�a: $opcRetorno";
		}
		
		$retorno.="</select>";
	}
	
	return($retorno);
}



# Fun��o de formul�rio de status de servi�o
function formStatusServicos($modulo, $sub, $acao, $registro, $matriz) {

	
	global $corFundo, $corBorda;
	
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
			<input type=hidden name=matriz[id] value=$registro>
			<input type=hidden name=registro value=$registro>
			&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Descri��o: </b><br>
				<span class=normal10>Descri��o do tipo de status</span>";
			htmlFechaColuna();
			$texto="<input type=text name=matriz[descricao] size=60 value='$matriz[descricao]'>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Status gera cobran�a ? </b><br>
				<span class=normal10>Selecionar \"Sim\" caso status gere cobran�a</span>";
			htmlFechaColuna();
			itemLinhaForm(formSelectCobranca($matriz[cobranca], 'cobranca', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Status de Servi�o: </b><br>
				<span class=normal10>Selecionar o status de visualiza��o do servi�o</span>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatus($matriz[status], 'status', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "&nbsp;";
			htmlFechaColuna();
			$texto="<input type=submit name=matriz[bntConfirmar] value=Confirmar class=submit>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
}




# Fun��o para checagem de status do servi�o
function checkStatusServico($status) {

	$consulta=buscaStatusServicos($status, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Retornar valor
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[cobranca]=resultadoSQL($consulta, 0, 'cobranca');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
	}
	
	return($retorno);

}



# Fun��o para checagem de status do servi�o
function checkStatusStatusServico($status) {

	$consulta=buscaStatusServicos($status, 'status','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Retornar valor
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[cobranca]=resultadoSQL($consulta, 0, 'cobranca');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
	}
	
	return($retorno);

}

/**
 * @desc retorna um vetor com o id de todos os status cuja cobranca eh verdadeira
 * 
 * @return array ids dos status
 *
 */
function statusServicosCobradosId(){
	$id = array();
	$consulta=buscaStatusServicos("cobranca='S'",'','custom','id');
	
	for($i=0;$i<contaConsulta($consulta);$i++)
		$id[]=resultadoSQL($consulta, $i, 'id');
		
	return $id;
}


?>
