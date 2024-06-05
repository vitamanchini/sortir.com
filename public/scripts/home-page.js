
let filterSection = document.getElementById('filter-section')
let filterButton = document.getElementById('filterslogo')
filterButton.addEventListener('click',()=>{
    if (filterSection.style.display === "flex") {
        filterSection.style.display = "none"
    }
    else {
        filterSection.style.display = "flex"
    }
})
