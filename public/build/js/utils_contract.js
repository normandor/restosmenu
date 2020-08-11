var theTable;
var loadingGif;
var messageErrorProcessing;
var pathContratoApproveAnomaly;
var pathContratoUnapproveAnomaly;
var pathContratoDeleteAnomaly;
var pathContratoShowAnomaly;
var pathContratoAddAnomaly;
var pathContratoEditAnomaly;

$(document).ready(function () {
    var data = document.querySelector('.js-data');
    loadingGif = data.dataset.loadingGif;
    messageErrorProcessing = data.dataset.messageErrorProcessing;
    pathContratoApproveAnomaly = data.dataset.pathContratoApproveAnomaly;
    pathContratoUnapproveAnomaly = data.dataset.pathContratoUnapproveAnomaly;
    pathContratoDeleteAnomaly = data.dataset.pathContratoDeleteAnomaly;
    pathContratoShowAnomaly = data.dataset.pathContratoShowAnomaly;
    pathContratoAddAnomaly = data.dataset.pathContratoAddAnomaly;
    pathContratoEditAnomaly = data.dataset.pathContratoEditAnomaly;
    pathTableData = data.dataset.pathTableData;
    $.fn.dataTable.moment('DD/MM/YYYY');
    theTable = $('#tableFormularios').DataTable({
//  destroy: true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": pathTableData,
            "data": function (d) {
                d.terminados_filter = $('#terminados_filter').is(':checked');
                d.comunazona_filter = $('#comunazona_filter').val();
                d.rubro_filter = $('#rubro_filter').val();
                d.urgencia_filter = $('#urgencia_filter').val();
            }
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
            {className: "dt-center nowrap"},
            {className: "dt-center"},
            {className: "dt-left"},
            {className: "dt-center",
                render: function (data, type, row) {
                    if (type === "sort" || type === "type" || null === data) {
                        return data;
                    }
                    return moment.unix(data).format("DD/MM/YYYY");
                }},
            {className: "dt-center"},
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-left",
                render: function (data, type, row) {
                    if ("sort" === type || "type" === type) {
                        return data;
                    }
                    if ('0' === data) {
                        return '';
                    }
                    return '$' + data;
                }},
            {className: "dt-center"},
            {className: "dt-left",
                render: function (data, type, row) {
                    if ('0' === data) {
                        return '-';
                    }
                    if (type === "sort" || type === "type" || null === data) {
                        return data;
                    }
                    return moment.unix(data).format("DD/MM/YYYY");
                },
            },
            {className: "dt-left"},
            {className: "dt-left"},
            {className: "dt-left"}
        ]
    });
    theTable.column( 11 ).visible( false );
});
$('#rubro_filter, #urgencia_filter, #comunazona_filter, #terminados_filter').on('change', function () {
    setCookie('urgencia_filter', $('#urgencia_filter').val(), 10);
    setCookie('rubro_filter', $('#rubro_filter').val(), 10);
    setCookie('comunazona_filter', $('#comunazona_filter').val(), 10);
    setCookie('terminados_filter', $('#terminados_filter').is(':checked'), 10);
    theTable.draw();
});
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function showIncident(theId)
{
    $('.modal-title').html('Avances de la anomalía');
    $('.modal-body-show-incident').html('<div align="center"><img height="100" src="' + loadingGif + '"></div>');
    $('.modal-body-show-incident').load(pathContratoShowAnomaly + '/' + theId, function () {});
    $("#showIncidentModal").modal('show');
}

function showApprove(theId) {
    $(".modal-header #incidentIdApprove").html(theId);
    $("#modalConfirmApprove").modal('show');
}

var modalConfirmApproveAnomaly = function (callback) {
    $("#modal-btn-approve-anomaly-yes").on("click", function () {
        callback(true);
        $("#modalConfirmApprove").modal('hide');
    });
    $("#modal-btn-approve-anomaly-no").on("click", function () {
        callback(false);
        $("#modalConfirmApprove").modal('hide');
    });
};
modalConfirmApproveAnomaly(function (confirm) {
    if (confirm) {
        var theVal = $(".modal-header #incidentIdApprove").html();
        $.ajax({
            url: pathContratoApproveAnomaly,
            type: 'POST',
            dataType: 'json',
            data: {'id': theVal, 'approved': true},
            success: function (data) {
                location.reload();
            },
            error: function (xhr, status) {
                alert(messageErrorProcessing);
            }
        });
    } else {
    }
});
function showEditIncident(theId)
{
    $('.modal-title').html('Editar anomalía');
    $('.modal-body-edit-incident').html('<div align="center"><img height="100" src="' + loadingGif + '"></div>');
    $('.modal-body-edit-incident').load(pathContratoEditAnomaly + '/' + theId, function () {});
    $("#editIncidentModal").modal('show');
}

