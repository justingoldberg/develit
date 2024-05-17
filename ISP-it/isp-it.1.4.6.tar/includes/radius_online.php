<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 06/07/2004
# Ultima alteração: 06/07/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para interação online com radius
#


# função para listargem de usuários conectados
function radiusOnlineListar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $conn, $html, $corFundo, $corBorda, $limite;
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Seleção de registros
		$consulta=radiusBuscaUsuariosOnline('');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][usuarios], $registro, 'normal', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Login', 'center', '40%', 'tabfundo0');
				itemLinhaTabela('Conexão', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('IP', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Telefone', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '20%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][usuarios];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$login=resultadoSQL($consulta, $i, 'login');
				$dtLogin=resultadoSQL($consulta, $i, 'inicio');
				$telefone=resultadoSQL($consulta, $i, 'telefone');
				$ip=resultadoSQL($consulta, $i, 'ip');
				
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desconectar&registro=$login>Desconectar</a>",'cancelar');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($login, 'left', '40%', 'normal');
					itemLinhaTabela(converteData($dtLogin,'banco','form'), 'center', '20%', 'normal');
					itemLinhaTabela($ip, 'center', '10%', 'normal');
					itemLinhaTabela(formatarFone($telefone), 'center', '10%', 'normal');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
}



# Função para exclusão de conta de radius relacionado do serviço
function radiusOnlineDesconectar($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda;
	
	$registro=stripslashes($registro);

	// Buscar conta
	$consulta=radiusBuscaUsuariosOnline($registro);
	
	if($consulta && contaConsulta($consulta)>0) {
		
		# Form de inclusao
		if(!$matriz[bntDesconectar]) {
			# receber valores
			$login=resultadoSQL($consulta, $i, 'login');
			$dtLogin=resultadoSQL($consulta, $i, 'inicio');
			$telefone=resultadoSQL($consulta, $i, 'telefone');
			$ip=resultadoSQL($consulta, $i, 'ip');
			
			# Motrar tabela de busca
			novaTabela2("[Desconectar Usuário]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');

				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value='$registro'>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b></span>";
					htmlFechaColuna();					
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data de Conexão: </b></span>";
					htmlFechaColuna();					
					itemLinhaForm(converteData($dtLogin,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Telefone: </b></span>";
					htmlFechaColuna();					
					itemLinhaForm(formatarFone($telefone), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp','center',$corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntDesconectar] value=Desconectar class=submit2>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		elseif($matriz[bntDesconectar]) {
			
			# Mensagem de aviso
			$msg="Usuários desconectado com sucesso!";
			avisoNOURL("Aviso", $msg, 760);
			
			echo "<br>";
			
			// Fechar LOG
			radiusOnlineDesconectarServidor($registro);
			
			radiusOnlineListar($modulo, $sub, '', '', $matriz);
			
		}
		
	}
	else {
		# Mensagem de aviso
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=listar";
		aviso("Aviso", $msg, $url, 760);
	}
}


# Função para desconexão de usuário
function radiusOnlineDesconectarServidor($conta) {

	global $radius;
	
	$connRadius=conectaRadius();
	
	$parametros=carregaParametrosConfig();
	
	# Verificação de login para quebrar o "@dominio"
	if(strstr($conta, "@")) {
		$tmpConta=explode("@",$conta);
		$opcSQL="WHERE (UserName='$conta' OR UserName='$tmpConta[0]') AND AcctStopTime='' ";
	}
	else {
		$opcSQL="WHERE UserName='$conta' AND AcctStopTime=''";
	}
	
	if(!$parametros[radiuslogoff] || $parametros[radiuslogoff] == 'delete') {
		# Buscar usuario no banco de dados
		$sql="DELETE FROM $radius[db].radacct $opcSQL";
	}
	elseif($parametros[radiuslogoff] == 'update') {
		# fechar accouting com horário atual
		$sql="DELETE FROM $radius[db].radacct $opcSQL";
	}
	
	if($sql) $consulta=consultaSQL($sql, $connRadius);
	
}

?>