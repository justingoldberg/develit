<?
################################################################################
#       Criado por: Josщ Roberto Kerne - joseroberto@kerne.org
#  Data de criaчуo: 20/05/2003
# Ultima alteraчуo: 15/04/2004
#    Alteraчуo No.: 047
#
# Funчуo:
#    Pсgina principal (index) da aplicaчуo
#
ob_start();
# Carregar Sessoes para informaчѕes de usuario
session_start();
session_register("sessLogin","sessCadastro");

# Verifica se sessуo deve ser matada
if($modulo=='logoff' && session_is_registered("sessLogin")) {
	session_destroy();
	$sessLogin[login]='';
	$sessSenha[senha]='';
}

# Verifica se щ necessсrio zerar a sessуo de cadastro
//if( $acao == 'novo' ) {
//	$sessCadastro = '';
//	$acao = 'adicionar';
//}
# Verifica se щ necessсrio zerar a sessуo de cadastro
if( strstr( $acao, 'novo') ) {
	$sessCadastro='';
	$acao=str_replace( 'novo','adicionar', $acao );
}
if( $acao == 'procurar' ) {
	$sessCadastro='';
}
# Carregar configuraчѕes
include('config/index.php');

# Variсveis globais
global $configHostMySQL, $configUserMySQL, $configPasswdMySQL, $configDBMySQL, $configAppName, $configAppVersion, $corFundo, $corBorda;

# Carregar funчѕes
include('includes/db.php');
include('includes/html.php');
include('includes/menu.php');
include('includes/data.php');
include('includes/arquivos.php');
include('includes/arquivos_layout.php');
include('includes/parametros_config.php');
include('includes/formtemplates.php');

# Validaчуo de Formulсrios
include('includes/valida_form.php');
include('includes/valida_documentos.php');

# Classes de Banco de Dados
include('class/InterfaceBD.php');
include('class/BDIT.php');

//# ajax
//include_once("class/xajax/xajax.inc.php");

# Funчѕes de E-mail
include('includes/mail.php');

# Funчѕes de Usuario
include('includes/usuarios.php');

# Funчѕes de Grupos
include('includes/grupos.php');
include('includes/usuarios_grupos.php');

# Paginador
include('includes/paginador.php');

# Formulсrios
include('includes/form.php');

# HOME / Principal
include('includes/home.php');

# Configuraчѕes
include('includes/configuracoes.php');
include('includes/cidades.php');
include('includes/tipo_documentos.php');
include('includes/tipo_enderecos.php');
include('includes/tipo_pessoas.php');
include('includes/tipo_cobranca.php');
include('includes/vencimentos.php');
include('includes/unidades.php');
include('includes/modulos.php');
include('includes/parametros.php');
include('includes/parametros_modulos.php');
include('includes/bancos.php');
include('includes/forma_cobranca.php');
include('includes/servicos.php');
include('includes/grupos_servicos.php');
include('includes/servicos_gruposservicos.php');
include('includes/servicos_parametros.php');
include('includes/servicos_contratos.php');
include('includes/status_servicos.php');
include('includes/condominios.php');
include('includes/servico_adicional_tipos.php');
include('includes/servidores.php');
include('includes/interfaces.php');
include('includes/bases.php');

# Cadastros
include('includes/cadastros.php');
include('includes/pessoas.php');
include('includes/pessoa_tipo.php');
include('includes/form_pessoas.php');
include('includes/enderecos.php');
include('includes/documentos.php');
include('includes/pop.php');
include('includes/pop_cidade.php');
include('includes/PopPessoaTipo.php');

# Lanчamentos
include('includes/lancamentos.php');
include('includes/planos.php');
include('includes/planos_servicos.php');
include('includes/planos_servicos_parametros.php');
include('includes/planos_servicos_descontos.php');
include('includes/planos_servicos_adicionais.php');
include('includes/manutencao.php');
include('includes/notafiscal.php');
include('includes/itens_nota_fiscal.php');
include('includes/desconto_nota_fiscal.php');
include('includes/NaturezaPrestacao.php');
include('includes/NotaFiscalFaturaServico.php');
include('includes/Itens_NFServico.php');

#Ordem de Servico
include('includes/ordemdeservico.php');
include('includes/ordemdeservicodetalhe.php');
include('includes/produto.php');
include('includes/maodeobra.php');
include('includes/aplicacao.php');

