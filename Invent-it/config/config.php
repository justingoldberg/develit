<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/01/2004
# Ultima alteração: 10/01/2005
#    Alteração No.: 004
#
# Função:
#    Configurações utilizadas pela aplicação

# Configurações da Aplicação
$configAppName="Devel-IT - .:Invent-IT:.";
$configAppVersion="Versão 0.5";
	
# Configurações para Conexão com banco de dados
$configHostMySQL="localhost";
$configUserMySQL="root";
$configPasswdMySQL="";
$configDBMySQL="ticket";

# Interação com ISP      -- Yuri 06/04/2010
$_REQUEST['integraIsp']=false;

# Configurações de cor
$corFundo="#ffffff";
$corBorda="#223366";

#############################################
# Limites de listagens e paginações
$limite[lista][usuarios]=10;
$limite[lista][grupos]=10;
$limite[lista][parametros]=10;
$limite[lista][maquinas]=10;
$limite[lista][programas]=10;
$limite[lista][empresas]=10;
$limite[lista][tickets]=10;

?>
