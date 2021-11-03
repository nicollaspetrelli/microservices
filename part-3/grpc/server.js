const PROTO_PATH = __dirname + '/acl.proto';
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

const address = '127.0.0.1';
const port = process.env.PORT || 50051;
const server = new grpc.Server();
server.addService(rpcProto.Greeter.service, {
    sayHello: (call, callback) => {
        callback(null, {message: 'Hello ' + call.request.name});
    }
});

const rolesByUser = {
    root: [
        'admin',
        'moderator',
    ],
    moderator: [
        'moderator',
    ],
}
const resourcesByRole = {
    admin: [
        'resource 1',
        'resource 2',
        ['resource 3', 5],
    ],
    moderator: [
        ['resource 1', 1],
        ['resource 1', 2],
    ],
};

/**
 * Builds a status response
 *
 * @param {grpc.status} code
 * @param {string} message
 * @param {array} details
 * @returns {{code, details: null, message}}
 */
const buildStatus = (code, message, details = null) => {
    return {
        code,
        message,
        details
    };
};

/**
 * @param {ServerWritableStreamImpl} call
 * @param {String} role
 * @returns {boolean}
 */
const writeResourcesByRole = (call, role) => {
    if (typeof resourcesByRole[role] === 'undefined') {
        call.write({
            error: buildStatus(grpc.status.INVALID_ARGUMENT, 'Invalid role: ' + role)
        });
        return false;
    }

    resourcesByRole[role].forEach(resource => {
        const response = {
            role: role,
        };
        if (typeof resource === 'string') {
            response.resource = resource;
        } else {
            response.resource = resource[0];
            response.record_id = resource[1];
        }
        call.write({response: response});
    });
    return true;
};
server.addService(rpcProto.Roles.service, {
    getRolesByUser: (call, callback) => {
        if (typeof rolesByUser[call.request.username] === 'undefined') {
            callback(new Error('Invalid user'));
            return;
        }
        callback(null, {
            username: call.request.username,
            roles: rolesByUser[call.request.username]
        });
    },
    getResourcesStreamByRole: (call) => {
        writeResourcesByRole(call, call.request.role);
        call.end();
    },
    getResourcesStreamByRoleStream: (call) => {
        call.on('data', (data) => {
            writeResourcesByRole(call, data.role);
        }).on('end', () => {
            call.end();
        });
    },
});

console.log('Starting server at ' + address + ':' + port);
server.bindAsync(address + ':' + port, grpc.ServerCredentials.createInsecure(), (error, port) => {
    if (error) {
        throw error;
    }
    server.start();
});