# Formulas e calculos
include('includes/planos_formulas.php');

# Faturamento
include('includes/faturamento.php');
include('includes/faturamento_clientes.php');
include('includes/faturamento_documentos_gerados.php');
include('includes/faturamento_planos_documentos_gerados.php');
include('includes/faturamento_servicos_planos_documentos_gerados.php');
include('includes/parametros_bancos.php');

# Arquivos Remessa
include('includes/arquivos_remessa.php');
include('class/PracaSicredi.php');

# Boleto Bancсrio
include('includes/boletos_bancarios.php');
include('includes/boletos_bancarios_verso.php');
include('class/boletoIT/BoletoIT.php');
include('class/boletoIT/BoletoModelo.php');
include('class/boletoIT/BoletoCEF.php');
include('class/boletoIT/BoletoSicoob.php');

# Arquivos Retorno
include('includes/arquivos_retorno.php');

# Contas a Receber
include('includes/contas_receber.php');

/* Contra Partida*/
include('includes/contra_partida.php');
include('includes/contra_partida_padrao.php');

# Exportaчуo de dados
include('includes/exportacao.php');

# Administraчуo de Configuraчѕes
include('includes/administracao.php');
include('includes/administracao_limites.php');
include('includes/administracao_modulos.php');
include('includes/administracao_radius.php');
include('includes/administracao_dominio.php');
include('includes/administracao_mail.php');
include('includes/administracao_suporte.php');
include('includes/administracao_maquinas.php');
include('includes/servicos_ivr.php');

# Radius
include('includes/radius.php');
include('includes/radius_online.php');
include('includes/radius_grupos.php');
include('includes/radius_usuarios.php');
include('includes/radius_usuarios_telefones.php');

# Radius - Usuсrios Pessoas
include('includes/radius_usuarios_pessoas.php');

# Servicos por Grupo - Radius
include('includes/radius_servicos.php');

# Dominios
include('includes/dominios.php');
include('includes/dominios_servicos_planos.php');
include('includes/dominios_servicos_parametros.php');

# Maquinas
include('includes/maquinas.php');

#Maquinas Suporte
include('includes/maquinas_suporte.php');

# Suporte
include('includes/suporte.php');

# Emails
include('includes/email.php');
include('includes/email_config.php');
include('includes/email_dominios.php');
include('includes/email_alias.php');
include('includes/email_forward.php');
include('includes/email_autoreply.php');

# Manager
include('includes/manager.php');

# Ocorrъncias
include('includes/ocorrencias.php');
include('includes/ocorrencias_comentarios.php');
include('includes/prioridades.php');

# Contratos
include('includes/contratos.php');
include('includes/contratos_paginas.php');
include('includes/contratos_pessoas_tipos.php');
include('includes/contratos_servicos_planos.php');

# PDF
include('includes/pdf.php');
require_once('class/HTML_ToPDF.php');
include('class/ocorrencias2PDF.php');
# HTML Template
include('includes/template.php');

# Geraчуo de relatѓrios 
include('includes/report.php');

# Relatorios
include('includes/relatorios/index.php');

# Consultas
include('includes/consultas/index.php');

#Equipamentos
include('includes/equipamento.php');
include('includes/equiptotipo.php');
include('includes/equiptocaracteristica.php');

# Debito automatico
include('includes/tipocarteira.php');
include('includes/debitoautomatico.php');
include('class/autorizacao.php');
include('class/relatorio2PDF.php');

# Nota Fiscal
include('class/NotaFiscal.php');
include('class/NotaFiscalServico.php');
include('class/ImpressaoNFServico.php');
include('class/ImpressaoNF.php');
include('class/ImpressaoNFModeloB.php');
include('class/ImpressaoNFModeloC.php');
include('class/ImpressaoNFModeloD.php');
include('class/ImpressaoNFModeloE.php');
include('class/ImpressaoNFModeloF.php');
#include('class/InterfaceBD.php');
//include('class/BDIT.php');
include('class/BeanDescontoNF.php');
include('class/BeanAcrescimoNF.php');
include('class/BeanItemNF.php');
include('class/ItensNFServico.php');
include('includes/impostos_nf.php');

#Pagamento Avulso
include('includes/pagamento_avulso.php');
include('includes/planoDeContas.php');
include('includes/planoDeContasSub.php');
include('includes/planoDeContasDetalhes.php');
include('includes/centroDeCusto.php');
include('includes/centroDeCustoPrevisao.php');
include('includes/contas_a_pagar.php');

