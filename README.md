# Presença +

Sistema de controle de presença para grupos/participantes, desenvolvido em PHP com Yii 2.

## Como funciona

O projeto organiza a rotina de frequência em três pilares principais:

- Grupos: cadastramento dos grupos/turmas que receberão a presença;
- Participantes: cadastro de pessoas com matrícula, nome, idade e vínculo com um grupo;
- Presenças: registro diário do status de cada participante (presente, faltou ou falta justificada).

A aplicação é acessada por login e cada módulo exige permissão específica para o usuário. A rota inicial redireciona para a área de frequência após autenticação.

## Fluxo principal

1. O administrador faz login.
2. Cria ou edita grupos e participantes.
3. Na tela de presença, escolhe a data e o grupo.
4. Marca o status de cada participante.
5. Salva as ocorrências no banco.
6. A consulta/listagem exibe os registros por data e grupo.

## Estrutura do projeto

- `config/` — configurações do app, módulos e banco de dados;
- `controllers/` — controladores globais do sistema;
- `models/` — modelos principais: `Grupo`, `Participante`, `Presenca` e `User`;
- `modules/` — módulos do sistema:
  - `grupo/` — cadastro de grupos;
  - `participante/` — cadastro de participantes;
  - `frequencia/` — registro e consulta de presença;
- `views/` — templates e telas da interface;
- `web/` — entrada web da aplicação.

## Módulos e regras

### Grupo

O módulo de grupo permite criar, editar e excluir grupos. Cada grupo possui uma descrição. Os participantes pertencem a um grupo via `grupoId`.

### Participante

O módulo de participante cobre:

- cadastro do nome;
- matrícula como identificador único;
- vínculo com um grupo;
- idade;
- edição e exclusão.

A validação impede duplicidade de matrícula.

### Frequência

O módulo de presença tem a lógica central do sistema:

- recebe data, grupo e lista de frequências;
- valida se a data foi preenchida corretamente;
- grava uma presença por participante por data/grupo;
- permite justificar faltas quando o status for de falta justificada;
- salva em transação para evitar inconsistências.

Os status esperados são tratados como presença/ausência:

- presente;
- faltou;
- falta justificada.

## Banco e dados principais

As entidades principais são:

- `Grupo` — descrição do grupo;
- `Participante` — nome, matrícula, grupo e idade;
- `Presenca` — matrícula, data, presença, justificativa e grupo;
- `User` — autenticação e permissões do usuário.

As permissões são verificadas por meio da tabela de usuários e permissões, com métodos como `hasPermission()`.

## Como rodar

### Requisitos

- PHP
- Composer
- Banco de dados compatível com Yii 2 (configurado em `config/db.php`)
- Servidor web ou uso do ambiente do projeto

### 1) Instale as dependências

```bash
composer install
```

### 2) Configure o banco

Copie `.env.example` para `.env` e preencha `DB_DSN`, `DB_USERNAME` e `DB_PASSWORD` com os dados do seu PostgreSQL. No Docker, o host do banco é `db`; para acesso direto pela máquina, use `localhost`.

Gere uma chave e salve o resultado em `COOKIE_VALIDATION_KEY` no `.env`:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

O `.env` é ignorado pelo Git e não deve ser publicado. O Docker Compose também usa suas variáveis para configurar o banco. Em PHP 5.6, gere a chave com `openssl_random_pseudo_bytes(32)` no lugar de `random_bytes(32)`.

### 3) Inicie a aplicação

Se estiver usando o servidor embutido do Yii:

```bash
php yii serve
```

Ou use o ambiente web do projeto apontando para `web/index.php`.

### 4) Acesse a aplicação

O login fica em:

- `/site/login`
- ou `/admin/login`

Após entrar, o sistema redireciona para a tela de frequência:

- `/admin/frequencia/default/index`

## Workflow sugerido

Para usar o sistema do zero, o ideal é seguir este fluxo:

1. Criar um grupo;
2. Cadastrar participantes vinculados a esse grupo;
3. Registrar presença por data;
4. Consultar a frequência por grupo/data;
5. Ajustar justificativas quando necessário.

## Observações

- O sistema foi pensado para uso interno de controle de frequência escolar/associativo.
- A autenticação e a autorização são feitas por usuário logado e permissões específicas por módulo.
- O código é baseado em Yii 2, então a organização segue padrões do framework MVC.

## Dicas para desenvolvimento

- Sempre mantenha a base de dados alinhada com os modelos em `models/`;
- Use os módulos já existentes para manter a estrutura organizada;
- Verifique as permissões antes de expor novas telas ou ações;
- Para adicionar novos fluxos, siga o padrão de `Controller` + `Model` + `View` do Yii.
