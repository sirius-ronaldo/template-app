# Roteiro caixa-preta — Sprint 13

## Objetivo

Homologar o padrão visual reutilizável, os temas, a navegação responsiva e a
preservação dos fluxos funcionais existentes.

## Pré-condições

1. Aplicação iniciada com `make up`.
2. CSS gerado com `make css-build`.
3. Banco e migrations preparados.
4. Usuário ADMIN ativo disponível para os cenários autenticados.
5. Navegador com ferramentas de desenvolvimento para simular larguras.

## 1. Login e layout público

1. Acessar `/login` sem sessão autenticada.
2. Confirmar card centralizado, nome configurado e marca textual ou imagem.
3. Confirmar labels associados aos campos Login e Senha.
4. Navegar pelos controles usando somente Tab e Shift+Tab.
5. Enviar credenciais inválidas.
6. Confirmar mensagem de erro textual, legível e anunciada como alerta.
7. Repetir em 320 px, 390 px e desktop.

Resultado esperado: não há overflow horizontal, perda de foco, texto ilegível
ou dependência de identidade do SOL.

## 2. Shell autenticado no desktop

1. Autenticar como ADMIN e acessar `/Home`.
2. Confirmar sidebar de 300 px, topbar sticky e conteúdo centralizado.
3. Confirmar usuário conectado e logout no canto direito.
4. Navegar por Início, Minha senha e grupo Configurações.
5. Confirmar `aria-current` visual no item correspondente à rota.
6. Expandir e recolher Configurações usando teclado.
7. Confirmar que o estado `aria-expanded` acompanha a apresentação.
8. Acionar Sair e confirmar retorno ao login.

Resultado esperado: navegação, item ativo e logout POST funcionam sem alterar os
fluxos de autenticação e autorização.

## 3. Drawer mobile

1. Simular largura de 390 px em `/Home`.
2. Acionar o botão Abrir menu por teclado.
3. Confirmar abertura do drawer e exibição do backdrop.
4. Pressionar Tab repetidamente e confirmar que o foco permanece no drawer.
5. Pressionar Escape e confirmar fechamento e retorno do foco ao botão.
6. Reabrir e fechar pelo backdrop.
7. Reabrir e selecionar um link de navegação.

Resultado esperado: todos os meios fecham o drawer, sem foco perdido ou
rolagem da página ao fundo.

## 4. Temas claro e escuro

1. Em `/Home`, alternar para o tema claro.
2. Recarregar a página e confirmar que o tema claro permanece.
3. Acessar uma tela administrativa e confirmar a mesma preferência.
4. Alternar para o tema escuro, recarregar e confirmar persistência.
5. Verificar textos, campos, bordas, badges, flashes e foco nos dois temas.

Resultado esperado: a preferência salva tem precedência sobre o tema inicial e
nenhum controle fica ilegível.

## 5. Home demonstrativa e HTMX

1. Confirmar métricas, filtros, diagnóstico, tabela e badges.
2. Acionar Atualizar fragmento.
3. Confirmar atualização somente do painel de status.
4. Em 320 px, confirmar cards em uma coluna.
5. Na tabela, confirmar rolagem horizontal interna sem ampliar o viewport.

Resultado esperado: todos os componentes permanecem utilizáveis e o HTMX
continua funcionando.

## 6. Administração

Executar ao menos um fluxo de listagem e formulário em cada área:

- Segurança;
- Usuários;
- Grupos;
- Permissões;
- Tipos de aplicação;
- Aplicações;
- Grants;
- Manifesto.

Para cada área:

1. Confirmar item ativo no menu.
2. Usar filtro HTMX, quando disponível.
3. Confirmar que a URL e a tabela são atualizadas.
4. Abrir um formulário e conferir labels, foco e mensagens de erro.
5. Confirmar badges textuais de status.
6. Confirmar rolagem interna em tabelas no mobile.

Resultado esperado: não há alteração de campos, rotas, CSRF, permissões ou
regras funcionais.

## 7. Acessibilidade e redução de movimento

1. Usar o link Ir para o conteúdo por teclado.
2. Confirmar foco visível em links, botões, campos, selects e summaries.
3. Ativar `prefers-reduced-motion: reduce` no navegador.
4. Confirmar que drawer, hover e demais transições deixam de produzir movimento
   perceptível.
5. Verificar que status e erros possuem texto, não apenas cor.

## Registro da homologação

- Data:
- Responsável:
- Navegador e versão:
- Resolução:
- Resultado: pendente
- Evidências:
- Problemas encontrados:
