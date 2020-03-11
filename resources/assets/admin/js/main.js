$(function () {

    $('.delete-form').submit(function(){
        var sure = confirm('Are you sure?');

        return sure;
    });

    // Froala license
    $.Editable.DEFAULTS.key = '4Wa1WDPTf1ZNRGb1OG1g1==';

    /* froala editor */
    if ($('.wysiwyg-editor').length > 0) {
        //$('.wysiwyg-editor').trumbowyg();
        $('.wysiwyg-editor').editable({
            inlineMode: false,
            height: 300,
            theme: 'dark',
            /* buttons: ["bold", "italic", "underline", "strikeThrough", "subscript", "superscript", "fontFamily", "fontSize", "color", "formatBlock", "blockStyle", "align", "insertOrderedList", "insertUnorderedList", "outdent", "indent", "selectAll", "createLink", "insertImage", "insertVideo", "undo", "redo", "html", "save", "insertHorizontalRule", "table", "uploadFile"] */
            buttons: ["bold", "italic", "underline", "strikeThrough", "fontFamily", "fontSize", "color", "sep", "formatBlock", "blockStyle", "align", "insertOrderedList", "insertUnorderedList", "outdent", "indent", "sep", "createLink", "insertImage", "insertVideo", "uploadFile", "table", "insertHorizontalRule", "undo", "redo", "html"],
            imageUploadURL: '/assets/upload',
            imagesLoadURL: '/assets/get'
        });
    }

    if ($('.datetimepicker').length > 0) {
        $('.datetimepicker').datetimepicker();
    }

    if ($('.datepicker').length > 0) {
        $('.datepicker').datetimepicker({
            pickTime: false
        });
    }

    if ($('.timepicker').length > 0) {
        $('.timepicker').datetimepicker({
            pickDate: false
        });
    }

    /* dropzone */
    if ($('#template').length > 0)
    {
        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        $('.upload-area').each(function(index){

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

          myDropzone.on("sending", function(file) {
            //document.querySelector(".upload-instructions").style.display = "none";
            // Show the total progress bar when upload starts
            //document.querySelector("#total-progress").style.opacity = "1";
            // And disable the start button
            //file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
          });

          // Hide the total progress bar when nothing's uploading anymore
          myDropzone.on("queuecomplete", function(progress) {
            //document.querySelector("#total-progress").style.opacity = "0";
          });

          myDropzone.on("success", function(file, xhr) {
            console.log(file);
            $(file.previewElement).find('.uploading').hide();
            $(file.previewElement).find('.success').show();
            console.log(xhr);
            $(file.previewElement).find('img').attr('data-src', xhr.link);

            // drop all the paths into the textarea
            var sources = [];
            var images = $(file.previewElement).closest('.upload-area').find('.preview img');
            $.map(images, function(image, index){
              sources.push($(image).attr('data-src'));
            });

            $(file.previewElement).closest('.upload-area').find('textarea').val(JSON.stringify(sources));
            console.log(sources);
          });

        });

    }

    /* File input */
    $(document).on('change', '.btn-file :file', function() {
      var input = $(this),
          numFiles = input.get(0).files ? input.get(0).files.length : 1,
          label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
      input.trigger('fileselect', [numFiles, label]);
    });

    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if( input.length ) {
            input.val(log);
        } else {
            if( log ) alert(log);
        }

    });

    /* Trigger for gallery meta modals */
    $('.gallery-image .image-edit').on('click', function(){
        $(this).closest('.gallery-image').find('.meta-modal').modal('show');
    });

    /* Delete gallery image */
    $('.gallery-image .image-delete').on('click', function(){
        $(this).closest('.gallery-image').remove();
    });

    /* uploader code */
    var uploadButtonClicked = null;
    var uploadTarget = null;

    $('.uploader-combo').each(function()
    {
        var input = $(this).find('.uploader-combo-input input');

        var val = input.val();

        console.log(val);

        if (val)
        {

            // add preview image if link has an allowed extension
            if (extAllowed(val))
            {
                // show preview image
                $(this).find('.uploader-combo-preview').html('<img src="'+val+'">');
            }
            else
            {
                // clear preview area
                $(this).find('.uploader-combo-preview').html('');
            }

        }
    });

    $('.btn-upload').click(function()
    {
        uploadButtonClicked = $(this);

        uploadTarget = $(this).attr('data-target');

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

    $('#file-upload-input').change(function()
    {
        $('#upload-form').submit();
    });

    $('#upload-form').submit(function()
    {
        var formData = new FormData($(this)[0]);

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();

                //Upload progress
                xhr.upload.addEventListener("progress", function(evt){
                  if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    // console.log(percentComplete);
                    uploadButtonClicked.closest('.uploader-combo').find('.upload-progress').show().find('.bar').css('width', (percentComplete*100)+'%');
                  }
                }, false);

                return xhr;
            },
            url: '/assets/upload',
            type: 'POST',
            data: formData,
            async: true,
            beforeSend: function(jqXHR, settings) {
                // add spinner
                uploadButtonClicked.append('<span class="upload-spinner">&nbsp;&nbsp;<i class="fa fa-spin fa-circle-o-notch"></i></span>');

                // progress bar
                uploadButtonClicked.closest('.uploader-combo-input').append('<div class="upload-progress" style="display: none;"><div class="bar" style="width: 0px;"></div></div>');
            },
            success: function(data) {
                // set the input value
                $('#' + uploadTarget).val(data.link);

                // remove the upload spinner
                $('.upload-spinner').remove();

                // remove the upload progress bar
                uploadButtonClicked.closest('.uploader-combo').find('.upload-progress').remove();

                // add preview image if link has an allowed extension
                if (extAllowed(data.link))
                {
                    // show preview image
                    uploadButtonClicked.closest('.uploader-combo').find('.uploader-combo-preview').html('<img src="'+data.link+'">');
                }
                else
                {
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

    $('.uploader-combo-delete').click(function()
    {
        $(this).closest('.uploader-combo').find('.uploader-combo-preview').html('');
        $(this).closest('.uploader-combo').find('.uploader-combo-input input').val('');
        return false;
    });

    function extAllowed(link)
    {
        // get extension of link
        var ext = link.substr(link.length-3, 3).toLowerCase();

        var allowedExts = ['jpg','png','gif'];

        return allowedExts.indexOf(ext) >= 0;
    }

    /* end uploader code */

    // date time picker
    if ($('.datetimepicker').length > 0)
    {
        $('.datetimepicker').each(function () {
            $('#'+this.id).datetimepicker({
                format: 'n/j/Y g:i a',
                formatTime: 'g:i a',
            });
        });
    }

    // Chosen select
    if ($('.chosen-select').length > 0)
    {
        $('.chosen-select').chosen();
    }

    // Unslider
    if ($('.blog-slider').length > 0)
    {
        $('.blog-slider').unslider({
            delay: 6000,
            dots: true
        });
    }

    if ($('.event-slider').length > 0)
    {
        $('.event-slider').unslider({
            delay: 6000,
            dots: true
        });
    }

    // FullCalendar
    if ($('#fullCalendar').length > 0)
    {
        $('#fullCalendar').fullCalendar({
            events: '/intranet/calendar/json'
        });
    }

    // Sortable
    if ($('#gallery-container').length > 0)
    {
        var gallery = document.getElementById("gallery-container");
        Sortable.create(gallery);
    }

    // inline editor code
    if ($('.inline-editable').length > 0)
    {
        $('body').on('click', '.inline-editable', function()
        {
            // reset previous editors
            $('.inline-editable').show();
            $('.inline-editor').hide();

            // hide the clicked text
            $(this).hide();

            // show the associated editor
            $(this).closest('.inline-editable-container').find('.inline-editor').show();
        });

        $('body').on('click', '.inline-editable-save', function()
        {
            var $editor = $(this).closest('.inline-editor');

            // get the new value from the editor
            var val = $editor.find('input').val();

            var id = $editor.find('input').attr('data-id');

            var endpoint = $editor.find('input').attr('data-endpoint');

            // send value to endpoint via ajax
            $.post(endpoint, { name: val, id: id, _method: 'PUT' }, function(data)
            {
                console.log(data);
            });

            var $editable = $(this).closest('.inline-editable-container').find('.inline-editable');

            // set the new value
            $editable.text(val);

            // hide editor and show editable
            $editor.hide();
            $editable.show();
        });

        $('body').on('click', '.inline-editable-cancel', function()
        {
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
                        <span class="inline-editor" style="display: none;"><input type="text" value="{{ item.name }}" data-id="{{ item.id }}" data-endpoint="{{ endpoint }}"> <br> <button type="button" class="inline-editable-save btn btn-primary btn-sm">Save</button> <button type="button" class="inline-editable-cancel btn-secondary btn-sm">Cancel</button> <button type="button" class="inline-editable-delete btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteTagModal">Delete</button></span></span>  \
                    </p>';

    if ($('.simple-create').length > 0)
    {
        $('.simple-create button').on('click', function()
        {
            var $input = $(this).closest('.simple-create').find('input');

            var val = $input.val();

            var endpoint = $input.attr('data-endpoint');

            // send value to endpoint via ajax
            $.post(endpoint, { name: val }, function(data)
            {
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

});
