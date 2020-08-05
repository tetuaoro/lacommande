var rel = $("form[name=meal]");

function onClick() {
  rel.validate({
    submitHandler: function (form) {
      grecaptcha.ready(function () {
        grecaptcha
          .execute($("#meal_recaptcha").data("sitekey"))
          .then(function (token) {
            $("#meal_recaptcha").val(token);
            form.submit();
          });
      });
      rel.children("fieldset").LoadingOverlay("show", {
        imageColor: "#dcdc0a",
      });
      if ($("#meal_recaptcha").val() == "") {
        rel.children("fieldset").LoadingOverlay("hide");
      }
    },
  });
}
