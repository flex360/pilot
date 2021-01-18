@extends('layouts.internal')

@section('content')


    {{--
    route: /faqs -- shows index view of faqs
    Properties and Methods of $faqs:
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

{{-- {!! drop($faqsWithoutCategory) !!}
{!! drop($faqCategories, ['faqs']) !!} --}}

    <!-- FAQs Container -->
    <div class="py-5">
        <div class="w-full uppercase text-lg font-light mt-12"><span class="text-crail">Answers to your </span></div>
        <div class="w-full text-3xl font-display font-bold mt-2 border-b border-solid border-crail mb-10"><span class="">FAQs</span></div>

        <div class="container w-full">
            <div class="flex flex-wrap">

                <!-- FAQs that don't have categories get listed on top first here -->
                <div class="pb-4">
                    @foreach ($faqsWithoutCategory as $faq)
                    <div class="w-full md:pl-8 md:pr-12">
                        <div class="w-full pb-5 border-b border-madison text-madison uppercase">

                            <!-- Invisible anchor to question -->
                            <div id="cat-0-faq-{{ $faq->id }}" class="pt-5"></div>

                            <!-- Tooltip to show user copied successfully -->
                            <div class="hidden relative">
                                <span class="faq-copied-tooltip absolute bg-primaryBlue text-sm text-white normal-case rounded p-1">Copied link!</span>
                            </div>

                            <!-- container for question mark icon and question -->
                            <div class="flex">
                                <!-- question mark icon to copy link to FAQ -->
                                <div height="50" width="50" onclick="showCopiedTooltip(this)"><x-pilot::icon.question-circle class="question-mark inline-block text-denim mr-2 cursor-pointer" data-clipboard-text="{{ $faq->getCategoryUrl(0) }}"></x-pilot::icon.question-circle></div>
                                
                                <!-- actual question --> 
                                <div class="">{{ $faq->question}}</div>
                            </div>

                            <!-- Answer -->
                            <div class="fr-view user-html text-shark text-sm normal-case mt-4">
                                {!! $faq->answer !!}                                        
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Loop thru categories inside a collapsable component and then loop thru all the questions of that category -->
                @foreach ($faqCategories as $cat)
                    @if ($cat->faqs->isNotEmpty())
                        <accordion label="{{ $cat->name }}" :expanded="{{ $catId == $cat->id ? 'true' : 'false' }}">
                            <div class="pb-4">
                                @foreach ($cat->faqs as $faq)
                                    <div class="w-full md:pl-8 md:pr-12">
                                        <div class="w-full pb-5 border-b border-madison text-madison uppercase">

                                            <!-- Invisible anchor to question -->
                                            <div id="cat-{{ $cat->id }}-faq-{{ $faq->id }}" class="pt-5"></div>

                                            <!-- Tooltip to show user copied successfully -->
                                            <div class="hidden relative">
                                                <span class="faq-copied-tooltip absolute bg-primaryBlue text-sm text-white normal-case rounded p-1">Copied link!</span>
                                            </div>

                                            <!-- container for question mark icon and question -->
                                            <div class="flex">
                                                <!-- question mark icon to copy link to FAQ -->
                                                <div height="50" width="50" onclick="showCopiedTooltip(this)"><x-pilot::icon.question-circle class="question-mark inline-block text-denim mr-2 cursor-pointer" data-clipboard-text="{{ $faq->getCategoryUrl($cat->id) }}"></x-pilot::icon.question-circle></div>
                                                
                                                <!-- actual question --> 
                                                <div class="">{{ $faq->question}}</div>
                                            </div>

                                            <!-- Answer -->
                                            <div class="fr-view user-html text-shark text-sm normal-case mt-4">
                                                {!! $faq->answer !!}                                        
                                            </div>
                                            
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </accordion>
                    @endif
                @endforeach
            </div>

        </div>
    </div> <!-- end FAQs container -->

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
        <script src="/pilot-assets/legacy/js/faqCopyLink.js"></script>
    @endpush


@endsection

