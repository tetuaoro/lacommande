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
    },
  });
}

$(document).ready(function () {
  // IMG
  
  var meal = $("#data-meal").data("meal");
  console.log(meal);
  if (meal.display != "none") {
    $(".custom-input-bfi").fileinput({
      language: meal.lang,
      browseClass: "btn",
      removeClass: "btn",
      uploadClass: "disabled",
      theme: "fa",
      allowedFileExtensions: ["png", "jpg", "jpeg"],
      initialPreview: meal.img ? meal.img : "",
      initialPreviewAsData: meal.img ? true : false,
      initialPreviewConfig: [
        {
          caption: meal.info.length ? meal.info.metadata.filename : "",
          size: meal.info.length ? meal.info.size : "",
          key: 1,
        },
      ],
      minImageWidth: 480,
      minImageHeight: 480,
      maxImageWidth: 1920,
      maxImageHeight: 1920,
      maxFileCount: 1,
      maxSize: "2M",
    });
  }

  // TEXTAREA

  var labels = $("#data-textarea").data("textareaEditor");
  var functions = [];
  var oDoc = document.getElementById("prte_editor_id");
  document.execCommand("defaultParagraphSeparator", false, "p");

  labels.forEach((element, i) => {
    functions.push($("i[command='" + element + "']").parent());
    functions[i].click(function (e) {
      e.preventDefault();
      if (element != "list-ul") {
        $(this).toggleClass("prte_on");
      } else {
        element = "insertunorderedlist";
      }
      formatDoc(element);
    });
  });

  function formatDoc(sCmd, sValue) {
    document.execCommand(sCmd, false, sValue);
    oDoc.focus();
  }

  document
    .querySelector("[contenteditable]")
    .addEventListener("paste", function (event) {
      event.preventDefault();
      document.execCommand(
        "inserttext",
        false,
        event.clipboardData.getData("text/plain")
      );
    });

  $(document).keypress(function (e) {
    if (e.which == 13) {
      $("textarea[name='comments']").val($(".prte_editor").html());
    }
  });
});
