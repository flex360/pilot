@extends('layouts.internal')

@section('content')


    {{--
    route: /products -- shows index view of products
    Properties and Methods of $productCategories:
    ====================================
        id
        title
        featured_image
        created_at
        updated_at
        delete_at

        ->products() // relationship
        ->url()
    --}}

{{-- {!! drop($faqsWithoutCategory) !!}
{!! drop($faqCategories, ['faqs']) !!} --}}

    @push('scripts')
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
        <script src="/pilot-assets/legacy/js/faqCopyLink.js"></script> --}}
    @endpush


@endsection

