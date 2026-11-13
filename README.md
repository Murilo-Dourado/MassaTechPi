# MassaTeech

Site para pizzaria com **cardápio online** e **acompanhamento da localização do entregador**. O projeto surgiu de um problema real: o dono da pizzaria tinha dificuldade para enviar o cardápio aos clientes pelo WhatsApp.

Esse Projeto por enquanto é um Protótipo, mas a entrega oficial do site para o empreendedor será no 3º Semestre.

**Projeto:** MassaTechPI, 2º Semestre

## Participantes

- João Gabriel de Lima
- João Vitor Schumaher Zangerolamo
- Diogo Sebanico Santos
- Murilo Dourado
- Pedro Henrique Albertine Subrinho
- Lorenzo Gomes
- Gustavo Pereira Silva

## Objetivos de Desenvolvimento Sustentável (ODS)

- **ODS 8:** Trabalho decente e crescimento econômico
- **ODS 9:** Indústria, inovação e infraestrutura

## O que o sistema faz

- Exibe o cardápio da pizzaria diretamente no site.
- Permite ao cliente acompanhar a localização do entregador durante a entrega.
- Organiza o fluxo do pedido, do cadastro do cliente até a finalização da entrega (detalhado abaixo).

## Tecnologias

| Camada | Tecnologias |
| --- | --- |
| Front-end | HTML, CSS e JavaScript |
| Back-end | PHP |
| Banco de dados | MySQL |

## Status

🚧 **Em desenvolvimento.**

- [x] Front-end (HTML e CSS): Feito
- [x] Documentação: Feito
- [ ] JavaScript: a implementar
- [x] Back-end (PHP e MySQL): Feito

## Fluxo do sistema

### Etapa do cliente
1. **Cadastro e acesso:** o cliente se cadastra e faz login.
2. **Seleção:** abre o cardápio e escolhe o pedido.
3. **Notificação:** o pedido é realizado e uma notificação é enviada via WhatsApp.

### Etapa de preparo
1. **Cozinha:** o atendente repassa o pedido para a cozinha, que inicia o preparo.
2. **Verificação de status:** o sistema verifica se o pedido está pronto. Se não estiver, é preciso verificar o andamento com a cozinha.
3. Quando fica pronto, o status é atualizado para "para entrega".

### Etapa de logística e entrega
1. **Disponibilidade do motoboy:** o sistema verifica se há motoboy disponível. Se não houver, aguarda.
2. **Aceitação do pedido:** o motoboy acessa o sistema, vê os pedidos "prontos para entrega" e aceita a entrega pendente.
3. **Transferência:** o atendente entrega o produto ao motoboy responsável.
4. **Percurso:** o motoboy realiza o trajeto até o cliente.

### Conclusão
1. **Pagamento:** o motoboy recebe o pagamento de forma presencial.
2. **Finalização:** o motoboy finaliza a entrega em seu cadastro, o que contabiliza as taxas e um valor fixo da noite.
3. O processo é concluído.

## Como executar

Em breve. Este trecho será atualizado quando o JavaScript estiver estabelecido.
