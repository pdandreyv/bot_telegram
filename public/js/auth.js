function getPassword() {
    $('div#error').html('');
    $('div#session_error').html('');

    var csrftoken = $('meta[name="csrf-token"]').attr('content');
    var email = $('#email').val();

    $.ajax({
        url: '/password',
        type: "POST",
        cache: false,
        data: {
            'email': email,
            "_token": csrftoken
        },
        success: function (data) {
            if (data == 1) {
                $('button#get_password').hide();
                $('.password_field').show();
            }
            else {
                $('div#error').html('<div class="alert alert-danger status-box col-sm-6 alert-wrapper">' +
                    '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
                    data + '</div>');
            }
        }
    });
}
