$(document).ready(function () {
  //Single ul jquery object with id of children-tree-of-home-page
  var childPagesOfHomePageList = $("#children-tree-of-home-page");

  //Need it as Jquery and HTML elemtn because I couldn't get ONLY the li's of the home
  //page "ul" using vanilla javascript, and the Sortable class doesn't accept a
  //jquery object
  var childPagesOfHomePageListHtml = document.getElementById(
    "children-tree-of-home-page"
  );
  // console.log(childPagesOfHomePageListHtml);

  //HTMLCollection of all ul.children-tree's -- jquery object
  var allOtherChildrenTreesListAsRawHtml = $(".children-tree")
    .map(function () {
      return this;
    })
    .get();

  allOtherChildrenTreesList = $(allOtherChildrenTreesListAsRawHtml);

  var allOtherChildrenTrees = document.getElementsByClassName("children-tree");

  if (childPagesOfHomePageList != null) {
    var sort = Sortable.create(childPagesOfHomePageListHtml, {
      animation: 850,
      handle: ".handle",
      onUpdate: function (evt) {
        //Get Li's of only homepage using jquery object this will be used to
        //map thru the id's before sending to the reorder function to update database
        homePageChildren = childPagesOfHomePageList.children("li");

        var ids = homePageChildren.map(function () {
          return $(this).attr("data-id");
        });

        // console.log(ids.toArray());

        $.post("/pilot/page/reorder", { ids: ids.toArray() }, function (
          data
        ) { });
      }
    });
  }

  //Loop thru all over child page list and create Sortable List
  var i;
  var otherSort = [];
  for (i = 0; i < allOtherChildrenTrees.length; i++) {

    otherSort[i] = Sortable.create(allOtherChildrenTrees[i], {
      animation: 850,
      handle: ".handle",
      onUpdate: function (evt) {
        //Get all LIs
        childPages = allOtherChildrenTreesList.children("li");

        var ids = childPages.map(function () {
          return $(this).attr("data-id");
        });

        // console.log(ids.toArray());

        $.post("/pilot/page/reorder", { ids: ids.toArray() }, function (
          data
        ) { });
      }
    });
  }

  const pageParentOptions = $(".pageParentSelect");
  pageParentOptions.on("change", function () {
    const thisPageID = $(this).data("parent");
    const parentID = $("option:selected", this).val();
    const token = document
      .querySelector("meta[name='csrf-token']")
      .getAttribute("content");

    var postData = {
      newParentPageId: parentID,
      _token: token
    };

    if (confirm("Are you sure you want to change this page's Parent?")) {
      $.post(
        "/pilot/page/" + thisPageID + "/updateParent/" + parentID,
        postData,
        function (data) {
          window.location.href = "/pilot";
        }
      );
    } else {
      $(this).val('');
    }
  });
});
