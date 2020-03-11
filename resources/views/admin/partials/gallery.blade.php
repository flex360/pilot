<p>Only upload images with with a longest dimension of 2000 pixels or smaller.</p>
<div class="input-group">
    <span class="input-group-btn">
        <span class="btn btn-primary btn-file">
            Add Images&hellip; {!! Form::file('gallery[]', array('multiple' => true, 'class' => 'gallery-input')) !!}
        </span>
    </span>
    <input type="text" class="form-control" readonly>
</div>

<div id="gallery-container" class="gallery-container">
    @if (is_array($item->gallery))
        @foreach ($item->gallery as $index => $image)
            <div class="gallery-image">
                <img class="gallery-img" src="{{ $image['path'] or '' }}">
                <input type="hidden" name="gallery_existing[{{ $index }}][path]" value="{{ $image['path'] or ''}}">

                <div class="controls">
                    <span class="image-edit badge alert-info"><i class="fa fa-pencil"></i></span>
                    <span class="image-delete badge alert-danger"><i class="fa fa-trash"></i></span>
                </div>

                <div class="modal fade meta-modal">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Image Information</h4>
                      </div>
                      <div class="modal-body">

                        <div class="form-group">
                            <label for="">Title</label>
                            <input type="text" name="gallery_existing[{{ $index }}][title]" class="form-control" value="{{ $image['title'] or '' }}">
                        </div>

                        <div class="form-group">
                            <label for="">Caption</label>
                            <textarea id="gallery-caption-{{ $index }}" name="gallery_existing[{{ $index }}][caption]" class="form-control" cols="30" rows="10">{{ $image['caption'] or '' }}</textarea>
                            <?php // <pre id="gallery-caption-edit-{{ $index }}" class="gallery-caption-edit">{{{ $image['caption'] or '' }}}</pre> ?>
                        </div>

                        <div class="form-group">
                            <label for="">Extra</label>
                            <input type="text" name="gallery_existing[{{ $index }}][extra]" class="form-control" value="{{ $image['extra'] or '' }}">
                        </div>

                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

            </div>
        @endforeach
    @endif
</div>
