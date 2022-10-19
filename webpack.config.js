const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: './web/themes/pub/index.ts',
  mode: "development",

  watchOptions: {
    poll: 100
  },

  module: {
    rules: [
      {
        test: /\.tsx?$/,
        use: [{
          loader: 'ts-loader'
          , 'options': {
            transpileOnly: true
          }
        }]
      },
      {
        test: /\.scss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          {
            loader: "css-loader",
            options: {
              sourceMap: true
            }
          },
          {
            loader: "sass-loader",
            options: {
              sourceMap: true,
              implementation: require('sass'),
              sassOptions: {
                includePaths: [
                  path.resolve(__dirname, "./node_modules"),
                  path.resolve(__dirname, "./node_modules/compass-mixins/lib")
                ]
              },
              importer: function(url, prev) {
                var nodeModulePath;
                if(url.indexOf('@material') === 0) {
                  var filePath = url.split('@material')[1];
                  nodeModulePath = './node_modules/@material/' + filePath;
                  return { file: require('path').resolve(nodeModulePath) };
                }

                if(url.indexOf('material-components-web') === 0) {
                  nodeModulePath = './node_modules/material-components-web/material-components-web';
                  return { file: require('path').resolve(nodeModulePath) };
                }

                return { file: url };
              }
            }
          },
        ]
      },
      {
        test: /\.html$/i,
        use: 'raw-loader',
      },
    ]
  },
  output: {
    path: __dirname + '/web/themes/pub'
  },
  devtool: "inline-source-map",
  plugins: [
    new MiniCssExtractPlugin({})
  ],
};
