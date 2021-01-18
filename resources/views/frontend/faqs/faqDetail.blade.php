@extends('layouts.internal')

@section('content')

    {{--
    route: /faqs/{faq}/{slug} -- shows view of individual faq
    Properties and Methods of $faq:
    ====================================
        id
        question
        short_answer
        long_answer
        status
        created_at
        updated_at
        delete_at

        ->getStatus
        ->faq_categories() //when you call this function you will recieve all the faqs cats for this faq sorted by name
        ->url()

        Example route: /faqs/1/faq1
    --}}

{!! drop($faq, ['faq_categories']) !!}

@endsection
