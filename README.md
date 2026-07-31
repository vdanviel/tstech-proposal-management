# Visão geral:
Esse projeto é desenvolvido com Laravel e seus recursos, ele possui Docker Compose, cache com Redis e swagger com Scramble.

## Orientações:

### Rodar projeto:
Para rodar e acessar o projeto em sua máquina siga os passos:

- Rode na raiz da aplicação ``docker compose up -d``
- No terminal do conteiner será necessário rodar as migrations da aplicação com ``docker exec -it tstech php artisan migrate``
- Se quiser popular o banco de dados com as seeds da aplicação, rode o comando ``docker exec -it tstech php artisan db:seed``
- Depois disso acesse: http://localhost:8300/docs/api na sua máquina para acessar o swagger

### Entidades:
1. **User**

    A entidade autenticável usuário da aplicação, é com ela que logamos e acessamos a aplicação e ela é quem as auditorias _(Proposal)_ salvam no campo de ``actor``. A estrutura salva é ``nome:id (Lucas:15)``.

    Estrutura:
    | Coluna      | Tipo |
    | ----------- | ----------- |
    | id      | int       |
    | name   | string        |
    | password   | string        |
    | deleted_at   | timestamp        |
    | created_at   | timestamp        |
    | updated_at   | timestamp        |



2. **Client**

    A entidade que é o cliente da proposta, _(Proposal)_ que fica diretamente atrelada a ela.

    Estrutura:
    | Coluna      | Tipo |
    | ----------- | ----------- |
    | id      | int       |
    | name   | string        |
    | document   | string        |
    | deleted_at   | timestamp        |
    | created_at   | timestamp        |
    | updated_at   | timestamp        |

3. **Proposal**

    Entidade de propostas.

    Estrutura:
    | Coluna      | Tipo |
    | ----------- | ----------- |
    | id      | int       |
    | client_id   | int        |
    | monthly_value   | double        |
    | status   | enum('DRAFT','SUBMITTED','APPROVED','REJECTED','CANCELED')  
    | origin   | enum('APP','SITE','API')        |
    | version   | int        |      |
    | created_at   | timestamp        |
    | updated_at   | timestamp        |
    | deleted_at   | timestamp        |


4. **ProposalAudit**

    Entidade de auditoria. Qualquer tipo de alteração na proposta, _(Proposal)_ é gerado uma auditoria _ProposalAudit_.

    Estrutura:
    | Coluna      | Tipo |
    | ----------- | ----------- |
    | id      | int       |
    | proposal_id   | int        |
    | actor   | string        |
    | event   | enum('CREATED','UPDATED_FIELDS','STATUS_CHANGED','DELETED_LOGICAL')  
    | payload   | longtext        |      |
    | created_at   | timestamp        |
    | updated_at   | timestamp        |
    | deleted_at   | timestamp        |


### Testes:

Os testes do sistema são feito com PHPUnit. Para visualizar acesse _/tests/Feature/_.

- Transação válida/inválida:
    - Comando: ``docker exec -it tstech php artisan test tests/Feature/AvailableProposalStatusTransitionsTest.php``
    - Link: https://github.com/vdanviel/tstech-proposal-management/blob/main/tests/Feature/AvailableProposalStatusTransitionsTest.php
- Idempotência:
    - Comando: ``docker exec -it tstech php artisan test tests/Feature/IdempotencySavingTest.php``
    - Link: https://github.com/vdanviel/tstech-proposal-management/blob/main/tests/Feature/IdempotencySavingTest.php
- Conflito de versão:
    - Comando: ``docker exec -it tstech php artisan test tests/Feature/OptimisticLockingSavingTest.php``
    - Link: https://github.com/vdanviel/tstech-proposal-management/blob/main/tests/Feature/OptimisticLockingSavingTest.php
- Busca com filtros e paginação:
    - Comando: ``docker exec -it tstech php artisan test tests/Feature/PaginationFilterSearchTest.php``
    - Link: https://github.com/vdanviel/tstech-proposal-management/blob/main/tests/Feature/PaginationFilterSearchTest.php

#

### Visão de requisitos:
- Padrão de erros: 
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/config/app.php [padrão de responses]
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/app/Enums/AppErrorType.php [tipagem de erros]

- Idempotency-Key: 
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/app/Http/Middleware/IdempotencyMiddleware.php [um middleware adicionado em todas as rotas que alteram ou criam um registro, o middleware usa cache com Redis para localizar a key com expiração de 10 minutos]

- Optimistic Lock: 
    - https://github.com/vdanviel/tstech-proposal-management/tree/main/app/Traits/HasOptimisticLocking.php [um trait que utiliza o evento de ciclo de vida de models no Laravel "``updating::``"]

- Fluxo de status: 
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/app/Enums/ProposalStatus.php [um enum que dispõe de um metodo chamado ``ableToTransitionStatus()``, esse método analisa um fluxo lógico de status, e retorna `true` se a transação de status estiver correta e `false` se não estiver]

- Auditoria obrigatória: 
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/app/Http/Controllers/V1/ProposalController.php#L289 [um dos exemplos de como a auditoria _(ProposalAudit)_ sempre é registrada quando há qualquer tipo de alteração/crisção de proposta, olhe o controller de _Proposal_ e perceba que nos métodos `store()`,`update()`, `submit()`, `approve()`, `reject()`, `cancel()`, há criação em de auditoria]

- Exclusão lógica: 
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/app/Models/Proposal.php#L16 [feature de soft delete sendo acionada no model _Proposal_]
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/app/Http/Controllers/V1/ProposalController.php#L363 [no método `cancel()` é feita uma exclusão lógica _soft delete_]

- Busca avançada: 
    - https://github.com/vdanviel/tstech-proposal-management/blob/main/app/Http/Controllers/V1/ProposalController.php#L20 [busca de propostas _Proposal_ com paginação e filtragem direta de dados]
