$(function () {

    if ($('.datetimepicker').length > 0) {
        $('.datetimepicker').datetimepicker();
    }

    if ($('.datepicker').length > 0) {
        $('.datepicker').datetimepicker({
            timepicker: false
        });
    }

    if ($('.timepicker').length > 0) {
        $('.timepicker').datetimepicker({
            datepicker: false
        });
    }

    /* dropzone */
    if ($('#template').length > 0) {
        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        $('.upload-area').each(function (index) {

            var id = $(this).attr('id');
            var myDropzone = new Dropzone('#' + id, { // Make the whole body a dropzone
                url: "/assets/upload", // Set the url
                thumbnailWidth: 120,
                thumbnailHeight: 120,
                parallelUploads: 20,
                previewTemplate: previewTemplate,
                autoProcessQueue: true, // Make sure the files aren't queued until manually added
                previewsContainer: '#' + id + " .upload-inner", // Define the container to display the previews
                clickable: '#' + id + ' .upload-add' // Define the element that should be used as click trigger to select files.
            });

            myDropzone.on("sending", function (file) {
                //document.querySelector(".upload-instructions").style.display = "none";
                // Show the total progress bar when upload starts
                //document.querySelector("#total-progress").style.opacity = "1";
                // And disable the start button
                //file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
            });

            // Hide the total progress bar when nothing's uploading anymore
            myDropzone.on("queuecomplete", function (progress) {
                //document.querySelector("#total-progress").style.opacity = "0";
            });

            myDropzone.on("success", function (file, xhr) {
                console.log(file);
                $(file.previewElement).find('.uploading').hide();
                $(file.previewElement).find('.success').show();
                console.log(xhr);
                $(file.previewElement).find('img').attr('data-src', xhr.link);

                // drop all the paths into the textarea
                var sources = [];
                var images = $(file.previewElement).closest('.upload-area').find('.preview img');
                $.map(images, function (image, index) {
                    sources.push($(image).attr('data-src'));
                });

                $(file.previewElement).closest('.upload-area').find('textarea').val(JSON.stringify(sources));
                console.log(sources);
            });

        });

    }

    /* File input */
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });

    $('.btn-file :file').on('fileselect', function (event, numFiles, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if (input.length) {
            input.val(log);
        } else {
            if (log) alert(log);
        }

    });

    /* Trigger for gallery meta modals */
    $('.gallery-image .image-edit').on('click', function () {
        $(this).closest('.gallery-image').find('.meta-modal').modal('show');
    });

    /* Delete gallery image */
    $('.gallery-image .image-delete').on('click', function () {
        $(this).closest('.gallery-image').remove();
    });

    /* uploader code */
    var uploadButtonClicked = null;
    var uploadTarget = null;

    $('.uploader-combo').each(function () {
        var input = $(this).find('.uploader-combo-input input');

        var val = input.val();

        // console.log(val);

        if (val) {

            // add preview image if link has an allowed extension
            if (extAllowed(val)) {
                $('.uploader-empty').hide();

                // show preview image
                $(this).find('.uploader-combo-preview').html('<img src="' + val + '">');
            }
            else {
                // clear preview area
                $(this).find('.uploader-combo-preview').html('');
            }

        }
    });

    $('.btn-upload').click(function () {
        uploadButtonClicked = $(this);

        uploadTarget = $(this).attr('data-target');

        params = $(this).attr('data-params');

        $('#file-upload-params').val(params);

        $('#file-upload-input').click();

        return false;

        // var percentComplete = 0.1;
        // setInterval(function()
        // {
        //     console.log(percentComplete);
        //     uploadButtonClicked.closest('.form-group').find('.upload-progress').show().find('.bar').css('width', (percentComplete*100)+'%');
        //     percentComplete += 0.1;
        // }, 1000);
    });

    $('#file-upload-input').change(function (event) {
        // Read in file
        var file = event.target.files[0];

        // Ensure it's an image
        if (file.type.match(/image.*/)) {
            console.log('An image has been loaded');
            uploadButtonClicked.append('<span class="upload-spinner">&nbsp;&nbsp;<i class="fa fa-spin fa-circle-o-notch"></i></span>');

            // Load the image
            var reader = new FileReader();
            reader.onload = function (readerEvent) {
                var image = new Image();
                image.onload = function (imageEvent) {

                    // Resize the image
                    var canvas = document.createElement('canvas'),
                        max_size = 2000,// TODO : pull max size from a site config
                        width = image.width,
                        height = image.height;

                    canvas.width = width;
                    canvas.height = height;

                    if (width > height) {
                        if (width > max_size) {
                            height *= max_size / width;
                            width = max_size;
                        }
                    } else {
                        if (height > max_size) {
                            width *= max_size / height;
                            height = max_size;
                        }
                    }

                    canvas.getContext('2d').drawImage(image, 0, 0);
                    var HERMITE = new Hermite_class();
                    HERMITE.resample(canvas, width, height, true, function (ctx) {
                        var dataUrl = ctx.canvas.toDataURL('image/jpeg');
                        var resizedImage = dataURLToBlob(dataUrl);
                        $.event.trigger({
                            type: "imageResized",
                            blob: resizedImage,
                            url: dataUrl
                        });
                    });
                }
                image.src = readerEvent.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            $('#upload-form').submit();
        }

    });

    /* Handle image resized events */
    var resizedImage = null;

    $(document).on("imageResized", function (event) {
        var data = new FormData(document.getElementById('upload-form'));
        if (event.blob && event.url) {
            resizedImage = event.blob;

            $('#upload-form').submit();
        }
    });

    $('#upload-form').submit(function () {
        var formData = new FormData($(this)[0]);

        if (resizedImage != null) {
            formData.set('file', resizedImage, 'resized-image.jpg');
            resizedImage = null;
        }

        $.ajax({
            xhr: function () {
                var xhr = new window.XMLHttpRequest();

                //Upload progress
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with upload progress
                        // console.log(percentComplete);
                        uploadButtonClicked.closest('.uploader-combo').find('.upload-progress').show().find('.bar').css('width', (percentComplete * 100) + '%');
                    }
                }, false);

                return xhr;
            },
            url: '/assets/upload',
            type: 'POST',
            data: formData,
            async: true,
            beforeSend: function (jqXHR, settings) {
                // add spinner
                uploadButtonClicked.append('<span class="upload-spinner">&nbsp;&nbsp;<i class="fa fa-spin fa-circle-o-notch"></i></span>');

                // progress bar
                uploadButtonClicked.closest('.uploader-combo-input').append('<div class="upload-progress" style="display: none;"><div class="bar" style="width: 0px;"></div></div>');
            },
            success: function (data) {
                // set the input value
                $('#' + uploadTarget).val(data.link);

                // remove the upload spinner
                $('.upload-spinner').remove();

                // remove the upload progress bar
                uploadButtonClicked.closest('.uploader-combo').find('.upload-progress').remove();

                // add preview image if link has an allowed extension
                if (extAllowed(data.link)) {
                    $('.uploader-empty').hide();

                    // show preview image
                    uploadButtonClicked.closest('.uploader-combo').find('.uploader-combo-preview').html('<img src="' + data.link + '">');
                }
                else {
                    // clear preview area
                    uploadButtonClicked.closest('.uploader-combo').find('.uploader-combo-preview').html('');
                }

            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });

    $('.uploader-combo-delete').click(function () {
        $(this).closest('.uploader-combo').find('.uploader-combo-preview').html('');
        $(this).closest('.uploader-combo').find('.uploader-combo-input input').val('');
        $('.uploader-empty').show();
        return false;
    });

    function extAllowed(link) {
        // get extension of link
        var ext = link.substr(link.length - 3, 3).toLowerCase();

        var allowedExts = ['jpg', 'png', 'gif'];

        return allowedExts.indexOf(ext) >= 0;
    }

    /* end uploader code */

    // date time picker
    if ($('.datetimepicker').length > 0) {
        $('.datetimepicker').each(function () {
            $('#' + this.id).datetimepicker({
                format: 'n/j/Y g:i a',
                formatTime: 'g:i a',
            });
        });
    }

    // Chosen select
    if ($('.chosen-select').length > 0) {
        //$('.chosen-select').chosen();
        $('.chosen-select').select2({
            tags: true,
            tokenSeparators: [','],
            width: '100%'
        });
    }

    // Unslider
    if ($('.blog-slider').length > 0) {
        $('.blog-slider').unslider({
            delay: 6000,
            dots: true
        });
    }

    if ($('.event-slider').length > 0) {
        $('.event-slider').unslider({
            delay: 6000,
            dots: true
        });
    }

    // FullCalendar
    if ($('#fullCalendar').length > 0) {
        $('#fullCalendar').fullCalendar({
            events: '/intranet/calendar/json'
        });
    }

    // Sortable
    if ($('#gallery-container').length > 0) {
        var gallery = document.getElementById("gallery-container");
        Sortable.create(gallery);
    }

    // inline editor code
    if ($('.inline-editable').length > 0) {
        $('body').on('click', '.inline-editable', function () {
            // reset previous editors
            $('.inline-editable').show();
            $('.inline-editor').hide();

            // hide the clicked text
            $(this).hide();

            // show the associated editor
            $(this).closest('.inline-editable-container').find('.inline-editor').show();
        });

        $('body').on('click', '.inline-editable-save', function () {
            var $editor = $(this).closest('.inline-editor');

            // get the new value from the editor
            var val = $editor.find('input').val();

            var id = $editor.find('input').attr('data-id');

            var endpoint = $editor.find('input').attr('data-endpoint');

            // send value to endpoint via ajax
            $.post(endpoint, { name: val, id: id, _method: 'PUT', _token: $('[name=csrf-token]').attr('content') }, function (data) {
                console.log(data);
            });

            var $editable = $(this).closest('.inline-editable-container').find('.inline-editable');

            // set the new value
            $editable.text(val);

            // hide editor and show editable
            $editor.hide();
            $editable.show();
        });

        $('body').on('click', '.inline-editable-cancel', function () {
            var $editor = $(this).closest('.inline-editor');

            var $editable = $(this).closest('.inline-editable-container').find('.inline-editable');

            // reset the editors orignal value
            $editor.find('input').val($editable.text());

            // hide editor and show editable
            $editor.hide();
            $editable.show();
        });
    }

    // Handlebars partial
    var source = '<p class="inline-editable-container"> \
                        <span class="inline-editable">{{ item.name }}</span> \
                        <span class="inline-editor" style="display: none;"><input type="text" value="{{ item.name }}" data-id="{{ item.id }}" data-endpoint="{{ endpoint }}"> <br> <button type="button" class="inline-editable-save btn btn-primary btn-sm">Save</button> <button type="button" class="inline-editable-cancel btn-secondary btn-sm">Cancel</button> <button type="button" class="inline-editable-delete btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteTagModal">Delete</button></span> \
                    </p>';

    if ($('.simple-create').length > 0) {
        $('.simple-create button').on('click', function () {
            var $input = $(this).closest('.simple-create').find('input');

            var val = $input.val();

            var endpoint = $input.attr('data-endpoint');

            // send value to endpoint via ajax
            $.post(endpoint, { name: val, _token: $('[name=csrf-token]').attr('content') }, function (data) {
                // clear the input value
                $input.val('');

                // determine where to put the new html
                var $container = $input.closest('.simple-create').next('.simple-create-list');

                endpoint = endpoint + '/' + data.id;

                // render the template with the returned data
                var context = { item: data, endpoint: endpoint };

                var template = Handlebars.compile(source);

                var html = template(context);

                // append the new html
                $container.append(html);
            });
        });
    }

    /* page blocks */

    $('.block-delete').click(function (event) {
        event.preventDefault();

        var $el = $(this);
        var id = $el.attr('data-id');
        var token = $el.attr('data-token');

        $el.find('.block-delete-spin').show();

        $.post('/pilot/block/' + id, { '_method': 'delete', '_token': token }, function () {
            $el.find('.block-delete-spin').hide();
            $('#modal-block-' + id).modal('hide');
            $el.closest('.page-block-wrapper').find('.page-block').remove();
            // window.location = 'edit';
        });

        return false;
    });

    $('.block-sort-toggle').click(function () {
        $('.page-block-list').toggle();
        $('.page-block-sorter').toggle();

        return false;
    });

    if ($('#page-block-sorter').length > 0) {
        var el = document.getElementById('page-block-sorter');
        var sortable = Sortable.create(el);
    }

    /* page types */
    $('#page-type-save').click(function () {

        page_id = $(this).attr('data-page-id');

        token = $(this).attr('data-token');

        name = $('#page-type-name').val();

        $.post('/pilot/pagetype', { page_id: page_id, name: name, _token: token }, function () {
            window.location = 'edit';
        });

        return false;
    });

    // disable the enter key on the page form
    // $('.page-module').on('keyup keypress', function(e) {
    //   var code = e.keyCode || e.which;
    //   if (code == 13) {
    //     e.preventDefault();
    //     return false;
    //   }
    // });

    $(".category-dual-list").bootstrapDualListbox({
        // see next for specifications
    });

});

