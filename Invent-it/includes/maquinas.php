<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/01/2004
# Ultima alteração: 12/01/2005
#    Alteração No.: 004
#
# Função:
#    Painel - Funções para cadastro

# Função para cadastro
function maquinas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPassPhrase;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');


	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Cadastro de Máquinas]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>Máquinas</b>
					<br><span class=normal10>Cadastro de <b>máquinas</b> e geração de inventário de software.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=maquina&sub=&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=maquina&sub=&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=maquina&sub=&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
	
		if(!$sub) {
			# Inclusão
			if($acao=="adicionar") {
				echo "<br>";
				adicionarMaquinas($modulo, $sub, $acao, $registro, $matriz);
			}
			
			# Alteração
			elseif($acao=="alterar") {
				echo "<br>";
				alterarMaquinas($modulo, $sub, $acao, $registro, $matriz);
			}
			
			# Exclusão
			elseif($acao=="excluir") {
				echo "<br>";
				excluirMaquinas($modulo, $sub, $acao, $registro, $matriz);
			}
		
			# Listar / Buscar
			elseif($acao=="listar" || $acao=='procurar' || !$acao) {
				echo "<br>";
				procurarMaquinas($modulo, $sub, $acao, $registro, $matriz);
			} #fecha listagem de servicos
			
			# Detalhes
			elseif($acao=='detalhes') {
				echo "<br>";
				verMaquina($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='programas') {
			if($acao=='listar') listarProgramas($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='adicionar') adicionarProgramas($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='excluir') excluirProgramas($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='alterar') alterarProgramas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($sub=='usuarios') {
			# Validar permissões e passphrase
			if(passphrase($modulo, $sub, $acao, $registro, $sessPassPhrase)) {
				if($acao=='listar') listarUsers($modulo, $sub, $acao, $registro, $matriz);
				elseif($acao=='adicionar') adicionarUsers($modulo, $sub, $acao, $registro, $matriz);
				elseif($acao=='excluir') excluirUsers($modulo, $sub, $acao, $registro, $matriz);
				elseif($acao=='alterar') alterarUsers($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='tickets') {
			if($acao=='listar' || $acao=='procurar') procurarTickets($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='adicionar') adicionarTickets($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='excluir') excluirTickets($modulo, $sub, $acao, $registro, $matriz);
			elseif($acao=='alterar') alterarTickets($modulo, $sub, $acao, $registro, $matriz);
		}
	}
} #fecha menu principal 


# função de busca 
function buscaMaquinas($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Maquinas] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Maquinas] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Maquinas] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Maquinas] WHERE $texto ORDER BY $ordem";
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




