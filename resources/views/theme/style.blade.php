$main-color: {{ !empty($navColor) ? $navColor : '#000' }};
$nav-color: {{ !empty($navColor) ? $navColor : '#000' }};
$nav-text-color: {{ !empty($navTextColor) ? $navTextColor : '#fff' }};
$secondary-color: {{ !empty($secondaryColor) ? $secondaryColor : '#555' }};
$link-color: {{ !empty($linkColor) ? $linkColor : 'blue' }};

$nav-color-dark: darken($nav-color, 20%);
$nav-color-light: lighten($nav-color, 10%);

$secondary-color-dark: darken($secondary-color, 10%);
$secondary-color-light: lighten($secondary-color, 10%);

$header-background-image: '{{ !empty($headerBackgroundImage) ? $headerBackgroundImage : '' }}';

.{{ $siteClass or '' }} {

    a {
        color: $link-color;
    }

    h1, h2, h3, nav a {
        @if (! empty($font))
        font-family: '{{ $font }}', serif;
        @endif
    }

    .header-wrapper {
        background-image: url($header-background-image);
    }

    .nav-wrapper, .nav-wrapper nav {

        background-color: $nav-color;
        background-image: -webkit-gradient(linear, left top, left bottom, from($nav-color), to($nav-color-light)); 
        background-image: -webkit-linear-gradient(top, $nav-color, $nav-color-light); 
        background-image:    -moz-linear-gradient(top, $nav-color, $nav-color-light); 
        background-image:     -ms-linear-gradient(top, $nav-color, $nav-color-light); 
        background-image:      -o-linear-gradient(top, $nav-color, $nav-color-light);

        a {
            color: $nav-text-color;
        }
    }

    .nav-wrapper {
        
        .dropdown-nav {
            background-color: $nav-color;
            border-top-color: lighten($nav-color, 10%);
        }

        li {

            a:hover {
                background-color: lighten($nav-color, 10%);
            }
        }

    }

    .ribbon-wrapper {
        
        .ribbon {

            background-color: $secondary-color;
            background-image: -webkit-gradient(linear, left top, left bottom, from($secondary-color-dark), to($secondary-color-light)); 
            background-image: -webkit-linear-gradient(top, $secondary-color-dark, $secondary-color-light); 
            background-image:    -moz-linear-gradient(top, $secondary-color-dark, $secondary-color-light); 
            background-image:     -ms-linear-gradient(top, $secondary-color-dark, $secondary-color-light); 
            background-image:      -o-linear-gradient(top, $secondary-color-dark, $secondary-color-light); 
        }

        .ribbon:before, .ribbon:after {
            border-top-color: $secondary-color-dark;
        }
    }

    {{ Setting::get('custom-css', '') }}

}