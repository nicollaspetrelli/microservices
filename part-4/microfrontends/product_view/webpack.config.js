const { merge } = require("webpack-merge");
const singleSpaDefaults = require("webpack-config-single-spa-react");

module.exports = (webpackConfigEnv, argv) => {
  const defaultConfig = singleSpaDefaults({
    orgName: "workshop",
    projectName: "product-view",
    webpackConfigEnv,
    argv,
  });

  return merge(defaultConfig, {
    devServer: {
      hot: false,
      client: {
        webSocketURL: {
          port: 0
        },
      },
    },
  });
};
