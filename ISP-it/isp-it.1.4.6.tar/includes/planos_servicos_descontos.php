<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 16/07/2003
# Ultima alteração: 28/10/2003
#    Alteração No.: 009
#
# Função:
#    Painel - Funções para servicos dos planos


# Função de banco de dados - Pessoas
function dbDescontoServicoPlano($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[DescontosServicosPlanos] VALUES (0,
		'$matriz[idPlano]',
		'$matriz[idServicoPlano]',
		'$matriz[dtDesconto]',
		'$matriz[dtCancelamento]',
		'$matriz[dtCobranca]',
		'$matriz[valor]',
		'$matriz[descricao]',
		'$matriz[status]')";
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[DescontosServicosPlanos] where id=$matriz[id]";
	}
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM $tb[DescontosServicosPlanos] where idPlano=$matriz[id]";
	}
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[DescontosServicosPlanos] 
			SET 
				dtDesconto='$matriz[dtDesconto]',
				valor='$matriz[valor]',
				descricao='$matriz[descricao]',
				status='$matriz[status]'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='cancelar') {
		$sql="UPDATE $tb[DescontosServicosPlanos] 
			SET 
				dtCancelamento='$matriz[dtCancelamento]',
				status='C'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='desativar') {
		$sql="UPDATE $tb[DescontosServicosPlanos] 
			SET 
				status='I'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='ativar') {
		$sql="UPDATE $tb[DescontosServicosPlanos] 
			SET 
				status='A'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='cobranca') {
		$sql="UPDATE $tb[DescontosServicosPlanos] 
			SET 
				dtCobranca='$matriz[dtCobranca]',
				status='B'
			WHERE 
				id=$matriz[id]";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}



# função de busca 
function buscaDescontosServicosPlanos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[DescontosServicosPlanos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[DescontosServicosPlanos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[DescontosServicosPlanos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[DescontosServicosPlanos] WHERE $texto ORDER BY $ordem";
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





# função para adicionar pessoa
function descontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPlanos;
	
	# Recebe ID do Serviço do Plano - Procurar detalhes
	$consulta=buscaServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServico=resultadoSQL($consulta, 0, 'idServico');
		
		# Procurar por Pessoa
		$consultaPlanos=buscaPlanos($idPlano, 'id','igual','id');
		
		if($consultaPlanos && contaConsulta($consultaPlanos)>0) {
		
			# prosseguir e mostarar pessoa e plano
			$idPessoa=resultadoSQL($consultaPlanos, 0, 'idPessoaTipo');
			
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			
			# Listar Serviços do Plano
			listarDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			
		}
		else {
			# Erro
			$msg="ERRO ao selecionar o Plano do Cliente!";
			$url="?modulo=cadastros&sub=clientes";
			aviso("Aviso", $msg, $url, 760);
		}
	}
	else {
		# Erro
		$msg="ERRO ao selecionar o Serviço do Plano!";
		$url="?modulo=cadastros&sub=clientes";
		aviso("Aviso", $msg, $url, 760);
	}
}



# Função para listagem 
function listarDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos;

	echo "<br>";
	
	if($acao=='descontosservico') $consulta=buscaDescontosServicosPlanos("idServicoPlano=$registro AND status != 'C'", '','custom','dtDesconto');
	else $consulta=buscaDescontosServicosPlanos($registro, 'idServicoPlano','igual','dtDesconto');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Desconto do Serviço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionardesconto&registro=$registro>Adicionar desconto</a>",'incluir');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$registro>Descontos Ativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservicotodos&registro=$registro>Todos os Descontos</a>",'listar');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 5, 'tabfundo1');
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há descontos para este serviço', 'left', $corFundo, 5, 'txtaviso');
	}
	else {

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Descrição do Desconto', 'center', '40%', 'tabfundo0');
			itemLinhaTabela('Data Desconto', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Valor', 'center nowrap', '10%', 'tabfundo0');
			itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
		fechaLinhaTabela();

		for($i=0;$i<contaConsulta($consulta);$i++) {
			
			$id=resultadoSQL($consulta, $i, 'id');
			$idPlano=resultadoSQL($consulta, $i, 'idPlano');
			$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
			$dtDesconto=resultadoSQL($consulta, $i, 'dtDesconto');
			$valor=resultadoSQL($consulta, $i, 'valor');
			$descricao=resultadoSQL($consulta, $i, 'descricao');
			$status=resultadoSQL($consulta, $i, 'status');
			
			# Checar status
			if($status=='A') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verdesconto&registro=$id>Ver</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterardesconto&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelardesconto&registro=$id>Cancelar</a>",'cancelar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativardesconto&registro=$id>Desativar</a>",'ativar');
				$class='txtok';
				$class2='txtok8';
			}
			elseif($status=='I') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verdesconto&registro=$id>Ver</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterardesconto&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelardesconto&registro=$id>Cancelar</a>",'cancelar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativardesconto&registro=$id>Ativar</a>",'desativar');
				$class='txtaviso';
				$class2='txtaviso8';
			}
			else {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verdesconto&registro=$id>Ver</a>",'ver');
				$class='txtaviso';
				$class2='txtaviso8';
			}
			

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($descricao, 'center', '40%', 'normal10');
				itemLinhaTabela(converteData($dtDesconto,'banco','formdata'), 'center', '10%', 'normal10');
				itemLinhaTabela(formatarValoresForm($valor), 'center', '10%', 'txtaviso');
				itemLinhaTabela(formSelectStatusDescontos($status,'','check'), 'center', '10%', 'normal10');
				itemLinhaTabela($opcoes, 'left', '30%', 'normal8');
			fechaLinhaTabela();
			
		} #fecha laco de montagem de tabela
		
		fechaTabela();
	} #fecha servicos encontrados
	
}#fecha função de listagem








