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

            var id = $("#servicecategoryID").val();

            console.log(id);

            $.post('/pilot/service-category-services/' + id + '/services/reorderServicesWithinCategory', { ids: ids.toArray() });

            console.log('reordered');

        }
    });

});
