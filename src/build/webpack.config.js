const path = require('path');
const webpackConfig = require('webpack');
module.exports = function (env = {}) {
    if (env.production)
        process.env.NODE_ENV = 'production';
    return {
        entry: './src/main.js',
        output: {
            path: path.resolve(__dirname, '../../assets'),
            filename: env.production ? 'js/main.min.js' : 'js/main.js'
        },
        plugins: env.production ? [
            new webpackConfig.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                }
            }),
            new webpackConfig.optimize.UglifyJsPlugin({
                compress: {
                    warnings: false
                }
            }),
        ] : [],
        devtool: env.production ? false
            : '#cheap-module-eval-source-map',
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: makeStyleLoader()
                },
                {
                    test: /\.less$/,
                    use: makeStyleLoader('less')
                },
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/
                },
                {
                    test: /\.(png|jpg)$/,
                    loader: 'file-loader',
                    options: {
                        name: 'images/[name].[ext]'
                    }
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {
                            css: makeStyleLoader(),
                            less: makeStyleLoader('less')
                        }
                    }
                },
            ]
        },
        resolve: {
            extensions: ['.js', '.vue', '.json'],
            alias: {
                '@': path.resolve(__dirname, '..')
            }
        }
    };
};