# função para adicionar pessoa
function adicionarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	# Recebe ID do Plano
	$consulta=buscaServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServico=resultadoSQL($consulta, 0, 'idServico');
		
		# Procurar por Pessoa
		$consultaPlanos=buscaPlanos($idPlano, 'id','igual','id');

		if($consultaPlanos && contaConsulta($consultaPlanos)>0) {
		
			$idPessoa=resultadoSQL($consultaPlanos, 0, 'idPessoaTipo');
			$matriz[idVencimento]=resultadoSQL($consultaPlanos, 0, 'idVencimento');			
			
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $registro, $matriz);
			echo "<br>";
			
			$data=dataSistema();

			$matriz[dtDesconto]=formatarData($matriz[dtDesconto]);
			$mesDesconto=substr($matriz[dtDesconto],0,2);
			$anoDesconto=substr($matriz[dtDesconto],2,4);
			
			if(!$matriz[bntConfirmar] || !$matriz[descricao] || !$matriz[dtDesconto] || !$matriz[valor] 
				|| verificarVencimento2($matriz[idVencimento], 0, $mesDesconto, $anoDesconto) ) {
				
				# Formulário para adição de Desconto de Serviço
				$matriz[idPlano]=$idPlano;
				$matriz[idServico]=$idServico;
				$matriz[idServicoPlano]=$registro;
				$matriz[idPessoaTipo]=$idPessoa;
				formDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			else {

				# Gravar registro
				$mes=substr($matriz[dtDesconto],0,2);
				$ano=substr($matriz[dtDesconto],2,4);
				
				$vencimento=dadosVencimento($matriz[idVencimento]);
				if(strlen($vencimento[diaVencimento])==1) $diaVencimento="0".$vencimento[diaVencimento];
				else $diaVencimento=$vencimento[diaVencimento];
				$dataDesconto=$diaVencimento."/".$mes."/".$ano;
			
				$matriz[dtDesconto]=converteData($dataDesconto,'form','bancodata');
				$matriz[valor]=formatarValores($matriz[valor]);	
				
				$grava=dbDescontoServicoPlano($matriz, 'incluir');
				
				if($grava) {
					# OK
					$msg="Desconto adicionado com sucesso!!!";
					$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
					avisoNOURL("Aviso", $msg, 600);
					
					listarDescontosServicosPlanos($modulo, $sub, 'descontosservico', $registro, $matriz);
				}
				else {
					# Erro
					$msg="ERRO ao adicionar desconto! Tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
					avisoNOURL("Aviso", $msg, 600);
				}
			}
		}
		else {
			# Erro
			$msg="ERRO ao selecionar o Serviço do Plano!";
			$url="?modulo=cadastros&sub=clientes";
			aviso("Aviso", $msg, $url, 760);
		}
	}
	else {
		# Erro
		$msg="ERRO ao selecionar o Serviço do Plano!";
		$url="?modulo=cadastros&sub=clientes";
		aviso("Aviso", $msg, $url, 760);
	}

}




