<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 12/01/2004
# Ultima altera��o: 15/01/2004
#    Altera��o No.: 003
#
# Fun��o:
#    Ocorr�ncias - Fun��es para configura��es

# fun��o de busca 
function buscaOcorrenciasComentarios($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[OcorrenciasComentarios] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[OcorrenciasComentarios] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[OcorrenciasComentarios] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[OcorrenciasComentarios] WHERE $texto ORDER BY $ordem";
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



# Fun��o para grava��o em banco de dados
function dbOcorrenciaComentario($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[OcorrenciasComentarios] VALUES (0,
			'$matriz[idOcorrencia]',
			'$matriz[idUsuario]',
			'$matriz[status]',
			'$data[dataBanco]',
			'$matriz[descricao]'
		)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[OcorrenciasComentarios] WHERE id=$matriz[id]";
	}
	
	elseif($tipo=='excluirocorrencia') {
		$sql="DELETE FROM $tb[OcorrenciasComentarios] WHERE idOcorrencia=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[OcorrenciasComentarios] 
			SET 
				status='$matriz[status]',
				texto='$matriz[descricao]'
		WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados


# Funcao para exclus�o
function comentarOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
		$msg="Ocorr�ncia n�o informada!";
		avisoNOURL("Aviso", $msg, 400);
		
		# Listar 
		echo "<br>";
		procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntComentar] || !$matriz[descricao]) {
	
		# Buscar Valores
		$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$id=resultadoSQL($consulta, 0, 'id');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$status=resultadoSQL($consulta, 0, 'status');
			$idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
			$data=resultadoSQL($consulta, 0, 'data');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			 
			verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
			echo "<br>";
			
			# Visualizar Ocorr�ncia
			verOcorrencia($modulo, $sub, 'ver', $id, $matriz);
			echo "<br>";
					
			# Motrar tabela de busca
			novaTabela2("[Comentar Ocorr�ncia]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $id);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[status] value=$status>
					<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Coment�rios: </b><br>
						<span class=normal10>Coment�rios de abertura da ocorr�ncia</span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] cols=60 rows=10></textarea>";
					itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntComentar] value=Comentar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();			

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Altera��o - bntAlterar pressionado
	elseif($matriz[bntComentar]) {
	
		$matriz[idOcorrencia]=$registro;
		$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
		
		$matriz[status]='P';
		$grava=dbOcorrenciaComentario($matriz,'incluir');
		
		# Verificar inclus�o de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Comentario adicionado com sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Listar prioridades
			echo "<br>";
			historicoOcorrencias($modulo, $sub, 'historico', $matriz[idOcorrencia], '');
		}
	} #fecha 
	
} # fecha funcao 




# Fun��o para procura de servi�o
function historicoOcorrenciasComentarios($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar, $sessCadastro;
	
	# Atribuir valores a vari�vel de busca
	if($textoProcurar) {
		$matriz[bntProcurar]=1;
		$matriz[txtProcurar]=$textoProcurar;
	} #fim da atribuicao de variaveis
	
	/*
	# Motrar tabela de busca
	novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b>Procurar por:</b>";
			htmlFechaColuna();
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<input type=hidden name=registro value=$matriz[registro]>
			<input type=text name=matriz[txtProcurar] size=40 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	*/

	# Caso bot�o procurar seja pressionado
	if(($matriz[txtProcurar] && $matriz[bntProcurar] && $acao=='procurar') || $acao=='historico') {
		#buscar registros
		if($acao=='historico' || !$matriz[txtProcurar]) {
			$consulta=buscaOcorrenciasComentarios($registro, 'idOcorrencia', 'igual','data DESC');
		}
		elseif($matriz[txtProcurar]) {
			$consulta=buscaOcorrenciasComentarios("(upper(descricao) like '%$matriz[txtProcurar]%' OR upper(descricao) like '%$matriz[txtProcurar]%') and idOcorrencia=$registro",$campo, 'custom','data DESC');
		}

		novaTabela("[Resultados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

		# Opcoes Adicionais
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('65%', 'left', $corFundo, 5, 'tabfundo1');
				novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					menuOpcAdicional($modulo, $sub, 'historico', $registro);
				fechaTabela();
			htmlFechaColuna();
		fechaLinhaTabela();
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados ocorr�ncias cadastrados', 'left', $corFundo, 2, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			if($acao=='procurar') {
				itemTabelaNOURL('Ocorr�ncias encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 2, 'txtaviso');
			}

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Dados da Ocorr�ncia', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Descri��o', 'center', '70%', 'tabfundo0');
			fechaLinhaTabela();

			for($i=0;$i<contaConsulta($consulta);$i++) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idOcorrencia=resultadoSQL($consulta, $i, 'idOcorrencia');
				$idUsuario=resultadoSQL($consulta, $i, 'idUsuario');
				$status=resultadoSQL($consulta, $i, 'status');
				$data=resultadoSQL($consulta, $i, 'data');
				$descricao=resultadoSQL($consulta, $i, 'texto');				
				
				novaLinhaTabela($corFundo, '100%');
					$texto="<b>Autor: </b>";
					$texto.=checaUsuario($idUsuario);
					$texto.="<br><b>Data:</b> ";
					$texto.=converteData($data,'banco','form');
					$texto.="<br><b>Status: </b> ";
					$texto.=formSelectStatusOcorrencia($status,'','check');
					
					itemLinhaTabela($texto, 'left', '30%', 'normal10');
					itemLinhaTabela(nl2br($descricao), 'left', '70%', 'tabfundo3');
				fechaLinhaTabela();
				
			} #fecha laco de montagem de tabela
		} #fecha listagem
		
		fechaTabela();
	} # fecha bot�o procurar
} #fecha funcao de  procurar 


?>
