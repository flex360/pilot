$(document).ready(function () {

    var list = document.getElementById("dynamo-index-body");

    var sort = Sortable.create(list, {
        onUpdate: function (evt) {

            var ids = $('.dynamo-index-row').map(function () {
              return $(this).attr("data-id");
            });

            console.log(ids.toArray());

            var id = $("#productcategoryID").val();

            console.log(id);

            $.post('/pilot/product-category-products/' + id + '/products/reorderProductsWithinCategory', { ids: ids.toArray() });

            console.log('reordered');

        }
    });

});
