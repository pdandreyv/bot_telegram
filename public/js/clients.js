function getClientsList(url_default = null, perPage = null, sorting = null){
    var search = ($('#search2').val()) ? $('#search2').val() : null;
    var region = ($('#region').val()) ? $('#region').val() : 'all';
    var group = ($('#group').val()) ? $('#group').val() : 'all';
    var paymentType = ($('#paymentType').val()) ? $('#paymentType').val() : 'all';
    var perPage = (perPage) ? perPage : $('select.product-page-num-sel').val();
    var url = '';
    var sorting = sorting;

    if(url_default) {
        url = url_default;
    }
    else {
        url = "/ajax/clients/list";
        var page = ($('.pagination li.active span').text()) ? $('.pagination li.active span').text() : 1;
        if (page != 1) {
            url += '?page=' + page;
        }
    }

    $('div.wrapper').show();
    $.ajax({
        url: url,
        type: "GET",
        cache: false,
        dataType: "html",
        data: {
                'search': search,
                'region': region,
                'perPage': perPage,
                'group': group,
                'sorting': sorting,
                'paymentType': paymentType,
              },
        success: function (html) {
            $('#clients_list').html('');
            $('#clients_list').html(html);
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function showCats(id){
    $('.categories').prop("checked", false);
    var csrftoken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/getcats',
        type: "get",
        cache: false,
        dataType: "json",
        data: {'id':id, "_token": csrftoken},
        success: function (cats) {
            $('#client_id').val(id);
            $('.categories').each(function(){
                if(cats.length) for(i in cats){
                    if($(this).val()==cats[i]) {
                        $(this).prop('checked',true);
                    }
                }
            });
        }
    });
}

function saveClientCats(id,cats){
    var csrftoken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/savecats',
        type: "POST",
        cache: false,
        dataType: "json",
        data: {'id':id,'cats':cats, "_token": csrftoken},
        success: function (data) {
            $('#cats'+data['id']).html(data['cats']);
            $('.bs-example-modal-lg').modal('toggle');
        }
    });
}

function getClientData(id) {
    $.ajax({
        url: '/client',
        type: "GET",
        cache: false,
        dataType: "json",
        data: {'id':id},
        success: function (data) {
            $('.tgname').html('');
            $('.tgname').html(data['username']);
            $('.uid').html('');
            $('.uid').html(data['uid']);
            $('.unique_number').val('');
            $('.unique_number').val(data['unique_number']);
            $('.client_remove').val('');
            $('.client_remove').val(data['id']);

            if (data['showReceipts'] == 1) {
                $('#showReceipts').attr('checked', true);
            }
            else {
                $('#showReceipts').attr('checked', false);
            }

            $('#typeGroup').text('');
            var obj = {'1': 'Крупный опт', '0': 'Мелкий опт', '2': 'Средний опт'};

            Object.keys(obj).forEach(function(key, id) {
                if (data['type'] == key) {
                    $('#typeGroup').append("<option selected value='" + key + '_type_' + "'>" + obj[key] + "</option>");
                }
                else {
                    $('#typeGroup').append("<option value='" + key + '_type_' +"'>" + obj[key] + "</option>");
                }
            });

            if (typeof($('.type_group')) != 'undefined') {
                var value = '';
                switch(data['type']) {
                    case 1:
                        value = 'Крупный опт';
                        break;

                    case 0:
                        value = 'Мелкий опт';
                        break;

                    case 2:
                        value = 'Средний опт';
                        break;

                    default:
                        value = '';
                        break;
                }

                $('.type_group').html(value);
            }
        }
    });
}

function removeClient(id) {
    $.ajax({
        url: '/clients/' + id,
        type: "GET",
        cache: false,
        success: function () {
            $('.bs-example-modal-lg3').modal('toggle');
            getClientsList();
        }
    });
}

function changeActivity(id) {
    $.ajax({
        url: '/clients/activity',
        type: "GET",
        cache: false,
        data: {'id':id},
        success: function () {
            getClientsList();
        }
    });
}

function changeNumber(id) {
    $.ajax({
        url: '/clients/number',
        type: "GET",
        cache: false,
        data: {'data':id},
        success: function () {

        }
    });
}

function changeReceipts(id) {
    $.ajax({
        url: '/clients/receipts',
        type: "GET",
        cache: false,
        data: {'id':id},
        success: function () {

        }
    });
}




