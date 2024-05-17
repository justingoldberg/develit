<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 16/03/2004
# Ultima altera��o: 16/03/2004
#    Altera��o No.: 001
#
# Fun��o:
#    ISP-IT - Fun��es para liga��o de parametros com modulos - 
# carga de parametros

# Fun��o para carregar parametros 
function carregaParametrosServicoPlano($idServicoPlano, $modulo) {

	global $conn, $tb;

	if($idServicoPlano && $modulo) {
		# consultar
		
		$sql="
			SELECT
				$tb[Modulos].modulo modulo, 
				$tb[Parametros].parametro parametro, 
				$tb[Unidades].unidade unidade, 
				$tb[ServicosParametros].valor valor 
			FROM
				$tb[Modulos], 
				$tb[Parametros], 
				$tb[Unidades], 
				$tb[ParametrosModulos], 
				$tb[ServicosParametros], 
				$tb[ServicosPlanos], 
				$tb[Servicos] 
			WHERE
				$tb[Modulos].id=$tb[ParametrosModulos].idModulo 
				AND $tb[ParametrosModulos].idParametro=$tb[Parametros].id 
				AND $tb[Parametros].id=$tb[ServicosParametros].idParametro 
				AND $tb[Parametros].idUnidade=$tb[Unidades].id
				AND $tb[ServicosPlanos].idServico = $tb[ServicosParametros].idServico 
				AND $tb[Servicos].id=$tb[ServicosPlanos].idServico 
				AND $tb[ServicosPlanos].id=$idServicoPlano
				AND $tb[Modulos].modulo='$modulo' 
			GROUP BY
				$tb[Parametros].id 
			ORDER BY
				$tb[Modulos].modulo
		";
		
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
		
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$unidade=resultadoSQL($consulta, $a, 'unidade');
				$modulo=resultadoSQL($consulta, $a, 'modulo');
				$valor=resultadoSQL($consulta, $a, 'valor');

				$retorno[$parametro]="$valor $unidade";
			}
		}
	}
	
	
	return($retorno);
}

?>
