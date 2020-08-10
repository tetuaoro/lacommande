$(document).ready(function () {
  // input file type
  bsCustomFileInput.init();

  //   FORM CREATE
  var forms = [$("form[name=meal-create")];

  forms.forEach((form) => {
    $(form).submit(function (e) {
      e.preventDefault();
      var spinner = $(form).parents(".modal-content");
      spinner.LoadingOverlay("show", {
        imageColor: appColor2,
        background: "rgba(255, 255, 255, 0.6)",
      });
      checkRecaptcha(form, spinner, e);
    });
  });

  function checkRecaptcha(element, spinner, fn) {
    var recaptcha = element.find(".recaptcha-check");
    grecaptcha.ready(function () {
      grecaptcha
        .execute(recaptcha.data("sitekey"), { action: "submit" })
        .then(function (token) {
          recaptcha.val(token);
          fn.currentTarget.submit();
        })
        .catch((e) => {
          spinner.LoadingOverlay("hide");
        });
    });
  }

  //   FORM EDIT
  $("form[name^=meal-edit]").each(function (index, form) {
    $(form).submit(function (e) {
      e.preventDefault();
      var spinner = $(form).parents(".modal-content");
      spinner.LoadingOverlay("show", {
        imageColor: appColor2,
        background: "rgba(255, 255, 255, 0.6)",
      });
      checkRecaptcha($(form), spinner, e);
    });
  });

  // FORM DELETE
  var formsDelete = [$("form[name=meal-delete]")];

  formsDelete.forEach((form) => {
    form.submit(function (e1) {
      e1.preventDefault();
      var confirmBtn = form.data("btnDelete");
      var modal = form.data("modal");

      $(modal).modal("show");
      $(confirmBtn).click(function (e2) {
        e2.preventDefault();
        $(modal).find(".modal-content").LoadingOverlay("show", {
          imageColor: appColor2,
          background: "rgba(255, 255, 255, 0.6)",
        });
        e1.currentTarget.submit();
      });
    });
  });
});
