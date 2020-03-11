$(function () {

    $('.page-icon').click(function(){
        var $page = $(this).closest('.page');
        $page.toggleClass('collapsed');
    });

    $('.page-delete-confirm').click(function(event){
        var title = $(this).closest('.page').find('.page-link').text();
        return confirm("Are you sure you want to delete '" + title + "'?");
    });

    // enable floating page bar
    var  mn = $(".page-bar");
    mns = "page-bar-scrolled";
    hdr = $('.navbar').height();

    $(window).scroll(function() {
      if( $(this).scrollTop() > 0 ) {
        mn.addClass(mns);
      } else {
        mn.removeClass(mns);
      }

      if (hdr - $(this).scrollTop() > 0) {
        mn.css('top', hdr - $(this).scrollTop());
      } else {
        mn.css('top', 0);
      }
    });

});
