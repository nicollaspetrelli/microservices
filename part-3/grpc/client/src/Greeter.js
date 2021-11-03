const grpc = require("@grpc/grpc-js");
const chalk = require("chalk");

module.exports = class Greeter {
    #greeterClient;

    constructor(rpcProto, target) {
        this.#greeterClient = new rpcProto.Greeter(target, grpc.credentials.createInsecure());
    }

    sayHello(name) {
        let strPrefix = chalk.bgYellow.white(' SayHello ');
        console.log(strPrefix, 'Start', "\n");
        this.#greeterClient.sayHello({name}, (err, response) => {
            if (err) {
                throw new Error(err);
            }
            console.log(strPrefix, 'Response');
            console.log(response, "\n");
        });
    }
};
