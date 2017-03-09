/*
 * TODO: переписать функцию, чтобы она передавала не GET, а POST запрос, 
 * чтобы скрыть информацию передаваемую из поля
 */
/* функция отправляет данные из поля для ввода скрипту, который проверяет, есть ли
 уже такие данные в БД, в случае ошибки меняет подсказку к полю и его
 цвет на красный или в случае успеха на зеленый */
console.log('validate_form');
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
/* ответ сервера
 * json(
 *  error: '',
 *  message: '',
 *  )
 */