$(document).ready(function () {

    var list = document.getElementById("dynamo-index-body");

    var sort = Sortable.create(list, {
        animation: 350,
        ghostClass: 'blue-background-class',
        onUpdate: function (evt) {

            var ids = $('.dynamo-index-row').map(function () {
              return $(this).attr("data-id");
            });

            console.log(ids.toArray());

            var id = $("#faqcategoryID").val();

            console.log(id);

            // set the csrf token in the header of the ajax request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.post('/pilot/faqcategory/' + id + '/faqs/reorderFaqsWithinCategory', { 
                ids: ids.toArray(),
            });
        }
    });
});
