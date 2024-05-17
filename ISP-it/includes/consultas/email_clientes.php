<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Consultas de Email por Cliente

/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para parametros de consulta de Emails por Cliente
*/
function formConsultaEmailCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Consulta Contas de E-mail por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
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
			itemLinhaTMNOURL('<b>Busca por Cliente:</b><br>
			<span class=normal10>Informe nome ou dados do cliente para busca</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[txtProcurar] size=60 value='$matriz[txtProcurar]'> <input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if($matriz[txtProcurar]) {
			# Procurar Cliente
			$tipoPessoa=checkTipoPessoa('cli');
			$consulta=buscaPessoas("
				((upper(nome) like '%$matriz[txtProcurar]%' 
					OR upper(razao) like '%$matriz[txtProcurar]%' 
					OR upper(site) like '%$matriz[txtProcurar]%' 
					OR upper(mail) like '%$matriz[txtProcurar]%')) 
				AND idTipo=$tipoPessoa[id]", $campo, 'custom','nome');
			
			if($consulta && contaConsulta($consulta)>0) {
				# Selecionar cliente
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Clientes encontrados:</b><br>
					<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipo', $matriz[idPessoaTipo]), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
			}
		}
	
		htmlFechaLinha();
	fechaTabela();
	
}



/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int $registro
 * @param array $matriz
 * @desc Consulta de Emails por cliente
*/
function consultaEmailsCliente($modulo, $sub, $acao, $registro, $matriz) {

	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites;

	# SQL para consulta de emails por dominios do cliente informado
	$consulta=buscaEmails($matriz[idPessoaTipo],'idPessoaTipo','igual','idDominio, login');
	
	# Cabeçalho
	itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
	# Mostrar Cliente
	htmlAbreLinha($corFundo);
		htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
			novaTabela("[Resultados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
			# Opcoes Adicionais
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 5, 'tabfundo1');
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						menuOpcAdicional('lancamentos', 'planos', 'listar', $matriz[idPessoaTipo]);
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
			
			if(!$consulta || contaConsulta($consulta)==0 ) {
				# Não há registros
				itemTabelaNOURL('Não foram encontradas Contas de Email cadastrados', 'left', $corFundo, 5, 'txtaviso');
			}
			elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
				# Cabeçalho
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('E-mail', 'center', '20%', 'tabfundo0');
					itemLinhaTabela('Domínio', 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
				fechaLinhaTabela();
	
				/*# Setar registro inicial
				if(!$registro) {
					$i=0;
				}
				elseif($registro && is_numeric($registro) ) {
					$i=$registro;
				}
				else {
					$i=0;
				}*/
	
				//$limite=$i+$limite[lista][ocorrencias];
				
				//while($i < contaConsulta($consulta) && $i < $limite) {
				for($i=0;$i<contaConsulta($consulta);$i++) {
					# Mostrar registro
					$id=resultadoSQL($consulta, $i, 'id');
					$login=resultadoSQL($consulta, $i, 'login');
					$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
					$idDominio=resultadoSQL($consulta, $i, 'idDominio');
					$status=resultadoSQL($consulta, $i, 'status');
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=alterar&registro=$idPessoaTipo:$id>Senha</a>",'senha');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=excluir&registro=$idPessoaTipo:$id>Excluir</a>",'excluir');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=forward&registro=$idPessoaTipo:$id>Forward</a>",'forward');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=autoreply&registro=$idPessoaTipo:$id>AutoReply</a>",'autoresposta');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=emailconfig&registro=$idPessoaTipo:$id>Config</a>",'config');
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($login, 'left', '20%', 'normal10');
						itemLinhaTabela(formSelectDominioEmail($idDominio, '','check'), 'left', '30%', 'normal10');
						itemLinhaTabela(formSelectStatusEmails($status,'','check'), 'center', '10%', 'normal8');
						itemLinhaTabela($opcoes, 'left nowrap', '40%', 'normal8');
					fechaLinhaTabela();
					
				} #fecha laco de montagem de tabela
			} #fecha listagem
				
			fechaTabela();
		htmlFechaColuna();
	htmlFechaLinha();	
}


?>