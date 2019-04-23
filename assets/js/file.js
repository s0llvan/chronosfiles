$(".move-file").click(function () {
	let modal = $('.ui.modal.move');
	let filename = $(this).data("filename");
	let id = $(this).data("id");
	id = JSON.stringify([id]);

	modal.find('input#file_move_file').val(id);
	modal.find('.header').text(filename);
	modal.modal('show');
});

$(".move-all-file").click(function () {
	let modal = $('.ui.modal.move');
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

$(".delete-all-file").click(function () {
	let modal = $('.ui.modal.delete');
	let files = $('.checkbox.ui:not(.select-all) input[type=checkbox]:checked');

	if (files.length > 0) {

		modal.find("ul.files").empty();

		var json = [];
		files.each(function () {
			json.push($(this).data("id"));
			modal.find("ul.files").append("<li>" + $(this).data("name") + "</li>");
		});
		json = JSON.stringify(json);

		modal.find('input#file_delete_file').val(json);
		modal.find('.header').text(files.length + ' files selected');
		modal.modal('show');
	}
});

$(".download-all-file").click(function () {
	let form = $("form#downloadFiles");
	let files = $('.checkbox.ui:not(.select-all) input[type=checkbox]:checked');

	if (files.length > 0) {

		var json = [];
		files.each(function () {
			json.push($(this).data("id"));
		});
		json = JSON.stringify(json);

		form.find('input#file_download_file').val(json);
		form.submit();
	}
});

$(".checkbox").click(function () {
	var id = $(this).find("input").data("id");
	var initialChecked = $(this).find("input").is(":checked");
	var checked = !initialChecked;

	$(".checkbox").each(function () {

		if (checked) {
			return false;
		}

		if ($(this).find("input").data("id") != id) {
			checked = $(this).find("input").is(":checked");
		}
	});

	if ($(this).hasClass('select-all')) {
		checked = !initialChecked;
	}

	if (checked) {
		$("table th a.control").show();
	} else {
		$("table th a.control").hide();
	}
});

$(".checkbox.select-all").click(function () {
	let checked = $(this).find("input").is(":checked");
	$(this).closest("table").find(".checkbox:not(.select-all) input").prop("checked", !checked);
});