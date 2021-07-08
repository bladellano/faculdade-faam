$(function () {

    tinymce.init({
        selector: '.textareaCkeditor',
        height: 500,
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css'
        ]
    });

});