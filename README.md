# api de posts e comentários

## Tecnologias Usadas
| Nome | Versao | Descrição
|---|---|---|

## Instalação

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
DELETE | /posts/{post_id}/{account_id} | Apaga todos os comentários da conta (account_id) no post (post_id)

OBS: Todos os endpoints de listagem possuem paginação. Basta passar o número da página desejada na query string (?page=1). Os resultados são mostrados de 10 em 10.
Também na listagem é mostrado o total de itens no nó count e os itens no nó data.

OBS2: Não foi feito camada de autentição. Nos endpoints onde são necessários saber a conta que está enviando a requisição, tem que passar o nó account no corpo da requisição. Aqui está [um exemplo]().
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
        },
        {...}
    ],
    "count": 104
}
```

### Criar de conta
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

### Consultar de conta
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
                "name": "Eita",
                "email": "diego@eita.com",
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