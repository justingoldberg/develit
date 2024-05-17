<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 23/09/2003
# Ultima alteração: 02/02/2004
#    Alteração No.: 007
#
# Função:
#    Painel - Funções para configurações

# Função para listagem de limites de configuração
function listarLimitesAdministracao($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;
	
	$sql="
	 	SELECT
			$tb[Modulos].id id, 
			$tb[Modulos].modulo modulo ,
			$tb[Modulos].descricao descricao
		from 
			$tb[Modulos], 
			$tb[Parametros], 
			$tb[ParametrosModulos], 
			$tb[ServicosParametros], 
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[PessoasTipos], 
			$tb[Unidades], 
			$tb[Pessoas] 
		WHERE 
			$tb[Modulos].id=$tb[ParametrosModulos].idModulo  
			AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
			AND $tb[Parametros].idUnidade = $tb[Unidades].id
			AND $tb[Parametros].id = $tb[ServicosParametros].idParametro 
			AND $tb[ServicosParametros].idServico = $tb[Servicos].id 
			AND $tb[Servicos].id = $tb[ServicosPlanos].idServico 
			AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
			AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[PessoasTipos].id = $registro
			AND $tb[PlanosPessoas].status='A'
		GROUP BY 
			$tb[Modulos].id 
		ORDER BY 
			$tb[Pessoas].id,  
			$tb[Modulos].modulo";
			
			
	$consulta=consultaSQL($sql, $conn);
		
	if($consulta && contaConsulta($consulta) > 0) {
		# Listar limites por modulo selecionado na consulta
		novaTabela2SH("center", '100%', 0, 2, 0, $corFundo, $corBorda, 4);
		
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$id=resultadoSQL($consulta, $a, 'id');
				$descricao=resultadoSQL($consulta, $a, 'descricao');
				$modulo=resultadoSQL($consulta, $a, 'modulo');
				
				$url="<a href=?modulo=administracao&sub=$modulo&acao=config&registro=$registro>";
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("$url<img src=".$html[imagem][$modulo]." border=0></a>", 'right', 'middle', '5%', $corFundo, 0, 'normal10');
//					if($modulo == 'maquinas'){
//						itemLinhaTMNOURL("<b class='bold10'>$descricao</b></a>", 'left', 'middle', '65%', $corFundo, 0, 'normal10');
//					}
//					else{
						itemLinhaTMNOURL("$url<b>$descricao</b></a>", 'left', 'middle', '65%', $corFundo, 0, 'normal10');
//					}
					itemLinhaTMNOURL('Qtde', 'center', 'middle', '20%', $corFundo, 0, 'bold10');
					itemLinhaTMNOURL('Unidade', 'right', 'middle', '10%', $corFundo, 0, 'bold10');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'center" valign="top', $corFundo, 4, 'normal10');
						listarLimitesModulo($registro, $id);
						if($a+1 < contaConsulta($consulta)) echo "<br>";
					htmlFechaColuna();
				fechaLinhaTabela();
				
				# Selecionar Limites por modulo
			}
		fechaTabela();
	}
	else {
		# não foram encontrados registros
		echo "<span class=txtaviso>Não foram encontrados parâmetros para serviços configurados.</span>";
	}
}




