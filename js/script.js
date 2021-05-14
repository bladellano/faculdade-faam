
(function (doc, win) {

    'use strict';

    /* Appear - Exibição do botão topo */
    $(window).scroll(function (e) {
        
        if ($(this).scrollTop() - 1000 > 0) {
            $('.topo').fadeIn();
        } else {
            $('.topo').fadeOut();
        }
    });

    $('.topo').click(function(e) {
		e.preventDefault();
		$('html, body').animate({
			scrollTop: 0
		}, 500)
	});

    /* Appear - Exibição de dados em cronometro */
    $('.bg-stat-number').appear();

    var cont = 0;

    $('.bg-stat-number').on('appear', function () {

        var fx = function fx() {
            $(".stat-number").each(function (i, el) {
                var data = parseInt(this.dataset.n, 10);
                var props = {
                    "from": {
                        "count": 0
                    },
                    "to": {
                        "count": data
                    }
                };
                $(props.from).animate(props.to, {
                    duration: 1000 * 1,
                    step: function (now, fx) {
                        $(el).text(Math.ceil(now));
                    },
                    complete: function () {
                        if (el.dataset.sym !== undefined) {
                            el.textContent = el.textContent.concat(el.dataset.sym)
                        }
                    }
                });
            });
        };

        var reset = function reset() {
            if ($(this).scrollTop() > 90) {
                // $(this).off("scroll");
                if (!cont) fx();
                cont++
            }
        };


        $(window).on("scroll", reset);


    });

    /* Slick de Parceiros */

    $('.slick_parceiros').slick({
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 5,

    });

    /* Faz parte do evento onmouse para exibir o 
    quadro amarelo nos cursos (mosaico) */

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

