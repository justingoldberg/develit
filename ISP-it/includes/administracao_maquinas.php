<?
################################################################################
#       Criado por: Leandro Barros Grandinetti - leandro@seumadruga.com.br
#  Data de criação: 29/05/2007
# Ultima alteração: 29/05/2007
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para controle de serviço de suporte
#

function administracaoMaquinas($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao['admin'] && !$permissao['abrir'] && !$permissao['visualizar']){
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	elseif($acao == 'config'){
		maquinasListarPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($acao == 'adicionar'){
		adicionarMaquinas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($acao == 'excluir'){
		excluirMaquinas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($acao == 'alterar'){
		alterarMaquinas($modulo, $sub, $acao, $registro, $matriz);
	}
}

function maquinasListarPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $limite, $html;
	
	
	$idPessoaTipo = $matriz['idPessoaTipo'];
	$idModulo = $matriz['idModulo'];
	
	if( $matriz['txtProcurar'] ){
		$sqlADD = " AND ( 
						$tb[Maquinas].nome like '%$matriz[txtProcurar]%'
					OR $tb[Maquinas].ip like '%$matriz[txtProcurar]%'
				  )";
	}

	if( $idPessoaTipo ){
//		$sql = "
//				SELECT
//					$tb[ServicosPlanos].id idServicosPlanos,
//					$tb[Maquinas].id id,
//					$tb[Maquinas].nome nome,
//					$tb[Maquinas].ip ip
//						
//				FROM
//					$tb[Maquinas],
//					$tb[ServicosPlanos],
//					$tb[PlanosPessoas]
//				WHERE
//				    $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id
//				AND $tb[PlanosPessoas].idPessoaTipo = $idPessoaTipo
//					$sqlADD
//		";
		$sql = "SELECT $tb[ServicosPlanos].id as idServicosPlanos, " .
				"$tb[Maquinas].id as id, " .
				"$tb[Maquinas].nome as nome, " .
				"$tb[Maquinas].ip as ip " .
				"FROM $tb[Maquinas] " .
				"INNER JOIN $tb[MaquinasSuporte] " .
				"ON ($tb[Maquinas].id = $tb[MaquinasSuporte].idMaquina) " .
				"INNER JOIN $tb[Suporte] " .
				"ON ($tb[MaquinasSuporte].idSuporte = $tb[Suporte].id) " .
				"INNER JOIN ServicosPlanos " .
				"ON ($tb[Suporte].idServicoPlano = $tb[ServicosPlanos].id)";
		
		$consulta = consultaSQL($sql, $conn);
		
		novaTabela("Máquinas", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 3, 'tabfundo1');
				novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
						$texto = "
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=config>
						<input type=hidden name=registro value=$idPessoaTipo>
						<b>Procurar por:</b> <input type=text name=matriz[txtProcurar] size=25 value='$matriz[txtProcurar]'>
						<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
						itemLinhaForm($texto, 'center','middle', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			htmlFechaColuna();
			fechaLinhaTabela();
		
			# Verificar Maquinas configuradas para mostrar ou nao o link de "adicionar"
			$total = maquinasTotal($idPessoaTipo);
			$totalEmUso = maquinasTotalEmUso($idPessoaTipo);

			if( $total > $totalEmUso ){
				$opcoes = htmlMontaOpcao("<a href=?modulo=administracao&sub=maquinas&acao=adicionar&registro=$idPessoaTipo>Adicionar</a>",'incluir');
				itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
			}
			if( $consulta && contaConsulta($consulta)>0 ){
			
				$matriz['registro'] = $idPessoaTipo;
				paginador2($consulta, contaConsulta($consulta), $limite['lista']['maquinas'], $matriz, 'normal8', 3, "");
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("Máquina", 'center', '50%', 'tabfundo0');
					itemLinhaTabela('IP', 'center', '20%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
				fechaLinhaTabela();
				
					
				# Setar registro inicial
				if( !$matriz['pagina'] ){
					$i = 0;
				}
				elseif( $matriz['pagina'] && is_numeric($matriz['pagina']) ){
					$i = $matriz['pagina'];
				}
				else{
					$$i = 0;
				}
				
				$limite = $i+$limite['lista']['maquinas'];
				
				for( $a=$i;$a<contaConsulta($consulta) && $a < $limite;$a++){
				
					$id = resultadoSQL($consulta, $a, 'id');
					$idServicosPlanos = resultadoSQL($consulta, $a, 'idServicosPlanos');
					$nome = resultadoSQL($consulta, $a, 'nome');
					$ip = resultadoSQL($consulta, $a, 'ip');
															
					$opcoes = htmlMontaOpcao("<a href=?modulo=administracao&sub=maquinas&acao=alterar&registro=$idPessoaTipo:$id>Alterar</a>",'alterar');
					$opcoes .= htmlMontaOpcao("<a href=?modulo=administracao&sub=maquinas&acao=excluir&registro=$idPessoaTipo:$id>Excluir</a>",'excluir');
									
					#visualizar servico/plano.
					$opcoes .= "<br>";
					$opcoes .= htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>Visualizar Plano</a>", 'planos');
					
					novaLinhaTabela($corFundo, '100%');
						$maquina = "<img src=".$html[imagem][maquinas]." border=0 align=left>$nome";
						itemLinhaTMNOURL($maquina, 'left','middle','40%',$corFundo,0,'normal10');
						itemLinhaTMNOURL($ip, 'center','middle','15%',$corFundo,0,'normal8');
						itemLinhaTMNOURL($opcoes, 'left','middle','45%',$corFundo,0,'normal8');
					fechaLinhaTabela();
				}
			}
			else {
				$texto="<span class=txtaviso>Não existem máquinas cadastradas!</span>";
				itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
			}
		fechaTabela();
	}
}

# Funcao para cadastro de maquinas
function adicionarMaquinas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Form de inclusao
	if( !$matriz['bntAdicionar'] || !$matriz['nome'] ){
		# Motrar tabela de busca
		novaTabela2("[Adicionar Máquina]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
			$texto = "			
					  <form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>
						<input type=hidden name=matriz[idPessoasTipos] value=$registro>
						";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Host: </b><br>
					<span class=normal10>Nome da máquina</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=35 value='$matriz[nome]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>IP: </b><br>
					<span class=normal10>Endereço IP</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[ip] size=35 value='$matriz[ip]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Serviço: </b><br>
					<span class=normal10>Serviço a atribuir para esta máquina</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectServicoPlanoMaquina($matriz['idPessoaTipo'], $matriz['idServicosPlanos'], 'idServicosPlanos','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Observa&ccedil;&atilde;o: </b><br>
					<span class=normal10>Detalhes adicionais sobre a m&aacute;quina</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[observacao] rows=3 cols=35>$matriz[observacao]</textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif( $matriz['bntAdicionar'] ){
		# Conferir campos
		if( $matriz['nome'] ) {
			# Cadastrar em banco de dados
			$matriz['padrao'] = 'N';
			$grava = dbMaquina( $matriz, 'incluir' );
			
			# Verificar inclusão de registro
			if( $grava ) {
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "<br>";
				$matriz['idPessoaTipo'] = $matriz['idPessoasTipos'];
				maquinasListarPessoas($modulo, $sub, 'listar', $registro, $matriz);
			}
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			avisoNOURL("Aviso: Ocorrência de erro.", $msg, $url, 400);
		}
	}
}

function excluirMaquinas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb, $sessLogin;
	
	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if( !$permissao['admin'] && !$permissao['adicionar'] ){
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}	
	else{	
		if( !$matriz['bntExcluir'] ){
			# Procurar registro
			$sql = "
					SELECT
						$tb[Maquinas].id idMaquina,
						$tb[Maquinas].nome nome,
						$tb[Maquinas].observacao observacao,
						$tb[Maquinas].idServicoPlano idServicosPlanos,
						$tb[ServicosPlanos].idPlano idPlano,
						$tb[Servicos].nome nomeServico,
						$tb[PlanosPessoas].idPessoaTipo idPessoasTipos
					FROM 
						$tb[Maquinas],
						$tb[ServicosPlanos],
						$tb[Servicos],
						$tb[PlanosPessoas]
					WHERE
						$tb[Maquinas].idServicoPlano=$tb[ServicosPlanos].id
					AND $tb[Servicos].id=$tb[ServicosPlanos].idServico
					AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id
					AND $tb[Maquinas].id=$matriz[id]
				";
			
			$consulta = consultaSQL($sql, $conn);
			
			# Form de exclusao
			if( $consulta && contaConsulta($consulta)>0 ){
			
				$nome 		  = resultadoSQL($consulta, 0, 'nome');
				$observacao       = resultadoSQL($consulta, 0, 'observacao');
				$idMaquina        = resultadoSQL($consulta, 0, 'idMaquina');
				$idPessoasTipos   = resultadoSQL($consulta, 0, 'idPessoasTipos');
				$nomeServico 	  = resultadoSQL($consulta, 0, 'nomeServico');
				$idPlano 		  = resultadoSQL($consulta, 0, 'idPlano');
				$idServicosPlanos = resultadoSQL($consulta, 0, 'idServicosPlanos');
				# Montar URL para acesso ao serviço
				$nomeServico = "<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>
					<img src=".$html[imagem][planos]." align=right border=0 alt='Visualizar Serviço '></a>$nomeServico";
			
				novaTabela2("[Excluir Máquina]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
					novaLinhaTabela($corFundo, '100%');
						$texto = "			
							<form method=post name=excluirmaquinas action=index.php>
							<input type=hidden name=modulo value=$modulo>
							<input type=hidden name=sub value=$sub>
							<input type=hidden name=acao value=$acao>
							<input type=hidden name=registro value=$registro:$idDominio>
							<input type=hidden name=matriz[idMaquina] value=$idMaquina>
							<input type=hidden name=matriz[idPessoasTipos] value=$registro>
							";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Host: </b>";
						htmlFechaColuna();
						itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Observações: </b>";
						htmlFechaColuna();
						itemLinhaForm($observacao, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Serviço: </b>";
						htmlFechaColuna();
						itemLinhaForm($nomeServico, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
									
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit></form>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			}
		} #fecha form
		elseif( $matriz['bntExcluir'] ){
			# Excluir maquina
			$grava = dbMaquina($matriz, 'excluir');
			
			if( $grava ){
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo "<br>";
				$matriz['idPessoaTipo'] = $matriz['idPessoasTipos'];
				maquinasListarPessoas($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				$msg="Ocorreu um erro na tentativa de excluir o registro!";
				avisoNOURL("Aviso", $msg, 400);
			}
		}
	}
		
}

function alterarMaquinas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin['login'],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}	
	else{	
		if( !$matriz['bntAlterar'] ) {
			# Procurar registro
			$sql="
				SELECT
					$tb[Maquinas].id idMaquina,
					$tb[Maquinas].idSuporte idSuporte,
					$tb[Maquinas].ip ip,
					$tb[Maquinas].nome nome,
					$tb[Maquinas].observacao observacao,
					$tb[Maquinas].idServicoPlano idServicosPlanos,
					$tb[ServicosPlanos].idPlano idPlano,
					$tb[Servicos].nome nomeServico,
					$tb[PlanosPessoas].idPessoaTipo idPessoasTipos
				FROM 
					$tb[Maquinas],
					$tb[ServicosPlanos],
					$tb[Servicos],
					$tb[PlanosPessoas]
				WHERE
					$tb[Maquinas].idServicoPlano=$tb[ServicosPlanos].id
					AND $tb[Servicos].id=$tb[ServicosPlanos].idServico
					AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id
					AND $tb[Maquinas].id=$matriz[id]
			";
			
			$consulta = consultaSQL($sql, $conn);
			
			# Form de alteração
			if( $consulta && contaConsulta($consulta)>0 ){
			
				$nome   	  = resultadoSQL($consulta, 0, 'nome');
				$ip 			  = resultadoSQL($consulta, 0, 'ip');
				$observacao 	  = resultadoSQL($consulta, 0, 'observacao');
				$idMaquina  	  = resultadoSQL($consulta, 0, 'idMaquina');
				$idPessoasTipos   = resultadoSQL($consulta, 0, 'idPessoasTipos');
				$nomeServico	  = resultadoSQL($consulta, 0, 'nomeServico');
				$idPlano		  = resultadoSQL($consulta, 0, 'idPlano');
				$idSuporte		  = resultadoSQL($consulta, 0, 'idSuporte');
				$idServicosPlanos = resultadoSQL($consulta, 0, 'idServicosPlanos');
				# Montar URL para acesso ao serviço
				$nomeServico = "<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=$idServicosPlanos>
								<img src=".$html[imagem][planos]." align=right border=0 alt='Visualizar Serviço '></a>$nomeServico";
			
				novaTabela2("[Alterar Máquinas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro:$idMaquina>
						<input type=hidden name=matriz[idMaquina] value=$idMaquina>
						<input type=hidden name=matriz[idPessoasTipos] value=$registro>
						<input type=hidden name=matriz[idSuporte] value=$idSuporte>
						";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Host: </b>";
						htmlFechaColuna();
						$texto="<input name=matriz[nome] value='$nome' size=35>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>IP: </b>";
						htmlFechaColuna();
						$texto="<input name=matriz[ip] value='$ip' size=35>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Observações: </b>";
						htmlFechaColuna();
						$texto="<textarea name=matriz[observacao] rows=3 cols=35>$observacao</textarea>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Serviço: </b>";
						htmlFechaColuna();
						itemLinhaForm($nomeServico, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			}
		} #fecha form
		elseif( $matriz['bntAlterar'] ){

			$grava = dbMaquina($matriz, 'alterar');

			if( $grava ){
				# acusar falta de parametros
				# Mensagem de aviso
				$msg = "Registro Alterado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				$matriz['idPessoaTipo'] = $matriz['idPessoasTipos'];
				maquinasListarPessoas($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Apagar Dominio gravado
				$msg="Ocorreu um erro na tentativa de alterar o registro!";
				avisoNOURL("Aviso", $msg, 400);
			}
		}
		else {
			# Dominio nao encontrado
			$msg="Máquina não encontrada!";
			avisoNOURL("Aviso", $msg, 400);
		}
	}
		
}


?>