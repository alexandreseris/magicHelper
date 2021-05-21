for (const toggler of document.getElementsByClassName("category")) {
  toggler.addEventListener("click", function() {
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("category-down");
  });
}


for (const categoryInput of document.getElementsByClassName("categoryInput")) {
  categoryInput.addEventListener("change", function(event) {
    let checkState = event.target.checked
    for (let sibling of event.target.parentElement.querySelectorAll("input")) {
      sibling.checked = checkState
    }
  })
}