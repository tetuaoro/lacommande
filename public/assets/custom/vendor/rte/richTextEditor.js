jQuery(() => {
  var oDocs = [];

  $.fn.richTextEditor = function () {
    oDocs.push(document.getElementById($(this).attr("id")));
    initRichTextEditor();
  };

  function initRichTextEditor() {
    oDocs.forEach((oDoc) => {
      oDoc.addEventListener("paste", function (event) {
        console.log("paste event");
        event.preventDefault();
        formatDoc(
          oDoc,
          "inserttext",
          event.clipboardData.getData("text/plain")
        );
      });
      formatDoc(oDoc, "defaultParagraphSeparator", "p");
      if ($(oDoc).next(".rte.textarea").length == 0) {
        $(oDoc).after(`<div class="rte textarea">
        <div class="textBox" contenteditable="true">
        <p><br></p>
        </div>
      </div>`);
        if (
          $(oDoc).val() != "" &&
          $(oDoc).next(".rte.textarea").find(".textBox").children().length == 0
        ) {
          $(oDoc).next(".rte.textarea").find(".textBox").append($(oDoc).val());
        }
        $(oDoc)
          .next(".rte.textarea")
          .find(".textBox")
          .on("keyup", function (e) {
            setTimeout(() => {
              $(oDoc).val($(this).html());
            }, 700);
          });
      }
    });
  }
});

function formatDoc(sDoc, sCmd, sValue) {
  sDoc.focus();
  document.execCommand(sCmd, false, sValue);
  if (sCmd != "paste") {
    $(sDoc).toggleClass("btnRteActive");
  }
}