# função para adicionar pessoa
function alterarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaDescontosServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtDesconto=resultadoSQL($consulta, 0, 'dtDesconto');

		$matriz[dtDesconto]=formatarData($matriz[dtDesconto]);
		$mesDesconto=substr($matriz[dtDesconto],0,2);
		$anoDesconto=substr($matriz[dtDesconto],2,4);
		
		if(!$matriz[bntConfirmar] && verificarVencimento($matriz[idVencimento], 0, $mesDesconto, $anoDesconto)) {
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			
			echo "<br>";
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				if(!$matriz[bntConfirmar]) {
					# Prosseguir e procurar detalhes sobre plano
					$matriz[id]=resultadoSQL($consulta, 0, 'id');
					$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
					$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
					$matriz[descricao]=resultadoSQL($consulta, 0, 'descricao');
					$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
					$matriz[dtDesconto]=resultadoSQL($consulta, 0, 'dtDesconto');
				}

				# Converter data de desconto
				if($matriz[dtDesconto]) {
					# Converter para formato MM/AAAA
					$matriz[dtDesconto]=formatarData(converteData($matriz[dtDesconto],'banco','formdata'));
					
					$matriz[dtDesconto]=$mesDesconto.$anoDesconto;
				}

				formDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		else {
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			$matriz[dtCadastro]=$data[dataBanco];
			
			//$matriz[dtDesconto]=formatarData($matriz[dtDesconto]);
			# Gravar registro
			$matriz[dtDesconto]=formatarData($matriz[dtDesconto]);
			$mes=substr($matriz[dtDesconto],0,2);
			$ano=substr($matriz[dtDesconto],2,4);
			
			$vencimento=dadosVencimento($matriz[idVencimento]);
			if(strlen($vencimento[diaVencimento])==1) $diaVencimento="0".$vencimento[diaVencimento];
			else $diaVencimento=$vencimento[diaVencimento];
			$dataDesconto=$diaVencimento."/".$mes."/".$ano;
		
			$matriz[dtDesconto]=converteData($dataDesconto,'form','bancodata');;
			$matriz[valor]=formatarValores($matriz[valor]);	
			
			$grava=dbDescontoServicoPlano($matriz, 'alterar');
			
			if($grava) {
				# OK
				$msg="Desconto alterado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				listarDescontosServicosPlanos($modulo, $sub, 'descontosservico', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao alterar desconto! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}




# função para adicionar pessoa
function desativarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaDescontosServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtDesconto=resultadoSQL($consulta, 0, 'dtDesconto');

		if(!$matriz[bntDesativar] ) {
	
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				# Prosseguir e procurar detalhes sobre plano
				$matriz[id]=resultadoSQL($consulta, 0, 'id');
				$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
				$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
				$matriz[descricao]=resultadoSQL($consulta, 0, 'descricao');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtDesconto]=resultadoSQL($consulta, 0, 'dtDesconto');

				# Converter data de desconto
				if($matriz[dtDesconto]) {
					# Converter para formato MM/AAAA
					$matriz[dtDesconto]=formatarData(converteData($matriz[dtDesconto],'banco','formdata'));
					
					$mesDesconto=substr($matriz[dtDesconto],2,2);
					$anoDesconto=substr($matriz[dtDesconto],4,4);
					
					$matriz[dtDesconto]=$mesDesconto."/".$anoDesconto;
				}

				formDesativarDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		else {
		
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			$matriz[dtCadastro]=$data[dataBanco];
			
			$matriz[dtDesconto]=formatarData($matriz[dtDesconto]);
			$mes=substr($matriz[dtDesconto],0,2);
			$ano=substr($matriz[dtDesconto],2,4);
			
			$dataDesconto=$matriz[diaDesconto]."/".$mes."/".$ano;
		
			$matriz[dtDesconto]=converteData($dataDesconto,'form','bancodata');
			$matriz[valor]=formatarValores($matriz[valor]);	
			
			$grava=dbDescontoServicoPlano($matriz, 'desativar');
			
			if($grava) {
				# OK
				$msg="Desconto desativado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				listarDescontosServicosPlanos($modulo, $sub, 'descontosservico', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao desativar desconto! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}



# função para adicionar pessoa
function ativarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaDescontosServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtDesconto=resultadoSQL($consulta, 0, 'dtDesconto');

		if(!$matriz[bntAtivar] ) {
	
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				# Prosseguir e procurar detalhes sobre plano
				$matriz[id]=resultadoSQL($consulta, 0, 'id');
				$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
				$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
				$matriz[descricao]=resultadoSQL($consulta, 0, 'descricao');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtDesconto]=resultadoSQL($consulta, 0, 'dtDesconto');

				# Converter data de desconto
				if($matriz[dtDesconto]) {
					# Converter para formato MM/AAAA
					$matriz[dtDesconto]=formatarData(converteData($matriz[dtDesconto],'banco','formdata'));
					
					$mesDesconto=substr($matriz[dtDesconto],2,2);
					$anoDesconto=substr($matriz[dtDesconto],4,4);
					
					$matriz[dtDesconto]=$mesDesconto."/".$anoDesconto;
				}

				formAtivarDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		else {
		
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			$matriz[dtCadastro]=$data[dataBanco];
			
			$matriz[dtDesconto]=formatarData($matriz[dtDesconto]);
			$mes=substr($matriz[dtDesconto],0,2);
			$ano=substr($matriz[dtDesconto],2,4);
			
			$dataDesconto=$matriz[diaDesconto]."/".$mes."/".$ano;
		
			$matriz[dtDesconto]=converteData($dataDesconto,'form','bancodata');
			$matriz[valor]=formatarValores($matriz[valor]);	
			
			$grava=dbDescontoServicoPlano($matriz, 'ativar');
			
			if($grava) {
				# OK
				$msg="Desconto ativado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				listarDescontosServicosPlanos($modulo, $sub, 'descontosservico', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao ativar desconto! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}





# função para adicionar pessoa
function verDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaDescontosServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtDesconto=resultadoSQL($consulta, 0, 'dtDesconto');

		# Ver o Serviço
		verServicosPlanos($modulo, $sub, 'descontosservico', $idServicoPlano, $matriz);
		echo "<br>";
	
		
		# Selecionar informações do plano
		$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
		
		if($consultaPlano && contaConsulta($consultaPlano)>0) {
			$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
			$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
			
			# Prosseguir e procurar detalhes sobre plano
			$matriz[id]=resultadoSQL($consulta, 0, 'id');
			$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
			$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
			$matriz[descricao]=resultadoSQL($consulta, 0, 'descricao');
			$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
			$matriz[dtDesconto]=resultadoSQL($consulta, 0, 'dtDesconto');

			# Converter data de desconto
			if($matriz[dtDesconto]) {
				# Converter para formato MM/AAAA
				$matriz[dtDesconto]=formatarData(converteData($matriz[dtDesconto],'banco','formdata'));
				
				$mesDesconto=substr($matriz[dtDesconto],2,2);
				$anoDesconto=substr($matriz[dtDesconto],4,4);
				
				$matriz[dtDesconto]=$mesDesconto."/".$anoDesconto;
			}

			formVerDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}
	}
}



# função para adicionar pessoa
function cancelarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	
	$consulta=buscaDescontosServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtDesconto=resultadoSQL($consulta, 0, 'dtDesconto');

		if(!$matriz[bntCancelar] ) {
	
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				# Prosseguir e procurar detalhes sobre plano
				$matriz[id]=resultadoSQL($consulta, 0, 'id');
				$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
				$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
				$matriz[descricao]=resultadoSQL($consulta, 0, 'descricao');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtDesconto]=resultadoSQL($consulta, 0, 'dtDesconto');

				# Converter data de desconto
				if($matriz[dtDesconto]) {
					# Converter para formato MM/AAAA
					$matriz[dtDesconto]=formatarData(converteData($matriz[dtDesconto],'banco','formdata'));
					
					$mesDesconto=substr($matriz[dtDesconto],2,2);
					$anoDesconto=substr($matriz[dtDesconto],4,4);
					
					$matriz[dtDesconto]=$mesDesconto."/".$anoDesconto;
				}

				formCancelarDescontosServicosPlanos($modulo, $sub, 'descontosservico', $matriz[id], $matriz);
			}
		}
		else {
		
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			$matriz[dtCadastro]=$data[dataBanco];
			
			$matriz[dtDesconto]=formatarData($matriz[dtDesconto]);
			$mes=substr($matriz[dtDesconto],0,2);
			$ano=substr($matriz[dtDesconto],2,4);
			
			$dataDesconto=$matriz[diaDesconto]."/".$mes."/".$ano;
		
			$matriz[dtDesconto]=converteData($dataDesconto,'form','bancodata');
			$matriz[valor]=formatarValores($matriz[valor]);	
			
			$grava=dbDescontoServicoPlano($matriz, 'cancelar');
			
			if($grava) {
				# OK
				$msg="Desconto cancelado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				listarDescontosServicosPlanos($modulo, $sub, 'descontosservico', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao cancelar desconto! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}




# formulário de dados cadastrais
function formDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Descontos de Serviço do Plano]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[idVencimento] value=$matriz[idVencimento]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[descricao] value='$matriz[descricao]' size=60>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], 'diaDesconto','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[dtDesconto] value='$matriz[dtDesconto]' size=7 onBlur=verificaDataMesAno(this.value,9);> <span class=txtaviso>(Formato: ".$data[mes]."/".$data[ano].")</span>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=formataValor(this.value,10)>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectStatusDescontos($matriz[status],'status','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	fechaTabela();

}



# formulário de dados cadastrais
function formDesativarDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Visualização de Desconto de Serviço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[descricao], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtDesconto], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit class=submit name=matriz[bntDesativar] value=Desativar>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();				

	fechaTabela();

}



