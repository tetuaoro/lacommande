$(function () {
  $("span#myInput").on("click", function (e) {
    e.preventDefault();
    let element = document.getElementById("mySelect"); //select the element
    let elementText = element.value; //get the text content from the element
    //If you only want to put some Text in the Clipboard just use this function
    // and pass the string to copied as the argument.
    navigator.clipboard.writeText(elementText);
    /* Alert the copied text */
    alert("Lien copié: " + elementText);
  });
});
