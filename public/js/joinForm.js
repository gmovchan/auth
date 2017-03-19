window.onload = function () {
    console.log("joinForm.js");
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

    function check_form(data, type) {

        $.ajax({
            url: "../validate/checkjoin",
            cache: false,
            data: "data=" + data + "&type=" + type,
            dataType: "json",
            success: function (json) {

                var block,
                        block_help;

                switch (type) {
                    case 'join-name':
                        block = '#join-name';
                        block_help = '#join-name-help';
                        break;

                    case 'join-password':
                        block = '#join-password';
                        block_help = '#join-password-help';
                        break;

                    case 'join-password2':
                        block = '#join-password2';
                        block_help = '#join-password2-help';
                        break;

                    case 'join-mail':
                        block = '#join-mail';
                        block_help = '#join-mail-help';
                        break;

                    default:
                        break;
                }
                // FIXME: Дублирующийся код
                // если есть ошибка, то поле будет выделено красным и будет показан подсказка
                if (json.error == "true") {
                    $(block).parent().removeClass('has-success').addClass('has-error');
                    if (json.message != 'false') {
                        $(block_help).show();
                        $(block_help).text(json.message);
                    } else {
                        $(block_help).hide();
                    }
                } else {
                    $(block).parent().removeClass('has-error').addClass('has-success');
                    if (json.message != 'false') {
                        $(block_help).show();
                        $(block_help).text(json.message);
                    } else {
                        $(block_help).hide();
                    }

                }
            }
        });
    }
}
/* ответ сервера
 * json(
 *  error: '',
 *  message: '',
 *  )
 */
