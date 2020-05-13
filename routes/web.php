<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



//Auth::routes();

Route::get('/login', 'AuthController@login')
    ->name('loginPage');
Route::post('/login', 'AuthController@loginPost')
    ->name('login');
Route::any('/logout', 'AuthController@logout')
    ->name('logout');
Route::post('/password', 'AuthController@getPassword')
    ->name('getPassword');

Route::group(['middleware' => 'admin'], function () {

    Route::resource('products', 'ProductController');
    Route::post('/products/update', 'ProductController@update_table');
    Route::post('/products/subcat', 'ProductController@subcat');
    Route::post('/products/children', 'ProductController@getChildren');
    Route::get('/products/sort/{id}', 'ProductController@index');
    Route::post('/products/image', 'ProductController@add_image');
    Route::post('/products/create', 'ProductController@create');
    Route::post('/products/update/{id}', 'ProductController@update')->name('products.update');
    Route::post('/products/table_ajax', 'ProductController@table_ajax');
    Route::post('/products/delete', 'ProductController@delete');
    Route::post('/products/code/create', 'ProductController@addSerialNumber');
    Route::post('/products/code/delete/{id}', 'ProductController@deleteSerialNumber');
    Route::post('/products/show/{id}', 'ProductController@show');
    Route::post('/products/countries', 'ProductController@countries');
    Route::post('/products/addToStock', 'ProductController@addToStock');

    Route::get('/users', 'UserController@index')->name('users');
    Route::get('/user/create', 'UserController@create')->name('users.create');
    Route::get('/users/update/{id}', 'UserController@update')->name('users.update');
    Route::get('/users/delete/{id}', 'UserController@delete')->name('users.delete');
    Route::post('/users/update_checkbox', 'UserController@update_checkbox');
    Route::post('/users/show/{id}', 'UserController@show');

    Route::get('/clients', 'ClientController@index')->name('clients');
    Route::get('/client', 'ClientController@getClient')->name('client.get');
    Route::post('/clients/update', 'ClientController@update');
    Route::get('/clients/activity', 'ClientController@changeActivity');
    Route::get('/clients/receipts', 'ClientController@changeReceipts');
    Route::get('/clients/number', 'ClientController@changeNumber');
    Route::post('/client/test_ajax', 'ClientController@search_ajax');
    Route::post('/clients/update_checkbox', 'ClientController@update_checkbox');
    Route::get('/clients/{id}', 'ClientController@destroy')->name('clients.delete');
    Route::post('/clients/activate_all', 'ClientController@activate_all');
    Route::post('/clients/sendOrder', 'ClientController@sendOrder');

    Route::get('/history', 'HistoryController@index')->name('history');
    Route::get('/history/{id}', 'HistoryController@select')->name('history_select');

    Route::get('/', 'OrderController@index')->name('main');
    Route::get('/orders/{id}', 'OrderController@selectRegion')->name('select_region');
    Route::post('/orders/create', 'OrderController@create');
    Route::post('/orders/update', 'OrderController@update');
    Route::post('/orders/delete', 'OrderController@delete');
    Route::get('/ordersList/reseller/{id}', 'OrderController@reseller')->name('orders.reseller');


    Route::get('/category', 'CategoryController@index')->name('category');
    Route::get('/category/create', 'CategoryController@create')->name('category_create');
    Route::post('/categories/update', 'CategoryController@update');
    Route::post('/category/time', 'CategoryController@changeWorkingTime');
    Route::post('/category/visibility', 'CategoryController@changeVisibility');
    Route::get('/category/position', 'CategoryController@changePosition');
    Route::get('/category/children/{id}', 'CategoryController@getChildren');
    Route::get('/category/delete/{id}', 'CategoryController@destroy')->name('category_delete');

    Route::get('bot', 'Bot_settingController@index')->name('bot');
    Route::post('/bot/update', 'Bot_settingController@update_bot_settings');

    Route::get('/attributes', 'AttributesController@index')->name('attributes');
    Route::get('/attributes/value/create', 'AttributesController@create')->name('attributes.create');
    Route::post('/attributes/value/show/{id}', 'AttributesController@valueShow')->name('attributes.show');
    Route::post('/attributes/value/update/{id}', 'AttributesController@update')->name('attributes.update');
    Route::get('/attributes/value/delete', 'AttributesController@delete')->name('attributes.delete');

    Route::get('statistic', 'StatisticController@index')->name('statistic');

    Route::get('nomenclature', 'NomenclatureController@index')->name('nomenclature');
    Route::post('/nomenclature/import', 'NomenclatureController@import');

    Route::get('discount', 'DiscountController@index')->name('discount');
    Route::get('discount/delete/{id}', 'DiscountController@delete')->name('discount.delete');


    Route::get('mailing', 'MailingController@index')->name('mailing');
    Route::post('mailing/update', 'MailingController@update');
    Route::get('/mailing/generate', 'MailingController@generate');
    Route::post('cron/update', 'MailingController@cron_update');
//
    Route::get('mailing', 'MailingController@index')->name('mailing');

    Route::get('receipts', 'ReceiptsController@index')->name('receipts');

    Route::get('percent', 'PercentController@index')->name('percent');
    Route::post('/percents/update_checkbox', 'PercentController@update_checkbox');

//Ajax routes.
    Route::post('savecats', 'AjaxController@saveCats');
    Route::get('getcats', 'AjaxController@getCats');
    Route::get('getRegions', 'UserController@getRegions');
    Route::get('download/stats/{date}/{date2}', 'AjaxController@getDownload');
    Route::get('download/order/{date}', 'AjaxController@getDownloadOrder');

    Route::group(['prefix' => 'ajax'], function () {
        Route::post('/orders/total', 'AjaxController@getOrdersTotal')
            ->name('ajax.orders.total');
        Route::get('/orders/statistic', 'AjaxController@getOrdersStatistic')
            ->name('ajax.orders.statistic');
        Route::get('/orders/list', 'AjaxController@getOrdersList')
            ->name('ajax.orders.list');
        Route::get('/orders/listReseller', 'AjaxController@getOrdersListReseller')
            ->name('ajax.orders.listReseller');
        Route::get('/providers/lists', 'AjaxController@getProvidersLists')
            ->name('ajax.providers.lists');
        Route::get('/providers/listsReseller', 'AjaxController@getProvidersListsReseller')
            ->name('ajax.providers.listsReseller');
        Route::get('/orders/statistic/xls', 'AjaxController@getStatisticXls')
            ->name('ajax.statistic.xls');
        Route::get('/orders/get/xls', 'AjaxController@getOrdersXls')
            ->name('ajax.orders.xls');
        Route::get('/clients/list', 'AjaxController@getClientsList')
            ->name('ajax.clients.list');
        Route::get('/categories/list', 'AjaxController@getCategoriesList')
            ->name('ajax.categories.list');
        Route::get('/products/list', 'AjaxController@getProductsList')
            ->name('ajax.products.list');
        Route::get('/products/table', 'AjaxController@getProductsTable')
            ->name('ajax.products.table');
    });
});

// bot

Route::get('bot_home', 'TelegramController@getHome');
Route::get('get-updates', 'TelegramController@getUpdates');
Route::get('setWebhook', 'TelegramController@setWebhook');
Route::post('send', 'TelegramController@postSendMessage');

Route::get('get-updates-tutorial', 'TelegramController@getUpdatesTutorial');


Route::any('AAHBqVy1lj1H3kNgJdGKltEoOsi1N27t88o/webhook', 'TelegramController@webhook');


Route::get('getMe', 'TelegramController@getMe');
Route::get('removeWebhook', 'TelegramController@removeWebhook');