/* Utility function to convert a canvas to a BLOB */
var dataURLToBlob = function (dataURL) {
    var BASE64_MARKER = ';base64,';
    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        var parts = dataURL.split(',');
        var contentType = parts[0].split(':')[1];
        var raw = parts[1];

        return new Blob([raw], { type: contentType });
    }

    var parts = dataURL.split(BASE64_MARKER);
    var contentType = parts[0].split(':')[1];
    var raw = window.atob(parts[1]);
    var rawLength = raw.length;

    var uInt8Array = new Uint8Array(rawLength);

    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], { type: contentType });
}
/* End Utility function to convert a canvas to a BLOB      */

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

$(document).ready(function () {
    $('.character-limited').each(function () {
        handleCharacterLimit(this, true);
    });

    $('.character-limited').keyup(function () {
        handleCharacterLimit(this);
    });

    $('.character-counter-not-limited').each(function () {
        handleCharacterCounter(this, true);
    });

    $('.character-counter-not-limited').keyup(function () {
        handleCharacterCounter(this);
    });

    // Leave site? code
    $('form').dirtyForms();

});

function handleCharacterLimit(el, append = false) {
    if (append) {
        var html = $('<p class="help-block"></p>').html('<span class="character-counter"></span>/<span class="character-counter-total"></span>');
        $(el).closest('.form-group').append(html);
    }

    var maxLength = parseInt($(el).attr('maxlength'));
    $(el).closest('.form-group').find('.character-counter-total').text(maxLength);
    var length = $(el).val().length;
    // var length = maxLength-length;
    $(el).closest('.form-group').find('.character-counter').text(length);
}

/**
 * 
 * same as function above except is doesn't actually limit the input box characters. it just recommmends limit
 * used on pages module Meta Description field
 * 
 */
function handleCharacterCounter(el, append = false) {
    if (append) {
        var html = $('<p class="help-block"></p>').html('<span class="character-counter"></span>/<span class="character-counter-total"></span>');
        $(el).closest('.form-group').append(html);
    }

    var maxLength = parseInt($(el).attr('max-character'));
    $(el).closest('.form-group').find('.character-counter-total').text(maxLength);
    var length = $(el).val().length;
    if (length > maxLength) {
        $(el).closest('.form-group').find('.character-counter').css('color', 'red');
    } else {
        $(el).closest('.form-group').find('.character-counter').css('color', '#737373');
    }
    // var length = maxLength-length;
    $(el).closest('.form-group').find('.character-counter').text(length);
}
