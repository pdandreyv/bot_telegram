csrftoken = $('meta[name="csrf-token"]').attr('content');

function view_input(id, table, clientId = null, isReseller = false) {
    $( '#' + id ).show();
    $( '#a-' + id ).hide();
    $( '#' + id ).focus();
    $( '#' + id ).keypress(function(event) {
        // Enter
        if(event.keyCode==13){
            $( '#' + id ).blur();
        }
    });

    var arr = id.split('-');
    var cell = arr[0];
    var currentId = arr[1];

    // Focus out
    $( '#' + id ).focusout(function(){
        $( '#' + id ).hide();
        var csrftoken = $('meta[name="csrf-token"]').attr('content');
        var new_value = $('#' + id).val();

        $.ajax({
            url: '/' + table + '/update',
            type: 'post',
            data: {'new_value':new_value,"_token": csrftoken, 'id_value': id},
            dataType: 'html',
            success: function(data){
                if(table == 'products'){
                    getProductsTable();
                    $('#a-' + id).text(data);
                    $('#a-' + id).show();
                }
                else if(table == 'categories'){
                    getCategoriesList();
                }
                else if(table == 'orders') {
                    if (isReseller == false) {
                        getProvidersLists(clientId, true);
                    }
                    else {
                        getProvidersListsReseller(clientId, true);
                    }
                    //getOrdersList();
                }
                else if (!data) data='...';
                else {
                    $('#a-' + id).text(data);
                    $('#a-' + id).show();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            },
        });
        $( '#' + id ).unbind();
        return false;
    });
}
function table_update(table)
{
    $.ajax({
        url: '/'+table+'/table_ajax',
        type: 'post',
        data: {"_token": csrftoken},
        dataType: 'html',
        success: function(data){
            $('#'+table+'_table').html(data);
        },
        error: function(xhr, textStatus) {
            $('#'+table+'_table').html('<div class="error">Ошибка соединения с сервером</div>');
        }
    });
}
// clients and product update
function new_cron(id) {
    arr = id.split('-');
    id = arr[1];
    value = arr[0];
    $( '#cron_' + value ).keypress(function(event) {
        // Enter
        if(event.keyCode==13)
        {
            $( '#cron_' + value ).blur();
        }
    });

    // Focus out
    $( '#cron_' + value ).focusout(function(){
        var new_value = $('#cron_' + value).val();
        $.ajax({
            url: 'cron/update',
            type: 'post',
            data: {'new_value':new_value,"_token": csrftoken, 'id_value': id},
            dataType: 'html',
            success: function(data){
                $('#cron_' + value).text(data);
            },
            error: function(xhr, textStatus) {
                //alert( [ xhr.status, textStatus ] );
            }
        });

        return false;
    });
}



// clients
function check_checkbox(id, table = 'clients') {
    $.ajax({
        url: '/' + table + '/update_checkbox',
        type: 'POST',
        data: {"_token": csrftoken, 'id_value': id},
        dataType: 'html',
        success: function(data) {
            if (table = 'percents') {
                window.location.reload();
            }
        }
    });
}

// products
function select_category(id) {
    $('#div-subcat').addClass('hidden');
    $('#subcat').text('');
    $.ajax({
        url: '/products/subcat',
        type: 'post',
        data: {"_token": csrftoken, 'id': id},
        dataType: 'html',
        success: function(data){
            var subcat = $('#product_subcat').val();
            var get = JSON.parse(data);
            if(get[0]){
                $('#subcat').text('');
                $('#div-subcat').removeClass('hidden');
                for (var el, i = 0; i < get.length; i++) {
                    el = get[i];
                    if(el['id'] == subcat) {
                        $('#subcat').append("<option selected value='" + el.id + "'>" + el.name + "</option>");
                    }
                    else {
                        $('#subcat').append("<option value='" + el.id + "'>" + el.name + "</option>");
                    }
                }
            }
        }
    });
}

// bot settings
function view_textarea(id, table) {
    $( '#' + id ).show();
    $( '#a-' + id ).hide();
    $( '#area-' + id ).focus();
    $( '#button-' + id).on('click', function(event) {
        $( '#' + id ).hide();
        var new_value = $('#area-' + id).val();
        $.ajax({
            url: '/' + table + '/update',
            type: 'post',
            data: {'new_value':new_value,"_token": csrftoken, 'id_value': id},
            dataType: 'html',
            success: function(data){
                $('#area-' + id).text(data);
                $('#a-' + id).text(data);
                $( '#a-' + id ).show();
            },
            error: function(xhr, textStatus) {
                alert( [ xhr.status, textStatus ] );
            }
        });
        return false;
    });

    $( '#close-' + id).on('click', function(event) {
        $( '#' + id ).hide();
        $( '#a-' + id ).show();
        return false;
    });
}
function rassilka_save(id){
    srval = $('#send_rassilka').prop('checked');
    if(srval) $('#area-' + id).val(1);
    else $('#area-' + id).val(0);
    mailing_save(id);
}
function mailing_save(id) {
    var new_value = $('#area-' + id).val();
    $.ajax({
        url: '/mailing/update',
        type: 'post',
        data: {'new_value':new_value,"_token": csrftoken, 'id_value': id},
        dataType: 'html',
        success: function(data){
            $('#area-' + id).text(data);
        },
        error: function(xhr, textStatus) {
            //alert( [ xhr.status, textStatus ] );
        }
    });
    return false;
}
function activate_clients(id)
{
    $.ajax({
        url: '/clients/activate_all',
        type: 'post',
        data: {'value':id,"_token": csrftoken},
        dataType: 'html',
        success: function(data){
            $('.active-checkbox input').attr('checked',(id)?true:false);
            alert(data);
        },
        error: function(xhr, textStatus) {
            //alert( [ xhr.status, textStatus ] );
        }
    });
    return false;
}

function search_image(id=0) {
    $( '#image-' + id).click();
}
function deleteItem(table,id)
{
    $.ajax({
        url: '/' + table + '/delete',
        type: 'post',
        data: {'id':id,"_token": csrftoken},
        success: function(data){
            if(table == 'products') {
                $('#remove-product').modal('toggle');
                getProductsTable();
            }
            else {
                table_update(table);
            }
            //alert('Объект удален успешно');
        },
        error: function(xhr, textStatus) {
            alert('По неизвестной причине мы не смогли удалить этот объект');
        }
    });
}
function click_button(id=0) {
    $('#submit-' + id).click();
}

$(document).ready(function(){
    $("#search").keyup(function(){
        var new_value = $('#search').val();
        var page = $('.ajax_info').val();

        $.ajax({
            url: '/' + page + '/test_ajax',
            type: 'post',
            data: {'new_value':new_value,"_token": csrftoken},
            dataType: 'html',
            success: function(data){
                $('#test_ajax').text('');
                $('#test_ajax').append(data);
            },
            error: function(xhr, textStatus) {
                //alert( [ xhr.status, textStatus ] );
            }
        });

        $(".pagination_block").hide();

        if( new_value == '') {
            $(".pagination_block").show();
        }
    });
});

function getOrdersTotal(){
    var date = $('.datepicker').val();

    $.ajax({
        url: "/ajax/orders/total",
        type: "POST",
        cache: false,
        dataType: "html",
        data: {"_token": csrftoken, 'date': date},
        success: function (html) {
            $('div.order-total').html('');
            $('div.order-total').html(html);
        }
    });
}

function getOrdersStatistic(date = null, date2 = null, url_default = null, client_id = null, perPage = null, category = null, categoriesContainer = null){
    var date = (date) ? date : $('.datepicker').val();
    var date2 = (date2) ? date2 : $('.datepicker2').val();
    var categoriesContainer = (categoriesContainer) ? categoriesContainer : $('#categoriesContainer').val();

    var provider = [];
    $('#providers input:checkbox:checked').each(function(index, value){
        provider.push($(value).val());
    });

    var search = ($('#search').val()) ? $('#search').val() : null;
    var region = ($('#region').val()) ? $('#region').val() : 'all';
    var client_id = (client_id) ? client_id : $('.current_user').val();
    var perPage = (perPage) ? perPage : $('select.product-page-num-sel').val();

    var category = [];
    $('#categories input:checkbox:checked').each(function(index, value){
        category.push(parseInt($(value).val()));
    });

    var url = '';

    if(url_default) {
        url = url_default;
    }
    else {
        url = "/ajax/orders/statistic";
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
                'provider':provider,
                'date': date,
                'date2': date2,
                'search': search,
                'region': region,
                'client_id': client_id,
                'perPage': perPage,
                'category': category,
                'categoriesContainer': categoriesContainer,
              },
        success: function (html) {
            $('div.orders-statistic').html('');
            $('div.orders-statistic').html(html);
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function getStatisticXls(){
    var date = (date) ? date : $('.datepicker').val();
    var date2 = (date2) ? date2 : $('.datepicker2').val();

    var provider = [];
    $('#providers input:checkbox:checked').each(function(index, value){
        provider.push($(value).val());
    });
    var category = [];
    $('#categories input:checkbox:checked').each(function(index, value){
        category.push(parseInt($(value).val()));
    });
    var client_id = (client_id) ? client_id : $('.current_user').val();

    $('div.wrapper').show();
    $.ajax({
        url: 'ajax/orders/statistic/xls',
        type: "GET",
        cache: false,
        dataType: "html",
        data: {
            'provider':provider,
            'date': date,
            'date2': date2,
            'client_id': client_id,
            'category': category
        },
        success: function () {
            $('button.download_xls').attr('disabled', false);
            location.href = '/download/stats/' + date + '/' + date2 + '.xls';
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function getOrdersXls(){
    var date = (date) ? date : $('.datepicker').val();
    var provider = $('#provider').val();
    var region = $('#region').val();

    $('div.wrapper').show();
    $.ajax({
        url: 'ajax/orders/get/xls',
        type: "GET",
        cache: false,
        dataType: "html",
        data: {
            'provider':provider,
            'date': date,
            'id': region
        },
        success: function () {
            $('button.download_xls').attr('disabled', false);
            location.href = '/download/order/' + date + '.xls';
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function getOrdersList(url_default = null, perPage = null){
    var provider = ($('#provider').val()) ? $('#provider').val() : 'all';
    var region = ($('#region').val());
    var date = $('.datepicker').val();
    var perPage = (perPage) ? perPage : $('select.product-page-num-sel').val();
    var client_id = $('#client_show').val();

    var url = '';
    if(url_default) {
        url = url_default;
    }
    else {
        url = "/ajax/orders/list";
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
        data: {'id': region, 'provider': provider, 'date': date, 'perPage': perPage},
        success: function (html) {
            $('div.orders-list').html('');
            $('div.orders-list').html(html);
        },
        complete: function() {
            $('div.wrapper').hide();
            getProvidersLists(client_id);
            var div = $('div#client_' + client_id);
            $(div).show();
            var image = $(div).parent().parent().prev().find('img.show_details');
            var src = "/img/minus.png";
            image.attr("src", src);
        }
    });
}

function getOrdersListReseller(url_default = null, perPage = null, resellerId){
    var provider = ($('#provider').val()) ? $('#provider').val() : 'all';
    var region = ($('#region').val());
    var date = $('.datepicker').val();
    var perPage = (perPage) ? perPage : $('select.product-page-num-sel').val();
    var client_id = $('#client_show').val();
    var resellerId = (resellerId) ? resellerId : $('#resellerId').val();

    var url = '';
    if(url_default) {
        url = url_default;
    }
    else {
        url = "/ajax/orders/listReseller";
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
        data: {'id': region, 'provider': provider, 'date': date, 'perPage': perPage, 'resellerId': resellerId},
        success: function (html) {
            $('div.orders-list').html('');
            $('div.orders-list').html(html);
        },
        complete: function() {
            $('div.wrapper').hide();
            getProvidersListsReseller(client_id);
            var div = $('div#client_' + client_id);
            $(div).show();
            var image = $(div).parent().parent().prev().find('img.show_details');
            var src = "/img/minus.png";
            image.attr("src", src);
        }
    });
}

function getProvidersLists(client_id, isChanged = false){
    var date = $('.datepicker').val();
    var url = "/ajax/providers/lists";
    var provider = ($('#provider').val()) ? $('#provider').val() : 'all';

    $('div.wrapper').show();
    $.ajax({
        url: url,
        type: "GET",
        cache: false,
        dataType: "html",
        data: {'date': date,
               'client_id': client_id,
               'provider': provider
        },
        success: function (html) {
            $('div#client_' + client_id).html('');
            $('div#client_' + client_id).html(html);

            var order = $('tr#order_client_' + client_id);
            var total = $('div#client_' + client_id);
            var quantity = 0;
            var quantityTotal = 0;
            var quantities = $(total).find('.total_quantity');
            $.each(quantities, function(value, index){
                quantity += parseInt($(index).text());
            });
            $(order).find('.order_quantity').text(quantity);

            var orders = $('.order_quantity');
            $.each(orders, function(value, index){
                quantityTotal += parseInt($(index).text());
            });
            $('#order_quantity').text('');
            $('#order_quantity').text(quantityTotal);

            var sum = 0;
            var sumTotal = 0;
            var sums = $(total).find('.total_sum');
            $.each(sums, function(value, index){
                sum += +$(index).text().replace(/\./g, "");
            });
            sum = sum.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $(order).find('.order_sum').text(sum);

            var sumsAll = $('.order_sum');
            $.each(sumsAll, function(value, index){
                sumTotal += +$(index).text().replace(/\./g, "");
            });
            sumTotal = sumTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $('#order_sum').text(sumTotal + ' руб.');

            var positions = $('div#client_' + client_id).find('span.order_position').length;
            $(order).find('.order_products').text(positions);

            var positionsTotal = 0;
            $.each($('.order_products'), function(value, index){
                positionsTotal += parseInt($(index).text());
            });
            $('#order_products').text(positionsTotal);

            if (isChanged) {
                $('button#send_techn_' + client_id).removeClass('disabled');
                $('button#send_book_' + client_id).removeClass('disabled');
            }

            $('#client_show').val(client_id);
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}


function getProvidersLists(client_id, isChanged = false){
    var date = $('.datepicker').val();
    var url = "/ajax/providers/lists";
    var provider = ($('#provider').val()) ? $('#provider').val() : 'all';

    $('div.wrapper').show();
    $.ajax({
        url: url,
        type: "GET",
        cache: false,
        dataType: "html",
        data: {'date': date,
            'client_id': client_id,
            'provider': provider
        },
        success: function (html) {
            $('div#client_' + client_id).html('');
            $('div#client_' + client_id).html(html);

            var order = $('tr#order_client_' + client_id);
            var total = $('div#client_' + client_id);
            var quantity = 0;
            var quantityTotal = 0;
            var quantities = $(total).find('.total_quantity');
            $.each(quantities, function(value, index){
                quantity += parseInt($(index).text());
            });
            $(order).find('.order_quantity').text(quantity);

            var orders = $('.order_quantity');
            $.each(orders, function(value, index){
                quantityTotal += parseInt($(index).text());
            });
            $('#order_quantity').text('');
            $('#order_quantity').text(quantityTotal);

            var sum = 0;
            var sumTotal = 0;
            var sums = $(total).find('.total_sum');
            $.each(sums, function(value, index){
                sum += +$(index).text().replace(/\./g, "");
            });
            sum = sum.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $(order).find('.order_sum').text(sum);

            var sumsAll = $('.order_sum');
            $.each(sumsAll, function(value, index){
                sumTotal += +$(index).text().replace(/\./g, "");
            });
            sumTotal = sumTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $('#order_sum').text(sumTotal + ' руб.');

            var positions = $('div#client_' + client_id).find('span.order_position').length;
            $(order).find('.order_products').text(positions);

            var positionsTotal = 0;
            $.each($('.order_products'), function(value, index){
                positionsTotal += parseInt($(index).text());
            });
            $('#order_products').text(positionsTotal);

            if (isChanged) {
                $('button#send_techn_' + client_id).removeClass('disabled');
                $('button#send_book_' + client_id).removeClass('disabled');
            }

            $('#client_show').val(client_id);
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function getProvidersListsReseller(client_id, isChanged = false){
    var date = $('.datepicker').val();
    var url = "/ajax/providers/listsReseller";
    var provider = ($('#provider').val()) ? $('#provider').val() : 'all';

    $('div.wrapper').show();
    $.ajax({
        url: url,
        type: "GET",
        cache: false,
        dataType: "html",
        data: {'date': date,
            'client_id': client_id,
            'provider': provider
        },
        success: function (html) {
            $('div#client_' + client_id).html('');
            $('div#client_' + client_id).html(html);

            var order = $('tr#order_client_' + client_id);
            var total = $('div#client_' + client_id);
            var quantity = 0;
            var quantityTotal = 0;
            var quantities = $(total).find('.total_quantity');
            $.each(quantities, function(value, index){
                quantity += parseInt($(index).text());
            });
            $(order).find('.order_quantity').text(quantity);

            var orders = $('.order_quantity');
            $.each(orders, function(value, index){
                quantityTotal += parseInt($(index).text());
            });
            $('#order_quantity').text('');
            $('#order_quantity').text(quantityTotal);

            var sum = 0;
            var sumTotal = 0;
            var sums = $(total).find('.total_sum');
            $.each(sums, function(value, index){
                sum += +$(index).text().replace(/\./g, "");
            });
            sum = sum.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $(order).find('.order_sum').text(sum);

            var sumsAll = $('.order_sum');
            $.each(sumsAll, function(value, index){
                sumTotal += +$(index).text().replace(/\./g, "");
            });
            sumTotal = sumTotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $('#order_sum').text(sumTotal + ' руб.');

            var positions = $('div#client_' + client_id).find('span.order_position').length;
            $(order).find('.order_products').text(positions);

            var positionsTotal = 0;
            $.each($('.order_products'), function(value, index){
                positionsTotal += parseInt($(index).text());
            });
            $('#order_products').text(positionsTotal);

            if (isChanged) {
                $('button#send_techn_' + client_id).removeClass('disabled');
                $('button#send_book_' + client_id).removeClass('disabled');
            }

            $('#client_show').val(client_id);
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}


function Timer(){
    //getOrdersTotal();
    getOrdersStatistic();
    getOrdersList();
    getOrdersListReseller();
}

function removeCategory(id) {
    $.ajax({
        url: '/category/delete',
        type: "GET",
        cache: false,
        dataType: "html",
        data: {'id': id},
    });
}

function updateTime(id) {
    $.ajax({
        url: '/category/time',
        type: "POST",
        cache: false,
        dataType: "html",
        data: {'id_value': id, "_token": csrftoken},
    });
}

function updateBotSetting(id) {
    var arr = id.split('-');
    var new_value = arr[2];

    $.ajax({
        url: '/bot/update',
        type: "post",
        cache: false,
        dataType: "html",
        data: {'id_value': id, 'new_value': new_value, "_token": csrftoken},
    });
}

function getCategoriesList(id_category = null){
    $('div.wrapper').show();
    $.ajax({
        url: '/ajax/categories/list',
        type: "GET",
        cache: false,
        dataType: "html",
        success: function (html) {
            $('#catgories_list').html('');
            $('#categories_list').html(html);
            if(id_category) {
                $("tr#cat_" + id_category + '_children').show();
            }
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function changeCatVisibility(id, parent = true) {
    $.ajax({
        url: '/category/visibility',
        type: "POST",
        cache: false,
        data: {'id':id, "_token": csrftoken},
        success: function (data) {
            if (data === 'parent') {
                getCategoriesList();
            }
            else {
                var categoryId = $('input.current_category').val();
                getChildren(categoryId);
            }
        }
    });
}

function getChildren(id) {
    $.ajax({
        url: '/category/children/' + id,
        type: "GET",
        cache: false,
        success: function (html) {
            $('tr#cat_' + id + '_children').html('');
            $('tr#cat_' + id + '_children').html(html);
        }
    });
}

function changePosition(id_current, id_sibling, id_category = null) {
    $('div.wrapper').show();
    $.ajax({
        url: '/category/position',
        type: "GET",
        cache: false,
        data: {'id_current':id_current, 'id_sibling':id_sibling},
        success: function () {
            if(id_category != 'parent') {
                getChildren(id_category);
            }
            else {
                getCategoriesList();
            }
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function getAttributeData(id){
    $.ajax({
        url: '/attributes/value/show/' + id,
        type: "POST",
        cache: false,
        data: {"_token": csrftoken},
        success: function (html) {
            $('div#value_info').html('');
            $('div#value_info').html(html);
        }
    });
}

function getRegions(access) {
    $('#regions_list').addClass('hidden');
    $('#regions_list_new').addClass('hidden');

    $('#keyword').addClass('hidden');
    $('#keyword_new').addClass('hidden');

    if (access == 2) {
        $('#regions_list').removeClass('hidden');
        $('#regions_list_new').removeClass('hidden');
    }
    else if (access == 5) {
        $('#keyword').removeClass('hidden');
        $('#keyword_new').removeClass('hidden');
    }
}

function getUser(user_id){
    $.ajax({
        url: '/users/show/' + user_id,
        type: "POST",
        cache: false,
        data: {'user_id': user_id, "_token": csrftoken},
        success: function (html) {
            $('div#user_info').html('');
            $('div#user_info').html(html);
        }
    });
}

function removeOrder(order_id, client_id) {
    $.ajax({
        url: '/orders/delete',
        type: "POST",
        cache: false,
        data: {'id': order_id,
            "_token": csrftoken
        },
        success: function (html) {
            getProvidersLists(client_id, true);
        },
    });
}

function addOrder(product_id, quantity, client_id, provider) {
    var date = $('.datepicker').val();

    $.ajax({
        url: '/orders/create',
        type: "POST",
        cache: false,
        data: {
            'product_id': product_id,
            'quantity': quantity,
            'client_id': client_id,
            'date': date,
            'provider': provider,
            "_token": csrftoken
        },
        success: function (html) {
            getProvidersLists(client_id, true);
        }
    });
}

function sendOrder(client_id) {
    $.ajax({
        url: '/clients/sendOrder',
        type: "POST",
        cache: false,
        data: {
            'client_id':client_id,
            "_token": csrftoken
        },
        success: function () {
            $('button#send_techn_' + client_id).addClass('disabled');
            $('button#send_book_' + client_id).addClass('disabled');
            $('button#send_techn_' + client_id).removeClass('btn-default');
            $('button#send_techn_' + client_id).addClass('btn-primary');
        }
    });
}