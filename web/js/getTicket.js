/**
 * Created by julienmoulis on 20/03/2017.
 */

function getTicket(table, url) {
    $(table).DataTable({
        responsive: true,
        processing: true,
        ajax: url,
        columns: [
            {"title": "Date", "data": "date"},
            {"title": "Auteur", "data": "auteur"},
            {"title": "Message","data": "message"},
            {"title": "Niveau",
                "data": "niveau",
                "render": function (data, type, full, meta){
                    if (data == 'urgent') {
                        return '<span class="badge badge-danger">'+ data +'</span>'
                    } else {
                        return '<span class="badge badge-info">'+ data +'</span>'
                    }
                }
            },
            {"title": "Commentaire",
                "data": "commentaire",
                "render": function(data, type, full, meta){
                    return '<span class="badge badge-success">'+ data +'</span>'
                }
            },
            {"title": "Action",
                "data": "id",
                "render": function (data, type, full, meta) {
                    var showTicketDetail = Routing.generate('ticket_show', { id: data });
                    return '<a class="btn btn-warning" href="'+ showTicketDetail +'">Voir</a>'
                }}
        ]
    });
}