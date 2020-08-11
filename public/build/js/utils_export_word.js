var theTable;
var pictures;
var picturesPaths;
var maxPics;
var id;
var urlApiExportWordDownload;
var urlApiExportWordUpdatePhotos;
var urlApiExportWordUpdateFields;

$(document).ready(function() {
    var data = document.querySelector('.js-data');
    pictures = JSON.parse(data.dataset.pictures);
    picturesPaths = JSON.parse(data.dataset.picturesPaths);
    maxPics = data.dataset.maxPics;
    id = data.dataset.id;
    urlApiExportWordDownload = data.dataset.urlApiExportWordDownload;
    urlApiExportWordUpdatePhotos = data.dataset.urlApiExportWordUpdatePhotos;
    urlApiExportWordUpdateFields = data.dataset.urlApiExportWordUpdateFields;
});

function qtyPics() {
    var ret = 0;
    for (i = 0; i < pictures.length; i++) {
        if (1 == pictures[i]) {
            ret++;
        }
        return ret;
    }
}

function grayOutPic(pic) {
    if (1 == pictures[pic]) {
        pictures[pic] = 0;
        document.getElementById("pic" + pic).style = "opacity: 0.2;";
    } else {
        if (qtyPics() > maxPics - 1) {
            alert("Se pueden agregar como máximo " + maxPics + " fotos");
        } else {
            pictures[pic] = 1;
            document.getElementById("pic" + pic).style = "opacity: 1;";
        }
    }
}

function save_word_pics(exportAfterSave) {
    var txt = "";
    var theIncident_id = id;
    for (i = 0; i < pictures.length; i++) {
        if (1 === pictures[i]) {
            txt = txt + picturesPaths[i] + ";";
        }
    }

    var theUrl = urlApiExportWordUpdatePhotos;
    var dataToSend = {
        'paths': txt,
        'incident_id': theIncident_id
    };
    $.ajax({
        url: theUrl,
        type: "POST",
        dataType: 'json',
        data: dataToSend,
        success: function(dataReceived) {
            if (exportAfterSave) {
                download_word();
            }
        },
        error: function(xhr, status) {}
    });
}

function save_word_fields(exportAfterSave) {
    var theReferencia = $("#form_referencia").val();
    var theComuna = $("#form_comuna").val();
    var theTexto_cuerpo = $("#form_texto").val();
    var theIncident_id = id;
    var theUrl = urlApiExportWordUpdateFields;
    var dataToSend = {
        'comuna': theComuna,
        'referencia': theReferencia,
        'texto_cuerpo': theTexto_cuerpo,
        'incident_id': theIncident_id
    };
    $.ajax({
        url: theUrl,
        type: "POST",
        dataType: 'json',
        data: dataToSend,
        success: function(dataReceived) {
            save_word_pics(exportAfterSave);
        },
        error: function(xhr, status) {}
    });
}

function download_word() {
    var theUrl = urlApiExportWordDownload;
    window.location = theUrl;
}