function showAddIncident() {
    $('.modal-title').html('Nueva anomalía');
    $('.modal-body-edit-incident').html('<div align="center"><img height="100" src="' + loadingGif + '"></div>');
    $('.modal-body-edit-incident').load(pathContratoAddAnomaly, function () {});
    $("#editIncidentModal").modal('show');
//    $('.modal-body-new-incident').load('php/contentAddIncident.php', function () {
//        clearAll();
//    });
}

function showDelete(theId) {
    $(".modal-header #incidentIdDelete").html(theId);
    $("#modalConfirmDelete").modal('show');
}

var modalConfirmDeleteAnomaly = function (callback) {
    $("#modal-btn-delete-anomaly-yes").on("click", function () {
        callback(true);
        $("#modalConfirmDelete").modal('hide');
    });
    $("#modal-btn-delete-anomaly-no").on("click", function () {
        callback(false);
        $("#modalConfirmDelete").modal('hide');
    });
};
modalConfirmDeleteAnomaly(function (confirm) {
    if (confirm) {
        var theVal = $(".modal-header #incidentIdDelete").html();
        $.ajax({
            url: pathContratoDeleteAnomaly,
            type: 'POST',
            dataType: 'json',
            data: {'id': theVal},
            success: function (data) {
                location.reload();
            },
            error: function (xhr, status) {
                alert(messageErrorProcessing);
            }
        });
    } else {
    }
});
// unprogress
var modalConfirmUnprogress = function (callback) {
    $("#modal-unprogress-btn-si").on("click", function () {
        callback(true);
        $("#unprogressincidentmodal").modal('hide');
    });
    $("#modal-unprogress-btn-no").on("click", function () {
        callback(false);
        $("#unprogressincidentmodal").modal('hide');
    });
};
modalConfirmUnprogress(function (confirm) {
    if (confirm) {
        var theVal = $(".modal-header #incidentIdUnprogress").val();
        var theStage = $(".modal-header #stageUnprogress").val();
        var theRubro = $(".modal-header #rubroUnprogress").val();
        $.ajax({
            type: "POST",
            url: pathContratoUnapproveAnomaly,
            data: {'incident_id': theVal, 'stage': theStage, 'rubro': theRubro},
            success: function (text) {
                text = JSON.parse(text);
                if (text.message === "success") {
                    $("#unprogressincidentmodal").modal('hide');
                    reloadIncidentTable(theVal);
                } else {
                    alert("Hubo un error al procesar los datos.\n" + text);
                }
            }
        });
    }
});
function reloadIncidentTable(theId) {
    $('.modal-body-show-incident').load('../../modal/anomaly/show/' + theId, function () {});
}

function progressStage(theId) {
    $('.modal-body-new-stage').html('<div align="center"><img height="100" src="' + loadingGif + '"></div>');
    $('.modal-body-new-stage').load('../../admin/modal/anomaly/show-progress-update/' + theId, function () {});
    $("#progressStageModal").modal('show');
}

function showUnprogress(theId, theStage, theRubro) {
    $(".modal-header #incidentIdUnprogress").val(theId);
    $(".modal-header #stageUnprogress").val(theStage);
    $(".modal-header #rubroUnprogress").val(theRubro);
    $("#unprogressincidentmodal").modal('show');
}

function showExportWord(theId) {
    $('.modal-body-export-word').html('<div align="center"><img height="100" src="' + loadingGif + '"></div>');
    $('.modal-body-export-word').load('../../admin/modal/anomaly/show-export-word/' + theId, function () {});
    $("#exportWordModal").modal('show');
}
function closeExportWordModal() {
    $("#exportWordModal").modal('hide');
}

function showSendMessage(theId) {
    $('.modal-body-send-message').html('<div align="center"><img height="100" src="' + loadingGif + '"></div>');
    $('.modal-body-send-message').load('../../admin/modal/anomaly/show-create-message/' + theId, function () {});
    $("#sendMessageModal").modal('show');
}
