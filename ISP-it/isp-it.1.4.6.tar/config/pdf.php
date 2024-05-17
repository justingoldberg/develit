<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 12/04/2004
# Ultima alteração: 12/04/2004
#    Alteração No.: 001
#
# Função:
#    Configurações utilizadas pela aplicação

# Configurações da Aplicação
$configPDF[layout]='portait';
$configPDF[linhas]='40';
$configPDF[]='';



# define o caminho para a classe padrão FPDF
$pathFPDF= "./class/";

# CONSTANTE utilizada para prover as fontes utilizadas na geração do arquivo PDF
define("FPDF_FONTPATH", $pathFPDF."fpdf/font/");

# classe padrão FPDF
require($pathFPDF.'fpdf/fpdf.php');

?>
