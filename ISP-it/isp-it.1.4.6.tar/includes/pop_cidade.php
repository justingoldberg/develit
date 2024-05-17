<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 19/08/2003
# Ultima alteração: 16/03/2004
#    Alteração No.: 002
#
# Função:
#    Painel - Funções para cadastro de parametros de servicos 



# Função de banco de dados - Pessoas
function dbPOPCidade($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[POPCidade] VALUES (
			$matriz[pop],
			'$matriz[cidade]',
			'$matriz[principal]'
		)";
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM 
					$tb[POPCidade] 
				WHERE 
					idPOP=$matriz[pop] 
					AND idCidade=$matriz[cidade]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


# função de busca 
function buscaPOPCidade($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[POPCidade] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[POPCidade] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[POPCidade] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[POPCidade] WHERE $texto ORDER BY $ordem";
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
function cidadesPOP($modulo, $sub, $acao, $registro, $matriz) {

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
	
		verPOP($modulo, $sub, $acao, $registro, $matriz);	
		itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
	
		listarPOPCidade($modulo, $sub, $acao, $registro, $matriz);
	}
	
}#fecha função de listagem



function listarPOPCidade($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Seleção de registros
	$consulta=buscaPOPCidade($registro, 'idPOP','igual','idPOP');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Cidades]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cidadesadicionar&registro=$registro>Adicionar</a>",'incluir');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 4, 'tabfundo1');
	
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há cidades cadastradas para este POP", 'left', $corFundo, 4, 'txtaviso');
	}
	else {


		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há cidades cadastradas para este POP', 'left', $corFundo, 4, 'txtaviso');
		}
		else {

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Cidade', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('UF', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Principal', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
			fechaLinhaTabela();

			$i=0;
			
			while($i < contaConsulta($consulta)) {
				# Mostrar registro
				$idCidade=resultadoSQL($consulta, $i, 'idCidade');
				$principal=resultadoSQL($consulta, 0, 'principal');
				
				# Buscar parametro
				$consultaCidade=buscaCidades($idCidade, 'id','igual','id');
				$nome=resultadoSQL($consultaCidade, 0, 'nome');
				$uf=resultadoSQL($consultaCidade, 0, 'uf');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".excluir."&registro=$registro:$idCidade>Excluir</a>",'excluir');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'center', '60%', 'normal10');
					itemLinhaTabela($uf, 'center', '10%', 'normal10');
					itemLinhaTabela(formSelectSimNao($principal,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
			
			fechaTabela();
		} #fecha servicos encontrados
	} #fecha listagem
}



# Funcao para cadastro de servicos
function adicionarPOPCidade($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntConfirmar]) {

		# Seleção de registros
		$consulta=buscaPOP($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('POP não foi encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informações sobre Servidor
			verPOP($modulo, $sub, $acao, $registro, $matriz);
			echo "<br>";
			
	
			# Motrar tabela de busca
			novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[pop] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				# Demais campos do endereço
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>UF:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar>";
					itemLinhaForm(formSelectUF($matriz[uf], 'uf','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if($matriz[uf]) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Cidade: </b><br>
							<span class=normal10>Selecione o cidade para este POP</span>";
						htmlFechaColuna();
						itemLinhaForm(formSelectPOPCidade($registro, $matriz[cidade], $matriz[uf], 'cidade', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Cidade principal: </b><br>
							<span class=normal10>Selecione se cidade é a principal cidade do POP</span>";
						htmlFechaColuna();
						itemLinhaForm(formSelectSimNao($matriz[principal], 'principal', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntConfirmar] value=Adicionar>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				
			fechaTabela();
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[cidade] && $matriz[pop]) {
			# Cadastrar em banco de dados
			$grava=dbPOPCidade($matriz, 'incluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# OK - listar 
				
				verPOP($modulo, $sub, $acao, $registro, $matriz);
				echo "<br>";
				listarPOPCidade($modulo, $sub, 'cidades', $matriz[pop], $matriz);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de inclusao de servicos




# Funcao para cadastro de servicos
function excluirPOPCidade($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Quebrar registro
	$matRegistro=explode(":", $registro);
	
	$matriz[pop]=$matRegistro[0];
	$matriz[cidade]=$matRegistro[1];
	
	# Form de inclusao
	if(!$matriz[bntConfirmar]) {

		# Seleção de registros
		$consulta=buscaPOPCidade("idPOP=$matriz[pop] AND idCidade=$matriz[cidade]", '', 'custom', 'idPOP');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Cidade não encontrada!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informações sobre Servidor
			verPOP($modulo, $sub, $acao, $matriz[pop], $matriz);
			
			echo "<br>";
			
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $matriz[servico]);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[pop] value=$matriz[pop]>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Cidade: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectPOPCidade($matriz[pop], $matriz[cidade], '', '', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntConfirmar] value=Excluir>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[pop] && $matriz[cidade]) {
			# Cadastrar em banco de dados
			$grava=dbPOPCidade($matriz, 'excluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# OK - Listar
				verPOP($modulo, $sub, $acao, $registro, $matriz);
				echo "<br>";
				listarPOPCidade($modulo, $sub, 'cidades', $matriz[pop], $matriz);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao excluir parâmetro!";
				$url="?modulo=$modulo&sub=$sub&acao=parametros&registro=$matriz[pop]";
				aviso("Aviso", $msg, $url, 760);
			}
			
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de excluir



# Função para montar campo de formulario
function formSelectPOPCidade($pop, $cidade, $uf, $campo, $tipo) {

	global $conn, $tb;
	
	if($tipo=='form') {
		# Buscar Serviços de servidor (ja cadastrados)
		$tmpConsulta=buscaPOPCidade($pop, 'idPOP','igual','idPOP');
		
		$consulta=buscaCidades("uf='$uf'", '', 'custom', 'nome');
		
		$item="<select name=matriz[$campo] onChange=form.submit();>\n";
		
		# Listargem
		for($i=0;$i<contaConsulta($consulta);$i++) {
			# Zerar flag de registro já cadastrado
			$flag=0;
			
			# Valores dos campos
			$id=resultadoSQL($consulta, $i, 'id');
			$nome=resultadoSQL($consulta, $i, 'nome');
			$uf=resultadoSQL($consulta, $i, 'uf');
	
			# Verificar se serviço já está cadastrado
			for($x=0;$x<contaConsulta($tmpConsulta);$x++) {
				# Verificar
				$idTmp=resultadoSQL($tmpConsulta, $x, 'idCidade');
				
				if($idTmp == $id) {
					# Setar Flag de registro já cadastrado
					$flag=1;
					break;
				}
			}
	
			if(!$flag || $flag==0) {
				# Mostrar serviço		
				if($cidade==$id) $opcSelect='selected';
				else $opcSelect='';
				$item.= "<option value=$id $opcSelect>$nome/$uf";
				
				if($idUnidade)  $item.=" - $unidade";
			}
		}
		
		$item.="</select>";
		
		return($item);
		
	}
	elseif($tipo=='check') {
		# Selecionar Parametro
		$consulta=buscaCidades($cidade, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# Retornar nome do parametro
			$retorno=resultadoSQL($consulta, 0, 'nome');
			
		}
		
		
		return($retorno);
	}
	
	
} #fecha funcao de montagem de campo de form


# Função para busca de cidade principal do POP
function cidadePOPPessoaTipo($idPessoaTipo) {

	global $conn, $tb;
	
	$sql="
		SELECT
			Cidades.nome, 
			Cidades.uf, 
			Cidades.id 
		FROM
			Cidades, 
			PessoasTipos, 
			Pessoas, 
			Pop, 
			PopCidade 
		WHERE
			Pessoas.idPOP = Pop.id 
			AND Pop.id = PopCidade.idPOP 
			AND PopCidade.idCidade = Cidades.id 
			AND PopCidade.principal='S' 
			AND PessoasTipos.idPessoa=Pessoas.id 
			AND PessoasTipos.id=$idPessoaTipo
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[uf]=resultadoSQL($consulta, 0, 'uf');
	}
	
	return($retorno);
	
}


?>
