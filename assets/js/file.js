$(".move-file").click(function () {
    let modal = $('.ui.modal');
    let filename = $(this).data("filename");
    let id = $(this).data("id");
    modal.find('input#file_move_file').val(id);
    modal.find('.header').text(filename);
    modal.modal('show');
});