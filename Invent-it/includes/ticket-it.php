<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 11/01/2005
# Ultima alteração: 11/01/2005
#    Alteração No.: 001
#
# Função:
#    Funções para manipulação de banco de dados do TICKET-IT

/**
 * @return unknown
 * @desc Conectar ao banco de dados do TICKET-IT
*/
function conectaTicket() {

	global $ticket;
	
	$connTicket = conectaMySQL($ticket[host],$ticket[user], $ticket[pass]);

	if(!$connTicket) {
		avisoNOURL("ERRO", "Erro ao tentar conectar ao banco de dados do Ticket-IT", 400);
		return 0;
	}
	else {
		return $connTicket;
	}
}


function formSelectTicket($idMaquina, $registro, $texto, $campo, $tipo) {
	
	global $ticket;
	
	$connTicket=conectaTicket();
	
	$consultaTickets=buscaTickets($idMaquina,'idMaquina','igual','id');
	
	# MONTAR SQL PARA REMOVER OS IDs já cadastrados
	if($consultaTickets && contaConsulta($consultaTickets)>0) {
		for($x=0;$x<contaConsulta($consultaTickets);$x++) {
			
			$tmpID=resultadoSQL($consultaTickets, $x, 'idTicket');
			
			if($tmpID != $registro) $matID[]=$tmpID;
		}
		
		$i=0;
		while($matID[$i]) {
			
			if($i>0) $idTicket.=",";
			$idTicket.=$matID[$i];
			
			$i++;
		}
		
		$sqlADD="AND $ticket[db].ticket.id NOT IN ($idTicket)";
	}
	
	if($tipo=='form') {
		$sql="
			SELECT
				$ticket[db].ticket.id, 
				$ticket[db].ticket.assunto titulo, 
				$ticket[db].ticket.data
			FROM 
				$ticket[db].ticket
			WHERE
				($ticket[db].ticket.assunto LIKE '%$texto%'
					OR $ticket[db].ticket.protocolo LIKE '%$texto%')
				$sqlADD
			ORDER by
				$ticket[db].ticket.data DESC
		";
		
	}
	elseif($tipo=='check') {
		$sql="
			SELECT
				$ticket[db].ticket.id, 
				$ticket[db].ticket.assunto titulo, 
				$ticket[db].ticket.data
			FROM 
				$ticket[db].ticket
			WHERE
				$ticket[db].ticket.id = '$registro'
		";
	}
	
	$consulta=consultaSQL($sql, $connTicket);
	
	if($consulta && contaConsulta($consulta)>0) {
		if($tipo=='form') {
			
			$tmpJS="onChange=javascript:submit();";
			
			$retorno="<select name=matriz[$campo] $tmpJS>\n";
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				
				$id=resultadoSQL($consulta, $a, 'id');
				$titulo=substr(resultadoSQL($consulta, $a, 'titulo'),0,40);
				$data=converteData(resultadoSQL($consulta, $a, 'data'),'banco','formdata');
				
				if($registro==$id) $opcSelect='selected';
				else $opcSelect='';
				
				# Verificar se registro já está em banco de dados de Empresas
				$retorno.="<option value=$id $opcSelect>$data - $titulo\n";
			}
			
			$retorno.="</select>";
		}
		elseif($tipo=='check') {
			$retorno[titulo]=resultadoSQL($consulta, 0, 'titulo');
			$retorno[data]=resultadoSQL($consulta, 0, 'data');
		}
	}
	
	return($retorno);
	
}


?>