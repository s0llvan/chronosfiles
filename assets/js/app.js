require('../css/app.scss');

var $ = require('jquery');

require('semantic-ui-css/semantic.min.js');

$(function () {
	$('.right.menu.open').on("click", function (e) {
		e.preventDefault();
		$('.ui.vertical.menu').fadeToggle();
	});

	$('.ui.dropdown').dropdown({
		clearable: true
	});
});