@extends('pilot::layouts.admin.panel')

@section('panel-heading', (isset($action) ? ucwords($action) : 'Edit') . ' ' . ($page->exists ? $page->title : 'Page'))

@section('buttons')
    @if ($page->exists)
        <a href="{{ $page->url() }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-eye"></i> Preview</a>
    @endif
@endsection

@section('panel-body')

    <div class="module page-module">

        <div class="alert-wrapper" style="margin-top: 15px;">

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (! $page->isType('page') && $page->hasType() && ! $page->type->inSyncWith($page))
                <div class="alert alert-warning">This page is out of sync with its page type. <a href="{{ route('admin.page.sync', $page->id) }}">Resync Now</a></div>
            @endif

            @if ($page->isRedirect())
                <div class="alert alert-info" role="alert">
                    <strong>Remember:</strong> This page is a redirect! You can change the redirect on the Settings tab.
                </div>
            @endif

        </div>

        @if ($page->exists && ! config('pilot.disable_page_parts', false))
            <form action="{{ route('admin.block.store') }}" method="post" class="form-inline" style="float: right; margin-bottom: 0;">

                {{ csrf_field() }}

                <div class="form-group">
                    <select name="type" id="" class="form-control">
                        <option value="">Block Type</option>
                        <option value="string">Text</option>
                        <option value="text">Paragragh</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="date">Date</option>
                        <option value="image">Image</option>
                        {{-- <option value="gallery">Gallery [not working]</option>
                        <option value="file">File [not working]</option> --}}
                    </select>
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" name="title" placeholder="Block Name">
                </div>

                <button type="submit" class="btn btn-success btn-sm">Add Block</button>

                <button class="block-sort-toggle btn btn-default btn-sm"><i class="fa fa-sort"></i> Sort</button>

                <input type="hidden" name="page_id" value="{{ $page->id }}">
            </form>
        @endif

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="index-nav-tabs">
              <li class="nav-item active">
                <a class="nav-link active" role="tab" data-toggle="tab" href="#content">Content <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                title="Click in the <b>'Learn'</b>
                tab in the sidebar and then click <b>'WYSIWYG Editor'</b> to learn more about how to use the text editor below."></i></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" role="tab" data-toggle="tab" href="#settings">Settings
                    {{-- <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                title="Learn more about page settings by clicking the <b>'Learn'</b>
                tab on the black nav bar above and then click <b>'Page Settings'</b>."></i> --}}
            </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" role="tab" data-toggle="tab" href="#meta">Metadata <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                title="It's important to fill out this section to increase your rank in Google."></i></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" role="tab" data-toggle="tab" href="#code">Code <i id="dont-show-on-mobile-tooltip" style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                title="Click on the <b>'Learn'</b>
                tab in the sidebar and then click <b>'Code'</b> to learn more"></i></a>
              </li>

              {{-- <button type="button" class="btn-sm btn-secondary" data-toggle="tooltip" data-html="true" title="<em>Tooltip</em> <u>with</u> <b>HTML</b>">[?]</button> --}}

              {{-- <i style="font-size: 20px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true" title="<em>Tooltip</em> <u>with</u> <b>HTML</b>"></i> --}}


           </ul>
       </div>

        {!! Form::model($page, $formOptions) !!}

            <div class="tab-content">

                <div class="tab-pane active" id="content">

                    <div class="form-group">
                        {!! Form::label('title', 'Title') !!}
                        {!! Form::text('title', null, array('class' => 'form-control', 'autofocus' => true)) !!}
                    </div>

                    @if (! $page->body_hidden)
                        <div class="form-group">
                            {!! Form::label('page-body-editor', 'Body') !!}
                            {!! Form::textarea('body', null, array('class' => 'form-control wysiwyg-editor', 'id' => 'page-body-editor')) !!}
                        </div>
                    @endif

                    @if(config('pilot.plugins.pages.fields.vertical_featured_image', false))
                    

                    <div class="row">

                        <div class="form-group col-lg-6">

                            <label for="" title="">
                                Horizontal Featured Image
                            </label>

                            <div class="help-block">
                                For the entire image to display, it needs to be a 5:2 ratio. We recommend minimum 1600:640px for quality.
                            </div>

                            <?php $field = \Jzpeepz\Dynamo\DynamoField::make(['key' => 'featured_image', 'options' => ['maxWidth' => 2000]]); ?>

                            @include('dynamo::bootstrap4.partials.fields.singleImage', ['item' => $page, 'display' => true, 'field' => $field])

                        </div>

                    </div>

                    <div class="row">

                        <div class="form-group col-lg-6">

                            <label for="" title="">
                                Vertical Featured Image
                            </label>

                            <div class="help-block">
                                For the entire image to display, it needs to be a 1:2 ratio. We recommend minimum 800:1600px for quality.
                            </div>

                            <?php $field = \Jzpeepz\Dynamo\DynamoField::make(['key' => 'vertical_featured_image', 'options' => ['maxWidth' => 2000]]); ?>

                            @include('dynamo::bootstrap4.partials.fields.singleImage', ['item' => $page, 'display' => true, 'field' => $field])

                        </div>

                    </div>

                    @else

                    <div class="row">

                        <div class="form-group col-lg-6">

                            <label for="" title="">
                                Featured Image
                            </label>

                            <div class="help-block">
                                For the entire image to display, it needs to be a 5:2 ratio. We recommend minimum 1600:640px for quality.
                            </div>

                            <?php $field = \Jzpeepz\Dynamo\DynamoField::make(['key' => 'featured_image', 'options' => ['maxWidth' => 1600]]); ?>

                            @include('dynamo::bootstrap4.partials.fields.singleImage', ['item' => $page, 'display' => true, 'field' => $field])

                        </div>

                    </div>

                    @endif

                    @if ($page->exists)

                        <div class="page-block-list">

                        @foreach ($page->blocks as $block)

                            <div class="page-block-wrapper">

                                <div class="page-block">

                                    <div class="page-block-controls">
                                        <a href="" data-toggle="modal" data-target="#modal-block-{{ $block->id }}"><i class="fa fa-gear"></i></a>
                                    </div>

                                    @if ($block->type == 'string')
                                        <div class="form-group">
                                            {!! Form::label('block-'.$block->slug, $block->title , ['title' => $block->slug]) !!}
                                            {!! Form::text('blocks['.$block->id.']', $block->body, array('class' => 'form-control')) !!}
                                        </div>
                                    @elseif ($block->type == 'text')
                                        <div class="form-group">
                                            {!! Form::label('block-'.$block->slug, $block->title, ['title' => $block->slug]) !!}
                                            {!! Form::textarea('blocks['.$block->id.']', $block->body, array('class' => 'form-control ' . ($block->getSetting('editor') == 1 ? 'wysiwyg-editor' : null))) !!}
                                        </div>
                                    @elseif ($block->type == 'checkbox')
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="{{ 'blocks['.$block->id.']' }}" value="0" checked style="display: none;">
                                                <input type="checkbox" name="{{ 'blocks['.$block->id.']' }}" value="1" {{ $block->body == '1' ? 'checked' : null }}> <span title="{{ $block->slug }}">{{ $block->title }}</span>
                                            </label>
                                        </div>
                                    @elseif ($block->type == 'date')
                                        <div class="form-group">
                                            {!! Form::label('block-'.$block->slug, $block->title, ['title' => $block->slug]) !!}
                                            {!! Form::text('blocks['.$block->id.']', $block->body, array('class' => 'form-control datetimepicker')) !!}
                                        </div>
                                    @elseif ($block->type == 'image')
                                        {{ Uploader::input('blocks['.$block->id.']', $block->title, $block->body) }}
                                        <style>.uploader-combo-preview { width: 300px; }</style>
                                    @endif

                                </div>

                                <div class="modal fade" id="modal-block-{{ $block->id }}" tabindex="-1" role="dialog" aria-labelledby="Block {{ $block->id }}">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">{{ $block->title }}</h4>
                                            </div>

                                            <div class="modal-body">
                                                @if ($block->type == 'text')
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="{{ 'block_settings['.$block->id.'][editor]' }}" value="0" checked style="display: none;">
                                                            <input type="checkbox" name="{{ 'block_settings['.$block->id.'][editor]' }}" value="1" {{ isset($block->settings['editor']) && $block->settings['editor'] == 1 ? 'checked' : null }}> <span title="{{ $block->slug }}">Editor</span>
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="modal-footer" style="background-color: #FFE4E4;">
                                                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                                                <button class="btn btn-danger btn-block block-delete" data-id="{{ $block->id }}" data-token="{{ csrf_token() }}">Delete Block <i class="block-delete-spin fa fa-circle-o-notch fa-spin" style="display: none;"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        @endforeach

                        </div>

                        <div id="page-block-sorter" class="page-block-sorter" style="display: none;">

                            @foreach ($page->blocks as $block)

                                <div class="page-block-sorter-item">
                                    <i class="fa fa-bars"></i> {{ $block->title }}
                                    <input type="hidden" name="block_order[]" value="{{ $block->id }}">
                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>

                <div class="tab-pane" id="meta">


                {{-- <div class="container">
                    <div class="row">
                      <div class="span4"></div>
                      <div class="span4"><img class="center-block img-fluid" src="/images/meta-data-example.png" /></div>
                      <div class="span4"></div>
                    </div>
                </div> --}}

                    {{-- <hr> --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">
                                {!! Form::label('path', 'Path') !!}
                                <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                title="You will see the path of this page update after making changes to slug, meta title, meta description, and saving it."></i>
                                {!! Form::text('path', null, array('class' => 'form-control', 'disabled' => true)) !!}
                            </div>

                            @php $isHomepage = empty($page->getParent()) @endphp

                            <div class="form-group">
                                {!! Form::label('slug', 'Slug') !!}
                                {!! Form::text('slug', null, array('class' => 'form-control', 'disabled' => $isHomepage)) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('meta_title', 'Meta Title') !!}
                                {!! Form::text('meta_title', null, array('class' => 'form-control')) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('meta_description', 'Meta Description') !!}
                                <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                title="Google and other search engines usually only display the first 120 characters of your meta description, so we recommend keeping them under that length."></i>
                                {!! Form::text('meta_description', null, array('class' => 'form-control character-counter-not-limited', 'max-character' => 120)) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('breadcrumb', 'Breadcrumb') !!}
                                <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                title="Breadcrumb is a type of secondary navigation scheme that reveals the user's location in a website or Web application.
                                Because so many people visit websites through mobile devices and displaying all the breadcrumb navigation can clutter up the screen, we've moved away from using this tool."></i>
                                {!! Form::text('breadcrumb', null, array('class' => 'form-control')) !!}
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div id="google-preview">
                                <p>Search Engine Result Preview</p>
                                <p id="google_title">{{ $page->getMetaTitle() }}</p>
                                <p id="google_url">{{ empty($page->getParent()) ? $page->url() : $page->getParent()->url() }}<span id="google_slug">{{empty($page->getParent()) ? '' : '/' }}{{ $page->slug }}</span></p>
                                <p id="google_description">{{ $page->getMetaDesc() }}</p>
                                <p> NOTE: A Search Engine doesn't exactly go by your meta data, <br>sometimes if it finds more relavent information,
                                    it will use it.</p>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="tab-pane" id="settings">

                    <div class="row">

                        <div class="form-group col-md-4">
                            {!! Form::label('status', 'Status') !!}
                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="Published: Page will show up in your navigation.<br>
                                   Draft: Page will be visible only to website admins/editors when they are logged in.<br>
                                   Hidden: Page is &quot;live&quot; but will not show up in navigation."></i>
                            {!! Form::select('status', $page->getStatusSelectList(), null, array('class' => 'form-control')) !!}
                        </div>

                        @if (Auth::user()->isAdmin())
                        <div class="form-group col-md-4">
                            {!! Form::label('layout', 'Layout') !!}
                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="Content pages always need to be set to &quot;Internal&quot;. &quot;Master&quot; is the homepage layout. The &quot;Sidebar&quot; layout
                                   will create a page with a sidebar. Try changing the layouts and then click the Preview button at the top right of this pane to check out
                                   how the layouts change the page. You may perhaps want to make the status of the page Hidden while playing around with the settings until it's the way you want it.
                                   Then you can change the status back to published."></i>
                            {!! Form::select('layout', $page->getLayoutList(), request()->has('layout') ? request()->input('layout') : null, array('class' => 'form-control', 'onchange' => 'layoutChange()')) !!}
                        </div>
                        @endif

                        @if (!Auth::user()->isAdmin() && $page->title != "Home")
                        <div class="form-group col-md-4">
                            {!! Form::label('layout', 'Layout') !!}
                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="Content pages always need to be set to &quot;Internal&quot;. &quot;Master&quot; is the homepage layout. The &quot;Sidebar&quot; layout
                                   will create a page with a sidebar. Try changing the layouts and then click the Preview button at the top right of this pane to check out
                                   how the layouts change the page. You may perhaps want to make the status of the page Hidden while playing around with the settings until it's the way you want it.
                                   Then you can change the status back to published."></i>
                            {!! Form::select('layout', $page->getLayoutList(), request()->has('layout') ? request()->input('layout') : null, array('class' => 'form-control', 'onchange' => 'layoutChange()')) !!}
                        </div>
                        @endif

                        @if (! $page->isRoot())
                            <div class="form-group col-md-4">
                                {!! Form::label('parent_id', 'Parent') !!}
                                <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                                title="Select the parent of this page. By default, if you created this page by clicking &quot;Add New Child Page&quot;, the page you clicked that button
                                       on is set as the parent. But you may change the parent here, and you will see that reflected in the navigation on the front-end of the site."></i>
                                {!! Form::select('parent_id', PilotPage::selectList(), request()->has('parent_id') ? request()->input('parent_id') : null, array('class' => 'form-control')) !!}
                            </div>
                        @endif

                    </div>

                    <div class="row">

                        <div class="form-group col-md-4">
                            {!! Form::label('type_id', 'Type') !!}
                            {!! Form::select('type_id', PilotPageType::pluck('name', 'id'), request()->has('type_id') ? request()->input('type_id') : null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group col-md-6">
                            {!! Form::label('link', 'Redirect') !!}
                            <i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="A link is expected here. If this is used, the page will redirect to the link given."></i>
                            {!! Form::text('link', null, array('class' => 'form-control')) !!}

                        </div>

                        <div class="checkbox col-md-2 pt-4">
                            <label>
                                <input type="hidden" name="open_in_new_tab" value="0">
                                <input type="checkbox" name="open_in_new_tab" value="1" {{ $page->open_in_new_tab == 1 ? 'checked' : null }}> <span title="">Set page/redirect to open in new tab.</span>
                            </label>
                        </div>

                    </div>

                    <div class="row">

                        <div class="form-group col-md-4">

                            {{ Form::label('password', 'Password') }} <i style="font-size: 16px; display: inline-block" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
                            title="If a password is set, the page will be password protected. Remember that links within the page are not password protected."></i>
                            {{ Form::text('password', null, array('class' => 'form-control')) }}

                        </div>

                    </div>

                    @foreach ($page->settingsConfig as $name => $options)
                        <div class="form-group">
                            {!! Form::label('setting_' . $name, $options['label']) !!}
                            {!! Form::$options['type']('settings[' . $name . ']', $page->getSetting($name), array('class' => 'form-control')) !!}
                        </div>
                    @endforeach

                    @if ($page->exists && $page->hasBlocks() && $page->isType('page'))
                        <div class="form-group">
                            <input id="page-type-name" type="text" class="form-control" style="display: inline-block; width: auto; vertical-align: middle;">
                            <button id="page-type-save" data-page-id="{{ $page->id }}" data-token="{{ csrf_token() }}" class="btn btn-sm btn-success">Create Page Type</button>
                            <span class="help-block">Save the current block configuration as a page type.</span>
                        </div>
                    @endif

                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="body_hidden" value="0">
                            <input type="checkbox" name="body_hidden" value="1" {{ $page->body_hidden == 1 ? 'checked' : null }}> <span title="">Hide Page Body</span>
                        </label>
                    </div>

                </div>

                <div class="tab-pane" id="code">

                    <div class="form-group">
                        {!! Form::label('block_1', 'Before') !!}
                        {!! Form::textarea('block_1', null, array('class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('block_2', 'After') !!}
                        {!! Form::textarea('block_2', null, array('class' => 'form-control')) !!}
                    </div>

                </div>

            </div>

            <button class="btn btn-primary">Save</button>
            <a class="form-cancel" href="{{ route('admin.page.index') }}">Cancel</a>

        {!! Form::close() !!}

        @if (Auth::user()->isAdmin() && !$page->isRoot() && $page->exists)
            {!! Form::model($page, array('route' => array('admin.page.destroy', $page->id), 'method' => 'delete', 'class' => 'delete-form float-right', 'onsubmit' => 'return confirm(\'Are you sure?\');')) !!}
                <button class="btn btn-danger"><i class="fa fa-trash-o"></i> Delete</button>
            {!! Form::close() !!}
        @endif

    </div>
  
  <!-- Modal -->
  <div class="modal fade" id="layoutChangeWarningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="layoutChangeWarningModalCenterTitle">Warning!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Changing the layout of a page can have some unwanted effects. <br><br>After changing the layout and saving the page, make sure you Preview the page
          and everything appears as expected. If things seem to look incorrect, change the layout back, or change it to the default "Internal" layout.
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('.character-limited').each(function () {
        handleCharacterLimit(this);
    });

    $('.character-limited').keyup(function() {
        handleCharacterLimit(this);
    });
});

function handleCharacterLimit(el)
{
    var maxLength = parseInt($(el).attr('maxlength'));
    $(el).closest('.form-group').find('.character-counter-total').text(maxLength);
    var length = $(el).val().length;
    var length = maxLength-length;
    $(el).closest('.form-group').find('.character-counter').text(length);
}

function layoutChange()
{
    $('#layoutChangeWarningModal').modal('show');    
}
</script>

<script>
        document.addEventListener('DOMContentLoaded', function(){
            let meta_title = document.getElementById('meta_title');
            meta_title.addEventListener('input', updateMetaTitlePreview);

            let meta_description = document.getElementById('meta_description');
            meta_description.addEventListener('input', updateMetaDescriptionPreview);

            let meta_path = document.getElementById('slug');
            meta_path.addEventListener('input', updateMetaPathPreview);

        });

        function updateMetaTitlePreview(ev){
            document.getElementById('google_title').innerHTML = ev.target.value;
        }

        function updateMetaPathPreview(ev){
            document.getElementById('google_slug').innerHTML = '/' + ev.target.value;
        }

        function updateMetaDescriptionPreview(ev){
            document.getElementById('google_description').innerHTML = ev.target.value;
        }


    </script>
@endsection
