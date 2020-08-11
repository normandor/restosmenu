var theTable;

var trans_date_start_invalid;
var trans_date_end_invalid;
var trans_date_start_greater_end;
var zones_visible;

$(document).ready(function () {
    var data = document.querySelector('.js-data');
    var yes = data.dataset.translationYes;
    var no = data.dataset.translationNo;
    var imgSrc = data.dataset.imgSrc;
    var type = data.dataset.type;

    trans_date_start_invalid = data.dataset.translationDateEndIncorrect;
    trans_date_end_invalid = data.dataset.translationDateStartIncorrect;
    trans_date_start_greater_end = data.dataset.translationDateStartBiggerEnd;
    zones_visible = data.dataset.zonesVisible;

    $.fn.dataTable.moment('DD/MM/YYYY');
    theTable = $('#tableFormularios').DataTable({
        //  destroy: true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "../../tabledata-planilla/" + type,
            "data": function (d) {
                d.hecho_planilla_cor_filter = $('#hecho_planilla_cor_filter').val();
                d.ano_planilla_cor_filter = $('#ano_planilla_cor_filter').val();
                d.mes_planilla_cor_filter = $('#mes_planilla_cor_filter').val();
                d.dia_planilla_cor_filter = $('#dia_planilla_cor_filter').val();
                d.usuario_planilla_cor_filter = $('#usuario_planilla_cor_filter').val();
                d.comunazona_planilla_cor_filter = $('#comunazona_planilla_cor_filter').val();
                d.plaza_planilla_cor_filter = $('#plaza_planilla_cor_filter').val();
                d.zones_visible = zones_visible;
            }
        },
        // must be translated also in locale file
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
            {className: "dt-center",
                render: function (data, type, row) {
                    if (type === "sort" || type === "type" || null === data) {
                        return data;
                    }
                    return moment.unix(data).format("DD/MM/YYYY");
                }},
            {className: "dt-center"},
            {className: "dt-center"},
            {className: "dt-left"},
            {className: "dt-center"},
            {className: "dt-center"},
            {className: "dt-center",
                render: function (data, type, row) {
                    if ("1" === data) {
                        return yes;
                    }
                    return no;
                }},
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-center",
                render: function (data, type, row) {
                    if (null === data) {
                        return data;
                    }
                    var picsArray = data.split(';');
                    var ret = '';
                    for (var o = 0; o < picsArray.length; o++) {
                        ret = ret + '<span style="white-space: nowrap">';
                        ret = ret + '<a href="../../show/image//' + picsArray[o] + '" target="_blank"><img src="' + imgSrc + '" class="pics_list"></a>';
                        ret = ret + '</span>';
                    }

                    return ret;
                }}
        ]
    }
    );
    $('#from').datetimepicker({
        language: 'es',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        minView: 2,
        startView: 2,
        format: "dd/mm/yyyy"
    });
    $('#to').datetimepicker({
        language: 'es',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        minView: 2,
        startView: 2,
        format: "dd/mm/yyyy"
    });
});

$('#usuario_planilla_cor_filter, #ano_planilla_cor_filter, #mes_planilla_cor_filter, #dia_planilla_cor_filter, #hecho_planilla_cor_filter, #comunazona_planilla_cor_filter, #plaza_planilla_cor_filter').on('change', function () {
    setCookie('hecho_planilla_cor_filter', $('#hecho_planilla_cor_filter').val(), 10);
    setCookie('usuario_planilla_cor_filter', $('#usuario_planilla_cor_filter').val(), 10);
    setCookie('comunazona_planilla_cor_filter', $('#comunazona_planilla_cor_filter').val(), 10);
    setCookie('plaza_planilla_cor_filter', $('#plaza_planilla_cor_filter').val(), 10);
    setCookie('ano_planilla_cor_filter', $('#ano_planilla_cor_filter').val(), 10);
    setCookie('mes_planilla_cor_filter', $('#mes_planilla_cor_filter').val(), 10);
    setCookie('dia_planilla_cor_filter', $('#dia_planilla_cor_filter').val(), 10);

    theTable.draw();
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function removeAllFilters() {
    $('#usuario_planilla_cor_filter').val('').change();
    $('#ano_planilla_cor_filter').val('-').change();
    $('#mes_planilla_cor_filter').val('-').change();
    $('#dia_planilla_cor_filter').val('-').change();
    $('#hecho_planilla_cor_filter').val('').change();
    $('#comunazona_planilla_cor_filter').val('').change();
    $('#plaza_planilla_cor_filter').val('').change();

    theTable.draw();
}

function dnldXls(table) {
    var from = $('#from').find("input").val();
    myDate = from.toString().split("/");
    var newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
    from = new Date(newDate).getTime() / 1000;

    var to = $("#to").find("input").val();
    myDate = to.toString().split("/");
    newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
    to = new Date(newDate).getTime() / 1000;

    var responsable = $("#responsable").find(":selected").val();
    var responsableName = $("#responsable").find(":selected").text();

    var ok = true;

    if (isNaN(from)) {
        alert(trans_date_start_invalid);
        ok = false;
    }
    if (isNaN(to)) {
        alert(trans_date_end_invalid);
        ok = false;
    }
    if (from > to) {
        alert(trans_date_start_greater_end);
        ok = false;
    }

    if (ok) {
        window.location = '../contrato/export-excel-download/?from=' + from + '&to=' + to + '&responsable=' + responsable + '&responsableName=' + responsableName+ '&table=' + table;
    }

}