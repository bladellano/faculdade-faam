$(function () {


    /* Controla exibição dos campos de ensinos para os cursos */

    changeInputsFormCurso($('select[name="ensino"]'));
    $('select[name="ensino"]').change(function (e) {
        changeInputsFormCurso($(this));
    });

    /* Exclui documento pdf do curso */
    $('.btnDestroyDoc').click(function () {

        var doc = $(this);

        $.ajax({
            type: "post",
            url: "/admin/cursos/delete-doc",
            data: { id: doc.data('id') },
            dataType: "json",
            success: function (r) {
                if (r.success) {
                    $('[data-id="' + doc.data('id') + '"]').filestyle('disabled', false);
                    $('[data-id="' + doc.data('id') + '"]').filestyle('placeholder', '');
                }
            }
        });
    });

    /* Verifica se existe documento e exibe no modificar do curso */
    $('[accept="application/pdf"]').each((i, e) => {

        let doc = $(e).data('documento');

        if (doc.length) {

            $(e).filestyle({
                text: 'Carregar',
                btnClass: 'btn-primary',
                htmlIcon: '<span class="glyphicon glyphicon-file"></span> ',
                disabled: true,
                placeholder: doc

            });
        }
    });

    /* Deleta a imagem */
    $('#loading_all_images').delegate(".btn-delete-image a", "click", function (e) {
        e.preventDefault();

        const id = $(this).data('id');
        const path = $(this).attr('href');

        $.ajax({
            type: "get",
            url: "/admin/delete-image",
            data: { id, path },
            dataType: "json",
            success: function (r) {
                if (r.success == true) {
                    $('#file_send_image').val('');
                    alertify.success(r.msg);
                    showContentAllImages();
                } else {
                    alertify.error(r.msg);
                }
            }
        });

    });

    /* Copia o caminho da imagem */
    $("#loading_all_images").delegate(".btn-copy-image a", "click", function (e) {

        e.preventDefault();

        var textarea = document.createElement('textarea');
        textarea.textContent = $(this).attr('href');
        document.body.appendChild(textarea);

        var selection = document.getSelection();
        var range = document.createRange();

        range.selectNodeContents(textarea);
        selection.removeAllRanges();
        selection.addRange(range);

        document.execCommand('copy');
        selection.removeAllRanges();
        document.body.removeChild(textarea);

        alertify.success('Texto copiado para a área de transferência');

    });

    /* Dá um load nas imagens já existens */
    showContentAllImages();

    $('#btn_save_send_image').click(function () {

        const formData = new FormData($("#form_send_image")[0]);

        $.ajax({
            type: "post",
            url: "/admin/send-image",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: () => {
                load('open');
            },
            success: function (r) {
                if (r.success == true) {
                    $('#file_send_image').val('');
                    alertify.message(r.msg);
                } else {
                    alertify.error(r.msg);
                }
            },
            complete: function () {
                load('close');
                showContentAllImages();
            }
        });
    })

    /* Seta a lib datepicker na class */
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        language: 'pt-BR',
        startDate: '+0d'
    });

    /* Status Evento */
    $('[name="status_change"]').change(function () {
        let id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "/admin/eventos/change-status",
            data: { id },
            dataType: "html",
            beforeSend: () => {
                load('open');
            },
            success: function () {
                location.reload();
            }
            ,
            complete: () => {
                load('close');
            }
        });
    });

    /* Ordena Evento */
    $('[name="in_order_change"]').change(function () {
        let id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "/admin/eventos/change-order",
            data: { id },
            dataType: "html",
            beforeSend: () => {
                load('open');
            },
            success: function () {
                location.reload();
            }
            ,
            complete: () => {
                load('close');
            }
        });
    });

    /* POPOVER */
    $('[data-toggle="popover"]').popover();

    /* Plugin iCheck */
    $('input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });

    /* Garante a quantidade de arquivos. */
    $('#images').change(function () {
        var files = $(this).get(0).files;
        if (files.length > 10) {
            $(this).val('');
            $(this).next().find(':input').val('');
            return alert('Permitido até 10 imagens selecionadas.');
        }
    });

    /* Customiza os inputs do bootstrap */
    $(':file').filestyle({
        text: 'Carregar',
        btnClass: 'btn-primary',
        htmlIcon: '<span class="glyphicon glyphicon-file"></span> ',
        'onChange': function (files) {
            console.log(files)
        }
    });

    $('[data-toggle="tooltip"]').tooltip();

    /* Trata entrada de valores para o input slug */
    $('#slug').keydown(e => {
        e.target.value = string_to_slug(e.target.value);
    });

    /* Modal for create new name album */

    $('#btnSaveNewAlbum').click(() => {

        const album = $('#name_album').val();

        if (!album.length) return alert('Por favor, digite um nome.')

        $.ajax({
            type: "POST",
            url: "/admin/albums/create-name",
            data: { album },
            dataType: "json",
            beforeSend: () => {
                load('open');
            },
            success: function (r) {

                if (r.success == true) {
                    var html = `<option value="">SELECIONE</option>`;
                    r.data.forEach((e) => {
                        html += `<option value="${e.id}">${e.album}</option>`;
                    });

                    $('#id_photos_albums').html(html);
                    $('#modalNewAlbum').modal('hide');
                    $('#id_photos_albums option:contains("' + album + '")').attr('selected', true);
                    $('#name_album').val('');
                } else {
                    $('.show-error').html(r.msg).addClass('text-danger');
                }
            }
            ,
            complete: () => {
                load('close');
            }

        });
    });

});

/*===============================*/
/*=======ALL FUNCTIONS===========*/
/*===============================*/

function changeInputsFormCurso(elem) {

    let ensino = $(elem).val();

    if (ensino == "PÓS-GRADUAÇÃO") {
        $('.show--graduacao').hide();
        $('.show--pos--graduacao').show();
    } else {

        $('.show--graduacao').show();
        $('.show--pos--graduacao').hide();
    }
}

function showContentAllImages() {
    $('#loading_all_images').load('/admin/list-images').fadeIn('slow');
}

function previewFile(e) {
    var file = $(e).get(0).files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function () {
            $(e).siblings().attr('src', reader.result).fadeIn();
        }
        reader.readAsDataURL(file);
    }
}

/* Function of create slug */
function string_to_slug(str) {
    str = str.replace(/^\s+|\s+$/g, '');
    str = str.toLowerCase();

    var from = "àáãäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to = "aaaaaeeeeiiiioooouuuunc------";

    for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

    return str;
}

/*Function of loading*/
function load(action) {
    var load_div = $('.ajax_load');
    if (action === 'open') {
        load_div.fadeIn().css('display', 'flex');
    } else {
        load_div.fadeOut();
    }
}

