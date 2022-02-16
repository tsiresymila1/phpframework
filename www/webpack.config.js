const path = require("path");
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')


module.exports = {
    entry: "./assets/js/index.tsx",
    output: { path: path.join(__dirname, "public", 'js'), filename: "index.bundle.js" },
    mode: process.env.NODE_ENV || "development",
    watch : true,
    watchOptions: { poll: 1000 },
    resolve: {
        extensions: [".tsx", ".ts", ".js"],
    },
    devServer: { contentBase: path.join(__dirname, "src") },
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: ["babel-loader"],
            },
            {
                test: /\.(ts|tsx)$/,
                exclude: /node_modules/,
                use: ["ts-loader"],
            },
            {
                test: /\.(css|scss)$/,
                use: ["style-loader", "css-loader"],
            },
            {
                test: /\.(jpg|jpeg|png|gif|mp3|svg)$/,
                use: ["file-loader"],
            },
        ],
    },
    plugins: [
        new BrowserSyncPlugin(
            {
              host: 'localhost',
              port: 4444,
              proxy: 'localhost:4444',
              files: [
                './src/**/*.php',
                './public/**/*'
              ],
              reloadDelay: 0,
              online: true,
            },
            {
              reload: true,
            }
          )
    ],
};