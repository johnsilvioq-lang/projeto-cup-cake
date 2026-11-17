Projeto Integrador Transdisciplinar em Engenharia de Software II
nesse projeto foi feito usando PHP em sua maioria.
usando back end no MySQL. 
MySQL com opção de cadastro de conta para integração do site.
foi hospedado no Infinityfree para demonstração rodando online.
após testes feito uma correção de itens para melhorar a interação com o site e guardar informações de forma devida.
atualizando arquivos para rodar online o novo site com as mudanças ja em funcinamento.


  # DOCUMENTAÇÃO TÉCNICA E DICIONÁRIO DE DADOS

## Sistema: Dulcen Cup Cakes



---



### 1. ESTRUTURA DE ARQUIVOS PHP



- **config.php**

  - **Função:** Estabelece a conexão com o banco de dados MySQL utilizando PDO.

  - **Detalhes:** Configura o modo de erro (`PDO::ERRMODE_EXCEPTION`), o modo de busca padrão (`PDO::ATTR_DEFAULT_FETCH_MODE`) e desativa a emulação de prepared statements para maior segurança.



- **cadastro.php**

  - **Função:** Página de registro de novos usuários.

  - **Detalhes:** Valida nome completo (mínimo de duas palavras), formato de CPF (com verificação dos dígitos verificadores), formato e tamanho de telefone, validade de e-mail e comprimento mínimo de senha (6 caracteres). As senhas são criptografadas com `password_hash`.



- **loguin_page.php**

  - **Função:** Página de autenticação de clientes.

  - **Detalhes:** Verifica credenciais no banco de dados, utiliza `password_verify` para validar a senha e regenera o ID da sessão (`session_regenerate_id`) para prevenir ataques de fixação de sessão.



- **logout.php**

  - **Função:** Encerramento da sessão ativa.

  - **Detalhes:** Limpa o array de sessão, apaga o cookie de sessão do navegador e destrói a sessão, redirecionando o usuário para a página inicial (`index.php`).



- **index.php**

  - **Função:** Vitrine principal e catálogo de produtos.

  - **Detalhes:** Exibe a lista de 12 cupcakes artesanais, gerencia o painel lateral do carrinho de compras (exigindo login do usuário para adicionar itens) e realiza requisições assíncronas (via Fetch API) para salvar pedidos.



- **salvar_pedido.php**

  - **Função:** Endpoint de backend para registro de pedidos.

  - **Detalhes:** Recebe um payload JSON contendo os itens do carrinho, valida se o usuário está logado, inicia uma transação no banco de dados (`beginTransaction`), calcula o valor total, insere o registro na tabela `pedidos`, grava os itens na tabela `itens_pedido` e efetiva a transação (`commit`).



- **pedidos.php**

  - **Função:** Painel de histórico de compras do cliente.

  - **Detalhes:** Lista todos os pedidos realizados pelo usuário logado, exibe os detalhes e status ("Pendente", "Confirmado", "Cancelado") e permite o cancelamento de pedidos que estejam com status "Pendente".



---



### 2. DICIONÁRIO DE DADOS (BANCO DE DADOS MYSQL)



#### A. Tabela: `usuarios`

Armazena as informações cadastrais dos clientes da loja.



| Campo      | Tipo         | Restrições         | Descrição                                        |

| :---       | :---         | :---               | :---                                             |

| `id`       | INT          | PK, Auto Increment | Identificador único do usuário.                  |
 
| `nome`     | VARCHAR(150) | NOT NULL           | Nome completo do cliente.                        |

| `cpf`      | CHAR(11)     | UNIQUE, NOT NULL   | Número de CPF (apenas dígitos, validado).        |

| `telefone` | VARCHAR(11)  | NOT NULL           | Número de telefone com DDD (10 ou 11 dígitos).   |

| `email`    | VARCHAR(150) | UNIQUE, NOT NULL   | E-mail do usuário (armazenado em minúsculas).    |

| `senha`    | VARCHAR(255) | NOT NULL           | Hash seguro da senha gerado por `password_hash`. |



---



#### B. Tabela: `pedidos`

Armazena os cabeçalhos de cada compra realizada.



| Campo        | Tipo          | Restrições                     | Descrição                                                 |

| :---         | :---          | :---                           | :---                                                      |

| `id`         | INT           | PK, Auto Increment             | Identificador único do pedido.                            |

| `usuario_id` | INT           | FK (`usuarios.id`), NOT NULL   | Cliente que realizou o pedido.                            |

| `total`      | DECIMAL(10,2) | NOT NULL                       | Valor total financeiro da compra.                         |

| `status`     | VARCHAR(50)   | NOT NULL (Default: 'Pendente') | Estado atual do pedido (Pendente, Confirmado, Cancelado). |

| `criado_em`  | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP      | Data e hora em que o pedido foi efetuado.                 |



---



#### C. Tabela: `itens_pedido`

Armazena os itens individuais que compõem cada pedido.



| Campo          | Tipo           | Restrições                  | Descrição                                      |

| :---           | :---           | :---                        | :---                                           |

| `id`           | INT            | PK, Auto Increment          | Identificador único do item no pedido.          |

| `pedido_id`    | INT            | FK (`pedidos.id`), NOT NULL | Vínculo com o pedido correspondente.            |

| `nome_produto` | VARCHAR(150)   | NOT NULL                    | Nome/sabor do cupcake adquirido.                |

| `preco`        | DECIMAL(10,2)  | NOT NULL                    | Preço unitário do cupcake no momento da compra. |

| `quantidade`   | INT            | NOT NULL                    | Quantidade de unidades compradas daquele sabor. |



---



### 3. REGRAS DE SEGURANÇA E ARQUITETURA



1. **Prevenção contra SQL Injection:** Todas as interações com o banco de dados utilizam *Prepared Statements* do PDO com passagem de parâmetros por array.

2. **Proteção de Senhas:** O sistema não armazena senhas em texto plano, utilizando os algoritmos nativos do PHP (`PASSWORD_DEFAULT`).

3. **Consistência de Transações:** A finalização de pedidos utiliza transações SQL (`beginTransaction` / `commit` / `rollBack`) para garantir que dados de cabeçalho e itens do pedido nunca fiquem dessincronizados em caso de falha.

4. **Controle de Acesso:** Rotas sensíveis (como `pedidos.php` e ações de carrinho/checkout) validam a existência da variável de sessão `$_SESSION['usuario']`, redirecionando usuários não autenticados para a página de login.
