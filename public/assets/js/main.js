
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

$('.has-children').mouseenter(function(){
    $(this).children('ul').stop(true, true).fadeIn('fast');
}).mouseleave(function(){
    $(this).children('ul').stop(true, true).fadeOut('fast');
});

$('.has-children').click(function(){
    var $this = $(this);

    $(this).next().stop(true, true).slideToggle('fast', function(){
        $this.parent().toggleClass('tree-open');
    });
    return false;
});

// $('.sidebar').overlayScrollbars({scrollbars : {autoHide: 'leave', autoHideDelay: 100} });

showMenu("header-toggle", "sidebar", "app-wrapper", "content-header");

//preloader spninner
// const preloaderWrapper = document.querySelector('.preloader-wrapper');

// window.addEventListener('load', function() {
//     preloaderWrapper.classList.add('fade-out-animation');
// });


// $('#header-toggle').click( function () {

// });
