$(".delete-user").click(function () {
	$('.ui.modal')
		.modal({
			onApprove: () => {
				window.location = $(this).data("url");
			}
		}).modal('show');
});

$(".delete-role").click(function () {
	$('.ui.modal')
		.modal({
			onApprove: () => {
				window.location = $(this).data("url");
			}
		}).modal('show');
});