# formulário de dados cadastrais
function formAtivarDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Visualização de Desconto de Serviço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[descricao], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtDesconto], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit class=submit name=matriz[bntAtivar] value=Ativar>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();				

	fechaTabela();

}


# formulário de dados cadastrais
function formVerDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Visualização de Desconto de Serviço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[descricao], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtDesconto], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

	fechaTabela();

}



# formulário de dados cadastrais
function formCancelarDescontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Cancelamento de Desconto de Serviço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=cancelardesconto>
			<input type=hidden name=registro value=$matriz[id]>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[descricao], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtDesconto], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit class=submit name=matriz[bntCancelar] value=Cancelar>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();				

	fechaTabela();

}



# Função para seleção de dia de desconto
function formSelectDiaDesconto($dia, $idPessoaTipo, $campo, $tipo) {

	global $conn, $tb;
	
	# Selecionar os dias de vencimento e listar
	$sql="SELECT
				DISTINCT $tb[Vencimentos].diaVencimento dia,
				$tb[Vencimentos].descricao descricao
			FROM 
				$tb[Vencimentos], 
				$tb[PlanosPessoas] 
			WHERE
				$tb[PlanosPessoas].idVencimento = $tb[Vencimentos].id 
				AND $tb[PlanosPessoas].idPessoaTipo = $idPessoaTipo";
				
				
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# montar form com dias
		
		if($tipo=='form') {
			$retorno="<select name=matriz[$campo]>\n";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
				# Listar todos
				$diaVencimento=resultadoSQL($consulta, $i, 'dia');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				
				if($dia == $diaVencimento) $opcSelectDia="selected";
				else $opcSelectDia='';
				
				$retorno.="<option value=$diaVencimento $opcSelectDia>$descricao [ $diaVencimento ]\n";
			}
			
			$retorno.="</select>";
		}
	}
	
	return($retorno);
}



