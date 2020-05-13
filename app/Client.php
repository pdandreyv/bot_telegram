<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Bot_setting;
use App\MassMessage;
use Carbon\Carbon;
use App\Order;
use App\Message;

class Client extends Model
{
    protected $guarded = ['id'];
    public $text = '';
    public $keyboard = [];
    public $current_step = [];
    public $category;
    public $child_categories;
    public $product;
    public $currentPage;

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public static function getFieldsFromMessage($mess)
    {
        return [
            'first_name' => $mess['first_name'],
            'last_name' => isset($mess['last_name'])?$mess['last_name']:'',
            'username' => isset($mess['username'])?$mess['username']:'',
            'uid' => $mess['id'],
            'country' => isset($mess['language_code'])?$mess['language_code']:'',
            'city' => '',
            'active' => 0,
        ];
    }

    public function isManager()
    {
        $user = User::where('admin_uid', $this->uid)->first();
        return ($user && $user->access === 2) ?  true : false;
    }

    public function isReseller()
    {
        $user = User::where('admin_uid', $this->uid)->first();
        return ($user && $user->access === 5) ?  true : false;
    }

    /* step === 0 - первый шаг, показываем рассылку
     * 1 - сделан первый выбор
     * 2 - Выбрана категория с подкатегориями
     * 3 - Выбрана категория с товарами
     * 4 - Выбрана уценка
     * 5 - Выбрана подкатегория
     * 6 - Выбран товар у категории
     * 7 - Выбран товар уценки
     * 8 - Выбран товар подкатегории
     * 9 - Выбрано количество товара категории
     * 10 - Куплен уцененный товар
     * 11 - Выбрано каличество товара подкатегории
     * 12 - Выбрана кнопка "Обновить" (статистику)
     * 13 - Выбрана iWatch
     * 14 - Выбрана Админка
     * 15 - Выбрана Статистика
     * 16 - Выбрана Обратная Связь у клиента (не админа)
     * 17 - Выбраны Сообщения
     * 18 - Выбрано Сообщение для ответа
     * 19 - Выбраны Пользователи (список городов)
     * 20 - Выбран город (показан список пользователей по городу)
     * 21 - Выбран клиент (панель управления)
     * 22 - Выбрана Обратная связь у админа (для одного клиента)
     * 23 - Выбрана инлайн-кнопка "Добавить". Страница редактирования ФИО влиента.
     * 24 - Выбор опций "Сохранить" или "Далее" для ФИ клиента.
     * 25 - Список опций для выбора опта для клиента.
     * 26 - Выбран опт.
     * 27 - Выбор опций "Сохранить" или "Далее" для города клиента.
     * 28 - Сохранение города клиента.
     * 29 - Выбрана кнопка "Мой баланс"
     * 30 - Выбран продукт уцененки
     * 31 - Выбрана кнопка "Заказы" у перекупщика
     * 32 - Неактивный пользователь без привязки к перекупщику обращается к боту
     */

