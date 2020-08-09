$(document).ready(function () {
  // MODAL
  var modals = [$("#mealModal"), $("#menuModal"), $("#subuserModal")];
  modals.forEach((elem) => {
    elem.on("show.bs.modal", function () {
      FileInput();
    });
  });

  // FORM CREATE AJAX

  var forms = [
    $("form[name=menu]"),
    $("form[name=meal]"),
    $("form[name=subuser]"),
  ];

  forms.forEach((elem) => {
    elem.submit(function (e) {
      e.preventDefault();

      checkRecaptchaForm(elem, function () {
        var idLoadSpinner = elem.children("fieldset").data("loadSpinner");
        var form = new FormData(elem.get(0));
        var xhr = new XMLHttpRequest();
        $("#" + idLoadSpinner).LoadingOverlay("show", {
          imageColor: appColor1,
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
              ajaxComplete(elem, xhr);
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

  // FORM DELETE AJAX

  var formsDelete = [$("form[name=meal-delete]")];

  formsDelete.forEach((elem) => {
    console.log(elem.parents(".card"));
    elem.submit(function (e) {
      e.preventDefault();
      console.log("submit form delete");
      var modal = elem.data("modalDelete");
      $(modal).modal("show");

      $($(modal).data("targetBtn")).click(function (e) {
        e.preventDefault();
        var form = new FormData(elem.get(0));
        var xhr = new XMLHttpRequest();
        var spinner = $(this);
        spinner.LoadingOverlay("show", {
          imageColor: appColor1,
          background: "rgba(255, 255, 255, 0.6)",
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
              ajaxComplete(elem, xhr);
            }
            spinner.LoadingOverlay("hide");
            $(modal).modal("hide");
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
      $(".bg-faIcon").attr("data-fa-icon", $(this).data("faIcon"));
      location.hash = $(this).attr("href");
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
    location.reload();
    if (form.attr("name") == "meal") {
      if (xhr.status == 201) {
        $(".manage-meal").append(xhr.responseText);
        if ($(".manage-meal-noone").length > 0) {
          $(".manage-meal-noone").remove();
        }
        setTimeout(() => {
          $("#mealModal").modal("hide");
        }, 300);
      }
      if (xhr.status == 400) {
        form.html(xhr.responseText);
      }
    }
    if (form.attr("name") == "menu") {
      if (xhr.status == 201) {
        $(".manage-menu").append(xhr.responseText);
        if ($(".manage-menu-noone").length > 0) {
          $(".manage-menu-noone").remove();
        }
        setTimeout(() => {
          $("#menuModal").modal("hide");
        }, 300);
      }
      if (xhr.status == 400) {
        form.html(xhr.responseText);
        FileInput();
      }
    }

    if (form.attr("name") == "meal-delete") {
      if (xhr.status == 201) {
        console.log(xhr.responseText);
        form.parents(".card").get(0).remove();
      }
      if (xhr.status == 400) {
        console.log(xhr.responseText);
      }
    }

    return;
  }
});
