# Docker Swarm
Documentação em [docs.docker.com/engine/swarm](https://docs.docker.com/engine/swarm/)

## Inicializando
```sh
docker swarm init # --advertise-addr <ip> 

docker service create --name registry --publish published=5000,target=5000 registry:2

docker-compose up -d

docker-compose push

docker-compose down --volumes
```

## Criando a stack
```sh
docker stack deploy --compose-file docker-compose.yml workshop

docker stack services workshop
```

```sh
ID             NAME                         MODE         REPLICAS   IMAGE                                     PORTS
vef2mkoc12k8   workshop_acl_app             replicated   1/1        127.0.0.1:5000/acl_app:latest             
iwvkmx27209x   workshop_grades_app          replicated   1/1        127.0.0.1:5000/grades_app:latest          
cfwgpdulra04   workshop_grades_db           replicated   1/1        postgres:13-alpine                        
n388xvv88eoq   workshop_notifications_app   replicated   1/1        127.0.0.1:5000/notifications_app:latest   
l3ygxhrpf7m0   workshop_rabbitmq            replicated   1/1        rabbitmq:3-alpine                         
hu011ks6jwo8   workshop_redis               replicated   1/1        redis:6-alpine                            *:30000->6379/tcp
zeq5xwf3y3zx   workshop_reverse-proxy       replicated   1/1        nginx:latest                              *:8888->80/tcp
ygsofmf6s1m8   workshop_secrets_app         replicated   1/1        127.0.0.1:5000/secrets_app:latest         
8q92lkyajypv   workshop_secrets_db          replicated   1/1        postgres:13-alpine           
```

## Escalando
```sh
docker service scale workshop_secrets_app=2

docker service logs -f workshop_secrets_app
```

## Desinstalando
```sh
docker stack rm workshop
docker service rm registry
docker swarm leave --force
```
