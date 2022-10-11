const path = require("path");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");

module.exports = {
  entry: "./src/assets/js/index.tsx",
  output: {
    path: path.join(__dirname, "public", "js", "bundle"),
    filename: "index.bundle.js",
  },
  mode: process.env.NODE_ENV || "development",
  watch: true,
  watchOptions: { poll: 1000 },
  resolve: {
    extensions: [".tsx", ".ts", ".js", ".css"],
  },
  devServer: { contentBase: path.join(__dirname, "src") , hot: true,
  watchContentBase: true},
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
        use: [
          "style-loader",
          "css-loader",
        ],
      },
      {
        test: /\.(jpg|jpeg|png|gif|mp3|svg)$/,
        use: ["file-loader"],
      },
      {
        test: /.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        type: 'asset/resource',
        dependency: { not: ['url'] },
      },
    ],
  },
  plugins: [
    new BrowserSyncPlugin(
      {
        host: "localhost",
        port: 4445,
        proxy: "http://127.0.0.1:4444",
        files: ["./src/**/*.php", "./assets/**/*"],
        open: "local",
        online: true,
        notify: false,
      },
      {
        reload: true,
      }
    ),
  ],
};
