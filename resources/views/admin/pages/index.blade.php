@extends('pilot::layouts.admin.panel')

@section('panel-heading')

    Page Manager
    <i id="tooltip-modal" style="font-size: 16px; padding-left: 5px;" class="fas fa-question-circle" data-toggle="modal" data-target="#pages-modal" data-html="true"
    title=""></i>
    {{-- <i style="font-size: 16px; padding-left: 5px;" class="fas fa-question-circle" data-toggle="tooltip" data-html="true"
    title="If you are on mobile, you must click the page link once to select the page. You will know it is selected if you see the <b>'Add a Child Page'</b> button.<br><br>
        Then you may click the <b>'+'</b> button to see the child pages."></i> --}}

@endsection

@section('buttons')

    @if (! config('app.disable_menu_builder'))
        <a href="{{ route('admin.menu.index') }}" class="btn btn-info btn-sm"><i class="fa fa-list"></i> Menu Builder</a>
    @endif

@endsection

@section('panel-body')

    <div class="module page-module" style="margin: -20px;">

        <ul class="page-list">

            <li>
                <div class="page page-home">
                    <div class="page-link"><i class="fas fa-home home-style"></i><a href="{{ route('admin.page.edit', array('page' => $root->id)) }}">{{ $root->title }}</a></div>
                    <div class="page-buttons">
                        <a href="{{ route('admin.page.create', array('parent_id' => $root->id)) }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add Child Page</a>
                        <a href="{{ $root->url() }}" target="_blank" class="btn btn-secondary btn-sm ml-1"><i class="fa fa-eye"></i> View</a>
                    </div>
                </div>

                <ul id="children-tree-of-home-page">{!! $root->renderTree() !!}</ul>
            </li>

        </ul>

    </div>

    {{-- Modal to help user understand events  --}}
    <div class="modal fade" id="pages-modal" tabindex="-1" role="dialog" style="display: none;">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header d-table mr-auto ml-auto">

                    <h5 class="modal-title ">How Pages Work On Your Website</h5>

                </div>

                <div class="modal-body">

                    <div class="spacer-for-mobile-pages-modal"></div>
                    <div></div>

                    Here, you can click any page to edit it. When you hover over a page, you will see a <b>'Add a child page'</b> button.
                        This will create a new page that is a subpage of the page where you clicked the button.<br><br>

                    For example, if you had a page called <b>'Locations'</b>, you could create child pages called <b>'Our Little Rock Store'</b>,
                    <b>'Our Hot Springs Store'</b>, etc. <br><br>You can <b>Drag-N-Drop</b> the pages with the Handle bar to change the order in its current tree. 
                        If you want to change which tree the page is in, use the <b>'Change Parent Page: '</b> Select box to change the tree the page is in.<br><br>

                </div>

                <?php /* <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div> */ ;?>

            </div><!-- /.modal-content -->

        </div><!-- /.modal-dialog -->

    </div><!-- /.modal -->



@endsection

@section('scripts')

<script src="/pilot-assets/legacy/js/page-index.js"></script>

@endsection
