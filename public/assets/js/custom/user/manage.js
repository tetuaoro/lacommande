$(document).ready(function () {
  var menu = $("form[name=menu]");

  menu.submit(function (e) {
    e.preventDefault();

    var form = new FormData(menu.get(0));
    var xhr = new XMLHttpRequest();

    menu.LoadingOverlay("show", {
      imageColor: "#dcdc0a",
    });

    xhr.onreadystatechange = function () {
      if ((xhr.readyState === 4) & (xhr.status === 201)) {
        menu.LoadingOverlay("hide");
        console.log(xhr.responseText);
        // $("#menu-show").append(xhr.responseText);
        $("#menuModal").modal("hide");
      }
      if ((xhr.readyState === 4) & (xhr.status === 400)) {
        menu.LoadingOverlay("hide");
        menu.html(xhr.responseText);
      }
      if ((xhr.readyState === 4) & (xhr.status === 405)) {
        menu.LoadingOverlay("hide");
        console.log(xhr.responseText);
      }
    };

    xhr.open(menu.attr("method"), menu.attr("action"));
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(form);
  });
});
