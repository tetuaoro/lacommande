$("[data-href]").click(function (event) {
  var url = $(this).data("href");
  if (url && url.length > 0) {
    document.location.href = url;
    return false;
  }
});
