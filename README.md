# Coffee Counter APP

Landing page de um CRUD para criar, editar, excluir e deletar usuários, e permite atribuir um número de quantidade de café por usuário.

O app permite listar todos os usuarios, listar por ordem decrescente quem consumiu mais café, tanto especificando um número específico de "x" dias, quanto especificando um dia específico.

<h2 style="color: green;">Tecnologias</h2>

* Front-end
    - JavaScript
    - HTML
    - CSS

* Back-end
    - PHP 8.4.13 ( PHP Puro sem frameworks )
    - MySQL Database

<h2 style="color: green;">Informações importantes do APP</h2>

#### Configurar acesso ao banco de dados MySQL

Acessar o arquivo config.php e inserir o nome do bando de dados para a variavel 'dbname', nome de usuário para a variavel 'user', inserir a senha para a variável 'pass'.

#### Criar o banco de dados

#### Criar as tabelas no banco de dados
Rodar o comando 
```bash
php run_migrations.php 
```

#### Teste de conectividade
Rodar o camando
```bash
php test_connection.php 
```

#### Rodar aplicação 
Se estiver acessando a pasta raiz do diretório, rodar o comando

```bash
php -S localhost:8000 -t backend/public
```


<h2 style="color: yellow;">Observação</h2>
Basta clicar na url que aparece no terminal para abrir no navegador, não precisa abrir com o live server do vscode.
<br>
<br>
<br>
#####################################################################
<br>
<br>
<br>

# Coffee Counter APP

A CRUD landing page for creating, editing, deleting, and deleting users, and allows you to assign a coffee quantity per user.

The app allows you to list all users, listing in descending order who consumed the most coffee, either by specifying a specific number of "x" days or by specifying a specific day.

<h2 style="color: green;">Technologies</h2>

* Front-end
    - JavaScript
    - HTML
    - CSS

* Back-end
    - PHP 8.4.13 (Pure PHP without frameworks)
    - MySQL Database

<h2 style="color: green;">Important APP Information</h2>

#### Configure MySQL Database Access

Access the config.php file and enter the database name for the 'dbname' variable, the username for the 'user' variable, and the password for the 'pass' variable.

#### Create the database

#### Create the tables in the database
Run the command
```bash
php run_migrations.php
```

#### Connectivity test
Run the command
```bash
php test_connection.php
```

#### Run the application
If you are accessing the root folder of the directory, run the command

```bash
php -S localhost:8000 -t backend/public
```

<h2 style="color: yellow;">Observation</h2>

Just click on the URL that appears in the terminal to open it in the browser. You don't need to open it with the VS Code live server.