# Função para listagem de limites de configuração
function listarLimitesModulo($idPessoaTipo, $idModulo) {

	global $conn, $tb, $corFundo, $corBorda;
	
	$sql="
		select 
			PlanosPessoas.id idPlano, 
			PlanosPessoas.nome nomePlano,
			ServicosPlanos.id idServico, 
			Servicos.nome nomeServico,
			Modulos.id idModulo, 
			Modulos.modulo, 
			Parametros.descricao nomeParametro,
			Parametros.tipo tipoParametro,
			Parametros.parametro, 
			Parametros.id idParametro, 
			Unidades.unidade, 
			ServicosParametros.valor 
		FROM
			Modulos, 
			Parametros, 
			ParametrosModulos, 
			ServicosParametros, 
			Servicos, 
			ServicosPlanos, 
			StatusServicosPlanos,  
			PlanosPessoas, 
			PessoasTipos, 
			Pessoas, 
			Unidades 
		WHERE
			Modulos.id=ParametrosModulos.idModulo 
			AND ParametrosModulos.idParametro = Parametros.id 
			AND Parametros.idUnidade = Unidades.id 
			AND Parametros.id = ServicosParametros.idParametro 
			AND ServicosParametros.idServico  = Servicos.id 
			AND Servicos.id = ServicosPlanos.idServico 
			AND ServicosPlanos.idStatus = StatusServicosPlanos.id 
			AND ServicosPlanos.idPlano = PlanosPessoas.id 
			AND PlanosPessoas.idPessoaTipo = PessoasTipos.id 
			AND PessoasTipos.idPessoa = Pessoas.id 
			AND PessoasTipos.id=$idPessoaTipo
			AND idModulo=$idModulo
		GROUP BY
			Parametros.id
		ORDER BY
			idServico";
				
			
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# Listar limites por modulo selecionado na consulta
		novaTabela2SH("center", '100%', 0, 0, 0, $corFundo, $corBorda, 4);
		
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$idPlano=resultadoSQL($consulta, $a, 'idPlano');
				$nomePlano=resultadoSQL($consulta, $a, 'nomePlano');
				$idServico=resultadoSQL($consulta, $a, 'idServico');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');
				$idModulo=resultadoSQL($consulta, $a, 'idModulo');
				$modulo=resultadoSQL($consulta, $a, 'modulo');
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$tipoParametro=resultadoSQL($consulta, $a, 'tipoParametro');
				$nomeParametro=resultadoSQL($consulta, $a, 'nomeParametro');
				$valor=resultadoSQL($consulta, $a, 'valor');
				$unidade=resultadoSQL($consulta, $a, 'unidade');
				
				# Totalizar parametro
				if($tipoParametro=='nr') {
					$total=totalParametrosServicoModulo($idPessoaTipo, $idModulo, $idServico, $idParametro);
					
					if($modulo=='dial' && $parametro=='qtde') {
						# contar contas em uso
						$totalUso=radiusTotalContasEmUso($idPessoaTipo);
						$total="$totalUso / $total";
					}
					elseif($modulo=='dominio') {
						$totalUso=dominioTotalContasEmUso($idPessoaTipo);
						$total="$totalUso / $total";
					}
					elseif($modulo=='maquinas') {
						$totalUso=maquinasTotalEmUso($idPessoaTipo);
						$total="$totalUso / $total";
					}
					elseif($modulo=='suporte') {
						$totalUso=maquinasTotalEmUso($idPessoaTipo);
						$total="$totalUso / $total";
//						$total=suporteTotalHorasEmUso($idPessoaTipo);
					}	
					elseif($modulo=='mail') {
						if($parametro=='qtde') {
							$totalUso=emailTotalContasEmUsoPessoaTipo($idPessoaTipo);
							$total="$totalUso / $total";
						}
					}
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('&nbsp;', 'center', 'middle', '5%', $corFundo, 0, 'normal10');
						itemLinhaTMNOURL("<li>$nomeParametro</li>", 'left', 'middle', '65%', $corFundo, 0, 'normal10');
						itemLinhaTMNOURL($total, 'center', 'middle', '20%', $corFundo, 0, 'txtaviso');
						itemLinhaTMNOURL($unidade, 'center', 'middle', '10%', $corFundo, 0, 'txtaviso');
					fechaLinhaTabela();

				}
				else {
					# Selecionar Limites por modulo
					itemTabelaNOURL("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$nomeParametro", 'left', $corFundo, 0, 'normal10');
				}
				
				
			}
		fechaTabela();
	}
	else {
		# não foram encontrados registros
		echo "<span class=txtaviso>Não foram encontrados parâmetros para serviços configurados</span>";
	}
}

?>