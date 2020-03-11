$(function () {

    $('.page-link i').click(function(){
        var $page = $(this).closest('.page');
        $page.toggleClass('collapsed');
    });

    $('.page-sort-up').click(function(){
        var $source = $(this).closest('.page-child');
        var $above = $source.prev();
        $above.before($source);

        // get the ids of this page group
        var $list = $source.closest('ul');
        var $pages = $list.children('li');
        var ids = $.map($pages, function(val, index){
            return $(val).attr('data-id');
        });

        // save the page order
        $.post('/pilot/page/reorder', {ids: ids}, function(data){
            console.log(data);
        });
    });

    $('.page-sort-down').click(function(){
        var $source = $(this).closest('.page-child');
        var $below = $source.next();
        $below.after($source);

        // get the ids of this page group
        var $list = $source.closest('ul');
        var $pages = $list.children('li');
        var ids = $.map($pages, function(val, index){
            return $(val).attr('data-id');
        });

        // save the page order
        $.post('/pilot/page/reorder', {ids: ids}, function(data){
            console.log(data);
        });
    });

    $('.page-delete-confirm').click(function(event){
        var title = $(this).closest('.page').find('.page-link').text();
        return confirm("Are you sure you want to delete '" + title + "'?");
    });
});
