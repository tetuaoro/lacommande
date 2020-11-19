$(document).ready(function () {
  // MEAL COMMAND
  var form = $("form[name=command]");

  form.submit(function (e) {
    e.preventDefault();

    $("body").LoadingOverlay("show", {
      imageColor: appColor1,
    });
    checkRecaptcha(this, sendForm);
  });

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
        .catch((e) => {
          $("body").LoadingOverlay("hide");
        });
    });
  }

  function sendForm(element) {
    var form_ = new FormData(element);
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4) {
        $("body").LoadingOverlay("hide");
        if (xhr.status == 201) {
          location.href = xhr.responseText;
        }
        if (xhr.status == 400) {
          form.html(xhr.responseText);
          $(".timepicker").timepicker({
            timeFormat: "HH:mm",
            interval: 15,
            maxTime: "6:00pm",
            startTime: "07:00",
            dynamic: true,
            dropdown: true,
            scrollbar: true,
          });
        }
      }
    };

    xhr.open($(element).attr("method"), $(element).attr("action"));
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(form_);
  }
});
