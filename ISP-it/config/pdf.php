<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 12/04/2004
# Ultima altera��o: 12/04/2004
#    Altera��o No.: 001
#
# Fun��o:
#    Configura��es utilizadas pela aplica��o

# Configura��es da Aplica��o
$configPDF[layout]='portait';
$configPDF[linhas]='40';
$configPDF[]='';



# define o caminho para a classe padr�o FPDF
$pathFPDF= "./class/";

# CONSTANTE utilizada para prover as fontes utilizadas na gera��o do arquivo PDF
define("FPDF_FONTPATH", $pathFPDF."fpdf/font/");

# classe padr�o FPDF
require($pathFPDF.'fpdf/fpdf.php');

?>
