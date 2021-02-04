@extends('layouts.internal')

@section('content')


    {{--
    route: /product/{product}/{slug} -- shows index view of products
    Properties and Methods of $product:
    ====================================
        id
        name
        price
        short_description
        full_description
        featured_image
        gallery
        categories
        status
        created_at
        updated_at
        delete_at

        ->product_categories() // relationship
        ->url()
    --}}

{{-- {!! drop($faqsWithoutCategory) !!}
{!! drop($faqCategories, ['faqs']) !!} --}}

    @push('scripts')
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
        <script src="/pilot-assets/legacy/js/faqCopyLink.js"></script> --}}
    @endpush


@endsection