# Funcao para cadastro 
function adicionarMaquinas($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Form de inclusao
	if(!$matriz[bntAdicionar] || (!$matriz[nome] || !$matriz[ip] || !$matriz[idEmpresa])) {
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
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>&nbsp;
				";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Nome: </b><br>
					<span class=normal10>Nome da máquina</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60 value='$matriz[nome]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<b class=bold10>IP: </b><br>
				<span class=normal10>Endereço IP</span>";
				itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[ip] size=15 value='$matriz[ip]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<b class=bold10>Cliente: </b><br><span class=normal10>Cliente/proprietário da máquina</span>";
				itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
				$texto=formSelectEmpresas($matriz[idEmpresa],'idEmpresa','form');
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<b class=bold10>Observações: </b><br><span class=normal10>Comentários sobre a máquina</span>";
				itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
				$texto="<textarea name=matriz[obs] cols=60 rows=6>$matriz[obs]</textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[nome] && $matriz[ip] && $matriz[idEmpresa]) {
			# Buscar por prioridade
			# Cadastrar em banco de dados
			$matriz[id]=buscaIDNovoMaquina();
			$matriz[cliente]=formSelectEmpresas($matriz[idEmpresa], '', 'check');
			$grava=dbMaquina($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				avisoNOURL("Aviso", $msg, 400);
				
				//procurarMaquinas($modulo, $sub, 'listar', $registro, $matriz);
				listarProgramas($modulo, 'programas', 'listar', $matriz[id], '');
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}

} # fecha funcao de inclusao




# Função para gravação em banco de dados
function dbMaquina($matriz, $tipo)
{

	$data=dataSistema();
	
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Maquinas] (id, nome, ip, cliente, idEmpresa, data, obs) VALUES (" .
		"$matriz[id],
		'$matriz[nome]',
		'$matriz[ip]',
		'$matriz[cliente]',
		'$matriz[idEmpresa]',
		'$data[dataBanco]',
		'$matriz[obs]'
		)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaMaquinas($matriz[id], 'id', 'igual', 'id');
		
		# Registro já existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[Maquinas] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[Maquinas] 
			SET 
				nome='$matriz[nome]', 
				ip='$matriz[ip]',
				cliente='$matriz[cliente]',
				obs='$matriz[obs]',
				idEmpresa='$matriz[idEmpresa]'
			WHERE 
				id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Função para procura de serviço
function procurarMaquinas($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a variável de busca
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
			<input type=hidden name=nulo value=nulo>
			<input type=text name=matriz[txtProcurar] size=40 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>
			";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if( ($matriz[txtProcurar] && $matriz[bntProcurar]) || $acao=='listar') {
		#buscar registros
		if($acao=='listar' || $acao=='procurar') {
			
			if($acao=='procurar' || $matriz[txtProcurar]) {
				$sqlADD="WHERE ( 
					$tb[Maquinas].nome LIKE '%$matriz[txtProcurar]%'
					OR $tb[Maquinas].cliente LIKE '%$matriz[txtProcurar]%'
					OR $tb[Maquinas].obs LIKE '%$matriz[txtProcurar]%'
					OR $tb[Maquinas].ip LIKE '%$matriz[txtProcurar]%'
					OR $tb[Empresas].nome LIKE '%$matriz[txtProcurar]%'
				)";
			}
			
			$sql="
				SELECT 
					$tb[Maquinas].id,
					$tb[Maquinas].nome,
					$tb[Maquinas].ip,
					$tb[Empresas].nome empresa,
					$tb[Maquinas].cliente
				FROM
					$tb[Maquinas] LEFT JOIN $tb[Empresas] 
						ON $tb[Maquinas].idEmpresa = $tb[Empresas].id
				$sqlADD
				ORDER BY
					$tb[Maquinas].data DESC
			";
		}
		else {
		}
		
		$consulta=consultaSQL($sql, $conn);

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			if($matriz[txtProcurar]) itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][maquinas], $registro, 'normal10', 4, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('IP', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Cliente', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '50%', 'tabfundo0');
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

			$limite=$i+$limite[lista][maquinas];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$empresa=resultadoSQL($consulta, $i, 'empresa');
				if(!$empresa) $cliente=resultadoSQL($consulta, $i, 'cliente');
				else $cliente=$empresa;
				$ip=resultadoSQL($consulta, $i, 'ip');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=listar&registro=$id>Programas</a>",'cadastros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=listar&registro=$id>Usuários</a>",'chave');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=tickets&acao=listar&registro=$id>Tickets</a>",'ticket');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '20%', 'normal10');
					itemLinhaTabela($ip, 'center', '10%', 'normal10');
					itemLinhaTabela($cliente, 'left', '20%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '50%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
} #fecha funcao de  procurar 



