
// show sidebar
const showMenu = (headerToggle, sidebarId, subheaderId, mainId) => {
    const toggleBtn = document.getElementById(headerToggle),
        side = document.getElementById(sidebarId),
        subheader = document.getElementById(subheaderId),
        main = document.getElementById(mainId);

    if (headerToggle && sidebarId) {
        toggleBtn.addEventListener("click", () => {
            side.classList.toggle("show-menu"),
            subheader.classList.toggle("show-menu"),
            main.classList.toggle("show-menu");
            $('.dataTables_scrollHeadInner').css('width', '100%');
            $('.table').css('width', '100%');
        });
    }
};

showMenu("header-toggle", "sidebar", "app-wrapper", "content-header");

//preloader spninner
// const preloaderWrapper = document.querySelector('.preloader-wrapper');

// window.addEventListener('load', function() {
//     preloaderWrapper.classList.add('fade-out-animation');
// });


// $('#header-toggle').click( function () {

// });