#tipos de impostos
include ('includes/tipos_impostos.php');
include ('includes/impostos_pessoas.php');

#Fluxo de Caixa
include('includes/FluxoDeCaixa.php');

#Controle de Estoque
include_once('includes/produtos.php');
include_once('includes/produtosFracionado.php');
include_once('includes/produtoComposto.php');
include_once('includes/itemProdutoComposto.php');
include_once('includes/produtosEstoque.php');
include_once('includes/itensMovimentoEstoque.php');
include_once('includes/MovimentoEstoque.php');
include_once('includes/EntradaNotaFiscal.php');
include_once('includes/RequisicaoRetorno.php');
include_once('includes/OrdemServico.php');

# Ticket-IT - Empresas
include_once('includes/empresas.php');

$data = dataSistema();

### CARREGAMENTO DE VALORES PARA SESSION - Validaчуo
if($matValida) {
	$sessLogin=usuariosValidaForm($matValida);
}
###

### CARRETAMENTO DE VALORES PARA SESSSION
if($modulo=='cadastros') {
	if(!$matriz[bntConfirmar]) {
		$matriz[bntConfirmar]='';
	}
	$sessCadastro=alimentaForm($matriz, $sessCadastro);
	$matriz=$sessCadastro;
}
###


###########################################
# Conectar com banco de dados
$conn=conectaMySQL($configHostMySQL, $configUserMySQL, $configPasswdMySQL);
$db=selecionaDB($configDBMySQL, $conn);
###########################################

### Javascript ###
include('includes/javascript.php');

#############################################
//# Instancia o Xajax
//$xajax = new xajax();
//$xajax->registerFunction("produtosSetCampoUnidade");
//$xajax->processRequests();
//#############################################

###########################################
# Mostrar cabeчalhos
html_header($configAppName, $configAppVersion);
###########################################


###########################################
# Checar conexуo com banco de dados
if(!$conn){
	aviso("Conexуo com MySQL", "Erro na conexуo com Banco de Dados MySQL", "#", 500);
}
if(!$db){
	aviso("Banco de Dados", "Erro ao selecionar banco de dados", "#", 500);
}
# fim de conexуo com banco de dados
##########################################

# Montar tela -  Quadro Principal
# Mostrar menu principal
novaTabela("$configAppName - $configAppVersion - $sessLogin[login]", "center", 760, 0, 2, 1, $corFundo, $corBorda, 0);
	htmlAbreLinha($corFundo);
		htmlAbreColuna(760, 'center', $corFundo, 0, 'normal');
		
			# verificar se usuario estс conectado
			#if(!$sessLogin[login] || !$sessLogin[senha] || $modulo=='login' || $modulo=='logoff') {
			if($modulo=='login' || $modulo=='logoff' || ($sessLogin && !checaLogin($sessLogin, $modulo, $sub, $acao, $registro))) {
				# Usuсrio nуo estс conectado
				validacao($sessLogin, $modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Menu Principal
				htmlAbreTabelaSH("center", 760, 0, 0, 0, $corFundo, $corBorda, 2);
					htmlAbreLinha($corFundo);
						htmlAbreColuna('100%', 'center', $corFundo, 0, 'normal');
						if($sessLogin && checaLogin($sessLogin, $modulo, $sub, $acao, $registro)) menuPrincipal('usuario');
						else menuPrincipal('anonimo');
						htmlFechaColuna();
					htmlFechaLinha();
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 3, 'normal');
					
					htmlAbreLinha($corFundo);
						htmlAbreColuna('100%', 'center', $corFundo, 3, 'normal');
					
						## Verificar se acesso щ com ou sem login
						if($sessLogin) {
							# Menu de visualizaчуo resumida - principal
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						}
						else {
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						}
					
						htmlFechaColuna();
					htmlFechaLinha();
				fechaTabela();
				
			} #fecha verificaчуo de login
			
		htmlFechaColuna();
	htmlFechaLinha();
fechaTabela();
# Fecha separaчуo

htmlFechaColuna();
htmlFechaLinha();
fechaTabela();
# Fecha menu principal


###########################################
# Rodapщ
html_footer($configAppName." - ".$configAppVersion);
###########################################

?>