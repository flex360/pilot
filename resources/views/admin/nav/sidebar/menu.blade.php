<ul class="pilot-nav">
    @foreach ($nav->items() as $navItem)
        @include('pilot::admin.nav.sidebar._item', compact('navItem'))
    @endforeach
</ul>

@push('scripts')
<script>
window.addEventListener('load', function () {
    document.querySelectorAll('.pilot-nav__item--expanded > ul').forEach(function (el) {
        el.style.height = 'auto';
    });
    document.querySelectorAll('.pilot-nav__item--expanded [data-action=show]').forEach(function (el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('.pilot-nav__item--expanded [data-action=hide]').forEach(function (el) {
        el.style.display = 'inline-block';
    });
    document.querySelectorAll('[data-toggle-menu]').forEach(function (el) {
        el.addEventListener('click', function (event) {
            const action = event.target.getAttribute('data-action');
            const selector = event.currentTarget.getAttribute('data-toggle-menu');
            const dropdown = document.querySelector(selector);
            if (action == 'show') {
                dropdown.style.height = 'auto';
                event.currentTarget.querySelector('[data-action=show]').style.display = 'none';
                event.currentTarget.querySelector('[data-action=hide]').style.display = 'inline-block';
            } else {
                dropdown.style.height = '0px';
                event.currentTarget.querySelector('[data-action=show]').style.display = 'inline-block';
                event.currentTarget.querySelector('[data-action=hide]').style.display = 'none';
            }
        });
    });
});
</script>
@endpush
