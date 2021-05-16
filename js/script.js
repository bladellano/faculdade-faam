
(function (doc, win) {

    'use strict';

    /* Form - tentativa de contato */

    $('#form-contato').validate({
        rules: {
            name: {
                required: true,
                maxlength: 100,
                minlength: 3,
                minWords: 2
            },
            email: {
                required: true,
                email: true,
            },
            phone: {
                required: true,
                minlength: 15,
            },
            message: {
                required: true,
                minlength: 3,
            }
        },
        submitHandler: function () {

            var data = $('#form-contato').serializeArray();

            ajaxSubmitFormContact(data);
        }
    });

    const ajaxSubmitFormContact = (data) => {

        $.ajax({
            method: "POST",
            url: "/send-form-contact",
            data: data,
            dataType: "json",
            beforeSend: function () {
                load('open');
            },
            success: function (r) {
                if (r.success) {
                    Swal.fire(
                        'Tudo certo!',
                        r.msg,
                        'success'
                    );
                    $('#form-contato')[0].reset();
                } else {
                    Swal.fire(
                        'Ooops!',
                        r.msg,
                        'error'
                    );
                }
            },
            complete: () => {
                load('close');
            }
        });

    }


    /* Appear - Exibição do botão topo */
    $(window).scroll(function (e) {

        if ($(this).scrollTop() - 1000 > 0) {
            $('.topo').fadeIn();
        } else {
            $('.topo').fadeOut();
        }
    });

    $('.topo').click(function (e) {
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
    // const navbar = doc.querySelector('.navbar--faam');
    const navbar = doc.querySelector('nav.navbar');
    win.onscroll = () => {
        if (win.pageYOffset > 100) {
            navbar.classList.add('fixed-top');
        } else {
            navbar.classList.remove('fixed-top');
        }
    }

})(document, window);

/*Function of loading*/
function load(action) {
	var load_div = $('.ajax_load');
	if (action === 'open') {
		load_div.fadeIn().css('display', 'flex');
	} else {
		load_div.fadeOut();
	}
}

