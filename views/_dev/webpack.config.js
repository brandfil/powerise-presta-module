/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;

let config = {
  mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
  entry: {
    back: ['./src/js/back.js', './src/scss/back.scss'],
  },
  output: {
    path: path.resolve(__dirname, '../js'),
    filename: '[name].js',
  },
  devtool: 'source-map',
  resolve: {
    preferRelative: true,
  },
  module: {
    rules: [
      {
        test: /\.js/,
        loader: 'esbuild-loader',
      },
      {
        test: /\.scss$/,
        use:[
          MiniCssExtractPlugin.loader,
          'css-loader',
          'resolve-url-loader',
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
            },
          },
        ],
      },
      {
        test: /.(png|svg|gif|jpe?g)(\?[a-z0-9=\.]+)?$/,
        type: 'asset/resource',
        generator: {
          filename: '../img/[name][ext]',
        },
      },
      {
        test: /.(woff(2)?|eot|otf|ttf)(\?[a-z0-9=\.]+)?$/,
        type: 'asset/resource',
        generator: {
          filename: '../fonts/[name][ext]',
        },
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'style-loader', 'css-loader'],
      },
    ],
  },
  externals: {
    prestashop: 'prestashop',
    $: '$',
    jquery: 'jQuery',
  },
  plugins: [
    new MiniCssExtractPlugin({filename: path.join('..', 'css', '[name].css')}),
    new CssoWebpackPlugin({
      forceMediaMerge: true,
    }),
  ]
};

if (process.env.NODE_ENV === 'production') {
  config.optimization = {
    minimize: true,
    minimizer: [
      new TerserPlugin({
        parallel: true,
        extractComments: false,
      }),
    ],
  };
}

module.exports = config;
