<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 05/12/2002
# Ultima alteração: 29/12/2004
#    Alteração No.: 040
#
# Função:
#    Configurações HTML

# Diretório de imagens
$htmlDirImagem="imagens";

# diretório de imagems
$htmlDirIcones=$htmlDirImagem."/icones";

global $html;

# Tipos de imagem
# Icones pequenos - para formulários
$html['imagem']['incluir'] = $htmlDirIcones."/adicionar.gif";
$html['imagem']['alterar'] = $htmlDirIcones."/martelo.gif";
$html['imagem']['excluir'] = $htmlDirIcones."/remover.gif";
$html['imagem']['listar'] = $htmlDirIcones."/livros.gif";
$html['imagem']['imprimir'] = $htmlDirIcones."/impressora.gif";
$html['imagem']['parar'] = $htmlDirIcones."/parar.gif";
$html['imagem']['pasta'] = $htmlDirIcones."/pasta.gif";
$html['imagem']['procurar'] = $htmlDirIcones."/procurar.gif";
$html['imagem']['salvar'] = $htmlDirIcones."/salvar.gif";
$html['imagem']['ativar'] = $htmlDirIcones."/ativar.gif";
$html['imagem']['desativar'] = $htmlDirIcones."/desativar.gif";
$html['imagem']['usuario'] = $htmlDirIcones."/usuario.gif";
$html['imagem']['grupo'] = $htmlDirIcones."/grupo.gif";
$html['imagem']['senha'] = $htmlDirIcones."/chave2.gif";
$html['imagem']['config'] = $htmlDirIcones."/configure.gif";
$html['imagem']['mail'] = $htmlDirIcones."/mail.gif";
$html['imagem']['suporte'] = $htmlDirIcones."/suporte.gif";
$html['imagem']['maquinas'] = $htmlDirIcones."/maquinas.gif";
$html['imagem']['home'] = $htmlDirIcones."/home.gif";
$html['imagem']['relatorio'] = $htmlDirIcones."/relatorio.gif";
$html['imagem']['vencimento'] = $htmlDirIcones."/vencimentos.gif";
$html['imagem']['cobranca'] = $htmlDirIcones."/cobranca.gif";
$html['imagem']['consultas'] = $htmlDirIcones."/consulta.gif";
$html['imagem']['info'] = $htmlDirIcones."/info.gif";
$html['imagem']['aviso'] = $htmlDirIcones."/sino.gif";
$html['imagem']['fechar'] = $htmlDirIcones."/parar.gif";
$html['imagem']['cancelar'] = $htmlDirIcones."/fechar.gif";
$html['imagem']['abrir'] = $htmlDirIcones."/pasta.gif";
$html['imagem']['comentar'] = $htmlDirIcones."/relatorio.gif";
$html['imagem']['ver'] = $htmlDirIcones."/relatorio.gif";
$html['imagem']['modulo'] = $htmlDirIcones."/modulo.gif";
$html['imagem']['parametros'] = $htmlDirIcones."/parametros.gif";
$html['imagem']['documento'] = $htmlDirIcones."/documentos.gif";
$html['imagem']['endereco'] = $htmlDirIcones."/enderecos.gif";
$html['imagem']['lancamento'] = $htmlDirIcones."/money.gif";
$html['imagem']['menulancamento'] = $htmlDirIcones."/lancamentos.gif";
$html['imagem']['desconto'] = $htmlDirIcones."/moneyv.gif";
$html['imagem']['planos'] = $htmlDirIcones."/service.gif";
$html['imagem']['servicos'] = $htmlDirIcones."/service.gif";
$html['imagem']['condominios'] = $htmlDirIcones."/condominio.gif";
$html['imagem']['pops'] = $htmlDirIcones."/pop.gif";
$html['imagem']['cidades'] = $htmlDirIcones."/cidades.gif";
$html['imagem']['statusp'] = $htmlDirIcones."/status.gif";
$html['imagem']['faturamento'] = $htmlDirIcones."/relogio.gif";
$html['imagem']['financeiro'] = $htmlDirIcones."/money.gif";
$html['imagem']['arquivo'] = $htmlDirIcones."/arquivo.gif";
$html['imagem']['baixar'] = $htmlDirIcones."/adicionar.gif";
$html['imagem']['config_sistema'] = $htmlDirIcones."/config_sistema.gif";
$html['imagem']['configuracoesgerais'] = $htmlDirIcones."/configuracoes.gif";
$html['imagem']['ocorrencia'] = $htmlDirIcones."/ocorrencias.gif";
$html['imagem']['cadastros'] = $htmlDirIcones."/cadastro.gif";
$html['imagem']['dial'] = $htmlDirIcones."/dial.gif";
$html['imagem']['dominio'] = $htmlDirIcones."/web.gif";
$html['imagem']['web'] = $htmlDirIcones."/globo.gif";
$html['imagem']['admradius'] = $htmlDirIcones."/chave.gif";
$html['imagem']['pessoa'] = $htmlDirIcones."/pessoas.gif";
$html['imagem']['forma_cobranca'] = $htmlDirIcones."/forma_cobranca.gif";
$html['imagem']['banco'] = $htmlDirIcones."/bancos.gif";
$html['imagem']['processar'] = $htmlDirIcones."/processar.gif";
$html['imagem']['extrato'] = $htmlDirIcones."/extrato.gif";
$html['imagem']['importar'] = $htmlDirIcones."/importar.gif";
$html['imagem']['fone'] = $htmlDirIcones."/telefone.gif";
$html['imagem']['contrato'] = $htmlDirIcones."/contratos.gif";
$html['imagem']['paginas'] = $htmlDirIcones."/paginas.gif";
$html['imagem']['pdf'] = $htmlDirIcones."/pdf.gif";
$html['imagem']['ps'] = $htmlDirIcones."/ps.gif";
$html['imagem']['servico_adicional'] = $htmlDirIcones."/servico_adicional.gif";
$html['imagem']['renovar'] = $htmlDirIcones."/renovar.gif";
$html['imagem']['ivr'] = $htmlDirIcones."/ivr.gif";
$html['imagem']['transferencia'] = $htmlDirIcones."/transferir.gif";
$html['imagem']['relatorio'] = $htmlDirIcones."/relatorio2.gif";
$html['imagem']['devedores'] = $htmlDirIcones."/devedor.gif";
$html['imagem']['ordemdeservico'] = $htmlDirIcones."/service.gif";
$html['imagem']['produto'] = $htmlDirIcones."/configure.gif";
$html['imagem']['aplicacao'] = $htmlDirIcones."/parametro.gif";
$html['imagem']['maodeobra'] = $htmlDirIcones."/pessoas.gif";
$html['imagem']['bases'] = $htmlDirIcones."/ivr.gif";
$html['imagem']['interfaces'] = $htmlDirIcones."/renovar.gif";
$html['imagem']['servidores'] = $htmlDirIcones."/dial.gif";
$html['imagem']['equipamento'] = $htmlDirIcones."/dial.gif";
$html['imagem']['sincronizar'] = $htmlDirIcones."/sincronizar.gif";
$html['imagem']['enviar'] = $htmlDirIcones."/forward.gif";
$html['imagem']['estorno'] = $htmlDirIcones."/estorno.gif";
$html['imagem']['abaixo'] 			= $htmlDirIcones."/abaixo.gif";
$html['imagem']['produto']			= $htmlDirIcones."/martelo.gif";
$html['imagem']['unidades']			= $htmlDirIcones."/relogio.gif";
$html['imagem']['estoque']			= $htmlDirIcones."/estoque.gif";
$html['imagem']['estoque_entrada']	= $htmlDirIcones."/entradaestoque.gif";
$html['imagem']['estoque_saida']	= $htmlDirIcones."/saidaestoque.gif";