    public function getStep($mess) 
    {
        if($mess == Bot_setting::getBotText('start') || $mess=='/start' || strtolower($mess)=='start') {
            if($this->active == 0) {
                if ($this->user_id) {
                    $this->text = Bot_setting::getBotText('Сообщение неавторизованному клиенту');
                }
                else {
                    $this->setStep(32);
                    $this->text = Bot_setting::getBotText('Введите ключ');
                }
                $this->keyboard[] = [Bot_setting::getBotText('Start')];
                return $this->sendMessage();
            }
            $this->setStep(1);
            return $this->viewFirstCategories();
        }
        if(!$this->step) {
            if($this->active == 0) {
                if ($this->user_id) {
                    $this->text = Bot_setting::getBotText('Сообщение неавторизованному клиенту');
                }
                else {
                    $this->setStep(32);
                    $this->text = Bot_setting::getBotText('Введите ключ');
                }
                $this->keyboard[] = [Bot_setting::getBotText('Start')];
                return $this->sendMessage();
            }
            $this->setStep(1);
            return $this->viewFirstCategories();
        }
        $this->current_step = unserialize($this->step);

        if (!$this->current_step['step']) {
            if($this->active == 0) {
                if ($this->user_id) {
                    $this->text = Bot_setting::getBotText('Сообщение неавторизованному клиенту');
                }
                else {
                    $this->setStep(32);
                    $this->text = Bot_setting::getBotText('Введите ключ');
                }
                $this->keyboard[] = [Bot_setting::getBotText('Start')];
                return $this->sendMessage();
            }
            $this->setStep(1);
            return $this->viewFirstCategories();
        }
        
        if($mess == Bot_setting::getBotText('Назад')) {
            return $this->doBack();
        }
        
        // Была выбрана: Категория, Уценка, Поступления, Админка, Клиенты, Баланс
        if($this->current_step['step']==1){
            // Выбрана категория
            $this->category = Category::where('name',$mess)->first();
            if($this->category && in_array($this->category->id,explode(',',$this->disable_categories))){
                $this->category = false;
            }
            if($this->category){
                // Выбрана уценка
                /*if($this->category->id == 12){
                    $this->setStep(4);
                    return $this->viewProducts();
                }*/
                // Выбрана категория iWatch (new)
                if($_ENV['HOST']=='new-iphone' && $this->category->id == 9){
                    $this->setStep(13);
                    return $this->viewProducts();
                }
                else {
                    $this->child_categories = Category::getChieldCategories($this->category->id);
                    // Выбрана категория с подкатегориями
                    if($this->child_categories->count()){
                        $discountIds = Category::where('id', config('discount.discount_category_id'))
                            ->orWhere('parent_id', config('discount.discount_category_id'))
                            ->pluck('id')
                            ->toArray();

                        if (!in_array($this->category->id, $discountIds)) {
                            $this->setStep(2);
                        }
                        else {
                            $this->setStep(4);
                        }

                        return $this->viewCategories();
                    }
                    // Выбрана категория с товарами
                    else {
                        $this->setStep(3);
                        return $this->viewProducts();
                    }
                }
            }
            // Выбраны поступления
            elseif($this->showReceipts == 1 && $mess == Bot_setting::getBotText('Поступления')) {
                //$this->sendMessage(Bot_setting::getBotText('Поступления.'));
                $this->text = Bot_setting::getBotText('Поступления.');
                return $this->viewFirstCategories();
            }
            // Выбрана Админка
            elseif (($this->admin==1 || $this->isReseller()) && $mess == Bot_setting::getBotText('Админка')) {
                $this->setStep(14);
                return $this->viewAdmin();
            }
            elseif ($this->isManager() && $mess == Bot_setting::getBotText('Клиенты')) {
                $this->current_step['currentPage'] = 1;
                return $this->viewUsersList($this->current_step['currentPage']);
            }
            //Выбран баланс
            elseif ($mess == Bot_setting::getBotText('Баланс')) {
                return $this->viewUserBalance();
            }
            // Выбрана Обратная связь
            elseif ($mess == Bot_setting::getBotText('Обратная связь')) {
                $this->setStep(16);
                $this->viewClientMessages();
                return $this->sendMessage();
            }
            else {
                return $this->viewFirstCategories();
            }

        }
        // Выбрана подкатегория
        elseif($this->current_step['step']==2){
            $this->category = Category::where('name',$mess)->first();
            if($this->category){
                // Выбрана категория iWatch
                if($this->category->id == 11 || $this->category->id == 19 || $this->category->id == 22 || ($_ENV['HOST']=='new-iphone' && $this->category->id == 9)){
                    $this->setStep(13);
                    return $this->viewProducts();
                }
                else {
                    $this->setStep(5);
                    return $this->viewProducts();
                }
            }
        }

        // Выбран товар категории или уценки или iWatch
        elseif($this->current_step['step']==3 || $this->current_step['step']==13) {
            if($this->findProduct($mess)){
                // Выбран товар категории
                if($this->current_step['step']==3) {
                    $this->setStep(6);
                    return $this->viewCount();
                }
                // Выбран товар iWatch
                elseif($this->current_step['step']==13) {
                    $this->setStep(6);
                    return $this->viewPhoto(true);
                }
            }
        }
        elseif ($this->current_step['step'] == 4) {
            $this->category = Category::where('name',$mess)
                ->where('parent_id', config('discount.discount_category_id'))
                ->first();
            if ($this->category) {
                $this->setStep(30);
                return $this->viewProducts();
            }
        }
        // Выбран товар подкатегории
        elseif($this->current_step['step']==5){
            if($this->findProduct($mess)){
                $this->setStep(8);
                return $this->viewCount();
            }
        }
        // Выбрано количество товара категории
        elseif($this->current_step['step']==6){
            //$this->setStep(9);
            return $this->viewOrder($mess);
        }
        elseif ($this->current_step['step'] == 30) {
            if ($this->findProduct($mess)) {
                $this->setStep(7);
                return $this->viewPhoto();
            }
        }
        // Куплен уцененный товар
        elseif($this->current_step['step']==7){
            //$this->setStep(10);
            return $this->viewOrder(1);
        }
        // Выбрано количество товара подкатегории
        elseif($this->current_step['step']==8){
            //$this->setStep(11);
            return $this->viewOrder($mess);
        }
        // Выбрана Админка
        elseif (($this->admin==1 || $this->isReseller()) && $this->current_step['step']==14) {
            // Выбрана статистика или ее обновление (только для админов)
            if ($mess == Bot_setting::getBotText('Статистика') || $mess == Bot_setting::getBotText('Обновить')) {
                $this->setStep(15);
                return $this->viewStatistic();
            } 
            elseif ($mess == Bot_setting::getBotText('Сообщения')){
                $this->setStep(17);
                return $this->viewAllMessages();
            }
            elseif ($mess == Bot_setting::getBotText('Пользователи') || $mess == Bot_setting::getBotText('Клиенты')){
                $this->setStep(19);
                return $this->viewCitiesList();
            }
            elseif ($mess == Bot_setting::getBotText('Заказы')){
                $this->setStep(31);
                return $this->viewResellerOrders();
            }
        }
        // Выбрана Статистика
        elseif (($this->admin == 1 || $this->isReseller()) && $this->current_step['step']==15) {
            return $this->viewStatistic();
        }
        // Отправлено сообщение из обратной связи
        elseif($this->current_step['step']==16){
            return $this->saveMessage($mess,0);
        }
        // Выбрано сообщение пользователя
        elseif($this->current_step['step']==17){
            // Шаг записываем внутри функции, там передаем id 
            return $this->viewUserMessages($mess);
        }
        elseif($this->current_step['step']==18){
            $finish = false;
            if ($mess == "Завершить") {
                $finish = true;
            }
            return $this->saveMessage($mess,1, $finish);
        }
        elseif($this->current_step['step']==19){
            $this->setStep(20);
            $this->current_step['city'] = $mess;
            $this->current_step['currentPage'] = 1;
            return $this->viewUsersList($this->current_step['currentPage']);
        }
        // Выбраны пользователи (список)
        elseif (($this->admin==1 || $this->isManager() || $this->isReseller()) && $this->current_step['step']==20) {
            if (strpos($mess, '(') !== false) {
                $pos1 = mb_strpos($mess,'(');
                $pos2 = mb_strpos($mess,')');
                $this->current_step['client_id'] = mb_substr($mess, $pos1 + 1, $pos2 - $pos1 - 1);
                $this->setStep(21);
                return $this->viewUser($this->current_step['client_id']);
            }
            elseif ($mess == "След." || $mess == "Пред." || intval($mess)) {
                return $this->viewUsersList($mess);
            }

            $this->current_step['city'] = null;
            return $this->searchUser($mess);
        }
        // Выбрано меню клиента
        elseif (($this->admin==1 || $this->isManager() || $this->isReseller()) && $this->current_step['step']==21) {
            if(strpos($mess, Bot_setting::getBotText('Отключить')) !== false) {
                return $this->disableUser($this->current_step['client_id']);
            }
            elseif(strpos($mess, Bot_setting::getBotText('Включить')) !== false) {
                return $this->enableUser($this->current_step['client_id']);
            }
            elseif(strpos($mess, Bot_setting::getBotText('История')) !== false) {
                return $this->getUserHistory($this->current_step['client_id']);
            }
            elseif(strpos($mess, Bot_setting::getBotText('Обратная связь')) !== false) {
                return $this->getUserMessages($this->current_step['client_id']);
            }
        }
        elseif(($this->admin==1 || $this->isReseller()) && $this->current_step['step'] == 22) {
            return $this->saveMessage($mess,1);
        }
        elseif($this->admin==1 && $this->current_step['step'] == 23) {
            if($mess === Bot_setting::getBotText('Далее')) {
                $this->setStep(25);
                return $this->chooseClientWholesale();
            }

            $this->current_step['edition_client_id'] = $mess;
            $this->setStep(24);
            return $this->editClientName($mess);
        }
        elseif($this->admin==1 && $this->current_step['step'] == 24) {
            if($mess === Bot_setting::getBotText('Далее')) {
                $this->setStep(25);
                return $this->chooseClientWholesale();
            }
            else {
                return $this->saveClientName($mess);
            }
        }
        elseif($this->admin==1 && $this->current_step['step'] == 25) {
            return $this->chooseClientWholesale();
        }
        elseif($this->admin==1 && $this->current_step['step'] == 26) {
            $this->setStep(27);
            return $this->saveClientWholesale($mess);
        }
        elseif($this->admin==1 && $this->current_step['step'] == 27) {
            return $this->editClientCity();
        }
        elseif($this->admin==1 && $this->current_step['step'] == 28) {
            if($mess === Bot_setting::getBotText('Далее')) {
                $this->setStep(1);
                return $this->viewFirstCategories();
            }
            else {
                return $this->saveClientCity($mess);
            }
        }
        elseif ($this->current_step['step'] == 32) {
            $user = User::where('keyword', $mess)->first();
            if ($user && $user->access == 5) {
                $keyboard[] = ["Добавить", "add_user=" . $this->id];
                /*$admins = self::where('admin', 1)->get();
                foreach($admins as $admin) {
                    Telegram::sendMessage([
                        'chat_id' => $admin->uid,
                        'text' => 'Пользователь ' . $this->first_name . ' отправил запрос на добавление.',
                        'reply_markup' => $this->replyMarkup($keyboard, true),
                    ]);
                }*/

                $this->update(['user_id' => $user->id]);
                $this->text = Bot_setting::getBotText('Ключ верный');
                $this->keyboard[] = [Bot_setting::getBotText('Start')];
            } else {
                $this->text = Bot_setting::getBotText('Ключ неверный');
                $this->keyboard[] = [Bot_setting::getBotText('Start')];
            }

            return $this->sendMessage();
        }
        
        return $this->sendMessage(Bot_setting::getBotText('Сделайте Ваш выбор'));
    }
    
    public function setStep($step=0)
    {
        if($step == 'back'){
            // Удаляем послешний шаг из истории беря предпоследний за текущий
            array_pop($this->current_step['history']);
            $step = $this->current_step['step'] = end($this->current_step['history']);
        }
        if(isset($this->current_step['history']) 
           && is_array($this->current_step['history']) 
           && $this->current_step['step'] != $step)
        {
            $this->current_step['history'][] = $step;
        }
        if(!isset($this->current_step['history']) || !is_array($this->current_step['history'])) {
            $this->current_step['history'] = [0];
        }
        if($this->category) $this->current_step['category_id'] = $this->category->id;
        if($this->product) $this->current_step['product_id'] = $this->product->id;
        
        $this->current_step['step'] = $step;
        Log::info('step: '.print_r($this->current_step,1));
            $this->step = serialize($this->current_step);
            $this->save();
        }
    
    // Отобразить первый уровень категорий
    public function viewFirstCategories()
    {
        $this->current_step['history'] = 0;
        $this->setStep(1);
        
        if(!$this->text)
            $this->text = Bot_setting::getBotText('Выбор категории');
            
        // Составляем список категорий
        $categories = Category::getFirstCategories();
        if($categories->count()) {
            foreach($categories as $cat){
                if($cat->start_date <= date('H:i')) {
                    if (!in_array($cat->id, explode(',', $this->disable_categories))) {
                        $this->keyboard[] = [$cat->name];
                    }
                }
            }
        }
        if($this->showReceipts == 1 && isset($_ENV['HOST']) && $_ENV['HOST']=='grand-cms.ru')
            $this->keyboard[] = [Bot_setting::getBotText('Поступления')];

        if ($this->admin==1 || $this->isReseller()) {
            $this->keyboard[] = [Bot_setting::getBotText('Админка')];
        }
        elseif ($this->isManager()) {
            $this->keyboard[] = [Bot_setting::getBotText('Клиенты')];
            $this->keyboard[] = [Bot_setting::getBotText('Обратная связь')];
        }
        else {
            $this->keyboard[] = [Bot_setting::getBotText('Обратная связь')];
        }

        if ($this->payment_type_id) {
            if ($this->paymentType->code == 'by_prepayment') {
                $this->keyboard[] = [Bot_setting::getBotText('Баланс')];
            }
        }

        return $this->sendMessage();
    }
    
