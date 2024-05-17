<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/01/2005
# Ultima alteração: 11/01/2005
#    Alteração No.: 002
#
# Função:
#    Funções para manipulação de banco de dados do ISP-IT


/**
 * @return unknown
 * @desc Conectar ao banco de dados do ISP-IT
*/
function conectaISP() {

	global $isp;
	
	$connISP = conectaMySQL($isp[host],$isp[user], $isp[pass]);

	if(!$connISP) {
		avisoNOURL("ERRO", "Erro ao tentar conectar ao banco de dados do ISP-IT", 400);
		return 0;
	}
	else {
		return $connISP;
	}
}




function formSelectAddPessoaTipo($registro, $campo, $tipo, $indexform=0) {
	global $isp, $conn;
	
	$connISP = conectaISP();
	
	$consultaEmpresas=buscaEmpresas('','','todos','id');
	# MONTAR SQL PARA REMOVER OS IDs já cadastrados
	if($consultaEmpresas && contaConsulta($consultaEmpresas)>0) {
		for($x=0;$x<contaConsulta($consultaEmpresas);$x++) {
			
			$tmpID=resultadoSQL($consultaEmpresas, $x, 'idPessoaTipo');
			
			if($tmpID != $registro) $matID[]=$tmpID;
		}
		
		$i=0;
		while($matID[$i]) {
			
			if($i>0) $idPessoaTipo.=",";
			$idPessoaTipo.=$matID[$i];
			
			$i++;
		}
		
		$sqlADD="AND $isp[db].PessoasTipos.id NOT IN ($idPessoaTipo)";
	}

	if($tipo=='form') {
		$sql="
			SELECT
				$isp[db].PessoasTipos.id, 
				$isp[db].Pessoas.nome, 
				$isp[db].Pessoas.razao, 
				$isp[db].Pessoas.tipoPessoa
			FROM 
				$isp[db].Pessoas, 
				$isp[db].PessoasTipos 
			WHERE
				$isp[db].Pessoas.id = $isp[db].PessoasTipos.idPessoa
				$sqlADD
			ORDER by
				$isp[db].Pessoas.nome
		";

	}
	elseif($tipo=='check') {
		$sql="
			SELECT
				$isp[db].PessoasTipos.id, 
				$isp[db].Pessoas.nome
			FROM 
				$isp[db].Pessoas, 
				$isp[db].PessoasTipos 
			WHERE
				$isp[db].Pessoas.id = $isp[db].PessoasTipos.idPessoa
				AND $isp[db].PessoasTipos.id = $registro
		";
	}
	elseif($tipo=='semIsp') {
		$sql="
			SELECT
				id,
				Empresas.nome,
			FROM 
				Empresas
			WHERE
				Empresas.id = $registro
		";
	}
	
	if($tipo=='semIsp') $consulta=consultaSQL($sql, $conn);
	else $consulta=consultaSQL($sql, $connISP);
	
	if($consulta && contaConsulta($consulta)>0) {
		if($tipo=='form') {
			
			if($indexform>0) {
				$tmpJS="onChange=javascript:submit();";
			}
			
			$retorno="<select name=matriz[$campo] $tmpJS>\n";
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				
				$id=resultadoSQL($consulta, $a, 'id');
				$nome=substr(resultadoSQL($consulta, $a, 'nome'),0,40);
				
				if($registro==$id) $opcSelect='selected';
				else $opcSelect='';
				
				# Verificar se registro já está em banco de dados de Empresas
				$retorno.="<option value=$id $opcSelect>$nome\n";
			}
			
			$retorno.="</select>";
		}
		elseif($tipo=='check') {
			$retorno=resultadoSQL($consulta, 0, 'nome');
		}
	}
	
	return($retorno);
}

//function formSelect

?>