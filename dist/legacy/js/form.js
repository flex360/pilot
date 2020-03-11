$(document).ready(function(){
    $('.sync-button').click(function(){
        var hash = $(this).attr('data-hash');
        var token = $(this).attr('data-token');

        var $spinner = $(this).find('.fa-spinner');

        $spinner.show();

        $.post('/pilot/form/'+hash+'/sync', { _token: token }, function(){
            $spinner.hide();
            location.reload();
        });
    });
});
