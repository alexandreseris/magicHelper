let header = document.querySelector("header")
document.querySelector("#toogleSearch").addEventListener("click", function() {
  header.hidden = ! header.hidden
})

document.getElementById("delform").addEventListener("click", function(event) {
  event.preventDefault()
  document.querySelectorAll("#searchForm input").forEach(input => {
    switch (input.type) {
      case "text":
        input.value = "";
        break;
      case "checkbox":
        // input.removeAttribute("checked");
        input.checked = false;
        break;
    }
  })
  document.querySelectorAll("#searchForm select option").forEach(option => {
    // option.removeAttribute("selected")
    option.selected = false;
  })
})