$(".delete-profil").click(function () {
    $('.ui.modal')
        .modal({
            onApprove: () => {
                window.location = $(this).data("url");
            }
        }).modal('show');
});