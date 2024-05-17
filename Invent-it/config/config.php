<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 08/01/2004
# Ultima altera��o: 10/01/2005
#    Altera��o No.: 004
#
# Fun��o:
#    Configura��es utilizadas pela aplica��o

# Configura��es da Aplica��o
$configAppName="Devel-IT - .:Invent-IT:.";
$configAppVersion="Vers�o 0.5";
	
# Configura��es para Conex�o com banco de dados
$configHostMySQL="localhost";
$configUserMySQL="root";
$configPasswdMySQL="";
$configDBMySQL="ticket";

# Intera��o com ISP      -- Yuri 06/04/2010
$_REQUEST['integraIsp']=false;

# Configura��es de cor
$corFundo="#ffffff";
$corBorda="#223366";

#############################################
# Limites de listagens e pagina��es
$limite[lista][usuarios]=10;
$limite[lista][grupos]=10;
$limite[lista][parametros]=10;
$limite[lista][maquinas]=10;
$limite[lista][programas]=10;
$limite[lista][empresas]=10;
$limite[lista][tickets]=10;

?>
