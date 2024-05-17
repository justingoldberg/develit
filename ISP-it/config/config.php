<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 20/05/2003
# Ultima alteração: 20/03/2004
#    Alteração No.: 049
#
# Função:
#    Configurações utilizadas pela aplicação

# Configurações da Aplicação
$configAppName = "Devel-IT - .:ISP IT:.";
$configAppVersion = "Versão 1.4.6";
	
# Cadastrar qual é o diretório que contêm os arquivos .sql, que o instalador lerá.
$configDirSql = "sql2";

# Configurações de cor
$corFundo="#ffffff";
$corBorda="#223366";

#############################################
# Limites de listagens e paginações
$limite['lista']['usuarios']=10;
$limite['lista']['grupos']=10;
$limite['lista']['cidades']=20;
$limite['lista']['tipo_documentos']=10;
$limite['lista']['tipo_enderecos']=10;
$limite['lista']['tipo_pessoas']=10;
$limite['lista']['tipo_cobranca']=10;
$limite['lista']['vencimentos']=10;
$limite['lista']['unidades']=10;
$limite['lista']['modulos']=10;
$limite['lista']['parametros']=10;
$limite['lista']['parametros_bancos']=10;
$limite['lista']['parametros_config']=10;
$limite['lista']['pessoas']=10;
$limite['lista']['bancos']=10;
$limite['lista']['forma_cobranca']=10;
$limite['lista']['servicos']=15;
$limite['lista']['servicos_parametros']=10;
$limite['lista']['status_servicos']=10;
$limite['lista']['pop']=10;
$limite['lista']['documentos_gerados']=20;
$limite['lista']['planos_documentos_gerados']=5;
$limite['lista']['faturamentos']=10;
$limite['lista']['cobrancas']=10;
$limite['lista']['arquivosremessa']=10;
$limite['lista']['arquivosretorno']=10;
$limite['lista']['radius_grupos']=10;
$limite['lista']['radius_usuarios']=10;
$limite['lista']['radius_servicos']=10;
$limite['lista']['dominios']=10;
$limite['lista']['maquinas']=10;
$limite['lista']['email']=10;
$limite['lista']['alias']=10;
$limite['lista']['forward']=10;
$limite['lista']['ocorrencias']=5;
$limite['lista']['prioridades']=10;
$limite['lista']['extrato']=10;
$limite['lista']['contratos']=10;
$limite['lista']['tipo_servico_adicional']=10;
$limite['lista']['notafiscal']=10;
$limite['lista']['produtos'] = 15;
$limite['lista']['produtoComposto'] = 15;
$limite['lista']['produtosEstoque'] = 15;
$limite['lista']['entradaNF'] = 15;
$limite['lista']['requisicao'] = 15;
$limite['lista']['ordemServico'] = 15;
$limite['lista']['naturezaPrestacao'] = 15;

# Cores
$cores['Verde']="#03aa03";
$cores['Vermelho']="#AA0000";
$cores['Bege']="#cccc43";
$cores['Azul']="#001099";
$cores['Laranja']="#E89600";
$cores['Amarelo']="#e0d500";

# template
$template['dir']="templates/";

#Status PBC
$statusPBC = array( 'P'=> 'Pendente', 'B'=> 'Baixado', 'C'=> 'Cancelado');
$imagensPBC = array( 'P'=> 'desativar', 'B'=> 'ativar', 'C'=> 'cancelar');

#Configuracoes para o banco Sicoob fornecidos pelo banco
$bancoSicoob["modalidade"]  = "02";
?>
