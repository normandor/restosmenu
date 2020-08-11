var theTable;
var loadingGif;
var messageErrorProcessing;

$(document).ready(function () {
    var data = document.querySelector('.js-data');
    loadingGif = data.dataset.loadingGif;
    messageErrorProcessing = data.dataset.messageErrorProcessing;
    pathTableData = data.dataset.pathTableData;
    $.fn.dataTable.moment('DD/MM/YYYY');
    theTable = $('#tableFormularios').DataTable({
//  destroy: true,
        "pageLength": 50,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": pathTableData
        },
        "language": {
            "sEmptyTable": "Ningún dato",
            "sInfo": "_START_ a _END_ de _TOTAL_ líneas",
            "sInfoEmpty": "0 a 0 de 0 líneas",
            "sInfoFiltered": "(filtrado de un total de _MAX_ líneas)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "Mostrar _MENU_ entradas",
            "sLoadingRecords": "Cargando...",
            "sProcessing": "Cargando...",
            "sSearch": "Buscar",
            "sZeroRecords": "No se encontraron resultados.",
            "decimal": ",",
            "oPaginate": {
                "sFirst": "Primero",
                "sPrevious": "Anterior",
                "sNext": "Próximo",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": ordernar en forma ascendente",
                "sSortDescending": ": ordernar en forma descendente"
            }
        },
        stateSave: true,
        "columns": [
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-center",
                render: function (data, type, row) {
                    if (type === "sort" || type === "type" || null === data) {
                        return data;
                    }
                    return moment.unix(data).format("DD/MM/YYYY");
                }
            }
        ]
    });
});

function downloadXlsNoRealizadas() {
    window.open('php/export_csv_limpieza_no_realizada.php');
}
