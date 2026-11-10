# MassaTechPi

Projeto: MassaTech

Participantes: João Gabriel de Lima, João Vitor Schumaher Zangerolamo, Diogo Sebanico Santos, Murilo Dourado e Pedro Henrique Albertine Subrinho, Lorenzo Gomes, Gustavo Pereira Silva.

Identificação da ODS: ODS 8 (Trabalho decente e crescimento econômico) e ODS 9 (industria, inovação e infraestrutura).

Proposta de processos do nosso sistema:

Etapa do Cliente
O processo começa no lado do cliente, onde ele interage com a plataforma de pedidos:

Cadastro e Acesso: O cliente realiza o seu cadastro e faz o login no sistema.
Seleção: Ele faz a abertura do cardápio e realiza a escolha do pedido.
Notificação: O pedido é realizado e uma notificação é enviada via WhatsApp.
Etapa de Preparo
Após o envio do pedido, a operação interna do estabelecimento entra em ação:

Cozinha: O atendente repassa o pedido para a cozinha, que dá início ao preparo.
Verificação de Status: O sistema verifica se o pedido está pronto.
Caso não esteja, é necessário verificar o andamento com a cozinha.
Quando o pedido fica pronto, o status é atualizado para "para entrega".
Etapa de Logística e Entrega
Com o pedido pronto, o fluxo direciona à operação para a entrega:

Disponibilidade do Motoboy: O sistema verifica se há motoboy disponível. Se não houver, o sistema entra em espera ("Aguardar motoboy").

Aceitação do Pedido: Se houver motoboy disponível, ele acessa o sistema e verifica os pedidos "pronto para entrega" ', aceitando a entrega pendente.

Transferência: O atendente entrega o produto para o motoboy responsável pela entrega.

Percurso: O motoboy realiza o trajeto até o cliente.

Conclusão
O ciclo se encerra com o recebimento e fechamento financeiro:

Pagamento: O motoboy realiza o recebimento do pagamento de forma presencial.
Finalização: O motoboy finaliza a entrega em seu cadastro, o que contabiliza as taxas e um valor fixo da noite.
Fim: O processo é então concluído.
