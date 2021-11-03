# Preparação

1. Iniciar os containers
```sh
$ docker-compose up -d
```

2. Testar a conexão
```sh
$ curl localhost:8080
Hello world!
```

3. Acompanhar os logs do `Command Handler`
```sh
$ docker-compose logs -f command_handler
Attaching to cqrs_command_handler_1
```

4. Em outro terminal, acompanhar os logs do `Event Handler`
```sh
$ docker-compose logs -f event_handler
Attaching to cqrs_event_handler_1
```

## Passos extras

1. Se quiser acompanhar o banco de dados de escrita, execute _(esse comando irá automaticamente atualizar a cada um segundo)_
```sh
./watch-write-db
```

2. Se quiser acompanhar o banco de dados de leitura, execute _(esse comando irá automaticamente atualizar a cada um segundo)_
```sh
./watch-read-db
```

```
Every 1.0s: psql -U postgres -c "SELECT * FROM users"

 id | name | email
----+------+------
```

# Passos
1. Listar os usuários para verificar que realmente não há nada no banco
```sh
curl localhost:8080/users
```

```json
{
  "statusCode": 200,
  "data": []
}
```

2. Cadastrar um novo usuário
```sh
curl -X POST \
  -d "name=Nome&email=email@example.com" \
  localhost:8080/users
```

```json
{
    "statusCode": 200,
    "data": {
        "id": "03baa0e5-9124-4b54-b00e-f544ef0bd659"
    }
}
```

3. Conferir nos logs dos containers `command_handler` e `event_handler` o processamento sendo efetuado
4. Consultar a view atualizada 
```sh
curl localhost:8080/users
```

```json
{
  "statusCode": 200,
  "data": [
    {
      "id": "1",
      "name": "Nome",
      "email": "email@example.com"
    }
  ]
}
```
