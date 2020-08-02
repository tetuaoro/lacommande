$("form[name=user] input").keyup(function (e) {
  e.preventDefault();

  $("form[name=user] input:not([type=hidden])").each(function (index, element) {
    console.log(index);
    if ($(element).val() == "") {
      $(".btn-validation").attr("disabled", true);
      return false;
    } else {
      $(".btn-validation").attr("disabled", false);
    }
  });
});

$("form[name=user]").submit(function (e) {
  $(".btn-validation").attr("disabled", true);
  $(".btn-validation").LoadingOverlay("show", {
    imageColor: "#dcdc0a",
  });
});
