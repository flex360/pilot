{{--
<li class="page-child" data-id="{{ $page->id }}">
    <div class="page collapsed {{ $page->hasChildren() ? 'has-children' : 'no-children' }}">
        <div class="page-link pull-left"><i class="fas fa-plus big-icon"></i>{!! link_to_route('admin.page.edit', $page->title, array('page' => $page->id)) !!} <span class="page-status">{{ $page->getStatus() }}</span> <span class="badge-page">{{ $page->type }}</span></div>
        <div class="page-buttons pull-right">
            @if (! $page->isType('page'))
                <a href="{{ route('admin.page.create', array('parent_id' => $page->parent_id, 'type_id' => $page->type_id)) }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add {{ $page->type }}</a>
            @endif
            <a href="{{ route('admin.page.create', array('parent_id' => $page->id, 'layout' => $page->layout)) }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Child Page</a>
            <a href="{{ $page->url() }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i> View</a>
            <button class="btn btn-secondary btn-sm page-sort page-sort-up"><i class="fa fa-chevron-up"></i></button>
            <button class="btn btn-secondary btn-sm page-sort page-sort-down"><i class="fa fa-chevron-down"></i></button>
        </div>
    </div>


    <ul>{!! $children !!}</ul>



</li>
--}}




<li class="page-child" data-id="{{ $page->id }}">

    <div class="accordion" id="accordion-{{ $page->id }}">

      <div class="card">
        <div class="row align-items-center card-header pages-card-header" id="heading-{{ $page->id }}">

            <div class="col-12 col-md-6 col-lg-6 align-self-center page-link">
                @if ($page->children)
                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse-{{ $page->id }}" aria-expanded="false" aria-controls="collapse-{{ $page->id }}">

                    </button>
                @else
                    <button class="btn btn-link disabled" type="button">
                        <i class="fas fa-long-arrow-alt-right big-icon"></i>
                    </button>
                @endif
                {!! link_to_route('admin.page.edit', $page->title, array('page' => $page->id)) !!}
                &nbsp;<span class="page-status">{{ $page->getStatus() }}</span>
                &nbsp;<span class="badge-page">{{ $page->type }}</span>
            </div>
            <div class="col-12 col-md-6 col-lg-6 align-self-center page-buttons text-right">
                @if (! $page->isType('page'))
                    <a href="{{ route('admin.page.create', array('parent_id' => $page->parent_id, 'type_id' => $page->type_id)) }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add {{ $page->type }}</a>
                @endif

                @php 
                // $selectList = \Flex360\Pilot\Pilot\Page::selectList();
                // foreach($selectList as $key=>$item){
                //     dd($key);
                // }
                @endphp
                <div class="form-group">
                <div id="action-btn-container" class="d-flex align-items-center justify-content-end">
                    <label for="myParentSelect" class="sr-only myParentSelectLabel font-weight-light">Change parent page: </label>
                    <select class="form-control pageParentSelect mt-0" data-parent="{{ $page->id }}">
                        <option value="" selected>Move to...</option>
                        @foreach ($selectList as $id=>$pageName)
                            <option value="{{ $id }}">{{ $pageName }}</option>
                        @endforeach
                    </select>
                    {{-- {!! Form::select('parent_id', Page::selectList(), Input::has('parent_id') ? Input::get('parent_id') : null, array('class' => 'form-control')) !!} --}}
                    <a href="{{ route('admin.page.create', array('parent_id' => $page->id, 'layout' => $page->layout)) }}" class="btn btn-success btn-sm mr-2 add-child-page-btn"><i class="fas fa-plus"></i> Add Child Page</a>
                    <a href="{{ $page->url() }}" target="_blank" class="btn btn-secondary btn-sm mr-3"><i class="fa fa-eye"></i> View</a>
                    <i class="fas fa-grip-vertical handle" title="Drag to Sort"></i> 
                </div>
                </div>
            </div>

        </div>
        @if ($children)
            <div id="collapse-{{ $page->id }}" class="collapse" aria-labelledby="heading-{{ $page->id }}" data-parent="#accordion-{{ $page->id }}">
                <ul class="children-tree">{!! $children !!}</ul>
            </div>
        @endif
      </div>

    </div>

</li>

