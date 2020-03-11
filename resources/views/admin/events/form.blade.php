@extends('pilot::layouts.admin.panel')

@section('panel-heading', ($item->exists ? 'Edit' : 'Add') . ' Event')

@section('buttons')

    @if ($item->exists)

        <a href="{{ $item->url() }}" target="_blank" class="btn btn-info btn-sm">Preview</a>

    @endif

@endsection

@section('panel-body')

    <div class="module">

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item active">
                <a class="nav-link active" role="tab" data-toggle="tab" href="#content">Content</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" role="tab" data-toggle="tab" href="#gallery">Gallery</a>
              </li>
           </ul>
       </div>

        {!! Form::model($item, $formOptions) !!}

            <div class="tab-content">

                <div class="tab-pane active" id="content">

                    <div class="form-group">

                        {!! Form::label('title', 'Title') !!}

                        {!! Form::text('title', null, array('class' => 'form-control', 'autofocus' => true)) !!}

                    </div>

                    <div class="row">

                        <div class="form-group col-lg-6">

                            {!! Form::label('start', 'Start Date/Time') !!}

                            <i id="tooltip-modal" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="modal" data-target="#events-modal" data-html="true"
                            title=""></i>

                            {!! Form::text('start', $item->start->format('n/j/Y g:i a'), array('class' => 'datetimepicker form-control')) !!}

                        </div>

                        <div class="form-group col-lg-6">

                            {!! Form::label('end', 'End Date/Time') !!}

                            {!! Form::text('end', $item->end->format('n/j/Y g:i a'), array('class' => 'datetimepicker form-control')) !!}

                        </div>

                    </div>

                    <div class="form-group">

                        {!! Form::label('short_description', 'Short Description') !!}

                        {!! Form::text('short_description', null, array('class' => 'form-control character-limited', 'maxlength' => 255)) !!}

                    </div>

                    <div class="form-group">

                        {!! Form::label('body', 'Description') !!}

                        {!! Form::textarea('body', null, array('class' => 'form-control wysiwyg-editor')) !!}

                    </div>

                    <div class="row">

                        <div class="form-group col-lg-6">

                            <label for="" title="">
                                Main Image
                            </label>

                            <?php $field = \Jzpeepz\Dynamo\DynamoField::make(['key' => 'image', 'options' => ['maxWidth' => 1600]]); ?>

                            @include('dynamo::bootstrap4.partials.fields.singleImage', ['display' => true, 'field' => $field])

                        </div>

                    </div>

                    <div class="row">

                        <div class="form-group col-lg-6">

                            {!! Form::label('published_at', 'Publish Date') !!}

                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="If the Event's Status is set to published, it will show up on the front-end of the site at this exact date and time."></i>

                            {!! Form::text('published_at', $item->published_at->format('n/j/Y g:i a'), array('class' => 'datetimepicker form-control')) !!}

                        </div>

                        <div class="form-group col-lg-6">

                            {!! Form::label('status', 'Status') !!}

                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="If set to Draft, the Event will only be visible here in the CMS. If set to published, and the Published Date has been met,
                                   the Event will be on the front-end of the site."></i>

                            {!! Form::select('status', PilotEvent::getStatuses(), null, array('class' => 'form-control')) !!}

                        </div>

                    </div>

                    <div class="form-group">

                        {!! Form::label('tags', 'Tags') !!}
                        <i style="font-size: 16px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                        title="You can either search and select tags that already in the system or create new tags by typing out 'Dogs' for example and pressing Enter."></i>

                        {!! Form::select('tags[]', $tags, $item->tags->pluck('id'), array('class' => 'form-control chosen-select', 'data-placeholder' => 'Choose Tags', 'multiple' => true)) !!}

                    </div>

                </div>

                <div class="tab-pane" id="gallery">

                    @include('dynamo::bootstrap4.partials.fields.gallery', ['display' => true, 'field' => \Jzpeepz\Dynamo\DynamoField::make(['key' => 'gallery'])])

                </div>

                <div class="tab-pane" id="metadata">

                    <?php /*
                    {!! Form::openGroup('summary', 'Summary') !!}

                        {!! Form::textarea('summary', null, array('class' => 'form-control')) !!}

                    {!! Form::closeGroup() !!}
                    */ ?>


                </div>

            </div>

            <button class="btn btn-primary">Save</button>
            <a class="form-cancel" href="{{ route('admin.event.index') }}">Cancel</a>

        {!! Form::close() !!}

        @if (Auth::user()->isAdmin() && $item->exists)
            {!! Form::model($item, array('route' => array('admin.event.destroy', $item->id), 'method' => 'delete', 'class' => 'delete-form float-right', 'onsubmit' => 'return confirm(\'Are you sure?\');')) !!}
                <button class="btn btn-danger float-right"> Delete</button>
            {!! Form::close() !!}
        @endif

    </div>


    {{-- Modal to help user understand events  --}}
    <div class="modal fade" id="events-modal" tabindex="-1" role="dialog">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">How Events Start/End times Will Display On The Frontend Of Your Website</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                </div>

                <div class="modal-body">

                    <div class="spacer-for-mobile-events-modal"></div>

                    <strong id="modal-events-heading">There are three special cases programmed for Start/End Dates on Events.</strong><br><br>
                    <ol>
                      <li>If the start and end dates are on the <b><i>same day</i></b>, just <b><i>different times</i></b>,<br>
                          (such as START: 4/23/2019 8:00 am, END: 4/23/2019 5:00 pm)<br>
                          the format displayed will be: 4/23/2019 2:11 pm - 4:00 pm. on the frontend of the website.</li>
                        <hr>
                      <li>If the start and end dates are the <b><i>same day</i></b> AND set to <b><i>12am-11:59pm ( 24 hours apart )</i></b>,
                          then the 'All Day' format is used: 4/23/2019</li>
                        <hr>
                      <li>if the start and end dates are <b><i>24 hours apart</i></b> but on <b><i>different days</i></b>, then this format is used: 4/23/2019 - 4/27/2019</li>
                    </ol>

                    <hr>

                    <p style="margin-left: 20px;">If none of the three special cases are used, then the <b><i>default format</i></b> will display on the front-end of the website:
                        4/23/2019 2:11 pm - 4/23/2019 4:11 pm</p>
                    <hr>
                    <p style="margin-left: 20px;">Try these three special cases out, and press the 'Preview' button in the top right corner of this page after you save the event
                        to see what it looks like on the front-end of the site.<p>

                </div>

                <?php /* <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div> */ ;?>

            </div><!-- /.modal-content -->

        </div><!-- /.modal-dialog -->

    </div><!-- /.modal -->

@endsection
