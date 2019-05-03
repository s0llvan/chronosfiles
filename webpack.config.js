var Encore = require('@symfony/webpack-encore');

Encore
	// the project directory where compiled assets will be stored
	.setOutputPath('public/build/')
	// the public path used by the web server to access the previous directory
	.setPublicPath('/build')
	.cleanupOutputBeforeBuild()
	.enableSourceMaps(!Encore.isProduction())
	// uncomment to create hashed filenames (e.g. app.abc123.css)
	// .enableVersioning(Encore.isProduction())

	// uncomment to define the assets of the project
	.addEntry('app', './assets/js/app.js')

	.addEntry('profil', './assets/js/profil.js')

	.addEntry('file', './assets/js/file.js')

	.addEntry('admin', './assets/js/admin/app.js')

	// uncomment if you use Sass/SCSS files
	.enableSassLoader()

	// uncomment for legacy applications that require $/jQuery as a global variable
	.autoProvidejQuery()

	.enableSingleRuntimeChunk()

module.exports = Encore.getWebpackConfig();