    public function viewAdmin()
    {
        $this->text = Bot_setting::getBotText('Панель админа');
        $this->keyboard[] = [Bot_setting::getBotText('Статистика')];

        if ($this->admin == 1) {
            $this->keyboard[] = [Bot_setting::getBotText('Сообщения')];
            $this->keyboard[] = [Bot_setting::getBotText('Пользователи')];
        }
        elseif ($this->isReseller()) {
            $this->keyboard[] = [Bot_setting::getBotText('Сообщения')];
            $this->keyboard[] = [Bot_setting::getBotText('Клиенты')];
            $this->keyboard[] = [Bot_setting::getBotText('Заказы')];
        }
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function viewUserBalance()
    {
        $client = self::find($this->id);
        if ($client->payment_type_id) {
            if ($client->paymentType->code == 'by_prepayment') {
                $balance = $client->current_amount;
            }
            elseif ($client->paymentType->code == 'in_fact') {
                $ordersSum = Order::where('client_id', $this->id)
                    ->pluck('total')
                    ->sum();
                if (($this->max_amount - $ordersSum) < 0) {
                    $balance = 0;
                }
                else {
                    $balance = $this->max_amount - $ordersSum;
                }
            }

            $this->text = Bot_setting::getBotText('Баланс клиента', [
                'balance' => number_format($balance,0,".",".")
            ]);
        }
        else {
            $this->text = Bot_setting::getBotText('Сообщение пользователю без типа оплаты');
        }

        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function viewResellerOrders()
    {
        $resellerId = $this->user->id;
        $orders = Order::whereHas('client', function($query) use ($resellerId) {
            $query->where('user_id', $resellerId);
        })->get();

        if ($orders->count()) {
            $this->text = '';
            $i = 1;
            $ordersList = '';

            foreach ($orders as $one) {
                $nameFlag = '';
                if ($one->product->country && Attribute_value::where('value', $one->product->country)->first()) {
                    $nameFlag = Attribute_value::where('value', $one->product->country)->first()->additional_data;
                }

                $ordersList .= $i . '. ' . $nameFlag . ' ' . $one->product->name . ': ' . $one->quantity . ' шт. * ' . $one->price . ' руб. - ' . $one->total . ' руб. ' . "\n\n";
                $i++;
            }

            if($ordersList !== '') {
                $this->text .= '' //Bot_setting::getBotText('Обшая информация', ['bot_name' => 'Booking', 'name' => $this->first_name . ' ' . $this->last_name]) . "\n\n"
                    . Bot_setting::getBotText('Итоговое сообщение', [
                        'product_list' => $ordersList, 'total' => $orders->sum('total')
                    ]) . "\n\n";
            }
        }
        else {
            $this->text = Bot_setting::getBotText('На сегодня заказов нет');
        }

        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function editClientName($id)
    {
        $client = self::findOrFail($id);
        $this->text = Bot_setting::getBotText('Редактирование имени клиента', [
            'first_name' => $client->first_name,
            'last_name' => $client->last_name
        ]);
        $this->keyboard[] = [Bot_setting::getBotText('Назад'), Bot_setting::getBotText('Далее')];

        //$this->current_step['edition_client_id'] = $id;
        //$this->setStep(23);

        return $this->sendMessage();
    }

    public function saveClientName($name)
    {
        $data = explode(' ', $name);
        $client = self::findOrFail($this->current_step['edition_client_id']);
        $client->update(['first_name' => $data[0]]);
        if (isset($data[1])) {
            $client->update(['last_name' => $data[1]]);
        }

        $this->text = Bot_setting::getBotText('ФИО клиента', [
            'first_name' => $data[0],
            'last_name' => $data[1],
        ]);
        $this->keyboard[] = [Bot_setting::getBotText('Назад'), Bot_setting::getBotText('Далее')];

        return $this->sendMessage();
    }

    public function chooseClientWholesale()
    {
        $client = self::findOrFail($this->current_step['edition_client_id']);

        $this->text = Bot_setting::getBotText('ФИО клиента', [
            'first_name' => $client->first_name,
            'last_name' => $client->last_name
        ]);
        $this->keyboard[] = [Bot_setting::getBotText('Крупный опт')];
        $this->keyboard[] = [Bot_setting::getBotText('Мелкий опт')];
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        $this->setStep(27);

        return $this->sendMessage();
    }

    public function saveClientWholesale($mess)
    {
        $client = self::where('id', $this->current_step['edition_client_id']);
        if ($mess == Bot_setting::getBotText('Крупный опт')) {
            $client->update(['type' => 1]);
        }
        elseif ($mess == Bot_setting::getBotText('Мелкий опт')) {
            $client->update(['type' => 0]);
        }

        return $this->editClientCity();
    }

    public function editClientCity()
    {
        $client = self::find($this->current_step['edition_client_id']);
        $this->text = Bot_setting::getBotText('Редактирование города клиента', [
            'city' => $client->city,
        ]);
        $this->keyboard[] = [Bot_setting::getBotText('Назад'), Bot_setting::getBotText('Далее')];
        $this->setStep(28);

        return $this->sendMessage();
    }

    public function saveClientCity($city)
    {
        $client = self::findOrFail($this->current_step['edition_client_id']);
        $client->update(['city' => trim($city)]);
        $this->setStep(1);

        $this->text = Bot_setting::getBotText('Город клиента сохранен', ['city' => $city]);
        $this->sendMessage();
        $this->text = null;

        return $this->viewFirstCategories();
    }

    public function viewCitiesList()
    {
        $this->text = 'Выберите город.';

        if ($this->admin == 1) {
            $cities = self::groupBy('city')->orderBy('city')->pluck('city');
        }
        elseif ($this->isReseller()) {
            $cities = self::where('user_id', $this->user->id)
                ->groupBy('city')
                ->orderBy('city')
                ->pluck('city');
        }

        foreach ($cities as $city) {
            $this->keyboard[] = [$city];
        }

        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function viewUsersList($data)
    {
        $this->text = Bot_setting::getBotText('Список клиентов');
        if (isset($this->current_step['city']) && $this->current_step['city'] != null) {
            $clients = self::where('city', $this->current_step['city']);

            if ($this->isReseller()) {
                $clients = $clients->where('user_id', $this->user->id);
            }

            $clients = $clients->paginate(10);
        }

        if($this->isManager()) {
            $user_regions = User::where('admin_uid', $this->uid)->first()->regions;
            $regionsArr = explode(',', $user_regions);

            if(count($regionsArr) > 0 && $regionsArr[0] != '') {
                $clients = self::whereIn('city', $regionsArr)->paginate(10);
            }
            else {
                $this->text = Bot_setting::getBotText('Клиентов не найдено');
                $this->keyboard[] = [Bot_setting::getBotText('Назад')];

                return $this->sendMessage();
            }
        }

        $lastPage = $clients->lastPage();

        if ($data == "След.") {
            if ($this->current_step['currentPage'] != $lastPage) {
                $this->current_step['currentPage']++;
                $this->setStep(20);
            }
        }
        elseif($data == "Пред.") {
            if ($this->current_step['currentPage'] != 1) {
                $this->current_step['currentPage']--;
                $this->setStep(20);
            }
        }
        elseif(intval($data) && (intval($data) <= $lastPage)) {
            $this->current_step['currentPage'] = intval($data);
            $this->setStep(20);
        }

        if (isset($this->current_step['city']) && $this->current_step['city'] != null) {
            $clientsPage = DB::table('clients')
                ->where('city', $this->current_step['city']);

            if ($this->isReseller()) {
                $clientsPage = $clientsPage->where('user_id', $this->user->id);
            }

            $clientsPage = $clientsPage->skip(($this->current_step['currentPage'] - 1) * $clients->perPage())
                ->take(10)
                ->get();
        }
        elseif($this->isManager()) {
            $user_regions = User::where('admin_uid', $this->uid)->first()->regions;
            $regionsArr = explode(',', $user_regions);

            if(count($regionsArr) > 0 && $regionsArr[0] != '') {
                $clientsPage = DB::table('clients')
                    ->whereIn('city', $regionsArr)
                    ->skip(($this->current_step['currentPage'] - 1) * $clients->perPage())
                    ->take(10)
                    ->get();
            }
            else {
                $this->text = Bot_setting::getBotText('Клиентов не найдено');
                $this->keyboard[] = [Bot_setting::getBotText('Назад')];

                return $this->sendMessage();
            }
        }
        else {
            $clientsPage = DB::table('clients')
                ->skip(($this->current_step['currentPage'] - 1) * $clients->perPage())
                ->take(10)
                ->get();
        }

        foreach ($clientsPage as $client) {
            $active = ($client->active) ? '+ ' : '- ';
            $this->keyboard[] = [Bot_setting::getBotText('Список пользователей', ['user_data' => $active . $client->first_name . ' ' . $client->last_name . ' ' . $client->username . " ($client->id)"])];
            //$this->keyboard[] = [$active . $client->first_name . ' ' . $client->last_name . ' ' . $client->username . " ($client->id)"];
        }

        if ($this->current_step['currentPage'] == 1) {
            if ($lastPage == 2) {
                $this->keyboard[] = ["1", "2"];
            }
            elseif ($lastPage != 1){
                $this->keyboard[] = ["1", "След.", strval($lastPage)];
            }
        }
        elseif($this->current_step['currentPage'] == $lastPage) {
            if ($lastPage == 2) {
                $this->keyboard[] = ["1", "2"];
            }
            else {
                $this->keyboard[] = ["1", "Пред.", strval($lastPage)];
            }
        }
        else {
            $this->keyboard[] = ["1", "Пред.", strval($this->current_step['currentPage']), "След.", strval($lastPage)];
        }

        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function viewUser($data)
    {
        $client = self::find($data);
        $this->text = Bot_setting::getBotText('Панель управления клиентом');
        if($client->active) {
            $this->keyboard[] = [Bot_setting::getBotText('Отключить')];
        }
        else {
            $this->keyboard[] = [Bot_setting::getBotText('Включить')];
        }

        if($this->admin) {
            $this->keyboard[] = [Bot_setting::getBotText('История')];
            $this->keyboard[] = [Bot_setting::getBotText('Обратная связь')];
        }
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function disableUser($id)
    {
        $client = self::findOrFail($id);
        $client->update(['active' => 0]);

        $this->text = Bot_setting::getBotText('Пользователь отключен');
        if($client->active) {
            $this->keyboard[] = [Bot_setting::getBotText('Отключить')];
        }
        else {
            $this->keyboard[] = [Bot_setting::getBotText('Включить')];
        }

        if($this->admin) {
            $this->keyboard[] = [Bot_setting::getBotText('История')];
            $this->keyboard[] = [Bot_setting::getBotText('Обратная связь')];
        }
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function enableUser($id)
    {
        $client = self::findOrFail($id);
        $client->update(['active' => 1]);

        $this->text = Bot_setting::getBotText('Пользователь включен');
        if($client->active) {
            $this->keyboard[] = [Bot_setting::getBotText('Отключить')];
        }
        else {
            $this->keyboard[] = [Bot_setting::getBotText('Включить')];
        }

        if($this->admin) {
            $this->keyboard[] = [Bot_setting::getBotText('История')];
            $this->keyboard[] = [Bot_setting::getBotText('Обратная связь')];
        }
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function getUserMessages($id)
    {
        $this->current_step['answer_client_id'] = $id;
        $this->setStep(22);

        $messages1 = Message::where('client_id', $id)
            ->orWhere('answer_client_id', $id)
            ->latest()
            ->take(10)
            ->get();

        $messages = $messages1->reverse();
        $messages->all();

        $this->text = '';

        if ($messages->count() > 0) {
            $br = false;
            foreach ($messages as $message) {
                if ($message->answer_client_id) {
                    $this->text .= Bot_setting::getBotText('Aдмин: ');
                    $br_flag = true;
                } else {
                    if ($br) {
                        $this->text .= "\n";
                        $br = false;
                    }
                    if ($message->client->username) {
                        $this->text .= $message->client->username . ': ';
                    } else {
                        $this->text .= $message->client->first_name . ': ';
                    }
                }
                $this->text .= $message->message . "\n";
            }
        }
        else {
            $this->text = 'Нет сообщений.';
        }

        $this->text .= "\n\n" . "Введите сообщение для клиента: ";
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function getUserHistory($id)
    {
        $this->current_step['answer_client_id'] = $id;
        $this->setStep(22);
        $client = self::find($id);

        $history = History::where('client_id', $id)
            ->get();

        $this->text = '';

        if ($history->count() > 0) {
            foreach ($history as $one) {
                if ($one->text) {
                    $this->text .= $client->first_name . ': '. "\n" . $one->created_at . "\n" . $one->text . "\n\n";
                }
                elseif ($one->bot_text) {
                    $this->text .= "Бот: " . "\n" . $one->created_at . "\n" .  $one->bot_text . "\n\n";
                }
            }
        }
        else {
            $this->text = 'Нет истории.';
        }

        $length = strlen($this->text);
        $this->text = substr($this->text, $length - 3999, $length);

        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }

    public function searchUser($name)
    {
        $this->text = Bot_setting::getBotText('Список клиентов');

        $clients = DB::table('clients')
            ->where('first_name', 'like', "%$name%")
            ->orWhere('last_name', 'like', "%$name%")
            ->orWhere('username', 'like', "%$name%")
            ->get();

        if($clients->count() > 0) {
            foreach ($clients as $client) {
                $active = ($client->active) ? '+ ' : '- ';
                $this->keyboard[] = [$active . $client->first_name . ' ' . $client->last_name . ' ' . $client->username . " ($client->id)"];
            }
        }

        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }
    
    public function viewClientMessages()
    {
        $messagesReverse = Message::where('client_id', $this->id)
            ->orWhere('answer_client_id', $this->id)
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get();

        $messages = $messagesReverse->reverse();
        $messages->all();

        $br = false;
        foreach($messages as $m){
            if($m->answer_client_id){
                $this->text .= Bot_setting::getBotText('Aдмин: ');
                $br_flag = true;
            }
            else {
                if($br) {
                    $this->text .= "\n";
                    $br = false;
                }
                $this->text .= Bot_setting::getBotText('Я: ');
            }
            $this->text .= $m->message."\n";
        }
        $this->text .= "\n".Bot_setting::getBotText('Введите сообщение для админа');
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];
    }

    public function saveMessage($mess, $answer, $finish = false)
    {
        if($answer && $this->current_step['answer_client_id']){
            $client = Client::find($this->current_step['answer_client_id']);
            if ($finish) {
                Message::where('client_id', $this->current_step['answer_client_id'])->update(['status' => 1]);
                $this->text = 'Диалог завершен';
                $this->keyboard[] = [Bot_setting::getBotText('Назад')];

                return $this->sendMessage();
            }
            else {
                $message_new = new Message([
                    'client_id' => $this->id,
                    'answer_client_id' => $this->current_step['answer_client_id'],
                    'status' => 1,
                    'answer' => 1,
                    'message' => $mess
                ]);

                $keyboard[] = ["Ответить", "answer_client=" . $this->id];
                Telegram::sendMessage([
                    'chat_id' => $client->uid,
                    'text' => "Ответ от Админа:\n" . $mess,
                    'reply_markup' => $this->replyMarkup($keyboard, true),
                ]);
            }
            //$this->sendMessage("Ответ от Админа:\n".$mess, $client->uid);
        } else {
            $message_new = new Message([
                'client_id'=>$this->id,
                'status'=>0,
                'answer'=>0,
                'message'=>$mess
            ]);

            $keyboard[] = ["Ответить", "answer_admin=" . $this->id];
            if ($this->user_id == null) {
                $admins = Client::where('admin', 1)->get();
                if ($admins->count() > 0) {
                    foreach ($admins as $a) {
                        Telegram::sendMessage([
                            'chat_id' => $a->uid,
                            'text' => "Вопрос от {$this->first_name} {$this->last_name} {$this->username}:\n" . $mess,
                            'reply_markup' => $this->replyMarkup($keyboard, true),
                        ]);
                    }
                }
            }
            else {
                $admin = User::find($this->user_id);
                if ($admin && $admin->access == 5) {
                    Telegram::sendMessage([
                        'chat_id' => $admin->admin_uid,
                        'text' => "Вопрос от {$this->first_name} {$this->last_name} {$this->username}:\n" . $mess,
                        'reply_markup' => $this->replyMarkup($keyboard, true),
                    ]);
                }
            }
        }

        $message_new->save();
        
        return false;
    }

    public function viewAllMessages()
    {
        $messages = DB::table('messages')
            ->leftJoin('clients', 'clients.id', '=', 'messages.client_id')
            ->where('messages.status', '=', 0);

        if ($this->admin == 1) {
            $messages = $messages->where('clients.user_id', '=', null);
        }
        elseif ($this->isReseller()) {
            $messages = $messages->where('clients.user_id', '=', $this->user->id);
        }

        $messages = $messages->groupBy('messages.client_id')
            ->limit(20)
            ->get();

        foreach($messages as $mes) {
            $this->keyboard[] = [$mes->first_name."(".$mes->client_id."): ".mb_substr($mes->message, 0, 15)];
        }
        $this->text = Bot_setting::getBotText('Не отвеченные сообщения');
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        
        return $this->sendMessage();
    }

    public function viewUserMessages($mess=0)
    {
        $pos1 = mb_strpos($mess,'(');
        $pos2 = mb_strpos($mess,')');
        $client_id = mb_substr($mess,$pos1+1,$pos2-$pos1-1);
        $this->current_step['answer_client_id'] = $client_id;
        $this->setStep(18);
        
        if($client_id){
            $messages = DB::table('messages as m')
                ->select(DB::raw('m.client_id,m.answer_client_id,m.message,c1.first_name fn1,c2.first_name fn2,c1.last_name ln1,c2.last_name ln2,c1.username un1,c2.username un2'))
                ->leftJoin('clients as c1', 'c1.id', '=', 'm.client_id')
                ->leftJoin('clients as c2', 'c2.id', '=', 'm.answer_client_id')
                ->orderBy('m.id', 'DESC')
                ->where('m.client_id', '=', $client_id)
                ->orWhere('m.answer_client_id',$client_id)
                ->limit(10)
                ->get()->toArray();
            
            krsort($messages,SORT_NUMERIC);
            reset($messages);
            $br = false;
            foreach($messages as $m){
                if($m->answer_client_id){
                    $this->text .= 'Aдминистратор '.$m->fn2.": \n";
                    $br = true;
                }
                else {
                    if($br) {
                        $this->text .= "\n";
                        $br = false;
                    }
                    $this->text .= $m->fn1." ".$m->ln1." ".$m->un1.": \n";
                }
                $this->text .= $m->message."\n";
            }
            $this->text .= "\n".Bot_setting::getBotText('Напишите ответ пользователю');
        } 
        else {
            $this->text = Bot_setting::getBotText('Нет id этого клиента');
        }
        $this->keyboard[] = ["Завершить"];
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        return $this->sendMessage();
    }
    
    public function viewCategories()
    {
        foreach($this->child_categories as $cat){ 
            $this->keyboard[] = [$cat->name];
        }
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        $this->text = Bot_setting::getBotText('Выбор подкатегории');
        
        return $this->sendMessage();
    }

    public function viewStatistic()
    {
        $this->keyboard[] = [Bot_setting::getBotText('Обновить')];
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];

        if ($this->admin == 1) {
            $user_id = null;
            $this->text = Bot_setting::getBotText('Статистика товаров для админа', Order::ordersTotalStatistic($user_id));
        }
        elseif ($this->isReseller()) {
            $user_id = $this->user->id;
            $this->text = Bot_setting::getBotText('Статистика товаров для перекупщика', Order::ordersTotalStatistic($user_id));
        }

        return $this->sendMessage();
    }
    
    public function viewProducts($client=false,$category_id=false)
    {
        if (!$client) $client = $this;
        if (!$category_id) $category_id = $this->category->id;

        $products = Product::where([['category_id','=',$category_id],['quantity','>',0]]);
        $prod_ids = DB::table('one_hands')
            ->leftJoin('products', 'products.id', '=', 'one_hands.product_id')
            ->where('products.deleted_at', '=', null)
            ->select('one_hands.product_id')
            ->where([['one_hands.client_id', $client->id], ['one_hands.counts','>=', DB::raw('products.one_hand')]])
            ->get();
        
        if(!empty($prod_ids)) {
            $where_in = [];
            foreach($prod_ids as $p){
                $where_in[] = $p->product_id;
            }
            $products->whereNotIn('id', $where_in);
        }
        $prods = $products->orderBy('position', 'ASC')->get();

        if($prods->count()) {
            foreach($prods as $prod){
                $keyboard[] = [$this->getViewProduct($prod, $client)];
            }
            $keyboard[] = [Bot_setting::getBotText('Назад')];
            $this->text = Bot_setting::getBotText('Выбор модели');
            return $this->sendMessage(false, $client->uid, $keyboard, $client->id);
        }
        $this->text = Bot_setting::getBotText('Нет товаров в этой категории');
        $keyboard[] = [Bot_setting::getBotText('Назад')];
        return $this->sendMessage(false, $client->uid, $keyboard, $client->id);
    }
    
    public function viewCount()
    {
        $this->text = Bot_setting::getBotText('Вопрос кол-ва товара');
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        return $this->sendMessage();
    }
    
    public function viewPhoto($iwatch=false)
    {
        $file = base_path().'/public/images/'.$this->product->id.'.jpg';
        if(is_file($file)){
            $response = Telegram::sendPhoto([
                'chat_id' => $this->uid,
                'photo'   => $file,
            ]);
        }
        $this->text = $this->product->description;
        if($iwatch) {
            $this->sendMessage();
            $this->text = Bot_setting::getBotText('Вопрос кол-ва товара');
        } else {
            $this->keyboard[] = [Bot_setting::getBotText('Купить')];
        }
        
        $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        return $this->sendMessage();
    }

    public function viewOrder($mess)
    {
        if(isset($this->current_step['product_id']))
            $product = Product::find($this->current_step['product_id']);
        else
            return $this->viewFirstCategories();

        if(is_numeric($mess)) {
            $one_hand = OneHand::where([['client_id',$this->id],['product_id',$product->id]])->first();
            if(!$one_hand){
                $one_hand = new OneHand([
                    'client_id' => $this->id, 
                    'product_id' => $product->id, 
                    'counts' => 0
                ]);
            }
            $one_hand_count = $one_hand->counts + $mess;
        }

        if(!is_numeric($mess) || $mess <= 0){
            $this->text = Bot_setting::getBotText('Ошибка, ввели не число');
            $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        } 
        elseif($one_hand_count > $product->one_hand && $product->quantity < $product->one_hand){
            $this->text = Bot_setting::getBotText('При ошибке нет такого кол-ва',['quantity'=>$product->quantity]);
            $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        }
        elseif($one_hand_count > $product->one_hand){
            /*if($mess > $product->quantity) {
                $this->text = Bot_setting::getBotText('При ошибке нет такого кол-ва',['quantity'=>$product->quantity]);
            } else {*/
                $this->text = Bot_setting::getBotText('Ошибка, выход за пределы кол-ва',['max_count' => $product->one_hand - $one_hand->counts]);
            //}
            $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        }
        elseif($mess > $product->quantity) {
            $this->text = Bot_setting::getBotText('При ошибке нет такого кол-ва',['quantity'=>$product->quantity]);
            $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        }
        elseif (!$this->payment_type_id){
            $this->text = Bot_setting::getBotText('Сообщение пользователю без типа оплаты');
            $this->keyboard[] = [Bot_setting::getBotText('Назад')];
        }
        else {
            if ($this->user_id != null && $this->reseller->access == 5)
            {
                switch ($this->reseller->client->type) {
                    case 0:
                        $price = $product->price_old;
                        break;
                    case 1:
                        $price = $product->price_opt_old;
                        break;
                    case 2:
                        $price = $product->price_middle_old;
                        break;
                }

                $percent = $this->reseller->percents->where('category_id', $product->category_id)->first();

                switch ($this->type) {
                    case 0:
                        $percent = $percent->percentSmall;
                        break;
                    case 1:
                        $percent = $percent->percentLarge;
                        break;
                    case 2:
                        $percent = $percent->percentMiddle;
                        break;
                }

                $price_without_extra_charge = $price;
                $price = round(($price + (($price * $percent)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10;
            }
            else {
                switch ($this->type) {
                    case 0:
                        $price = $product->price_old;
                        break;
                    case 1:
                        $price = $product->price_opt_old;
                        break;
                    case 2:
                        $price = $product->price_middle_old;
                        break;
                }

                $price_without_extra_charge = $price;
            }

            if ($this->paymentType->code == 'in_fact') {
                $ordersSum = Order::where('client_id', $this->id)
                    ->pluck('total')
                    ->sum();
                $balance = $this->max_amount - $ordersSum;
            } elseif ($this->paymentType->code == 'by_prepayment') {
                $balance = $this->current_amount;
            }

            if ($price * $mess < $balance) {
                $one_hand->counts = $one_hand_count;
                $one_hand->save();
                $order = Order::where([
                    ['client_id', $this->id],
                    ['product_id', $product->id],
                    ['price', $price]
                ])->first();

                if ($order) {
                    $order->quantity += $mess;
                    $order->total = $price * $order->quantity;
                    $order->total_without_extra_charge = $price_without_extra_charge * $order->quantity;
                    $order->total_usd = $order->price_usd * $order->quantity;
                    $order->ordered = 0;
                } else {
                    $bookingCats = Category::getBookingCatsIds();
                    $productCat = Product::where('id', $product->id)
                        ->first()
                        ->category_id;

                    if (in_array($productCat, $bookingCats)) {
                        $provider = 'Booking';
                    } else {
                        $provider = 'Technotel';
                    }

                    $order = new Order([
                        'client_id' => $this->id,
                        'product_id' => $product->id,
                        'quantity' => $mess,
                        'price' => $price,
                        'price_without_extra_charge' => $price_without_extra_charge,
                        'price_usd' => $product->price_usd,
                        'total' => $price * $mess,
                        'total_without_extra_charge' => $price_without_extra_charge * $mess,
                        'total_usd' => $product->price_usd * $mess,
                        'provider' => $provider,
                    ]);
                }

                $order->save();

                if ($this->paymentType->code == 'by_prepayment') {
                    $this->update([
                        'current_amount' => $this->current_amount - $price * $mess,
                    ]);
                }

                $product->quantity -= $mess;
                $product->buy_count += $mess;
                if ($product->quantity && $product->addition_count > 0 && $product->buy_count >= $product->addition_count) {
                    $nameFlag = '';
                    if ($product->country && Attribute_value::where('value', $product->country)->first()) {
                        $nameFlag = Attribute_value::where('value', $product->country)->first()->additional_data;
                    }

                    $qty = floor($product->buy_count / $product->addition_count);
                    $product->buy_count = $product->buy_count - $product->addition_count * $qty;
                    $product->price_old += $product->addition_price * $qty;
                    $product->price_opt_old += $product->addition_price * $qty;
                    $product->price_middle_old += $product->addition_price * $qty;

                    $product->save();

                    // Массовая рассылка о повышении цены
                    $clients = DB::table('one_hands')
                        ->leftJoin('products', 'products.id', '=', 'one_hands.product_id')
                        ->select('one_hands.client_id')
                        ->where([['one_hands.product_id', $product->id], ['one_hands.counts', '>=', DB::raw('products.one_hand')]])
                        ->get();

                    if ($clients->count()) {
                        $clients = self::where([['active', 1], ['clients.step', '!=', '0']])
                            ->whereNotIn('id', $clients
                            ->pluck('client_id')
                            ->toArray())
                            ->get();
                    } else {
                        $clients = self::where([['active', 1], ['clients.step', '!=', '0']])
                            ->get();
                    }

                    foreach ($clients as $client) {
                        if ($client->disable_categories) {
                            $disableCategories = explode(',', $client->disable_categories);
                            if (in_array($product->category_id, $disableCategories)) {
                                continue;
                            }
                        } else {
                            if ($client->user_id != null && $client->reseller->access == 5) {
                                switch ($client->reseller->client->type) {
                                    case 0:
                                        $price = $product->price_old;
                                        break;
                                    case 1:
                                        $price = $product->price_opt_old;
                                        break;
                                    case 2:
                                        $price = $product->price_middle_old;
                                        break;
                                }

                                $percent = $client->reseller->percents->where('category_id', $product->category_id)->first();

                                switch ($client->type) {
                                    case 0:
                                        $percent = $percent->percentSmall;
                                        break;
                                    case 1:
                                        $percent = $percent->percentLarge;
                                        break;
                                    case 2:
                                        $percent = $percent->percentMiddle;
                                        break;
                                }

                                $price = round(($price + (($price * $percent)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10;
                            }
                            else {
                                switch ($client->type) {
                                    case 0:
                                        $price = $product->price_old;
                                        break;
                                    case 1:
                                        $price = $product->price_opt_old;
                                        break;
                                    case 2:
                                        $price = $product->price_middle_old;
                                        break;
                                }
                            }

                            $text = Bot_setting::getBotText('Повышение цены модели',
                                ['product_name' => $nameFlag . ' ' . $product->name, 'price' => $price]);
                            self::sendMessage($text, $client->uid);
                        }

                        $clientData = unserialize($client->step);
                        if (($clientData['step'] == 3 || $clientData['step'] == 5 || $clientData['step'] == 13) && $clientData['category_id'] == $product->category_id) {
                            $this->viewProducts($client, $product->category_id);
                        }
                    }
                } else {
                    $product->save();
                }

                // Массовая рассылка об окончании товара
                /*if($product->quantity == 0){
                    $product->buy_count = 0;
                    self::sendMassMessages(Bot_setting::getBotText('Окончание модели',['product_name'=>$product->name]),Client::where('active',1)->get()->pluck('id')->toArray());
                }*/

                return $this->doBack();
            }
            else {
                if ($this->paymentType->code == 'in_fact') {
                    $this->text = Bot_setting::getBotText('Баланс исчерпан для оплаты по факту', [
                        'balance' => number_format($this->max_amount, 0 , '.', '.'),
                    ]);
                } elseif ($this->paymentType->code == 'by_prepayment') {
                    $this->text = Bot_setting::getBotText('Баланс исчерпан для предоплаты', [
                        'balance' => number_format($this->current_amount, 0 , '.', '.'),
                    ]);
                }

                $this->keyboard[] = [Bot_setting::getBotText('Назад')];
            }
        }
        
        return $this->sendMessage();
    }
    
    public function doBack()
    {
        $this->setStep('back');
        if(($this->current_step['step'] == 2 || $this->current_step['step'] == 4) && isset($this->current_step['category_id'])){
            $this->category = Category::find($this->current_step['category_id']);
            $parent_category = Category::find($this->category->parent_id);
            if($parent_category){
                $this->category = $parent_category;
                $this->child_categories = Category::getChieldCategories($this->category->id);
                return $this->viewCategories();
            }
        }
        if(in_array($this->current_step['step'],[3,5,13,30]) && isset($this->current_step['category_id'])){
            $this->category = Category::find($this->current_step['category_id']);
            if($this->category){
                return $this->viewProducts();
            }
        }
        if(in_array($this->current_step['step'],[14,15,31])){
            return $this->viewAdmin();
        }
        if(in_array($this->current_step['step'],[17,18])){
            return $this->viewAllMessages();
        }
        if($this->current_step['step'] == 19){
            return $this->viewCitiesList();
        }
        if($this->current_step['step'] == 20){
            return $this->viewUsersList($this->current_step['currentPage']);
        }
        if($this->current_step['step'] == 21) {
            return $this->viewUser($this->current_step['client_id']);
        }
        if(in_array($this->current_step['step'],[24,25])) {
            return $this->editClientName($this->current_step['edition_client_id']);
        }
        if(in_array($this->current_step['step'],[26,27])) {
            return $this->chooseClientWholesale();
        }
        
        return $this->viewFirstCategories();
    }
    
    public function sendAnswer($mess)
    {
        // Если класс не имеет активного пользователя - выход
        if(!$this->id) return false;
        
        // Если время работы бота не совпадает с реальным временем
        $time_work = Bot_setting::getTimeWork();
        $time_now = date('H:i:s');
                
        if ($time_now < $time_work['from'] || $time_now > $time_work['to']) {
            return $this->sendMessage(Bot_setting::getBotText('Не работающий бот',['from'=>date('H:i',strtotime($time_work['from'])),'to'=>date('H:i',strtotime($time_work['to']))]));
        }
        
        // Если пользователь не активирован - говорим ему, что попросился активироваться
        /*if($this->active == 0){
            $keyboard[] = ["Добавить", "add_user=" . $this->id];
            /*$admins = self::where('admin', 1)->get();
            foreach($admins as $admin) {
                Telegram::sendMessage([
                    'chat_id' => $admin->uid,
                    'text' => 'Пользователь ' . $this->first_name . ' отправил запрос на добавление.',
                    'reply_markup' => $this->replyMarkup($keyboard, true),
                ]);
            }

            $this->text = Bot_setting::getBotText('Сообщение неавторизованному клиенту');
            $this->keyboard[] = [Bot_setting::getBotText('Start')];
            return $this->sendMessage();
        }*/

        if(strpos($mess, "add_user=") !== false) {
            $id = trim(str_replace("add_user=", "", $mess));
            $client = self::find($id);
            $client->update(['active' => 1]);
            $this->setStep(23);
            return $this->getStep($id);
        }
        if(strpos($mess, "answer_admin=") !== false) {
            $id = trim(str_replace("answer_admin=", "", $mess));
            $this->setStep(21);
            return $this->getUserMessages($id);
        }
        if(strpos($mess, "answer_client=") !== false) {
            $id = trim(str_replace("answer_client=", "", $mess));
            $this->setStep(1);
            return $this->getStep(Bot_setting::getBotText('Обратная связь'));
        }
        
        if($mess == Bot_setting::getBotText('Оформить')){
            $orders = Order::where(['client_id'=>$this->id],['ordered'=>0]);
            $orders->update(['ordered' => 1]);

            $this->text = $this->formOrder($this->id);

            $client = self::find($this->id);
            if ($client->payment_type_id) {
                if ($client->paymentType->code == 'by_prepayment') {
                    $balance = $client->current_amount;

                    $this->text .= "\n\n" .  Bot_setting::getBotText('Баланс клиента', [
                            'balance' => number_format($balance,0,".",".")
                    ]);
                }
            }

            return $this->viewFirstCategories();
        }

        // Если нажата кнопка Start первый раз - отправляем рассылку
        if($this->active == 1 && ($mess == Bot_setting::getBotText('start') || $mess=='/start' || strtolower($mess)=='start') && empty($this->step)){
            $this->sendRassilka();
        }
        
        return $this->getStep($mess);
    }

    
    public function sendRassilka()
    {
        if (Bot_setting::getBotText('send_rassilka') == 1){
            Bot_setting::generateRassilka($this->id);
        }
    }
    
    public function getViewProduct($product, $client=false)
    {
        if (!$client) $client = $this;

        if ($client->user_id != null && $client->reseller->access == 5)
        {
            switch ($client->reseller->client->type) {
                case 0:
                    $price = $product->price_old;
                    break;
                case 1:
                    $price = $product->price_opt_old;
                    break;
                case 2:
                    $price = $product->price_middle_old;
                    break;
            }

            $percent = $client->reseller->percents->where('category_id', $product->category_id)->first();

            switch ($client->type) {
                case 0:
                    $percent = $percent->percentSmall;
                    break;
                case 1:
                    $percent = $percent->percentLarge;
                    break;
                case 2:
                    $percent = $percent->percentMiddle;
                    break;
            }

            $price = round(($price + (($price * $percent)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10;
        }
        else {
            switch ($client->type) {
                case 0:
                    $price = $product->price_old;
                    break;
                case 1:
                    $price = $product->price_opt_old;
                    break;
                case 2:
                    $price = $product->price_middle_old;
                    break;
            }
        }

        if ($product->country != null && $product->attribute_value()->first()) {
            return $product->attribute_value()->first()->additional_data . ' ' . $product->name . ' || ' . $price;
        }
        else {
            return $product->name . ' || ' . $price;
        }
    }
    
    public function findProduct($mess)
    {
        $product_name = explode(' || ',$mess)[0];
        $name_array = explode(' ',$product_name);
        $flag = array_shift($name_array);
        $country_data = DB::select('select * from `attribute_values` where ORD(additional_data) = ORD("' . $flag . '")');
        if ($country_data) {
            $country = $country_data[0]->value;
        }
        else {
            $country = null;
        }
        $product_name = implode(' ', $name_array);

        $products = Product::where('name', '=', $product_name)
                            ->where('category_id', '=', $this->current_step['category_id'])
                            ->where('quantity', '>', 0)
                            ->where('country', $country)
                            ->get();

        foreach($products as $product){
            if($product->name == $product_name) {
                $this->product = $product;
            }
        }
        if($this->product) return true;
        
        return false;
    }
    
    public static function getBotText($type,$shortcodes=[])
    {
        $text = Bot_setting::where('type',$type)->first();
        if(!$text){
            $text = $type;
        } else {
            $text = $text->text;
        }
        
        if(count($shortcodes)){
            foreach($shortcodes as $key => $val){
                /*if($key=='price' || $key=='total'){
                    $val = number_format($val,0,".",".");
                }*/
                $text = str_replace("{{$key}}", $val, $text);
            }
        }
        return $text;
    }
    
    public function replyMarkup($keyboard,$inline=false) 
    {
        if($inline){
            $inline_keyboard = [];
            foreach($keyboard as $k){
                $inline_keyboard[] = [["text"=>$k[0],"callback_data"=>$k[1]]];
            }
            $keyboard=["inline_keyboard"=>$inline_keyboard];
            
            return json_encode($keyboard);
        }
        $flag = count($keyboard) > 4 ? false : true;
        return Telegram::replyKeyboardMarkup([
            'keyboard' => $keyboard, 
            'resize_keyboard' => $flag, 
            'one_time_keyboard' => true
        ]);
    }
    
    /*
     * Добавление сообщения в массовую рассылку
     * string $message - готовый текст сообщения
     * string $clients_ids - id клиентов через запятую, кому нужно отправить сообщения
     * array $keyboards - кнопки в телеграмме
     */ 
    public static function sendMassMessages($message,$clients_ids,$keyboards='')
    {
        Log::info("sendMassMessages clients: $clients_ids");
        if(!$clients_ids) return;
        
        if($keyboards){
            $keyboards = serialize($keyboards);
        }
        $mass = new MassMessage(['message'=>$message, 'status'=>0, 'clients_ids'=>$clients_ids, 'keyboards'=>$keyboards, 'sent_ids'=>'']);
        $mass->save();
        
        // Отсылаем сразу же эти сообщения
        MassMessage::sendMessages();
    }
    
    public function sendMessage($text=false,$uid=false,$keyboard=false,$client_id=false)
    {
        if($text) $this->text = $text;
        if(!$uid) $uid = $this->uid;
        if(!$keyboard) $keyboard = $this->keyboard;
        if(!$client_id) $client_id = $this->id;
        
        if(count($keyboard) && $this->text){
            // Если есть не оформленные заказы
            $orders = Order::where([['client_id',$client_id], ['ordered',0]])->get();
            if($orders->count()){
                $keyboard[] = [Bot_setting::getBotText('Оформить')];
            }
            History::saveHistory($client_id,['bot_text'=>$this->text]);
            return Telegram::sendMessage([
                'chat_id' => $uid, 
                'text' => $this->text, 
                'reply_markup' => $this->replyMarkup($keyboard)
            ]);
        }
        elseif ($this->text){
            History::saveHistory($client_id,['bot_text'=>$this->text]);
            return Telegram::sendMessage([
                'chat_id' => $uid, 
                'text' => $this->text, 
            ]);
        }
        
        return false;
    }
    
    public static function doMassMess()
    {
        $file = base_path().'/log/last_id.txt';
        $client_id = (int)file_get_contents($file);
        
        $clients = self::where([['active',1],['id','>',$client_id]])->orderBy('id', 'asc')->get();
        
        $keyboard = Telegram::replyKeyboardMarkup([
            'keyboard' => [[Bot_setting::getBotText('Start')]], 
            'resize_keyboard' => true, 
            'one_time_keyboard' => true
        ]);
        $last_id = 0;
        foreach($clients as $client){
            try{
                Telegram::sendMessage([
                    'chat_id' => $client->uid, 
                    'text' => Bot_setting::getBotText('Приветствие для зарегистрированного',['time'=>Bot_setting::getEndTime()]),
                    'reply_markup' => $keyboard
                ]);
                echo "Отправили id:{$client->id}, name: {$client->first_name} {$client->last_name}, uid: {$client->uid}<br>";
            } catch (Exception $e) {
                echo 'Ошибка от телеграма: ',  $e->getMessage(), "<br>";
                $last_id = $client->id;
            }
            $last_id = $client->id;
        }
        
        $f = fopen($file,'w');
        fwrite($f,$last_id);
        fclose($f);
        
        exit;
    }
    public static function doCron()
    {
        $echo = '';
        $time_start = time();
        $time_work = Bot_setting::getTimeWork();
        $time_now = date('H:i:s');
        $clients = self::where([['active',1],['step','!=','0']])->get();

        // Началось время работы бота
        if ($time_now >= $time_work['from'] && $time_now < $time_work['to'] && $time_work['status'] == 1)
        {
            // Мягко удаляем все старые заказы.
            $orders = Order::where('deleted_at', '=', null)
                ->get();
            foreach($orders as $order) {
                $order->delete();
            }

            DB::table('products')
                ->update([
                    'buy_count' => 0,
                    'price_old' => DB::raw('price'),
                    'price_opt_old' => DB::raw('price_opt'),
                    'price_middle_old' => DB::raw('price_middle')
                ]);
            
            // Ставим флаг, чтоб в условие начало работы бота больше не заходить
            Bot_setting::where('type','status')->update(['text' => 2]);

            // Массовое сообщение приветствия
            $clients = self::where([['active',1],['step','0']])->get();
            if($clients->count()){
                $keyboards = [[Bot_setting::getBotText('Start')]];
                $message = Bot_setting::getBotText('Приветствие для зарегистрированного',['time'=>Bot_setting::getEndTime()]);
                self::sendMassMessages($message,$clients->implode('id', ','),$keyboards);
            }

            $echo .= 'Bot begin: '.$clients->count()."\n";
        }
        // Закончилось время работы бота
        elseif (($time_now < $time_work['from'] || $time_now > $time_work['to']) && $time_work['status'] == 2)
        {
            // Обнуляем шаги всем пользователям
            DB::table('clients')->update(['step' => 0]);
            DB::table('products')
                ->update([
                    'buy_count' => 0,
                    'price_old' => DB::raw('price'),
                    'price_opt_old' => DB::raw('price_opt'),
                    'price_middle_old' => DB::raw('price_middle'),
                ]);
            DB::table('one_hands')->truncate();
            
            // Очищаем историю переписки старше 3-х дней
            DB::table('histories')->where('created_at','<=',Carbon::now()->subDays(3)->toDateTimeString())->delete();

            $bookingCategories = Category::scopeGetBookingCatsIds();

            $productsBooking = DB::table('orders')
                ->select('orders.*','products.name', 'products.country')
                ->where('orders.deleted_at', '=', null)
                ->leftJoin('products', 'orders.product_id', '=', 'products.id')
                //->where('client_id',$this->id)
                ->whereIn('category_id', $bookingCategories)
                ->get();

            $productsTechnotel = DB::table('orders')
                ->select('orders.*','products.name', 'products.country')
                ->where('orders.deleted_at', '=', null)
                ->leftJoin('products', 'orders.product_id', '=', 'products.id')
                //->where('client_id',$this->id)
                ->whereNotIn('category_id', $bookingCategories)
                ->get();

            $listBooking = [];
            $totalBooking = [];
            foreach($productsBooking as $prod){
                $nameFlag = '';
                if($prod->country && Attribute_value::where('value', $prod->country)->first()) {
                    $nameFlag = Attribute_value::where('value', $prod->country)->first()->additional_data;
                }
                if(!isset($listBooking[$prod->client_id])) {
                    $listBooking[$prod->client_id] = [];
                }
                if(!isset($totalBooking[$prod->client_id])) {
                    $totalBooking[$prod->client_id] = 0;
                }
                $i = count($listBooking[$prod->client_id]) + 1;
                $listBooking[$prod->client_id][] = $i.'. '.Bot_setting::getBotText('Итоговое сообщение один продукт',[
                    'product_name'=>$nameFlag . ' ' . $prod->name,
                    'count'=>$prod->quantity,
                    'price'=>$prod->price,
                    'total'=>$prod->total])."\n\n";
                $totalBooking[$prod->client_id] += $prod->total;
            }

            $listTechnotel = [];
            $totalTechnotel = [];
            foreach($productsTechnotel as $prod){
                $nameFlag = '';
                if($prod->country && Attribute_value::where('value', $prod->country)->first()) {
                    $nameFlag = Attribute_value::where('value', $prod->country)->first()->additional_data;
                }
                if(!isset($listTechnotel[$prod->client_id])) {
                    $listTechnotel[$prod->client_id] = [];
                }
                if(!isset($totalTechnotel[$prod->client_id])) {
                    $totalTechnotel[$prod->client_id] = 0;
                }
                $j = count($listTechnotel[$prod->client_id]) + 1;
                $listTechnotel[$prod->client_id][] = $j.'. '.Bot_setting::getBotText('Итоговое сообщение один продукт',[
                    'product_name'=> $nameFlag . ' ' . $prod->name,
                    'count'=>$prod->quantity,
                    'price'=>$prod->price,
                    'total'=>$prod->total])."\n\n";
                $totalTechnotel[$prod->client_id] += $prod->total;
            }

            // Ставим флаг, чтоб в условие завершение работы бота больше не заходить
            Bot_setting::where('type', '=', 'status')->update(['text' => 1]);
            
            // Массовое сообщение окончания работы
            foreach($clients as $client){
                if(isset($listTechnotel[$client->id])) {
                    $message = ''//Bot_setting::getBotText('Обшая информация', ['bot_name' => 'Technotel', 'name' => $client->first_name . ' ' . $client->last_name]) . "\n\n"
                        . Bot_setting::getBotText('Итоговое сообщение', ['product_list' => implode('', $listTechnotel[$client->id]), 'total' => $totalTechnotel[$client->id]]);
                    self::sendMassMessages($message, $client->id);
                }
                if(isset($listBooking[$client->id])) {
                    $message = ''//Bot_setting::getBotText('Обшая информация', ['bot_name' => 'Booking', 'name' => $client->first_name . ' ' . $client->last_name]) . "\n\n"
                        . Bot_setting::getBotText('Итоговое сообщение', ['product_list' => implode('', $listBooking[$client->id]), 'total' => $totalBooking[$client->id]]);
                    self::sendMassMessages($message, $client->id);
                }
            }
            if($clients->count()){
                $keyboards = [[Bot_setting::getBotText('Start')]];
                $message = Bot_setting::getBotText('Конечное сообщение рассылки');
                self::sendMassMessages($message,$clients->implode('id', ','),$keyboards);
            }
            
            $echo .= 'Bot end: '.$clients->count()."\n";
        }

        // Массовая рассылка
        //$messages = MassMessage::sendMessages();

        $time_end = time();
        $time = $time_end - $time_start;
        $time = date("H:i:s", mktime(0, 0, $time));
        
        $echo .= 'Cron worked. Time was '.$time;

        $time_cats = date('H:i');

        $categories = Category::where('parent_id', 0)->get();
        foreach($categories as $category) {
            if($category->start_date == $time_cats) {
                $message = Bot_setting::getBotText('Начало работы категории', ['category_name' => $category->name]);
                self::sendMassMessages($message, $clients->implode('id', ','));
            }
        }

        return $echo;
    }


    public function getCats()
    {
        $cats = explode(',',$this->disable_categories);
        if(count($cats)){
            $res = Category::whereIn('id',$cats)->get()->pluck('name');
            return $res;
        }
        return false;
    }

    public function histories()
    {
        return $this->hasMany('App\History');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'uid','admin_uid');
    }

    public function reseller()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function paymentType()
    {
        return $this->belongsTo('App\PaymentType');
    }

    public function formOrder($clientId)
    {
        $bookingCategories = Category::getBookingCatsIds();
        $productsBooking = DB::table('orders')
            ->select('orders.*','products.name', 'products.country')
            ->where('orders.deleted_at', '=', null)
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->where('client_id', $clientId)
            ->whereIn('category_id', $bookingCategories)
            ->get();

        $productsTechnotel = DB::table('orders')
            ->select('orders.*','products.name', 'products.country')
            ->where('orders.deleted_at', '=', null)
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->where('client_id', $clientId)
            ->whereNotIn('category_id', $bookingCategories)
            ->get();

        $this->text = '';

        $listBooking = '';
        $i = 1;
        foreach($productsBooking as $prod){
            $nameFlag = '';
            if($prod->country && Attribute_value::where('value', $prod->country)->first()) {
                $nameFlag = Attribute_value::where('value', $prod->country)->first()->additional_data;
            }
            $listBooking .= $i.'. '.Bot_setting::getBotText('Итоговое сообщение один продукт',[
                'product_name'=> $nameFlag . ' ' . $prod->name,'count'=>$prod->quantity,'price'=>$prod->price,'total'=>$prod->total
                ])."\n\n";
            $i++;
        }

        if($listBooking !== '') {
            $this->text .= '' //Bot_setting::getBotText('Обшая информация', ['bot_name' => 'Booking', 'name' => $this->first_name . ' ' . $this->last_name]) . "\n\n"
                . Bot_setting::getBotText('Итоговое сообщение', [
                    'product_list' => $listBooking, 'total' => $productsBooking->sum('total')
                ]) . "\n\n";
        }

        $listTechnotel = '';
        $j = 1;
        foreach($productsTechnotel as $prod){
            $nameFlag = '';
            if($prod->country && Attribute_value::where('value', $prod->country)->first()) {
                $nameFlag = Attribute_value::where('value', $prod->country)->first()->additional_data;
            }

            $listTechnotel .= $j.'. '.Bot_setting::getBotText('Итоговое сообщение один продукт',[
                'product_name'=>$nameFlag . ' ' .$prod->name,'count'=>$prod->quantity,'price'=>$prod->price,'total'=>$prod->total
                ])."\n\n";
            $j++;
        }

        if($listTechnotel !== '') {
            $this->text .= '' //Bot_setting::getBotText('Обшая информация', ['bot_name' => 'Technotel', 'name' => $this->first_name . ' ' . $this->last_name]) . "\n\n"
                . Bot_setting::getBotText('Итоговое сообщение', [
                    'product_list' => $listTechnotel, 'total' => $productsTechnotel->sum('total')
                ]);
        }

        return $this->text;
    }
}

