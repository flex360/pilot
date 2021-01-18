new ClipboardJS('.question-mark');

function showCopiedTooltip(elem) {
    elem.parentElement.previousSibling.previousElementSibling.style.display="block";

    setTimeout(function(){elem.parentElement.previousSibling.previousElementSibling.style.display="none"; }, 1000);
}

// function showCopiedTooltip(elem) {
// var op = 0.1;  // initial opacity
// var timer = setInterval(function () {
//         if (op <= 1){
//             clearInterval(timer);
//             elem.previousSibling.previousElementSibling.style.display = 'block';
//         }
//         elem.previousSibling.previousElementSibling.style.opacity = op;
//         elem.previousSibling.previousElementSibling.style.filter = 'alpha(opacity=' + op * 100 + ")";
//         op -= op * 0.1;
//     }, 10);
// }