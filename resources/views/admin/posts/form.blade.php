@extends('pilot::layouts.admin.panel')

@section('panel-heading', ($item->exists ? 'Edit' : 'Add') . ' News Post')

@section('buttons')

    @if ($item->exists)

        <a href="{{ $item->url() }}" target="_blank" class="btn btn-info btn-sm">Preview</a>

    @endif

@endsection

@section('panel-body')

    <div class="module">

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="index-nav-tabs">
              <li class="nav-item active">
                <a class="nav-link active" role="tab" data-toggle="tab" href="#content">Content</a>
              </li>
              @if (config('pilot.plugins.news.fields.gallery', true))
              <li class="nav-item">
                <a class="nav-link" role="tab" data-toggle="tab" href="#gallery">Gallery</a>
              </li>
              @endif
              @if (config('pilot.plugins.news.fields.slug', true) || config('pilot.plugins.news.fields.summary', true))
              <li class="nav-item">
                <a class="nav-link" role="tab" data-toggle="tab" href="#metadata">Metadata</a>
              </li>
              @endif
           </ul>
       </div>

        {!! Form::model($item, $formOptions) !!}

            <div class="tab-content">

                <div class="tab-pane active" id="content">

                    @if (config('pilot.plugins.news.fields.title', true))
                    <div class="form-group">

                        {!! Form::label('title', 'Title') !!}

                        {!! Form::text('title', null, array('class' => 'form-control', 'autofocus' => true)) !!}

                    </div>
                    @endif

                    @if (config('pilot.plugins.news.fields.body', true))
                    <div class="form-group">

                        {!! Form::label('body', 'Body') !!}

                        {!! Form::textarea('body', null, array('class' => 'form-control wysiwyg-editor')) !!}

                    </div>
                    @endif

                    <div class="row">

                        @if (config('pilot.plugins.news.fields.horizontal_featured_image', true))
                        <div class="form-group col-lg-6">

                            <label for="" title="">
                                Horizontal Featured Image
                            </label>

                            <!-- Background Color Swatch Radio Button Selector -->
                            @if (config('pilot.plugins.news.fields.image_background_swatch_picker', true))

                            <div class="help-block">
                                Select a background color if you don't have an image, and your logo will 
                                automatically display in front of this background color. Or upload your own image to show up at the top of this post.<br>
                            </div>

                            <div class="color-swatch-picker">
                                <ul>
                                @foreach (config('pilot.plugins.news.fields.background-color-options') as $color)
                               
                                    <li>
                                        <label>
                                            {!! Form::radio('fi_background_color', $color, $item->fi_background_color == $color || (UrlHelper::getPart(3) == 'create' && $loop->first) ? true : false) !!}
                                            {{-- <input type="radio" name="fi_background_color" value="black" {{ UrlHelper::getPart(3) == 'create' && $loop->first ? 'checked' : '' }}> --}}
                                            <span class="swatch" style="background-color: {{ $color }}"></span> 
                                        </label>
                                    </li>
                                @endforeach
                                </ul> 
                            </div> 
                            @endif

                            <div class="help-block">
                                For the entire image to display, it needs to be a 5:2 ratio. We recommend minimum 1600:640px for quality.
                            </div>

                            <?php $field = \Jzpeepz\Dynamo\DynamoField::make(['key' => 'horizontal_featured_image', 'options' => ['maxWidth' => 2000]]); ?>

                            @include('dynamo::bootstrap4.partials.fields.singleImage', ['display' => true, 'field' => $field])

                        </div>
                        @endif

                    </div>

                    <div class="row">

                        @if (config('pilot.plugins.news.fields.vertical_featured_image', true))
                        <div class="form-group col-lg-6">

                            <label for="" title="">
                                Vertical Featured Image
                            </label>

                            <div class="help-block">
                                For the entire image to display, it needs to be a 5:2 ratio. We recommend minimum 1600:640px for quality.
                            </div>

                            <?php $field = \Jzpeepz\Dynamo\DynamoField::make(['key' => 'vertical_featured_image', 'options' => ['maxWidth' => 2000]]); ?>

                            @include('dynamo::bootstrap4.partials.fields.singleImage', ['display' => true, 'field' => $field])

                        </div>
                        @endif

                    </div>

                    <div class="row">

                        @if (config('pilot.plugins.news.fields.publish_date', true))
                        <div class="form-group col-lg-6">

                            {!! Form::label('published_on', 'Publish Date') !!}

                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="If the Post Status is set to published, it will show up on the front-end of the site at this exact date and time."></i>

                            {!! Form::text('published_on', $item->published_on->format('n/j/Y g:i a'), array('class' => 'datetimepicker form-control')) !!}

                        </div>
                        @endif

                        @if (config('pilot.plugins.news.fields.status', true))
                        <div class="form-group col-lg-6">

                            {!! Form::label('status', 'Status') !!}

                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="If set to Draft, the Post will only be visible here in the CMS. If set to published, and the Published Date has been met,
                                   the Post will be on the front-end of the site."></i>

                            {!! Form::select('status', PilotPost::getStatuses(), null, array('class' => 'form-control')) !!}

                        </div>
                        @endif

                    </div>

                    @if (config('pilot.plugins.news.fields.tags', true))
                    <div class="form-group">

                        {!! Form::label('tags', 'Tags') !!}

                        <i style="font-size: 16px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                        title="You can either search and select tags that already in the system or create new tags by typing out 'Dogs' for example and pressing Enter."></i>

                        
                        {!! Form::select('tags[]', $tags, null, ['multiple' => true, 'class' => 'form-control chosen-select']) !!}

                    </div>
                    @endif

                    @if (config('pilot.plugins.news.fields.sticky_post', true))
                    <div class="form-group">

                        {!! Form::label('sticky', 'Sticky Post?') !!}

                        {!! Form::checkbox('sticky') !!}
                        <div class="help-block">
                            Check this to keep at the top of your news feed until it's un-checked.<br>

                            If multiple posts are marked "sticky" then they will be displayed from newest to oldest before the "live" list begins. (We recommend a max of 3 sticky posts at any given time)
                        </div>

                    </div>
                    @endif

                </div>

                @if (config('pilot.plugins.news.fields.gallery', true))
                <div class="tab-pane" id="gallery">
                        @include('dynamo::bootstrap4.partials.fields.gallery', ['display' => true, 'field' => \Jzpeepz\Dynamo\DynamoField::make(['key' => 'gallery'])])
                </div>
                @endif


                <div class="tab-pane" id="metadata">

                    @if (config('pilot.plugins.news.fields.slug', true))
                    <div class="form-group">

                        {!! Form::label('slug', 'Slug') !!}

                        <i style="font-size: 16px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                        title="If left blank, the slug of the URL will be set automatically based on the title of the post, but can overridden here."></i>

                        {!! Form::text('slug', null, array('class' => 'form-control')) !!}

                    </div>
                    @endif

                    @if (config('pilot.plugins.news.fields.summary', true))
                    <div class="form-group">

                        {!! Form::label('summary', 'Summary') !!}

                        {!! Form::textarea('summary', null, array('class' => 'form-control character-counter-not-limited', 'max-character' => 120)) !!}

                    </div>
                    @endif

                </div>

            </div>

            <button class="btn btn-primary">Save</button>
            <a class="form-cancel" href="{{ route('admin.post.index') }}">Cancel</a>

        {!! Form::close() !!}

        @if (Auth::user()->isAdmin() && $item->exists)
            {!! Form::model($item, array('route' => array('admin.post.destroy', $item->id), 'method' => 'delete', 'class' => 'delete-form float-right', 'onsubmit' => 'return confirm(\'Are you sure?\');')) !!}
                <button class="btn btn-danger float-right"><i class="fa fa-trash-o"></i> Delete</button>
            {!! Form::close() !!}
        @endif

    </div>

    {{-- @include('pilot::admin.posts.tags', compact('tags')) --}}

@endsection
