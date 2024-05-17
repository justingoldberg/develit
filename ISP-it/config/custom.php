<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 30/01/2004
# Ultima alteração: 09/04/2004
#    Alteração No.: 002
#
# Função:
#    Configurações utilizadas pela aplicação

# Configurações para Conexão com banco de dados
$configHostMySQL="localhost";
$configUserMySQL="root";
$configPasswdMySQL="";
$configDBMySQL="isp";

# Logo da janela principal
$html[imagem][logoPrincipal]=$htmlDirImagem."/logo_devel-it.jpg";
$html[imagem][logoPequeno]=$htmlDirImagem."/logo_pequeno-devel-it.gif";
$html[imagem][logoMedia]=$htmlDirImagem."/logo_media-devel-it.jpg";
$html[imagem][logoRelatorio]=$htmlDirImagem."/logo_fBlanc.gif";

#Exibição de Módulo
$modulos['controleEstoque'] = true; //exibe link referentes a controle de estoque em cadastros, lançamentos e relatórios
?>

