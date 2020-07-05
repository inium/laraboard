const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

// mix-manifest.json 저장경로 지정
mix.setPublicPath('./src/Public');

// pug loader 설정
mix.webpackConfig({
    module: {
       rules: [
          {
             test: /\.pug$/,
             oneOf: [
                {
                   resourceQuery: /^\?vue/,
                   use: ['pug-plain-loader']
                },
                {
                   use: ['raw-loader', 'pug-plain-loader']
                }
             ]
          }
       ]
    },
    plugins: [

        // new BundleAnalyzerPlugin(),

        // 기존 bundling 된 Asset 삭제
        new CleanWebpackPlugin()
    ]
});


mix.js('src/Resources/js/app.js', 'js').extract(['vue'])
   .sass('src/Resources/sass/app.scss', 'css')
   .sourceMaps();
