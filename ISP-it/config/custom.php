<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 30/01/2004
# Ultima altera��o: 09/04/2004
#    Altera��o No.: 002
#
# Fun��o:
#    Configura��es utilizadas pela aplica��o

# Configura��es para Conex�o com banco de dados
$configHostMySQL="localhost";
$configUserMySQL="root";
$configPasswdMySQL="";
$configDBMySQL="isp";

# Logo da janela principal
$html[imagem][logoPrincipal]=$htmlDirImagem."/logo_devel-it.jpg";
$html[imagem][logoPequeno]=$htmlDirImagem."/logo_pequeno-devel-it.gif";
$html[imagem][logoMedia]=$htmlDirImagem."/logo_media-devel-it.jpg";
$html[imagem][logoRelatorio]=$htmlDirImagem."/logo_fBlanc.gif";

#Exibi��o de M�dulo
$modulos['controleEstoque'] = true; //exibe link referentes a controle de estoque em cadastros, lan�amentos e relat�rios
?>

