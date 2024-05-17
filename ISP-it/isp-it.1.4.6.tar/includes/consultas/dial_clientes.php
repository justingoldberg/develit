<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Consulta Constas Dial-UP por Cliente


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para parametros de consulta de Contas Dial-UP por Cliente
*/
function formDialCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Consulta Contas de Dial-UP por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
 * @desc Consulta de Contas Dial-Up por cliente
*/
function consultaDialCliente($modulo, $sub, $acao, $registro, $matriz) {

	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites;

	# SQL para consulta de emails por dominios do cliente informado
	$sql="
		SELECT 
			$tb[RadiusUsuarios].id id, 
			$tb[RadiusUsuarios].login login, 
			$tb[RadiusUsuarios].status status, 
			$tb[RadiusUsuariosPessoasTipos].idPessoasTipos idPessoaTipo, 
			$tb[RadiusUsuariosPessoasTipos].idServicosPlanos idServicoPlano,
			$tb[Servicos].nome nomeServico
		FROM 
			$tb[RadiusUsuarios], 
			$tb[RadiusUsuariosPessoasTipos],
			$tb[ServicosPlanos],
			$tb[Servicos]
		WHERE 
			$tb[RadiusUsuarios].id=$tb[RadiusUsuariosPessoasTipos].idRadiusUsuarios
			AND $tb[ServicosPlanos].id=$tb[RadiusUsuariosPessoasTipos].idServicosPlanos
			AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
			AND $tb[RadiusUsuariosPessoasTipos].idPessoasTipos = '$matriz[idPessoaTipo]'
		ORDER BY
			$tb[RadiusUsuarios].login
	";
	
	$consulta=consultaSQL($sql, $conn);
	
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
				itemTabelaNOURL('Não foram encontradas Contas Dial-UP cadastrados', 'left', $corFundo, 5, 'txtaviso');
			}
			elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
				# Cabeçalho
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('Conta', 'center', '20%', 'tabfundo0');
					itemLinhaTabela('Serviço', 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
				fechaLinhaTabela();
	
				//while($i < contaConsulta($consulta) && $i < $limite) {
				for($i=0;$i<contaConsulta($consulta);$i++) {
					# Mostrar registro
					$id=resultadoSQL($consulta, $i, 'id');
					$login=resultadoSQL($consulta, $i, 'login');
					$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
					$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
					$nomeServico=resultadoSQL($consulta, $i, 'nomeServico');
					$status=resultadoSQL($consulta, $i, 'status');
					
					if($status=='A') $class='txtok';
					elseif($status=='I') $class='txtaviso';
					elseif($status=='T') $class='txttrial';
					else $class='bold10';
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=alterar&registro=$idPessoaTipo:$id>Senha</a>",'senha');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=excluir&registro=$idPessoaTipo:$id>Excluir</a>",'excluir');
					if($status=='A') {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=inativar&registro=$idPessoaTipo:$id>Inativar</a>",'ativar');
					}
					if($status=='I' || $status=='T') {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=ativar&registro=$idPessoaTipo:$id>Ativar</a>",'desativar');
					}
					$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=extrato&registro=$idPessoaTipo:$id>Extrato</a>",'extrato');
					
					# Checar se há telefones configurados
					//$consultaTelefones=buscaRadiusTelefones($id, 'idRadiusUsuarioPessoaTipo','igual','id');
					if($consultaTelefones && contaConsulta($consultaTelefones)>0) {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=telefones&registro=$idPessoaTipo:$id><b>Telefones</b></a>",'fone');
					}
					else {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=telefones&registro=$idPessoaTipo:$id>Telefones</a>",'fone');
					}					
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($login, 'left', '20%', 'normal10');
						itemLinhaTabela($nomeServico, 'left', '30%', 'normal10');
						itemLinhaTabela(formSelectStatusRadius($status,'','check'), 'center', '10%', 'normal8');
						itemLinhaTabela($opcoes, 'left nowrap', '40%', 'normal8');
					fechaLinhaTabela();
					
				} #fecha laco de montagem de tabela
			} #fecha listagem
				
			fechaTabela();
		htmlFechaColuna();
	htmlFechaLinha();	
}


?>