# Funcao para alteração
function alterarMaquinas($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif(!$matriz[bntAlterar] || (!$matriz[nome] || !$matriz[ip] || !$matriz[idEmpresa])) {
	
		# Buscar Valores
		$consulta=buscaMaquinas($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			if(!$matriz[bntAlterar]) {
				$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
				$matriz[ip]=resultadoSQL($consulta, 0, 'ip');
				$matriz[cliente]=resultadoSQL($consulta, 0, 'cliente');
				$matriz[obs]=resultadoSQL($consulta, 0, 'obs');
				$matriz[idEmpresa]=resultadoSQL($consulta, 0, 'idEmpresa');
			}
			
			# Motrar tabela de busca
			novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>&nbsp;
					";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b><br>
						<span class=normal10>Nome da máquina</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$matriz[nome]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>IP: </b><br>
					<span class=normal10>Endereço IP</span>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[ip] size=15 value='$matriz[ip]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Cliente: </b><br><span class=normal10>Cliente/proprietário da máquina</span>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					if(!$empresa) $texto=$matriz[cliente] . "<br>" . formSelectEmpresas($matriz[idEmpresa],'idEmpresa','form');
					else $texto=formSelectEmpresas($matriz[idEmpresa],'idEmpresa','form');
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Observações: </b><br><span class=normal10>Comentários sobre a máquina</span>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					$texto="<textarea name=matriz[obs] cols=60 rows=6>$matriz[obs]</textarea>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntAlterar]) {
		# Conferir campos
		# continuar
		# Cadastrar em banco de dados
		$matriz[id]=$registro;
		$grava=dbMaquina($matriz, 'alterar');
		
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			listarProgramas($modulo, 'programas', 'listar', $matriz[id], '');
			
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
	
} # fecha funcao de alteração


# Exclusão de servicos
function excluirMaquinas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaMaquinas($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$cliente=resultadoSQL($consulta, 0, 'cliente');
			$ip=resultadoSQL($consulta, 0, 'ip');
			$data=resultadoSQL($consulta, 0, 'data');
			$obs=resultadoSQL($consulta, 0, 'obs');
			
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
					<input type=hidden name=registro value=$registro>&nbsp;
					";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>IP: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($ip, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Cliente: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($cliente, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Data de Cadastro: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(strlen(trim($obs))>0) {
					novaLinhaTabela($corFundo, '100%');
						$texto="<b class=bold10>Observações: </b>";
						itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
						itemLinhaForm($obs, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$matriz[id]=$registro;
		$grava=dbMaquina($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			avisoNOURL("Aviso", $msg, 400);
			
			
			# Excluir Programas
			dbPrograma($matriz,'excluirmaquina');
			
			# Excluir Usuarios
			dbUser($matriz,'excluirmaquina');
			
			echo "<br>";
			procurarMaquinas($modulo, $sub, 'listar', 0, $matriz);
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# Função para busca de novo ID de cadastro
function buscaIDNovoMaquina() {

	global $conn, $tb;
	
	
	$sql="SELECT MAX(id)+1 id FROM  $tb[Maquinas]";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$id=resultadoSQL($consulta, 0, 'id');
		
		if(!is_numeric($id)) $retorno=1;
		else $retorno=$id;
	}
	else {
		$retorno=1;
	}
	
	
	return($retorno);
}



# Visualização de maquinas
function verMaquina($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html;

	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro) {
	
		# Buscar Valores
		$consulta=buscaMaquinas($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$ip=resultadoSQL($consulta, 0, 'ip');
			$cliente=resultadoSQL($consulta, 0, 'cliente');
			$idEmpresa=resultadoSQL($consulta, 0, 'idEmpresa');
			$data=resultadoSQL($consulta, 0, 'data');
			$obs=resultadoSQL($consulta, 0, 'obs');
			
			# Motrar tabela de busca
			novaTabela2("[Visualização de Máquina]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>&nbsp;
					";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>IP: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($ip, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Cliente: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					if($idEmpresa>0) itemLinhaForm(formSelectEmpresas($idEmpresa, '','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					else itemLinhaForm($cliente, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Data de Cadastro: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(strlen(trim($obs))>0) {
					novaLinhaTabela($corFundo, '100%');
						$texto="<b class=bold10>Observações: </b>";
						itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(nl2br($obs), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
}



/**
 * Retornar dados da maquina
 *
 * @param $registro
 * @return matriz de dados
 */
function dadosMaquinas($registro) {

	$consulta=buscaMaquinas($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[ip]=resultadoSQL($consulta, 0, 'ip');
		$retorno[cliente]=resultadoSQL($consulta, 0, 'cliente');
		$retorno[idEmpresa]=resultadoSQL($consulta, 0, 'idEmpresa');
		$retorno[data]=resultadoSQL($consulta, 0, 'data');
		$retorno[obs]=resultadoSQL($consulta, 0, 'obs');
	}
	
	return($retorno);
}

?>
