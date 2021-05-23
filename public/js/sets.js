document.querySelectorAll(".category").forEach(toggler => {
  toggler.addEventListener("click", function() {
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("category-down");
  });
})


document.querySelectorAll(".categoryInput").forEach(categoryInput => {
  categoryInput.addEventListener("change", function(event) {
    let checkState = event.target.checked
    event.target.parentElement.querySelectorAll("input").forEach(sibling => {
      sibling.checked = checkState
    })
  })
})