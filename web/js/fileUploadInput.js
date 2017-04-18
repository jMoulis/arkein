/**
 * Created by julienmoulis on 05/04/2017.
 */
/*
 * Gestion input file du modal d'ajout de fichier
 *
 */
$(document).on('click', '.browse', function(){
    var file = $(this).parent().parent().parent().find('.file');
    file.trigger('click');
});

$(document).on('change', '.file', function(){
    $(this).parent().find('.form-control').html($(this).val().replace(/C:\\fakepath\\/i, ''));
});