$(".move-file").click(function () {
	let modal = $('.ui.modal');
	let filename = $(this).data("filename");
	let id = $(this).data("id");
	id = JSON.stringify([id]);

	modal.find('input#file_move_file').val(id);
	modal.find('.header').text(filename);
	modal.modal('show');
});

$(".move-all-file").click(function () {
	let modal = $('.ui.modal');
	let files = $('.checkbox.ui:not(.select-all) input[type=checkbox]:checked');

	if (files.length > 0) {

		var json = [];
		files.each(function () {
			json.push($(this).data("id"));
		});
		json = JSON.stringify(json);

		modal.find('input#file_move_file').val(json);
		modal.find('.header').text(files.length + ' files selected');
		modal.modal('show');
	}
});

$(".checkbox.select-all").click(function () {
	let checked = $(this).find("input").is(":checked");

	if (checked) {
		$("table th .move-all-file").hide();
	} else {
		$("table th .move-all-file").show();
	}

	$(this).closest("table").find(".checkbox:not(.select-all) input").prop("checked", !checked);
});