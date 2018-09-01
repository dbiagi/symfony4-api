# api de posts e comentários

## Tecnologias Usadas
| Nome | Versao 
|---|:---:|
[PHP](https://php.net) | 7.2
[Symfony Framework](https://symfony.com) | 4.1
[Doctrine ORM](https://www.doctrine-project.org/) | 2.6
[MySQL](https://www.mysql.com) | 5.7

## Instalação

Crie um arquivo .env na raiz do projeto tomando como base o .env.dest

`cp .env.dist .env`

Substitua os valores no .env pelas configurações adequadas.

Exemplo de .env:

```sh
APP_ENV=prod
APP_SECRET=a4fdf33cae7126b8cedc6c01d08c38ae
TRUSTED_PROXIES=127.0.0.1,127.0.0.2
TRUSTED_HOSTS=localhost
DATABASE_URL=mysql://root:123@localhost:3306/esapiens
MAILER_URL=gmail://user@gmail.com:minhasenha@localhost
ADMIN_EMAIL=meuemail@gmail.com
```

Caso opte por usar Docker para montar esse projeto usando o docker-compose.yml da raiz do projeto, a variável DATABASE_URL deve ficar assim:
`DATABASE_URL=mysql://root:123@database:3306/esapiens`.

Depois de subir o serviço do PHP e do MySql, certifique-se de estar dentro da raiz do projeto.

Execute o script install.sh para criar o banco e popular com dados aleatórios.

```
sh install.sh
```

Pronto, se tudo der certo então deu certo \o/.

## Schema

Segue imagem com o diagrama do schema da aplicação



## Endpoints
| Método | URL | Descrição
|--------:|-----|-----|
GET | /accounts/ | Lista todas as contas                  
POST | /accounts/ | Cria uma conta             
GET | /accounts/{id} | Retorna uma conta pelo seu id
GET | /accounts/{id}/comments | Lista os comentários de uma conta pelo seu id                 
GET | /accounts/{id}/notifications | Lista as notificações de uma conta pelo seu id            
POST | /accounts/{id}/coins | Compra moedas para a conta                    
GET | /posts/{id} | Retorna um post pelo seu id                             
GET | /posts/ | Lista todos os posts                                 
GET | /posts/{id}/comments | Lista os comentários de um post seguindo a regra do escopo               
POST | /posts/{id}/comments | Posta um comentário                    
DELETE | /posts/{post_id}/comments/{comment_id} | Apaga um comentário específico (comment_id) de um post específico(post_id)
DELETE | /posts/{post_id}/comments/accounts/{account_id} | Apaga todos os comentários da conta (account_id) no post (post_id)

OBS: Todos os endpoints de listagem possuem paginação. Basta passar o número da página desejada na query string (?page=1). Os resultados são mostrados de 10 em 10.
Também na listagem é mostrado o total de itens no nó count e os itens no nó data.

OBS2: Não foi feito camada de autentição. Nos endpoints onde são necessários saber a conta que está enviando a requisição, tem que passar o nó account no corpo da requisição. Para mais informações veja o exemplo das requisições logo abaixo.

## Exemplos

### Listar todas as contas
#### Requisição: 
```
GET /accounts/
```
#### Resposta
```json
{
    "data": [
        {
            "id": 101,
            "name": "Raquel Terry",
            "email": "caleb.prosacco@corwin.com",
            "role": "guest",
            "coins": 0,
            "updated_at": "2018-08-26T18:06:56-03:00",
            "created_at": "2018-08-26T18:06:56-03:00"
        }
    ],
    "count": 104
}
```

### Criar conta
#### Requisição: 
```
POST /accounts/

{
    "name": "string",
    "email": "some@email.com",
    "role": "guest|subscriber",
    "coins": 0
}
```
#### Resposta
```json
{
    "id": 103,
    "name": "string",
    "email": "some@email.com",
    "role": "guest",
    "coins": 0,
    "updated_at": "2018-08-30T23:19:49-03:00",
    "created_at": "2018-08-30T23:19:49-03:00"
}
```

### Mostrar conta
#### Requisição: 
```
GET /accounts/103
```
#### Resposta
```json
{
    "id": 103,
    "name": "string",
    "email": "some@email.com",
    "role": "guest",
    "coins": 0,
    "updated_at": "2018-08-30T23:19:49-03:00",
    "created_at": "2018-08-30T23:19:49-03:00"
}
```

### Listar comentários de uma conta
#### Requisição:
```
GET /accounts/103/comments
```
#### Resposta
```json
{
    "data": [
        {
            "id": 1019,
            "coins": 0,
            "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas.",
            "author": {
                "id": 103,
                "name": "Diego",
                "email": "some@email.com",
                "role": "guest",
                "coins": 0,
                "updated_at": "2018-08-30T23:19:49-03:00",
                "created_at": "2018-08-30T23:19:49-03:00"
            },
            "post": {
                "id": 1,
                "title": "Quis inventore qui ea sit.",
                "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas. Similique consequatur nisi fuga dolores aut. Illo illum sint voluptas a aperiam. Aut qui neque minus eos fugit nostrum non.\n\nEt fugit reiciendis excepturi enim velit qui nam. Non dolore quis et eius. Et omnis eaque pariatur non ea vel. Rerum explicabo inventore quam suscipit qui accusamus.\n\nVoluptatem est similique tenetur aut. Aliquam provident et voluptatem eveniet consequatur. Impedit sint nam perferendis sit. Magnam ut nemo possimus qui totam iste.\n\nAnimi et ad rerum perferendis fugiat et. Quisquam et quod velit velit ut rem repellendus. Sit laudantium consequuntur aut et. Quas ut est in reprehenderit reiciendis accusamus.",
                "type": "text",
                "author": {
                    "id": 65,
                    "name": "Jedediah Metz",
                    "email": "agreenfelder@gorczany.org",
                    "role": "guest",
                    "coins": 0,
                    "updated_at": "2018-08-26T18:06:56-03:00",
                    "created_at": "2018-08-26T18:06:56-03:00"
                },
                "updated_at": "2018-08-26T18:06:56-03:00",
                "created_at": "2018-08-26T18:06:56-03:00"
            },
            "updated_at": "2018-08-31T00:12:10-03:00",
            "created_at": "2018-08-31T00:12:10-03:00"
        }
    ],
    "count": 1
}
```

### Listar notificações de uma conta
#### Requisição: 
```
GET /accounts/65/notifications
```
#### Resposta
```json
{
    "data": [
        {
            "id": 3,
            "title": "Someone commented on your post",
            "content": "The user Diego has commented on your post \"Quis inventore qui ea sit.\"",
            "account": {
                "id": 65,
                "name": "Jedediah Metz",
                "email": "agreenfelder@gorczany.org",
                "role": "guest",
                "coins": 0,
                "updated_at": "2018-08-26T18:06:56-03:00",
                "created_at": "2018-08-26T18:06:56-03:00"
            },
            "viewed_at": "2018-08-31T00:25:29-03:00",
            "expire_at": "2018-08-31T01:25:29-03:00",
            "updated_at": "2018-08-31T00:25:29-03:00",
            "created_at": "2018-08-31T00:02:18-03:00"
        },
        {}
    ],
    "count": 100
}
```

### Adicionar moedas ao usuário
#### Requisição: 
```
POST /accounts/103

{
    "amount": 100
}
```
#### Resposta
```json
{
    "id": 1,
    "name": "Miss Ashlynn Mante",
    "email": "ldooley@ratke.com",
    "role": "subscriber",
    "coins": 100,
    "updated_at": "2018-08-31T19:15:48-03:00",
    "created_at": "2018-08-26T18:06:56-03:00"
}
```

### Listar todos posts
#### Requisição: 
```
GET /posts/
```
#### Resposta
```json
{
    "data": [
        {
            "id": 1,
            "title": "Quis inventore qui ea sit.",
            "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas. Similique consequatur nisi fuga dolores aut. Illo illum sint voluptas a aperiam. Aut qui neque minus eos fugit nostrum non.\n\nEt fugit reiciendis excepturi enim velit qui nam. Non dolore quis et eius. Et omnis eaque pariatur non ea vel. Rerum explicabo inventore quam suscipit qui accusamus.\n\nVoluptatem est similique tenetur aut. Aliquam provident et voluptatem eveniet consequatur. Impedit sint nam perferendis sit. Magnam ut nemo possimus qui totam iste.\n\nAnimi et ad rerum perferendis fugiat et. Quisquam et quod velit velit ut rem repellendus. Sit laudantium consequuntur aut et. Quas ut est in reprehenderit reiciendis accusamus.",
            "type": "text",
            "author": {
                "id": 65,
                "name": "Jedediah Metz",
                "email": "agreenfelder@gorczany.org",
                "role": "guest",
                "coins": 0,
                "updated_at": "2018-08-26T18:06:56-03:00",
                "created_at": "2018-08-26T18:06:56-03:00"
            },
            "updated_at": "2018-08-26T18:06:56-03:00",
            "created_at": "2018-08-26T18:06:56-03:00"
        }     
    ],
    "count": 101
 }
```

### Mostrar post
#### Requisição: 
```
GET /posts/1
```
#### Resposta
```json
{
    "id": 1,
    "title": "Quis inventore qui ea sit.",
    "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas. Similique consequatur nisi fuga dolores aut. Illo illum sint voluptas a aperiam. Aut qui neque minus eos fugit nostrum non.\n\nEt fugit reiciendis excepturi enim velit qui nam. Non dolore quis et eius. Et omnis eaque pariatur non ea vel. Rerum explicabo inventore quam suscipit qui accusamus.\n\nVoluptatem est similique tenetur aut. Aliquam provident et voluptatem eveniet consequatur. Impedit sint nam perferendis sit. Magnam ut nemo possimus qui totam iste.\n\nAnimi et ad rerum perferendis fugiat et. Quisquam et quod velit velit ut rem repellendus. Sit laudantium consequuntur aut et. Quas ut est in reprehenderit reiciendis accusamus.",
    "type": "text",
    "author": {
        "id": 65,
        "name": "Jedediah Metz",
        "email": "agreenfelder@gorczany.org",
        "role": "guest",
        "coins": 0,
        "updated_at": "2018-08-26T18:06:56-03:00",
        "created_at": "2018-08-26T18:06:56-03:00"
    },
    "updated_at": "2018-08-26T18:06:56-03:00",
    "created_at": "2018-08-26T18:06:56-03:00"
}
```

### Mostrar comentários de um post
#### Requisição: 
```
GET /posts/1/comments
```
#### Resposta
```json
{
    "data": [
        {
            "id": 237,
            "coins": 0,
            "content": "Voluptatum velit esse id quae at error voluptas quis. Eius id atque consequuntur optio est reprehenderit deleniti rerum. Ea cupiditate nemo doloremque consequatur reprehenderit suscipit odit itaque. Enim voluptatem deserunt aspernatur consequuntur quia. Rerum facere libero eaque occaecati rem accusamus ea.",
            "author": {
                "id": 43,
                "name": "Dr. Johnathon Wolff",
                "email": "abbey.shields@flatley.com",
                "role": "subscriber",
                "coins": 0,
                "updated_at": "2018-08-26T18:06:56-03:00",
                "created_at": "2018-08-26T18:06:56-03:00"
            },
            "post": {
                "id": 1,
                "title": "Quis inventore qui ea sit.",
                "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas. Similique consequatur nisi fuga dolores aut. Illo illum sint voluptas a aperiam. Aut qui neque minus eos fugit nostrum non.\n\nEt fugit reiciendis excepturi enim velit qui nam. Non dolore quis et eius. Et omnis eaque pariatur non ea vel. Rerum explicabo inventore quam suscipit qui accusamus.\n\nVoluptatem est similique tenetur aut. Aliquam provident et voluptatem eveniet consequatur. Impedit sint nam perferendis sit. Magnam ut nemo possimus qui totam iste.\n\nAnimi et ad rerum perferendis fugiat et. Quisquam et quod velit velit ut rem repellendus. Sit laudantium consequuntur aut et. Quas ut est in reprehenderit reiciendis accusamus.",
                "type": "text",
                "author": {
                    "id": 65,
                    "name": "Jedediah Metz",
                    "email": "agreenfelder@gorczany.org",
                    "role": "guest",
                    "coins": 0,
                    "updated_at": "2018-08-26T18:06:56-03:00",
                    "created_at": "2018-08-26T18:06:56-03:00"
                },
                "updated_at": "2018-08-26T18:06:56-03:00",
                "created_at": "2018-08-26T18:06:56-03:00"
            },
            "updated_at": "2018-08-26T18:06:56-03:00",
            "created_at": "2018-08-26T18:06:56-03:00"
        }
    ],
    "count": 18
}    
```

### Postar um comentário em um post
O nó _account_ deve ser um email de uma conta existente na aplicação.

O nó _coins_ é opcional.
#### Requisição:
```
POST /posts/1/comments

{
    "account": "ldooley@ratke.com",
    "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas.",
    "coins": 100
}
```

O nó _account_ deve ser um email de uma conta existente na aplicação

#### Resposta
```json
{
    "id": 1021,
    "coins": 100,
    "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas.",
    "author": {
        "id": 1,
        "name": "Miss Ashlynn Mante",
        "email": "ldooley@ratke.com",
        "role": "subscriber",
        "coins": 2900,
        "updated_at": "2018-08-31T19:27:24-03:00",
        "created_at": "2018-08-26T18:06:56-03:00"
    },
    "post": {
        "id": 1,
        "title": "Quis inventore qui ea sit.",
        "content": "Qui fugiat dolores placeat reprehenderit nesciunt architecto quas. Similique consequatur nisi fuga dolores aut. Illo illum sint voluptas a aperiam. Aut qui neque minus eos fugit nostrum non.\n\nEt fugit reiciendis excepturi enim velit qui nam. Non dolore quis et eius. Et omnis eaque pariatur non ea vel. Rerum explicabo inventore quam suscipit qui accusamus.\n\nVoluptatem est similique tenetur aut. Aliquam provident et voluptatem eveniet consequatur. Impedit sint nam perferendis sit. Magnam ut nemo possimus qui totam iste.\n\nAnimi et ad rerum perferendis fugiat et. Quisquam et quod velit velit ut rem repellendus. Sit laudantium consequuntur aut et. Quas ut est in reprehenderit reiciendis accusamus.",
        "type": "text",
        "author": {
            "id": 65,
            "name": "Jedediah Metz",
            "email": "agreenfelder@gorczany.org",
            "role": "guest",
            "coins": 0,
            "updated_at": "2018-08-26T18:06:56-03:00",
            "created_at": "2018-08-26T18:06:56-03:00"
        },
        "updated_at": "2018-08-26T18:06:56-03:00",
        "created_at": "2018-08-26T18:06:56-03:00"
    },
    "updated_at": "2018-08-31T19:27:24-03:00",
    "created_at": "2018-08-31T19:27:24-03:00"
}
```

### Remover comentário específico de um post específico
#### Requisição: 
```
DELETE /posts/1/comments/1021
```
#### Resposta
```json
{}
```

### Remover todos comentários de um usuário específico específico de um post específico
#### Requisição: 
```
DELETE /posts/1/comments/accounts/1

{
    "account": "agreenfelder@gorczany.org"
}
```
O nó _account_ deve ser um email de uma conta existente na aplicação e essa conta deve ser o dono do post.
#### Resposta
```json
{}
```