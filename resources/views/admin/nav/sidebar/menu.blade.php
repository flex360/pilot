<ul class="pilot-nav">
    @foreach ($nav->items() as $navItem)
        @include('pilot::admin.nav.sidebar._item', compact('navItem'))
    @endforeach
</ul>

@push('scripts')
<script>
window.addEventListener('load', function () {
    document.querySelectorAll('.nav__item--expanded').forEach(function (el) {
        //rotate chevon 90 degrees
        el.lastElementChild.lastElementChild.classList.add('fa-rotate-90');

        // get the dropdown menu this parent menu references and show it
        var id = el.getAttribute("href");
        var collaspedMenu = document.querySelector(id).classList.add("show");

        // el.style.height = 'auto';
    });
    document.querySelectorAll('.nav__item--expanded [data-action=show]').forEach(function (el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('.nav__item--expanded [data-action=hide]').forEach(function (el) {
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

<style>
ul.pilot-nav {
    padding-left: 0;
    list-style: none;
    color: #fff;
}
ul.pilot-nav ul {
    padding-left: 20px;
    list-style: none;
}
.pilot-nav a {
    color: #fff;
}
.pilot-nav__item--active-child  a {
    color: green !important;
}
.pilot-nav__item--active  a {
    color: red !important;
}

.pilot-nav__item  div {
    padding: 0px 5px;
}
.pilot-nav__item--active-child  div {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 5px;
}
.pilot-nav__item--active > div > a {
    font-weight: bold;
    text-decoration: underline;
}
</style>
@endpush