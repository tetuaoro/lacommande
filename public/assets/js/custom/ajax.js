/* $(document).ready(function () {
  var menu = $("form[name=menu]");
  var meal = $("form[name=meal]");
  var subuser = $("form[name=subuser]");

  menu.submit(function (e) {
    e.preventDefault();

    var form = new FormData(menu.get(0));
    var xhr = new XMLHttpRequest();

    menu.LoadingOverlay("show", {
      imageColor: "#dcdc0a",
      background: "rgba(255, 255, 255, 0)",
    });

    xhr.onreadystatechange = function () {
      if ((xhr.readyState === 4) & (xhr.status === 201)) {
        menu.LoadingOverlay("hide");
        $("#menuModal").modal("hide");
      }
      if ((xhr.readyState === 4) & (xhr.status === 400)) {
        menu.LoadingOverlay("hide");
        menu.html(xhr.responseText);
      }
      if ((xhr.readyState === 4) & (xhr.status === 405)) {
        menu.LoadingOverlay("hide");
        document.location.url = document.location.href;
      }
    };

    xhr.open(menu.attr("method"), menu.attr("action"));
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(form);
  });

  $("form[name=contact]").submit(function (e) {
    e.preventDefault();
    $("form[name=contact]").LoadingOverlay("show", {
      imageColor: "#dcdc0a",
      background: "rgba(255, 255, 255, 0.4)",
      zIndex: 99
    });

  })
  
});
 */