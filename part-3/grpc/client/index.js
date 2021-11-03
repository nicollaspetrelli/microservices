const PROTO_PATH = __dirname + '/../acl.proto';
const grpc = require('@grpc/grpc-js');
const protoLoader = require('@grpc/proto-loader');
const packageDefinition = protoLoader.loadSync(
    PROTO_PATH,
    {
        keepCase: true,
        longs: String,
        enums: String,
        defaults: true,
        oneofs: true
    }
);
const rpcProto = grpc.loadPackageDefinition(packageDefinition).acl;

let target = (process.env.GRPC_SERVER_HOSTNAME || 'localhost') + ':' + (process.env.GRPC_SERVER_PORT || 50051);

const readline = require('readline').createInterface({
    input: process.stdin,
    output: process.stdout
});

const Greeter = require('./src/Greeter');
const greeter = new Greeter(rpcProto, target);
const Acl = require('./src/Acl');
const acl = new Acl(rpcProto, target);

const sayHello = (name) => {
    readline.close();
    greeter.sayHello((name) ? name : 'world');
};

const getRolesByUser = (username) => {
    readline.close();
    acl.getRolesByUser(username);
};

const getResourcesStreamByRole = (role) => {
    readline.close();
    acl.getResourcesStreamByRole(role);
};

const getResourcesStreamByRoleStream = () => {
    readline.close();
    acl.getResourcesStreamByRoleStream();
};

const ENTRY_START = "\33[33m",
    TEXT_START = "\33[97m",
    COLOR_RESET = "\33[0m";
const menu = `Escolha uma opção:

[${ENTRY_START}1${COLOR_RESET}] ${TEXT_START}Greeter::SayHello${COLOR_RESET}
[${ENTRY_START}2${COLOR_RESET}] ${TEXT_START}ACL::GetRolesByUser${COLOR_RESET}
[${ENTRY_START}3${COLOR_RESET}] ${TEXT_START}ACL::GetResourcesStreamByRole${COLOR_RESET}
[${ENTRY_START}4${COLOR_RESET}] ${TEXT_START}ACL::GetResourcesStreamByRoleStream${COLOR_RESET}

Qualquer outra tecla para ${TEXT_START}Sair${COLOR_RESET}

Opção: `;
readline.question(menu, entry => {
    console.log('');
    switch (entry) {
        case '1':
            readline.question('Dizer "Hello" para quem? ', sayHello);
            break;

        case '2':
            readline.question('Buscar papéis de qual usuário? ', getRolesByUser);
            break;

        case '3':
            readline.question('Buscar permissões de qual papel? ', getResourcesStreamByRole);
            break;

        case '4':
            getResourcesStreamByRoleStream();
            break;
    }
});
