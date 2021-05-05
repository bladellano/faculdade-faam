
(function (doc, win) {

    'use strict';

    $('.slick_parceiros').slick({
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 5,
        });

    $('.box--course > div').hover(function () {
        $(this).find('img,p').toggleClass("to--hide--img")
    });


    /* OWL CAROUSEL */
    $("#owl-banners").owlCarousel({
        slideSpeed: 300,
        paginationSpeed: 400,
        singleItem: true,
        navigation: false,
        autoPlay: true
    });

    /* FIXA O NAVBAR */
    const navbar = doc.querySelector('.navbar--faam');
    win.onscroll = () => {
        if (win.pageYOffset > 100) {
            navbar.style.position = 'fixed';
            navbar.style.top = 0;
        } else {
            navbar.style.position = '';
        }
    }

})(document, window);