# funcao para listagem de servicos adicionais
function listarDescontosVencimento($idServicoPlano, $dtVencimento) {

	global $corFundo, $corBorda, $modulo, $sub;
	
	$dtVencimento=date('Y-m-d',$dtVencimento);
	
	$consulta=buscaDescontosServicosPlanos("idServicoPlano=$idServicoPlano AND dtDesconto='$dtVencimento' and status='A'", '','custom','dtDesconto');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$nome=resultadoSQL($consulta, $a, 'descricao');
			$dtVencimento=resultadoSQL($consulta, $a, 'dtDesconto');
			$valor=resultadoSQL($consulta, $a, 'valor');
			
			$nome=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=descontosservico&registro=$idServicoPlano>$nome</a>",'desconto');
		
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Desconto', 'center', '20%', 'txtaviso');
				itemLinhaTabela($nome, 'left', '50%', 'normal10');
				itemLinhaTabela(formatarValoresForm($valor), 'center', '15%', 'txtaviso');
				itemLinhaTabela(converteData($dtVencimento, 'banco','formdata'), 'center', '15%', 'bold10');
			fechaLinhaTabela();
		}
					
	}
}



# Atualizar data de desconto
function atualizarDataDesconto($idPlano, $dtInicio, $idVencimento) {

	global $conn, $tb;
	$data = dataSistema();
	
	$vencimento=dadosVencimento($idVencimento);
	
	# Selecionar descontos para o serviço informado
	$consulta=buscaDescontosServicosPlanos($idPlano, 'idPlano','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		for($i=0;$i<contaConsulta($consulta);$i++) {
			$idDesconto=resultadoSQL($consulta, $i, 'id');
			$dtDesconto=resultadoSQL($consulta, $i, 'dtDesconto');
			
			//somente se o desconto ainda nao foi aplicado.
			if ( strtotime( $dtDesconto ) > time() ){
				
				# Quebrar data
				$tmpData=formatarData($dtDesconto);
				$dia=substr($tmpData, 6,2);
				$mes=substr($tmpData, 4,2);
				$ano=substr($tmpData, 0,4);
				
				//# faco a comparacao nao com o dia atual e sim com o novo dia de vencimento
				//# se a nova data(dia) de vencimento for menor que a anterior, é preciso colocá-lo para o proximo 
				//# faturamento, porem somente se a fatura ainda nao foi gerada que é vista verificiando se o dia 
				//# atual é maior que a tada de faturamento   
				
				if ($vencimento[diaVencimento] < $dia ){
					if ( $mes > $data[mes] ||  ($mes == $data[mes] && $dia > $data[dia]))
						if ($mes<12)
							$mes++;
						else{
							$ano++;
							$mes=1;
						}
				}
				
				
				# nova data
				$novaData="$ano-$mes-$vencimento[diaVencimento]";
				
				$sql="
					UPDATE $tb[DescontosServicosPlanos] SET
						dtDesconto='$novaData'
					WHERE
					id=$idDesconto";
				
				$grava=consultaSQL($sql, $conn);
			}
		}
	}
	return(0);
}


