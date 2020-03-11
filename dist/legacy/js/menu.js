$(function () {

    var menuId = $('#menu-id').attr('data-id');

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

        //console.log(ids);

        // save the page order
        $.post('/pilot/menu/' + menuId + '/reorder', {ids: ids}, function(data){
            console.log(data);
        });

        return false;
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

        console.log(ids);

        // save the page order
        $.post('/pilot/menu/' + menuId + '/reorder', {ids: ids}, function(data){
            console.log(data);
        });

        return false;
    });

});
