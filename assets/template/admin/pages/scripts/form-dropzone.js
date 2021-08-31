var FormDropzone = function () {


    return {
        //main function to initiate the module
        init: function () {  
            Dropzone.options.myDropzone = {
                init: function() {
                    this.on("addedfile", function(file) {
                        // Create the remove button
                        var removeButton = Dropzone.createElement("<button class='btn btn-sm btn-block'>Remove file</button>");
                        
                        // Capture the Dropzone instance as closure.
                        var _this = this;

                        // Listen to the click event
                        removeButton.addEventListener("click", function(e) {
                          // Make sure the button click doesn't submit the form:
                          e.preventDefault();
                          e.stopPropagation();

                          // Remove the file preview.
                          _this.removeFile(file);
                          //alert(file.name);
                          // If you want to the delete the file on the server as well,
                          // you can do the AJAX request here.
                        });

                        // Add the button to the file preview element.
                        file.previewElement.appendChild(removeButton);
                    });
                    
                    this.on("success", function(file, data) {
                        var fileData = $.parseJSON(data);
                        alert(fileData.id + '   ' + fileData.folder);
                        var input_id = Dropzone.createElement('<input type="hidden" name="image_id" value="'+ fileData.id +'" /><input type="hidden" name="folder" value="'+ fileData.folder +'" />');
                        file.previewElement.appendChild(input_id);
                    });
                },
                paramName: 'iioooop',
                uploadMultiple: false,
                parallelUploads: 1,
                params: {id:55, folder: 201505},
                autoProcessQueue: true,
                autoQueue: true,
                maxFilesize: 256,
                maxFiles: 1,
                acceptedFiles: 'image/*',
                dictDefaultMessage: "Drop files here to upload",
                  dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
                  dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
                  dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
                  dictInvalidFileType: "You can't upload files of this type.",
                  dictResponseError: "Server responded with {{statusCode}} code.",
                  dictCancelUpload: "Cancel upload",
                  dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
                  dictRemoveFile: "Remove file",
                  dictRemoveFileConfirmation: null,
                  dictMaxFilesExceeded: "You can not upload any more files."           
            }
            
            $('.btn-remove').click(function(e){
                e.preventDefault();
                e.stopPropagation();
                alert($(this).parent('.dz-preview').data('filename'));
                $(this).parent('.dz-preview').remove();
                
                //alert('111');
            });
        }
    };
}();