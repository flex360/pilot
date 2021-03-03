document.onkeydown = checkKey;
        
function checkKey(e) {

    e = e || window.event;

    if (e.key === 'ArrowLeft') {
        var modal = document.querySelector('.modal-is-open');
        console.log(modal.id);
        leftOneModal(modal.id);
    }
    else if (e.key === 'ArrowRight') {
        var modal = document.querySelector('.modal-is-open');
        rightOneModal(modal.id);
    }
}