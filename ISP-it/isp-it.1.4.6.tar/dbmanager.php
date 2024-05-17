<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 20/05/2003
# Ultima alteração: 03/02/2004
#    Alteração No.: 041
#
# Função:
#    Página principal (index) da aplicação

# Carregar Sessoes para informações de usuario
session_start();
session_register("sessLogin","sessCadastro");

# Verifica se sessão deve ser matada
if($modulo=='logoff' && session_is_registered("sessLogin")) {
	session_destroy();
	$sessLogin[login]='';
	$sessSenha[senha]='';
}


# Verifica se é necessário zerar a sessão de cadastro
if($acao=='novo') {
	$sessCadastro='';
	$acao='adicionar';
}


# Carregar configurações
include('config/config.php');
include('config/db.php');
include('config/html.php');
include('config/cobranca.php');
include('config/pessoas.php');
include('config/arquivo.php');
include('config/radius.php');
include('config/manager.php');
include('config/ocorrencias.php');
include('config/pdf.php');
include('config/custom.php');

# Variáveis globais
global $configHostMySQL, $configUserMySQL, $configPasswdMySQL, $configDBMySQL, $configAppName, $configAppVersion, $corFundo, $corBorda;

# Carregar funções
include('includes/db.php');
include('includes/html.php');
include('includes/data.php');
include('includes/arquivos.php');
include('includes/arquivos_layout.php');

# Validação de Formulários
include('includes/valida_form.php');
include('includes/valida_documentos.php');

# Funções de E-mail
include('includes/mail.php');

# Funções de Usuario
include('includes/usuarios.php');

# Funções de Grupos
include('includes/grupos.php');
include('includes/usuarios_grupos.php');

# Paginador
include('includes/paginador.php');

# Formulários
include('includes/form.php');

# HOME / Principal
include('includes/home.php');

# Configurações
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
include('includes/servicos_parametros.php');
include('includes/status_servicos.php');
include('includes/condominios.php');

# Cadastros
include('includes/cadastros.php');
include('includes/pessoas.php');
include('includes/pessoa_tipo.php');
include('includes/form_pessoas.php');
include('includes/enderecos.php');
include('includes/documentos.php');
include('includes/pop.php');
include('includes/pop_cidade.php');

# Lançamentos
include('includes/lancamentos.php');
include('includes/planos.php');
include('includes/planos_servicos.php');
include('includes/planos_servicos_descontos.php');
include('includes/planos_servicos_adicionais.php');
include('includes/manutencao.php');

# Formulas e calculos
include('includes/planos_formulas.php');

# Faturamento
include('includes/faturamento.php');
include('includes/faturamento_clientes.php');
include('includes/faturamento_documentos_gerados.php');
include('includes/faturamento_planos_documentos_gerados.php');
include('includes/faturamento_servicos_planos_documentos_gerados.php');

# Arquivos Remessa
include('includes/arquivos_remessa.php');

# Arquivos Retorno
include('includes/arquivos_retorno.php');

# Contas a Receber
include('includes/contas_receber.php');

# Exportação de dados
include('includes/exportacao.php');

# Administração de Configurações
include('includes/administracao.php');
include('includes/administracao_limites.php');
include('includes/administracao_modulos.php');
include('includes/administracao_radius.php');
include('includes/administracao_dominio.php');
include('includes/administracao_mail.php');

# Radius
include('includes/radius.php');
include('includes/radius_grupos.php');
include('includes/radius_usuarios.php');

# Radius - Usuários Pessoas
include('includes/radius_usuarios_pessoas.php');

# Servicos por Grupo - Radius
include('includes/radius_servicos.php');

# Dominios
include('includes/dominios.php');
include('includes/dominios_servicos_planos.php');
include('includes/dominios_servicos_parametros.php');

# Emails
include('includes/email.php');
include('includes/email_config.php');
include('includes/email_dominios.php');
include('includes/email_alias.php');
include('includes/email_forward.php');
include('includes/email_autoreply.php');

# Ocorrências
include('includes/ocorrencias.php');
include('includes/ocorrencias_comentarios.php');
include('includes/prioridades.php');

# Manager
include('includes/manager.php');

# DBManager
include('includes/dbmanager/dbmanager.php');
include('includes/dbmanager/importacao.php');


### CARRETAMENTO DE VALORES PARA SESSION - Validação
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


###########################################
# Mostrar cabeçalhos
html_header($configAppName, $configAppVersion);
###########################################

$data=dataSistema();

### Javascript ###
include('includes/javascript.php');
### Javascript ###


###########################################
# Checar conexão com banco de dados
if(!$conn)
{
        aviso("Conexão com MySQL", "Erro na conexão com Banco de Dados MySQL", "#", 500);
}
if(!$db)
{
        aviso("Banco de Dados", "Erro ao selecionar banco de dados", "#", 500);
}
# fim de conexão com banco de dados
###########################################


# Montar tela -  Quadro Principal

# Mostrar menu principal
novaTabela("ISP-IT DBManager - $sessLogin[login]", "center", 760, 0, 2, 1, $corFundo, $corBorda, 0);
	htmlAbreLinha($corFundo);
		htmlAbreColuna(760, 'center', $corFundo, 0, 'normal');
		
			# verificar se usuario está conectado
			#if(!$sessLogin[login] || !$sessLogin[senha] || $modulo=='login' || $modulo=='logoff') {
			if($modulo=='login' || $modulo=='logoff' || ($sessLogin && !checaLogin($sessLogin, $modulo, $sub, $acao, $registro))) {
				# Usuário não está conectado
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
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						htmlFechaColuna();
					htmlFechaLinha();
				fechaTabela();
				
			} #fecha verificação de login
			
			htmlFechaColuna();
	htmlFechaLinha();
fechaTabela();
# Fecha separação

htmlFechaColuna();
htmlFechaLinha();
fechaTabela();
# Fecha menu principal


###########################################
# Rodapé
html_footer($configAppName." - ".$configAppVersion);
###########################################

?>
