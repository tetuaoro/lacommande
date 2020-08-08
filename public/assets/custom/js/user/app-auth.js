$(document).ready(function () {
  // MODAL
  var modals = [$("#mealModal"), $("#menuModal"), $("#subuserModal")];
  modals.forEach((elem) => {
    elem.on("show.bs.modal", function () {
      FileInput();
    });
  });

  // FORM AJAX

  var forms = [
    $("form[name=menu]"),
    $("form[name=meal]"),
    $("form[name=subuser]"),
  ];

  forms.forEach((elem) => {
    elem.submit(function (e) {
      e.preventDefault();

      checkRecaptchaForm(elem, function () {
        console.log("2e", elem, $(this));
        var idLoadSpinner = elem.children("fieldset").data("loadSpinner");
        var form = new FormData(elem.get(0));
        var xhr = new XMLHttpRequest();
        $("#" + idLoadSpinner).LoadingOverlay("show", {
          imageColor: "#dcdc0a",
          background: "rgba(255, 255, 255, 0.4)",
        });
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            if (xhr.status === 201) {
              ajaxComplete(elem, xhr);
            }
            if (xhr.status === 400) {
              ajaxComplete(elem, xhr);
            }
            if (xhr.status === 405) {
              document.location.url = document.location.href;
            }
            $("#" + idLoadSpinner).LoadingOverlay("hide");
          }
        };
        xhr.open(elem.attr("method"), elem.attr("action"));
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.send(form);
      });
    });
  });

  // MENU TAB PILLS

  var pillsBtn = [
    $("#v-pills-meal-tab"),
    $("#v-pills-menu-tab"),
    $("#v-pills-subuser-tab"),
    $("#v-pills-setting-tab"),
  ];

  pillsBtn.forEach((elem) => {
    elem.click(function (e) {
      e.preventDefault();
      $(".user-auth").attr("data-fa-icon", $(this).data("faIcon"));
    });
  });

  // IMG BOOTSTRAP FILE INPUT

  function FileInput() {
    var meal = $("#data-meal").data("meal");
    $(".custom-input-bfi").fileinput({
      language: meal.lang,
      browseClass: "btn",
      showCaption: false,
      showUpload: false,
      theme: "fa",
      previewFileType: "image",
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
      maxSize: "5M",
    });
  }

  // TEXTAREA

  var labels = $("#data-textarea").data("textareaEditor");
  var functions = [];
  var oDoc = document.getElementById("prte_editor_id");
  document.execCommand("defaultParagraphSeparator", false, "p");

  if (labels && labels.length > 0) {
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
  }

  // FUNCTIONS

  function checkRecaptchaForm(form, fn) {
    if (form.find(".recaptcha-check").length > 0) {
      grecaptcha.ready(function () {
        grecaptcha
          .execute($(".recaptcha-check").data("sitekey"))
          .then(function (token) {
            $(".recaptcha-check").val(token);
            fn();
          })
          .catch(function (e) {
            return e;
          });
      });
    }
  }

  function ajaxComplete(form, xhr) {
    if (form.attr("name") == "meal") {
      if (xhr.status == 201) {
        $(".manage-meal").append(xhr.responseText);
        $("#mealModal").modal("hide");
      }
      if (xhr.status == 400) {
        form.html(xhr.responseText);
      }
    }
    if (form.attr("name") == "menu") {
      if (xhr.status == 201) {
        $(".manage-menu").append(xhr.responseText);
        $("#menuModal").modal("hide");
      }
      if (xhr.status == 400) {
        form.html(xhr.responseText);
        FileInput();
      }
    }

    return;
  }
});
