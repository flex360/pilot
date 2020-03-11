<div class="upload-area" id="dropzone-{{ $column->name }}">

    <div class="upload-inner">
        
        @foreach ($images as $image)
            <div class="file-row dz-image-preview">
                <div class="preview">
                    <img data-dz-thumbnail src="{{ $image }}" data-src="{{ $image }}" />
                </div>
            </div>
        @endforeach

    </div>
    
    <button class="upload-add" onclick="return false;"><i class="fa fa-plus fa-2x"></i></button>

    {{ $input }}
  
</div>

<div style="display: none;">
    <div id="template" class="file-row">
        <!-- This is used as the file preview template -->
        <div class="preview">
            <img data-dz-thumbnail />
            <span class="badge alert-info uploading"><i class="fa fa-spinner fa-spin fa-2x"></i></span>
            <span class="badge alert-success success" style="display: none;"><i class="fa fa-check fa-2x"></i></span>
        </div>
    </div>
</div>