<?php
/*
 * Created on Apr 11, 2005
 *         by Gustavo
 *    version 001
 * 
 * parametros utlizados pelo sistema atualme
 *
 */
 
 function parametrosBancos( $modulo, $sub, $acao, $registro, $matriz ){
 
	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		exibeSemPermissao($modulo, $acao);
	}
	else {
		$titulo = "<b>Parametros de Configurações</b>";
		$subtitulo = "<b> das Agências Bancárias</b>" ;
		$itens=Array('Adicionar', 'Listar');
		getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
		
		echo "<br>";
		switch ($acao) {
			case "adicionar":
				parametrosBancosAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "excluir":
				parametrosBancosExcluir($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				parametrosBancosAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			default:
				parametrosBancosListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}		
	echo "<script>location.href='#ancora';</script>";
 } 
 
//function getAtributos ($id) {
//    $atributosBanco = new AtributosArquivoRemessa($id);
//	print_r($atributosBanco);
//	$retorno = $atributosBanco->Banco[0];
//	return ($atributosBanco);
//}

//funcoes para manipulacao da base de dados
//desc ParametrosArquivosBancos;
//+-----------+-----------+------+-----+---------+----------------+
//| Field     | Type      | Null | Key | Default | Extra          |
//+-----------+-----------+------+-----+---------+----------------+
//| id        | int(11)   |      | PRI | NULL    | auto_increment |
//| idBanco   | int(11)   |      |     | 0       |                |
//| atributo  | char(30)  | YES  |     | NULL    |                |
//| valor     | char(100) | YES  |     | NULL    |                |
//| descricao | char(200) | YES  |     | NULL    |                |
//+-----------+-----------+------+-----+---------+----------------+
function dbParametrosBancos($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
					
		$sql="INSERT INTO $tb[ParametrosArquivosBancos] VALUES (0,
		'$matriz[idBanco]',
		'$matriz[atributo]',
		'$matriz[valor]',
		'$matriz[descricao]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaParametrosBancos($matriz[id], 'id', 'igual', 'id');
		
		# Registro já existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[ParametrosArquivosBancos] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="UPDATE $tb[ParametrosArquivosBancos] SET descricao='$matriz[descricao]', 
				idBanco='$matriz[idBanco]', 
				atributo='$matriz[atributo]',
				valor='$matriz[valor]'
			WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados

# função de busca 
function buscaParametrosBancos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ParametrosArquivosBancos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ParametrosArquivosBancos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ParametrosArquivosBancos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ParametrosArquivosBancos] WHERE $texto ORDER BY $ordem";
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
 
# função para adicionar
function parametrosBancosAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if(!$matriz[bntAdicionar]) {
		
		# Motrar tabela de adicao
		novaTabela2("[Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			getCampo('combo',  'Banco',       '',   formSelectBancos('$matriz[idBanco]', 'idBanco', 'form'));
			getCampo('text', 'Parametro',     'matriz[atributo]',   $dados[atributo],"", "", "20" );
			getCampo('text', 'Valor',     'matriz[valor]',   $dados[valor],"", "", "30");
			getCampo('text',   'Descrição',  'matriz[descricao]', $dados[descricao],"", "", "60");

			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}

	elseif($matriz[bntAdicionar]) {
	# Conferir campos
		if($matriz[atributo] && strlen($matriz[valor]) ) {
			# Buscar por prioridade
			# Cadastrar em banco de dados
			$matriz[atributo]=formatarString($matriz[atributo],'minuscula');
			$grava=dbParametrosBancos($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
		}
	}
	else {
		$msg="Ocorreram erros durante a gravação.";
		avisoNOURL("Aviso: Ocorrência de erro", $msg, '60%');
	}
}
	


/**
 * @author sombra
 *
 */
 function parametrosBancosListar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite,$acao;

	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Seleção de registros
		$consulta=buscaParametrosBancos($texto, $campo, 'todos','idBanco');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
		}
		else {
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Banco', 'center', '14%', 'tabfundo0');
				itemLinhaTabela('Descrição', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Parâmetro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '17%', 'tabfundo0');
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

			$limite=$i+$limite[lista][parametros_config];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idBanco=resultadoSQL($consulta, $i, 'idBanco');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$parametro=resultadoSQL($consulta, $i, 'atributo');
				$valor=resultadoSQL($consulta, $i, 'valor');
				
				$banco=dadosBanco($idBanco);
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($banco[nome], 'left', '10%', 'normal10');
					itemLinhaTabela($descricao, 'left', '30%', 'normal10');
					itemLinhaTabela($parametro, 'center', '20%', 'normal10');
					itemLinhaTabela($valor, 'center', '25%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '15%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
}

function parametrosBancosExcluir($modulo, $sub, $acao, $registro, $matriz){
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
		$consulta=buscaParametrosBancos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$idBanco = resultadoSQL($consulta, 0, 'idBanco');
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$parametro=resultadoSQL($consulta, 0, 'atributo');
			$valor=resultadoSQL($consulta, 0, 'valor');
			
			$banco=dadosBanco($idBanco);
			
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
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold>Banco: </b>";
					itemLinhaTMNOURL($texto, 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL($banco[nome], 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold>Descrição: </b>";
					itemLinhaTMNOURL($texto, 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL($descricao, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold>Parâmetro: </b>";
					itemLinhaTMNOURL($texto, 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL($parametro, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold>Valor: </b>";
					itemLinhaTMNOURL($texto, 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL($valor, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit2>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbParametrosBancos($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			parametrosBancosListar($modulo, $sub, $acao, 0, $matriz);
		}
		
	} #fecha bntExcluir
}	


# Funcao para alteração
function parametrosBancosAlterar($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de alteracao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaParametrosBancos($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$idBanco=resultadoSQL($consulta, 0, 'idBanco');
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$atributo=resultadoSQL($consulta, 0, 'atributo');
			$valor=resultadoSQL($consulta, 0, 'valor');
			
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
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				getCampo('combo',  'Banco',       '',   formSelectBancos("$idBanco", 'idBanco', 'form'));
				getCampo('text', 'Parametro',     'matriz[atributo]',   $atributo,"", "", "20" );
				getCampo('text', 'Valor',     'matriz[valor]',   $valor,"", "", "30");
				getCampo('text',   'Descrição',  'matriz[descricao]', $descricao,"", "", "60");
				getBotao('matriz[bntAlterar]', 'Alterar');
				
			fechaTabela();				

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntAlterar]) {
		# Conferir campos
		if($matriz[descricao] && $matriz[atributo] ) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbParametrosBancos($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				parametrosBancosListar($modulo, $sub, 'listar', 0, $matriz);

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
	} #fecha bntAlterar
	
} # fecha funcao de alteração

function parametrosBancosCarregar($idBanco = 0) {
	
	if ($idBanco == 0)		$consulta=buscaParametrosBancos('','','todos','atributo');
	else					$consulta=buscaParametrosBancos($idBanco,'idBanco','igual','descricao');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Retornar parametros
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$parametro=resultadoSQL($consulta, $a, 'atributo');
			$valor=resultadoSQL($consulta, $a, 'valor');
			$parametros[$parametro]=$valor;
		}
	}
	
	
	return($parametros);
	
}

?>
