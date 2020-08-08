$(document).ready(function () {
  // PAGINATION
  /*
  focus section id when change pagination
  */
  $(".pagination .page-item a").each(function (index, element) {
    $(this).attr("href", $(this).attr("href") + "#meal-section");
  });
});
