$(document).ready(function () {
  //   FORM CREATE/EDIT
  $("[id^='mealModal']").on("show.bs.modal", function () {
    bindMealForm(this);
    bsCustomFileInput.init();
    $(this).find("[id$='description']").richTextEditor();
    $(this).find("[id$='recipe']").richTextEditor();
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

/**
 * Send data form via ajax.
 * @param {Element} element
 */
function sendData(element) {
  var form = new FormData(element);
  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 400) {
        $(element).html(xhr.responseText);
        bsCustomFileInput.init();
        $(element).find("[id$='description']").richTextEditor();
        $(element).find("[id$='recipe']").richTextEditor();
      }
      if (xhr.status === 201) {
        location.href = xhr.responseText;
      }
      spinner("hide");
    }
  };

  xhr.open($(element).attr("method"), $(element).attr("action"));
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.setRequestHeader("Accept", "text/html");
  xhr.send(form);
}

/**
 * Check before recaptcha test.
 * @param {Elemet} element
 * @param {CallableFunction} fn
 */
function checkRecaptcha(element, fn) {
  var recaptcha = $(element).find(".recaptcha-check");
  grecaptcha.ready(function () {
    grecaptcha
      .execute(recaptcha.data("sitekey"), { action: "submit" })
      .then(function (token) {
        recaptcha.val(token);
        fn(element);
      })
      .catch(function () {
        spinner("hide");
      });
  });
}

/**
 * Bind all form when the button clicked.
 * @param {Element|null} element
 */
function bindMealForm(element) {
  $(element)
    .find("form")
    .submit(function (e) {
      e.preventDefault();
      spinner();
      checkRecaptcha(this, sendData);
    });
}

/**
 * Load spinner
 *
 * @param {null|string} mode
 * @param {number} [alpha=0.6]
 */
function spinner(mode, alpha = 0.6) {
  var spinner = $(".modal-content");
  if (mode == null || mode == "show") {
    spinner.LoadingOverlay("show", {
      imageColor: appColor2,
      background: "rgba(255, 255, 255, " + alpha + ")",
    });
  } else if (mode == "hide") {
    spinner.LoadingOverlay("hide");
  }
}
