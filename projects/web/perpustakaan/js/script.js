document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.querySelector(".search-box input");

    if (searchInput) {

        searchInput.addEventListener("keydown", function (event) {

            if (event.key === "Enter") {
                this.closest("form").submit();
            }

        });

    }

});