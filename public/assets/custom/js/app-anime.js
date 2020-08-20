$(document).ready(function () {
  // FALSH MESSAGE
  var flash = $("#flash");
  if (flash.children().length) {
    flash.show("fast");
    setTimeout(() => {
      flash.hide("fast");
    }, 11959);
  }

  // BTN COLLAPSE (PLUS OPTION - MOINS OPTION)
  $(".btn-form-collapse").click(function () {
    if ($(".multiCollapseMeal").css("display") != "none") {
      $(this).html(
        "Plus d'option <i class='fas fa-plus-circle ml-1' aria-hidden='true'></i>"
      );
    } else {
      $(this).html(
        "Moins d'option <i class='fas fa-minus-circle ml-1' aria-hidden='true'></i>"
      );
    }
    $(this).toggleClass("btn-show-toggle");
  });

  // USER FA-ICON
  $("#v-pills-tab a[id$='-tab']").click(function (e) {
    e.preventDefault();
    $(".user-auth.user-manage.bg-faIcon").attr(
      "data-fa-icon",
      $(this).data("faIcon")
    );
    var viewUrlParam = new URL(location.href);
    viewUrlParam.searchParams.set("view", $(this).attr("aria-controls"));
    history.pushState({}, null, viewUrlParam.toString());
  });
});
