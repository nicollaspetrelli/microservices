const grpc = require("@grpc/grpc-js");
const chalk = require("chalk");

module.exports = class Acl {
    #rolesClient;

    constructor(rpcProto, target) {
        this.#rolesClient = new rpcProto.Roles(target, grpc.credentials.createInsecure());
    }

    getRolesByUser(username) {
        let strPrefix = chalk.bgCyan.white(' GetRolesByUser ');
        Acl.#logStart(strPrefix);
        this.#rolesClient.getRolesByUser({username}, (error, response) => {
            if (error) {
                console.error(strPrefix, chalk.redBright('Error'));
                console.error(chalk.bgRed.white(error.message), "\n");
                return;
            }

            console.log(strPrefix, 'Response');
            console.log(response, "\n");
        });
    };

    getResourcesStreamByRole(role) {
        let strPrefix = chalk.bgBlue.white(' GetResourcesStreamByRole ');
        Acl.#logStart(strPrefix);
        Acl.#attachDefaultStreamListeners(
            strPrefix,
            this.#rolesClient.getResourcesStreamByRole({role})
        );
    };

    getResourcesStreamByRoleStream() {
        const call = this.#rolesClient.getResourcesStreamByRoleStream();
        let strPrefix = chalk.bgMagenta.white(' GetResourcesStreamByRoleStream ');
        Acl.#logStart(strPrefix);
        Acl.#attachDefaultStreamListeners(strPrefix, call);
        ['admin', 'some_invalid_role', 'moderator'].forEach((role) => {
            console.log(strPrefix, 'Asking for ' + role);
            call.write({role});
        });
        console.log('');
        call.end();
    }

    static #logStart(strPrefix) {
        console.log(strPrefix, 'Start', "\n");
    }

    static #attachDefaultStreamListeners(strPrefix, call) {
        call.on('data', (response) => {
            // Stream of "oneOfs"
            if (typeof response.type !== 'undefined') {
                // Error response
                if (response.type === 'error') {
                    console.log(strPrefix, chalk.redBright('Received error'));
                    console.error(response.error, "\n");
                    return;
                }

                // Extracting the "response" object in regular replies
                response = response.response || [];
            }

            console.log(strPrefix, 'Received');
            console.log(response, "\n");
        }).on('error', (error) => {
            console.error(strPrefix, chalk.redBright('Error'));
            console.error(chalk.bgRed.white(error.message), "\n");
        }).on('end', () => {
            console.log(strPrefix, 'Finished', "\n");
        });
    }
}
