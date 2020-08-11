
//https://stackoverflow.com/questions/33728943/how-to-add-removefile-option-in-dropzone-plugin
// default messages: https://stackoverflow.com/questions/17702394/how-do-i-change-the-default-text-in-dropzone-js


Dropzone.prototype.defaultOptions.dictFileTooBig = "El archivo es demasiado grande ({{filesize}} MB). Máximo aceptado: {{maxFilesize}} MB.";
Dropzone.prototype.defaultOptions.dictRemoveFile  = "Eliminar";


Dropzone.prototype.defaultOptions.dictDefaultMessage = "Deposite aquí o cliquee para agregar fotos";
Dropzone.prototype.defaultOptions.dictFallbackMessage = "Su explorador no permite depositar fotos";
Dropzone.prototype.defaultOptions.dictFallbackText = "Por favor utilice el botón para guardar fotos";
Dropzone.prototype.defaultOptions.dictInvalidFileType = "Este tipo de archivo no está permitido";
Dropzone.prototype.defaultOptions.dictResponseError = "El servidor respondió con el código {{statusCode}}";
Dropzone.prototype.defaultOptions.dictCancelUpload = "Anular";
Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Desea realmente anular esta acción?";
Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "No puede guardar esta foto";

Dropzone.autoDiscover = false;


// var keys = Object.keys(myObject);

Dropzone.options.imageuploadstage = {
        method: "post",
		parallelUploads: 100,
        uploadMultiple: true,
		addRemoveLinks: true,
		maxFilesize: 300,
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.txt,.doc,.docx,.xls,.xlsx,.xlsm,.ods,.csv",
        autoProcessQueue: true,
        init: function () {
            myDropzone = this;
            this.on("addedfile", function (file) {
					
            });
            this.on("sendingmultiple", function (file) {
                // And disable the start button
               // submitButton.setAttribute("disabled", "disabled");
            });
            this.on("completemultiple", function (file) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                   // submitButton.removeAttribute("disabled");
                }

            });
			
			  this.on("success", function(file, response) {
				//file.previewElement.parentNode.removeChild(file.previewElement);
				
				// var keys = Object.keys(file.previewElement);
				//file.previewElement.removeChild(file.previewElement.childNodes[8]);
				//alert($(file.previewElement).find("a"));
				$(file.previewElement).find("a").remove();
				
			  });
			  this.on("successmultiple", function(file, response) {
				//file.previewElement.parentNode.removeChild(file.previewElement);
				
				// var keys = Object.keys(file.previewElement);
				//file.previewElement.removeChild(file.previewElement.childNodes[8]);
				//alert($(file.previewElement).find("a"));
				$(file.previewElement).find("a").remove();
				
			  });
			  
            this.on("successmultiple", function (file, response) {
            });
            this.on("error", function (file, errorMessage) {
            });
        }
    };
