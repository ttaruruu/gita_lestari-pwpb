document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       PROJECT MODAL
    ===================================================== */

    const projectModal =
        document.getElementById("projectModal");

    const projectFrame =
        document.getElementById("projectFrame");

    const projectTitle =
        document.getElementById("projectTitle");


    window.openProject = function (title, url) {

        projectTitle.textContent = title;

        projectFrame.src = url;

        projectModal.classList.add("active");

        document.body.style.overflow = "hidden";
    };


    window.closeProject = function () {

        projectModal.classList.remove("active");

        projectFrame.src = "";

        document.body.style.overflow = "";
    };


    /* =====================================================
       IMAGE GALLERY
    ===================================================== */

    const imageModal =
        document.getElementById("imageModal");

    const galleryTitle =
        document.getElementById("galleryTitle");

    const galleryImage =
        document.getElementById("galleryImage");

    const galleryCounter =
        document.getElementById("galleryCounter");

    const galleryThumbnails =
        document.getElementById("galleryThumbnails");


    let galleryImages = [];

    let galleryIndex = 0;


    /* =====================================================
       OPEN IMAGE
    ===================================================== */

    window.openImage = function (title, images) {

        if (!Array.isArray(images)) {
            images = [images];
        }

        galleryImages = images;

        galleryIndex = 0;

        galleryTitle.textContent = title;

        imageModal.classList.add("active");

        document.body.style.overflow = "hidden";

        createThumbnails();

        showGallery();
    };


    /* =====================================================
       SHOW CURRENT IMAGE
    ===================================================== */

    function showGallery() {

        if (galleryImages.length === 0) {
            return;
        }

        galleryImage.src =
            galleryImages[galleryIndex];

        galleryImage.alt =
            galleryTitle.textContent;

        galleryCounter.textContent =
            `${galleryIndex + 1} / ${galleryImages.length}`;

        updateThumbnails();
    }


    /* =====================================================
       NEXT IMAGE
    ===================================================== */

    window.nextImage = function () {

        if (galleryImages.length <= 1) {
            return;
        }

        galleryIndex++;

        if (
            galleryIndex >=
            galleryImages.length
        ) {
            galleryIndex = 0;
        }

        showGallery();
    };


    /* =====================================================
       PREVIOUS IMAGE
    ===================================================== */

    window.previousImage = function () {

        if (galleryImages.length <= 1) {
            return;
        }

        galleryIndex--;

        if (galleryIndex < 0) {
            galleryIndex =
                galleryImages.length - 1;
        }

        showGallery();
    };


    /* =====================================================
       CREATE THUMBNAILS
    ===================================================== */

    function createThumbnails() {

        galleryThumbnails.innerHTML = "";

        galleryImages.forEach(
            function (image, index) {

                const thumb =
                    document.createElement("img");

                thumb.src = image;

                thumb.alt =
                    `Preview ${index + 1}`;

                thumb.addEventListener(
                    "click",
                    function (event) {

                        event.stopPropagation();

                        galleryIndex = index;

                        showGallery();
                    }
                );

                galleryThumbnails.appendChild(
                    thumb
                );
            }
        );

        updateThumbnails();
    }


    /* =====================================================
       ACTIVE THUMBNAIL
    ===================================================== */

    function updateThumbnails() {

        const thumbnails =
            galleryThumbnails.querySelectorAll("img");

        thumbnails.forEach(
            function (thumbnail, index) {

                thumbnail.classList.toggle(
                    "active",
                    index === galleryIndex
                );
            }
        );
    }


    /* =====================================================
       CLOSE IMAGE
    ===================================================== */

    window.closeImage = function () {

        imageModal.classList.remove("active");

        galleryImage.src = "";

        galleryThumbnails.innerHTML = "";

        galleryImages = [];

        galleryIndex = 0;

        document.body.style.overflow = "";
    };


    /* =====================================================
       KEYBOARD
    ===================================================== */

    document.addEventListener(
        "keydown",
        function (event) {

            /* ESC */

            if (event.key === "Escape") {

                if (
                    imageModal.classList.contains(
                        "active"
                    )
                ) {

                    closeImage();

                    return;
                }

                if (
                    projectModal.classList.contains(
                        "active"
                    )
                ) {

                    closeProject();

                    return;
                }
            }


            /* GALLERY LEFT / RIGHT */

            if (
                imageModal.classList.contains(
                    "active"
                )
            ) {

                if (
                    event.key === "ArrowRight"
                ) {
                    nextImage();
                }

                if (
                    event.key === "ArrowLeft"
                ) {
                    previousImage();
                }
            }
        }
    );


    /* =====================================================
       CLOSE MODALS BY BACKGROUND
    ===================================================== */

    const imageBackground =
        imageModal.querySelector(".image-bg");

    if (imageBackground) {

        imageBackground.addEventListener(
            "click",
            closeImage
        );
    }


    const projectBackground =
        projectModal.querySelector(".modal-bg");

    if (projectBackground) {

        projectBackground.addEventListener(
            "click",
            closeProject
        );
    }


    /* =====================================================
       FOLDER KEYBOARD ACCESS
    ===================================================== */

    document
        .querySelectorAll(".project-folder")
        .forEach(function (folder) {

            folder.addEventListener(
                "keydown",
                function (event) {

                    if (
                        event.key === "Enter" ||
                        event.key === " "
                    ) {

                        event.preventDefault();

                        folder.click();
                    }
                }
            );
        });


    console.log(
        "Portfolio JavaScript loaded successfully."
    );

});