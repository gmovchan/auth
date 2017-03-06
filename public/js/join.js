console.log("join.js");
/*
 * при потерии фокуса с поля формы данные из него отправляются функции, 
 * которая создает AJAX запрос для их проверки на сервере
 */
$('#join-name').blur(function (e) {
    check_form($(this).val(), 'join-name');
})

$('#join-password').blur(function (e) {
    check_form($(this).val(), 'join-password');
})

$('#join-password2').blur(function (e) {
    /*
     * если пароли совпадают, то пхп скрипту отправляется 'match' 
     * 'false' если не совпадают
     * 'empty' если поле пустое
     */
    if ($('#join-password2').val() == '') {
        check_form('empty', 'join-password2');
    } else if ($('#join-password').val() == $('#join-password2').val()) {
        check_form('match', 'join-password2');
    } else {
        check_form('false', 'join-password2');
    }

})

$('#join-mail').blur(function (e) {
    check_form($(this).val(), 'join-mail');
})
