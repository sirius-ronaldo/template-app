# Template App

Aplicação modelo para desenvolvimento de sistemas em **PHP 8.2**, baseada em uma arquitetura de **monólito tradicional**, utilizando **PHP puro**, **PDO**, **PostgreSQL**, **HTMX** e **Tailwind CSS**.

Este projeto serve como ponto de partida para novos sistemas da equipe, garantindo padronização de estrutura, organização do código e integração com o ambiente de desenvolvimento baseado em Docker.

## Objetivo

O Template App fornece uma estrutura inicial pronta para desenvolvimento, contendo:

- organização de diretórios padronizada;
- fluxo em camadas simples;
- configuração de Composer;
- configuração de Tailwind CSS;
- integração com HTMX;
- ambiente Docker para a aplicação;
- estrutura para testes automatizados;
- integração com a infraestrutura compartilhada.

## Uso rápido

1. Copie `.env.example` para `.env`.
2. Ajuste `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` e demais variáveis da aplicação.
3. Garanta que a rede Docker `development` e a stack `infraestrutura-php` estejam ativas.
4. Execute `make up`.
5. Execute `make install`.
6. Execute `make css-build`.
7. Execute `make migrate`.
8. Opcionalmente, execute `make seed-example`.
9. Execute `make validate`.

### Testes de integração

Os testes usam PostgreSQL real em um banco separado do desenvolvimento. Configure no
`.env` as variáveis `DB_DATABASE_TEST`, `DB_USERNAME_TEST`, `DB_PASSWORD_TEST` e
`DB_SCHEMA_TEST`. O PHPUnit força `APP_ENV=testing`; se esse ambiente não estiver ativo,
os testes de integração são interrompidos para evitar acesso acidental ao banco principal.

Na primeira execução, a suíte cria o schema de teste e executa as migrations Phinx no
ambiente `testing`. As execuções seguintes limpam somente o schema de teste.

```bash
make validate
```

Não é necessário executar SQL manualmente para preparar a estrutura de testes.

## Arquitetura

O projeto utiliza uma arquitetura em camadas simples.

```text
Browser
    │
    ▼
Apache
    │
Router
    │
Controller
    │
Service
    │
Repository
    │
PDO
    │
PostgreSQL
```

Cada camada possui responsabilidades bem definidas.

- **Controller**: recebe a requisição HTTP e retorna a resposta.
- **Service**: concentra as regras de negócio.
- **Repository**: concentra o acesso e a persistência dos dados.
- **View**: renderiza as páginas e fragmentos HTML.

Conforme a necessidade do projeto, outras estruturas podem ser adicionadas, como DTOs e objetos de domínio.

## Estrutura do projeto

```text
template-app/
├── app/
│   ├── Config/
│   ├── Controller/
│   ├── Exception/
│   ├── Repository/
│   ├── Service/
│   ├── Support/
│   └── View/
├── bootstrap/
├── database/
│   ├── migrations/
│   └── seeds/
├── docker/
│   ├── apache/
│   └── php/
├── public/
│   └── assets/
├── resources/
│   └── css/
├── routes/
├── storage/
├── tests/
│   ├── Integration/
│   └── Unit/
├── .env.example
├── AGENTS.md
├── compose.yaml
├── composer.json
├── Makefile
└── package.json
```

## Endpoints de exemplo

- `GET /`: página inicial com diagnóstico do banco e demonstração de interface.
- `GET /status`: fragmento HTML para demonstrar atualização parcial via HTMX.

## Tecnologias

- PHP 8.2
- Apache HTTP Server
- PostgreSQL
- PDO
- Docker
- Docker Compose
- HTMX
- Tailwind CSS
- Composer
- PHPUnit
- PHPStan
- PHP_CodeSniffer
- Phinx
- Xdebug

## Comandos principais

- `make up`
- `make down`
- `make shell`
- `make install`
- `make migrate`
- `make seed-example`
- `make bootstrap-example`
- `make test`
- `make analyse`
- `make validate`
- `make css-dev`
- `make css-build`

## Criando uma nova aplicação

### 1. Criar um novo repositório

Crie um novo repositório vazio no GitHub.

Exemplo:

```text
meu-sistema
```

### 2. Obter o template

Clone este repositório ou utilize-o como base.

```bash
git clone https://github.com/sirius-ronaldo/template-app.git meu-sistema
cd meu-sistema
```

Remova o vínculo com o repositório original e inicialize um novo repositório Git:

```bash
rm -rf .git
git init
git branch -M main
git remote add origin https://github.com/<organizacao>/meu-sistema.git
```

### 3. Ajustar a identificação do projeto

Atualize os seguintes arquivos conforme o novo sistema:

- `composer.json`
- `package.json`
- `compose.yaml`
- `AGENTS.md`
- `.env.example`

Em `compose.yaml`, ajuste ao menos o nome do projeto definido em `name:` e, se necessário, a porta publicada em `APP_PORT`.

Caso exista namespace próprio da aplicação, ajuste-o também.

### 4. Configurar a infraestrutura compartilhada

No repositório **infraestrutura-php**, realize os passos necessários para registrar a nova aplicação no ambiente de desenvolvimento.

Isso normalmente inclui:

- criar o banco de dados PostgreSQL da aplicação;
- criar usuário e permissões, quando houver estratégia de credenciais dedicadas;
- disponibilizar o projeto no ambiente de desenvolvimento;
- garantir a integração com a rede e serviços compartilhados.

Exemplo:

```sql
CREATE DATABASE meu_sistema;
CREATE USER meu_sistema_user WITH PASSWORD '<senha>';
GRANT ALL PRIVILEGES ON DATABASE meu_sistema TO meu_sistema_user;
```

Após a configuração, valide:

- PostgreSQL acessível;
- Adminer funcionando;
- Mailpit funcionando;
- container PHP inicializado;
- comunicação entre aplicação e banco de dados.

### 5. Configurar o ambiente da aplicação

Crie o arquivo `.env`:

```bash
cp .env.example .env
```

Atualize:

- credenciais do banco;
- nome do banco;
- porta da aplicação, se necessário;
- demais parâmetros específicos do projeto.

### 6. Instalar dependências e iniciar o ambiente

Utilize os comandos padronizados do projeto:

```bash
make up
make install
make css-build
```

Durante o desenvolvimento, você pode acompanhar o CSS com:

```bash
make css-dev
```

Se quiser carregar a estrutura de exemplo:

```bash
make bootstrap-example
```

### 7. Validar a instalação

Execute a validação completa:

```bash
make validate
```

Verifique também:

- acesso à aplicação;
- conexão com o banco de dados;
- funcionamento do HTMX;
- geração do CSS do Tailwind.

## Boas práticas

- Não editar diretamente o Template App para funcionalidades específicas de um sistema.
- Toda evolução estrutural deve ser avaliada para possível incorporação ao template.
- Manter os arquivos de configuração organizados conforme o padrão da equipe.
- Utilizar o `AGENTS.md` para orientar o uso do Codex no projeto.

## Fluxo de evolução

Quando uma melhoria estrutural for identificada em um projeto específico:

1. Validar a melhoria no projeto.
2. Avaliar se ela é genérica.
3. Incorporá-la ao **Template App**.
4. Atualizar a documentação.
5. Disponibilizar uma nova versão do template.

Dessa forma, o Template App evolui continuamente e permanece como referência para novos projetos.

## Documentação

A documentação completa da arquitetura, da infraestrutura e das convenções de desenvolvimento encontra-se no repositório **infraestrutura-php** e deve ser consultada sempre que houver dúvidas sobre configuração ou padrões adotados pela equipe.
