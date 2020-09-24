// Standard flex360/pilot modal is designed to have multiple modals on one page:
/* 

1. include <script src="/pilot-assets/legacy/js/modal.js"></script> on page

2. Create a button that has:
    onclick="toggleModal({{ $model->id }})" attribute

3. Then include the modal partial and pass in the current model:
    @include('pilot::partials.modal', compact($model)) 

4. If you'd like to override the modal, create "view->vendor->partials->modal.blade.php

Full example: 
    @foreach ($models as $model)
        <a onclick="toggleModal({{ $model->id }})" class="flex items-center font-display font-medium text-primaryBlue hover:darkBackground transition-colors ease-linear duration-300 cursor-pointer">View Credit Terms</a>
        @include('pilot::partials.modal', compact($model))
    @endforeach
*/

//If escape key is pressed, turn modal off
document.onkeydown = function(evt) {
    evt = evt || window.event;
    var isEscape = false;
    if ("key" in evt) {
        isEscape = (evt.key === "Escape" || evt.key === "Esc");
    } else {
        isEscape = (evt.keyCode === 27);
    }
    if (isEscape && document.body.classList.contains('modal-active')) {
        const body = document.querySelector('body');
        var modal = document.querySelector('.modal-is-open');
        modal.classList.toggle('opacity-0');
        modal.classList.toggle('pointer-events-none');
        modal.classList.toggle('modal-is-open');
        body.classList.toggle('modal-active');
    }
};

// set onclick="toggleModal({{ $model->id }})", that will toggle the corresponding modal attached for this button
function toggleModal (id) {
    const body = document.querySelector('body');
    var modal = document.querySelector('.modal-id-' + id);
    modal.classList.toggle('opacity-0');
    modal.classList.toggle('pointer-events-none');
    modal.classList.toggle('modal-is-open');
    body.classList.toggle('modal-active');
}