# Icones de email
$html['imagem']['forward'] = $htmlDirIcones."/forward.gif";
$html['imagem']['alias'] = $htmlDirIcones."/alias.gif";
$html['imagem']['quota'] = $htmlDirIcones."/quota.gif";
$html['imagem']['emailconfig'] = $htmlDirIcones."/emailconfig.gif";
$html['imagem']['autoresposta'] = $htmlDirIcones."/autoresposta.gif";
$html['imagem']['prioridade'] = $htmlDirIcones."/prioridades.gif";

# Setas de navegação
$html['imagem']['setaprimeira'] = $htmlDirImagem."/seta-primeira.gif";
$html['imagem']['setadireita'] = $htmlDirImagem."/seta-direita.gif";
$html['imagem']['setaesquerda'] = $htmlDirImagem."/seta-esquerda.gif";
$html['imagem']['setaultima'] = $htmlDirImagem."/seta-ultima.gif";

# Ocorrências
$html['imagem']['historico'] = $htmlDirIcones."/history.gif";

# Icones grandes - para menus
$html['imagem']['status'] = $htmlDirImagem."/status.gif";
$html['imagem']['cadastro'] = $htmlDirImagem."/cadastros.gif";
$html['imagem']['configuracoes'] = $htmlDirImagem."/configure.gif";
$html['imagem']['tipo_pessoa'] = $htmlDirImagem."/tipos_pessoas.gif";
$html['imagem']['lancamentos'] = $htmlDirImagem."/lancamentos.gif";
$html['imagem']['consulta'] = $htmlDirImagem."/consulta.gif";
$html['imagem']['vencimentos'] = $htmlDirImagem."/vencimentos.gif";
$html['imagem']['cobrancas'] = $htmlDirImagem."/cobranca.gif";
$html['imagem']['enderecos'] = $htmlDirImagem."/enderecos.gif";
$html['imagem']['documentos'] = $htmlDirImagem."/documentos.gif";
$html['imagem']['pop'] = $htmlDirImagem."/pop.gif";
$html['imagem']['cidade'] = $htmlDirImagem."/cidades.gif";
$html['imagem']['status'] = $htmlDirImagem."/status.gif";
$html['imagem']['faturamentos'] = $htmlDirImagem."/faturamento.gif";
$html['imagem']['arquivos'] = $htmlDirImagem."/arquivo.gif";
$html['imagem']['configuracoes_sistema'] = $htmlDirImagem."/config_sistema.gif";
$html['imagem']['ocorrencias'] = $htmlDirImagem."/ocorrencias.gif";
$html['imagem']['radius'] = $htmlDirImagem."/radius.gif";
$html['imagem']['pessoas'] = $htmlDirImagem."/pessoas.gif";
$html['imagem']['grupos'] = $htmlDirImagem."/usuarios.gif";
$html['imagem']['usuarios'] = $htmlDirImagem."/pessoal.gif";
$html['imagem']['forma_cobrancas'] = $htmlDirImagem."/forma_cobranca.gif";
$html['imagem']['bancos'] = $htmlDirImagem."/bancos.gif";
$html['imagem']['dominios'] = $htmlDirImagem."/web.gif";
$html['imagem']['contratos'] = $htmlDirImagem."/contratos.gif";
$html['imagem']['servicos_adicionais'] = $htmlDirImagem."/servico_adicional.gif";
$html['imagem']['relatorios'] = $htmlDirImagem."/relatorios.gif";

# para preenchimento
$html['imagem']['black'] = $htmlDirImagem."/black.gif";


?>
