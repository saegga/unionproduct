$(document).ready(function () {
    $('.catalog_ingroup_item').on('click', function (e) {
        if($(this).hasClass("active")){
            $(this).removeClass("active");
        }else{
            $(this).addClass("active");
        }
    });
});