/**
 * Created by julienmoulis on 28/03/2017.
 */
//Gestion de l'affichage de la dernière tab utilisée après refresh
$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
    localStorage.setItem('activeTab', $(e.target).attr('href'));
});
var activeTab = localStorage.getItem('activeTab');

if(activeTab != null){
    $('#tabs a[href="' + activeTab + '"]').tab('show');
} else {
    $('#tabs a:first').tab('show');
}
