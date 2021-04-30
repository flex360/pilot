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

            var id = $("#categoryId").val();

            console.log(id);

            $.post('/pilot/resourcecategory/' + id + '/resources/reorderResourcesWithinCategory', { ids: ids.toArray() });

            console.log('reordered');

        }
    });

});
