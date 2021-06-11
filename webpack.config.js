const path = require("path");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');
const HtmlWebpackPlugin = require("html-webpack-plugin");

module.exports = {
    entry: path.join(__dirname, "assets", "js", "app.js"),
    cache: false,
    output: {
        path: path.join(__dirname, "public", "build"),
        filename: "index.bundle.js",
        // publicPath: path.join(__dirname, "public"),

    },
    mode: process.env.NODE_ENV || "development",
    resolve: { modules: [path.resolve(__dirname, "assets"), "node_modules"] },
    watch: true,
    watchOptions: {
        ignored: /node_modules/,
    },
    devServer: {
        contentBase: path.join(__dirname, "public")
    },
    module: {
        rules: [{
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                include: [
                    path.resolve(__dirname, "assets")
                ],
                use: ["babel-loader"]
            },
            {
                test: /\.(css|scss)$/,
                use: [MiniCssExtractPlugin.loader, "style-loader", "css-loader"],
                include: [
                    path.resolve(__dirname, "assets")
                ],
            },
            {
                test: /\.(jpg|jpeg|png|gif|mp3|svg)$/,
                include: [
                    path.resolve(__dirname, "assets")
                ],
                use: ["file-loader"]
            },
        ],
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: path.join(__dirname, "public", "index.html"),
        }),
        new WebpackAssetsManifest(),
        new MiniCssExtractPlugin(),
    ],
};