/**
 * calcula se ao inativar o servico ja havim lhe cobrado o valor mensal inteiro,
 * e gera desconto do valor nao utilizado do servico
 * 
 * matriz[dtInativacao] data de inativacao vinda do banco 
 * matriz[dtAtivacao]  data de ativacao informada pela pessoa
 */
function adicionarDescontoServicosPlanosReativar($matriz, $ver = true){
	
	$parametros = carregaParametrosConfig();
	if ($parametros['diasFaturamento'] > 0 )	$diasFaturamento = $parametros['diasFaturamento'];
	else $diasFaturamento = 15;
	unset ( $parametros );
	
	$dtInativacao = converteData($matriz['dtInativacao'], 'banco', 'timestamp');
	#data de vencimento do faturamento do mes da inativacao.
	$dtVencimento = mktime(0,0,0,date('m', $dtInativacao), $matriz['vencimento']['diaVencimento'], date('Y', $dtInativacao));
	$dtAtivacao = converteData($matriz['dtAtivacao'], 'form', 'timestamp');
	
	#se a inativacao esta acima do vencimento. proximo mes :)
	if (date('d', $dtInativacao) > date('d', $dtVencimento) )
		$dtVencimento = strtotime (" + 1 month", $dtVencimento);
	
	$dtFaturamento = strtotime( "-".$diasFaturamento." days",$dtVencimento);
	//nao deve considerar meses com 31 ou 28 dias
	if (date('t', $dtFaturamento) != 30 ){
		$dtFaturamento = strtotime(30 - date('t',$dtFaturamento ).' day', $dtFaturamento);
	}
		

	if ( $dtInativacao > $dtFaturamento ){
		
		#se a ativacao ocorreu no mesmo mes, so calcula o valor ate a reativacao.
		$dtFinal = ($dtAtivacao < $dtVencimento ? $dtAtivacao : $dtVencimento);
		
		#foi inativado apos a cobranca.
		$qtdeDias = ceil(( $dtFinal - $dtInativacao ) /24 / 60 / 60);
		
		$qtdeDiasMes = (date('d', $dtFaturamento) > date('d', $dtVencimento) ? date('t', $dtFaturamento) : date('t', strtotime(' -1 month', $dtFaturamento) ) );
		
		#calcula a data de vencimento do desconto
		$dtVencimentoBoleto = mktime(0,0,0,date('m', $dtAtivacao), $matriz['vencimento']['diaVencimento'], date('Y', $dtAtivacao));
		
		#virada de mes	
		if ($dtVencimentoBoleto < $dtAtivacao)
			$dtVencimentoBoleto = strtotime (' + 1 month', $dtVencimentoBoleto);
		
		$dtFaturamentoAtivacao = strtotime( "-".$diasFaturamento." days",$dtVencimentoBoleto);
		#ja ocorreu o faturamento
		if ($dtAtivacao > $dtFaturamentoAtivacao)
			$dtVencimentoBoleto =  strtotime (' + 1 month', $dtVencimentoBoleto);
		
		#fim do calculo do vencimento do desconto
		
		#entao salva desconto.
		$vetor					= array();
		$vetor['idPlano'] 		= $matriz['idPlano'];
		$vetor['idServicoPlano']= $matriz['idServicoPlano'];
//		$vetor['dtDesconto']	= date('Y-m-d', $dtAtivacao);
//		$vetor['dtCobranca']	= date('Y-m-d', $dtVencimentoBoleto);
		$vetor['dtDesconto']	= date('Y-m-d', $dtVencimentoBoleto);
		$vetor['valor']			= $matriz['valorServico']/$qtdeDiasMes*$qtdeDias;
		$vetor['descricao']		= "Desconto de $qtdeDias dias, referente ao periodo inativo que havia sido faturado";
		$vetor['status']		= 'A';
		
		if ($vetor['valor'] > 0){
			dbDescontoServicoPlano($vetor, 'incluir');
			
			if ($ver){
				$vetor['qtdeDias'] = $qtdeDias;
				avisoDescontoIncluido($vetor);
			}
		}
	}
	
	return (1);
}


function avisoDescontoIncluido ($matriz) {
	global $corFundo, $corBorda;
	
	# Mostrar Mensagem
	novaTabela2("[Desconto Incluido ao Faturamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL("ATENÇÃO: Foi gerado um desconto ao ativar este servico<br>Conferir valores abaixo:", 'center', $corFundo, 2, 'txtaviso');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm($matriz['descricao'], 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm(formatarValoresForm($matriz[valor]), 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Tempo:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm($matriz[qtdeDias]." dias", 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Vencimento:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm(converteData($matriz['dtDesconto'],'banco','formdata'), 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
	fechaTabela();
	echo "<br>";
	
}



?>
