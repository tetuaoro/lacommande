$(document).ready(function () {
  var form = $("form[name=provider");

  form.submit(function (e) {
    e.preventDefault();
    sendData(this);
  });

  $("#providerModal").on("show.bs.modal", function () {
    bsCustomFileInput.init();
    autosize($('textarea'));
  });

  function sendData(element) {
    var form = new FormData(element);
    var xhr = new XMLHttpRequest();

    spinner();

    xhr.onreadystatechange = function () {
      spinner("hide");
      if (xhr.readyState == 4) {
        if (xhr.status == 201) {
          location.href = xhr.responseText;
        }
        if (xhr.status == 400) {
          $(element).html(xhr.responseText);
        }
      }
    };

    xhr.open($(element).attr("method"), $(element).attr("action"));
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(form);
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
        imageColor: appColor1,
        background: "rgba(255, 255, 255, " + alpha + ")",
      });
    } else if (mode == "hide") {
      spinner.LoadingOverlay("hide");
    }
  }
});
