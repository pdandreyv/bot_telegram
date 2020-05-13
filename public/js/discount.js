function addProduct(){
    var name = $('#name').val();
    var parent_id = $('#parent_id').val();
    var category_id = $('#category_id').val();
    var country = $('#country').val();
    var position = $('#position').val();
    var price = $('#price').val();
    var price_opt = $('#price_opt').val();
    var price_middle = $('#price_middle').val();

    $.ajax({
        url: '/products/create',
        type: "POST",
        cache: false,
        data: {'name': name,
            "_token": csrftoken,
            'parent_id': parent_id,
            'country': country,
            'position': position,
            'price': price,
            'price_middle': price_middle,
            'price_opt': price_opt,
            'category_id': category_id,
        },
        success: function (data) {
            $('#add-product').modal('toggle');
            window.location.reload(true);
        }
    });
}

function updateProduct(){
    var product_id = ($('#product').val()) ? $('#product').val() : $('#last_product').val();
    var parent_id = $('#parent_id').val();
    var description = $('#description_new').val();
    var name = $('#name_new').val();
    var country = $('#country_new').val();

    $.ajax({
        url: '/products/update/' + product_id,
        type: "POST",
        cache: false,
        data: {'product_id': product_id,
                "_token": csrftoken,
                'parent_id': parent_id,
                'description': description,
                'name': name,
                'country': country,
        },
        success: function () {
            $('#edit-product').modal('toggle');
            window.location.reload(true);
        }
    });
}

function deleteItem(table,id)
{
    $.ajax({
        url: '/' + table + '/delete',
        type: 'post',
        data: {'id':id,"_token": csrftoken},
        success: function(data){
            $('#remove-product').modal('toggle');
            window.location.reload(true);
        },
        error: function(xhr, textStatus) {
            alert('По неизвестной причине мы не смогли удалить этот объект');
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
        }
    });
}
