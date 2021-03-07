$(document).ready(function () {

    var list = document.getElementById("dynamo-index-body");

    // check if reordering departments is enabled
    var table = document.querySelector("#dynamo-index");
    console.log(table.firstChild.firstChild.firstChild.innerText);

    //if the first row of the table has the label "Sort" then we know sorting categories is enabled in the config file
    if (table.firstChild.firstChild.firstChild.innerText == 'Sort') {
        var sort = Sortable.create(list, {
            onUpdate: function (evt) {
    
                var ids = $('.dynamo-index-row').map(function () {
                  return $(this).attr("data-id");
                });
    
                
    
                console.log(ids.toArray());
    
                $.post('/pilot/productcategory/reorderProductCategories', { ids: ids.toArray() })
    
            }
        });
    }
    

});
