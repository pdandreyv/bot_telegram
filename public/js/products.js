function getProductsList(mode = null){
    url = "/ajax/products/list";

    $('div.wrapper').show();
    $.ajax({
        url: url,
        type: "GET",
        cache: false,
        dataType: "html",
        data: {
            'mode': mode,
        },
        success: function (html) {
            $('#products_list').html('');
            $('#products_list').html(html);
            $('#products_modal').html('');
            $('#products_modal').html(html);
            if (!mode) {
                select_category($('#parent_id').val());
            }
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function getProductsTable(url_default = null, perPage = null, catId){
    var perPage = (perPage) ? perPage : $('select.product-page-num-sel').val();
    var url = '';
    var catId = (catId) ? catId : $('#cat_id').val();
    var cat_id = $('#selected_cat').val();
    var country = $("#selected_country").val();
    var modal = ($('#modal').val());

    if(url_default) {
        url = url_default;
    }
    else {
        url = "/ajax/products/table";
        var page = ($('.pagination li.active span')) ? $('.pagination li.active span').first().text() : 1;
        if (page !== 1) {
            url +='?page=' + page;
        }
    }

    $('div.wrapper').show();
    $.ajax({
        url: url,
        type: "GET",
        cache: false,
        dataType: "html",
        data: {
            'perPage': perPage,
            'catId': catId,
            'category': cat_id,
            'country': country,
            'modal': modal,
        },
        success: function (html) {
            $('tr#category-' + cat_id + '-country-' + country).html('');
            $('tr#category-' + cat_id + '-country-' + country).html(html);
            if (!modal) {
                select_category($('#parent_id').val());
            }
        },
        complete: function() {
            $('div.wrapper').hide();
        }
    });
}

function addProduct(){
    var name = $('#name').val();
    var parent_id = $('#parent_id').val();
    var subcat_id = $('#subcat').val();
    var country = $('#country').val();
    var memory = $('#memory').val();
    var position = $('#position').val();

    $.ajax({
        url: '/products/create',
        type: "POST",
        cache: false,
        data: {'name': name,
            "_token": csrftoken,
            'subcat': subcat_id,
            'parent_id': parent_id,
            'country': country,
            'memory': memory,
            'position': position
        },
        success: function (data) {
            $('#add-product').hide();
            $('#last_product').val(data);
            getDataItem(data);
            $('#edit-product').modal('toggle');
            getProductsTable();
            //$('body').addClass('modal-open');
        }
    });
}

function updateProduct(){
    var product_id = ($('#product').val()) ? $('#product').val() : $('#last_product').val();
    var parent_id = $('#parent_id').val();
    var subcat_id = $('#subcat').val();
    var addition_price = $('#addition_price_new').val();
    var addition_count = $('#addition_count_new').val();
    var one_hand = $('#one_hand_new').val();
    var description = $('#description_new').val();
    var name = $('#name_new').val();
    var country = $('#country_new').val();
    var memory = $('#memory_new').val();

    $.ajax({
        url: '/products/update/' + product_id,
        type: "GET",
        cache: false,
        data: {'product_id': product_id,
                "_token": csrftoken,
                'subcat': subcat_id,
                'parent_id': parent_id,
                'addition_price': addition_price,
                'addition_count': addition_count,
                'one_hand': one_hand,
                'description': description,
                'name': name,
                'country': country,
                'memory': memory,
        },
        success: function () {
            $('#edit-product').modal('toggle');
            //getProductsTable();
            getCountries();
        }
    });
}

function getDataItem(product_id){
    $('.product_remove').val(product_id);
    $.ajax({
        url: '/products/show/' + product_id,
        type: "POST",
        cache: false,
        data: {'product_id': product_id, "_token": csrftoken},
        success: function (html) {
            $('div#product_info').html('');
            $('div#product_info').html(html);
            select_category($('#parent_id').val());
        }
    });
}

function addSerialNumber(){
    var product_id = $('#product').val();
    var code = $('#serial_number').val();

    $.ajax({
        url: '/products/code/create',
        type: "POST",
        cache: false,
        data: {'product_id': product_id, 'code': code, "_token": csrftoken},
        success: function () {
            $('#add-serial-number').modal('toggle');
            getDataItem(product_id);
        }
    });
}

function deleteSerialNumber(code_id){
    $.ajax({
        url: '/products/code/delete/' + code_id,
        type: "POST",
        cache: false,
        data: {"_token": csrftoken},
        success: function () {
            getDataItem($('#product').val());
        }
    });
}

function getChildren(id, currentChild) {
    $.ajax({
        url: '/products/children',
        type: "POST",
        cache: false,
        data: {"_token": csrftoken, 'id': id, 'currentChild': currentChild},
        success: function (html) {
            $('div#children_cats').html('');
            $('div#children_cats').html(html);
        },
    });
}

function getCountries(){
    var cat_id = $('#selected_cat').val();
    $.ajax({
        url: '/products/countries',
        type: "POST",
        cache: false,
        data: {"_token": csrftoken, 'id': cat_id},
        success: function (html) {
            $('tr#countries-' + cat_id).html('');
            $('tr#countries-' + cat_id).html(html);
        },
    });
}

function addStockProduct(product_id) {
    var tr = $('tr#product_' + product_id);
    var quantity = $(tr).find('input.quantity').val();
    var one_hand = $(tr).find('input.one_hand').val();
    var price_usd = $(tr).find('input.price_usd').val();
    var price = $(tr).find('input.price').val();
    var price_opt = $(tr).find('input.price_opt').val();
    var price_middle = $(tr).find('input.price_middle').val();
    var additional_count = $(tr).find('input.additional_count').val();
    var additional_price = $(tr).find('input.additional_price').val();

    $.ajax({
        url: '/products/addToStock',
        type: "POST",
        cache: false,
        data: {
            "_token": csrftoken,
            'product_id': product_id,
            'quantity': quantity,
            'one_hand': one_hand,
            'price_usd': price_usd,
            'price': price,
            'price_opt': price_opt,
            'price_middle': price_middle,
            'additional_price': additional_price,
            'additional_count': additional_count,
        },
        success: function (html) {
            window.location.reload();
        },
        error: function (data) {
            var response = data.responseJSON;
            $('span#error_one_hand_' + product_id).text('');
            if (response['one_hand']) {
                $('span#error_one_hand_' + product_id).text(response['one_hand'][0]);
                $('span#error_one_hand_' + product_id).show();
            }

            $('span#error_quantity_' + product_id).text('');
            if (response['quantity']) {
                $('span#error_quantity_' + product_id).text(response['quantity'][0]);
                $('span#error_quantity_' + product_id).show();
            }

            $('span#error_price_' + product_id).text('');
            if (response['price']) {
                $('span#error_price_' + product_id).text(response['price'][0]);
                $('span#error_price_' + product_id).show();
            }

            $('span#error_price_usd_' + product_id).text('');
            if (response['price_usd']) {
                $('span#error_price_usd_' + product_id).text(response['price_usd'][0]);
                $('span#error_price_usd_' + product_id).show();
            }

            $('span#error_price_opt_' + product_id).text('');
            if (response['price_opt']) {
                $('span#error_price_opt_' + product_id).text(response['price_opt'][0]);
                $('span#error_price_opt_' + product_id).show();
            }

            $('span#error_price_middle_' + product_id).text('');
            if (response['price_middle']) {
                $('span#error_price_middle_' + product_id).text(response['price_middle'][0]);
                $('span#error_price_middle_' + product_id).show();
            }

            $('span#error_additional_count_' + product_id).text('');
            if (response['additional_count']) {
                $('span#error_additional_count_' + product_id).text(response['additional_count'][0]);
                $('span#error_additional_count_' + product_id).show();
            }

            $('span#error_additional_price_' + product_id).text('');
            if (response['additional_price']) {
                $('span#error_additional_price_' + product_id).text(response['additional_price'][0]);
                $('span#error_additional_price_' + product_id).show();
            }
        }
    });
}

