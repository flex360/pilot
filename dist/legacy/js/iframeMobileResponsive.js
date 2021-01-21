document.addEventListener("DOMContentLoaded", function() {
    var iframes = document.querySelectorAll('iframe');
    
    var i = 0;

    for(i; i < iframes.length; i++) {
        if (iframes[i].getAttribute('title') != 'Embedded Wufoo Form') {
            var div = document.createElement('div');
            var iframeParent = iframes[i].closest('p');    
            div.classList.add("video-container");
            div.appendChild(iframes[i]);
            iframeParent.appendChild(div);
        }
    }
});