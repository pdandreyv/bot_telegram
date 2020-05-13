<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bot_setting extends Model
{
    public static function getTimeWork()
    {
        $settings = self::where('code','other')->get();
        foreach ($settings as $set) {
            if($set->type == 'from') {
                $from = date("H:i:s", strtotime($set->text));
            }
            if($set->type == 'to') {
                $to = date("H:i:s", strtotime($set->text));
            }
            if($set->type == 'status') {
                $status = $set->text;  
            } 
        }
        
        return ['from'=>$from,'to'=>$to,'status'=>$status];
    }
    
    public static function getEndTime()
    {
        return date("H:i", strtotime(self::getBotText('to')));
    }
    
    public static function getBotText($type,$shortcodes=[])
    {
        $text = self::where('type',$type)->first();
        if(!$text){
            $text = $type;
        } else {
            $text = $text->text;
        }
        
        if(count($shortcodes)){
            foreach($shortcodes as $key => $val){
            if(($type == 'Итоговое сообщение один продукт' || $type =='Итоговое сообщение') && ($key=='price' || $key=='total')){
                    $val = number_format($val,0,".",".");
                }
                $text = str_replace("{{$key}}", $val, $text);
            }
        }
        return $text;
    }

    public static function generateRassilka($clientId = null, $userId = null)
    {
        $categories = Category::where('parent_id', 0)
            ->where('id', '!=', config('discount.discount_category_id'))
            ->where('name', '!=', 'Booking')
            ->get();

        if ($clientId) {
            $client = Client::findOrFail($clientId);
            $all_products = '';
            $dis = explode(',', $client->disable_categories);
            $messages = [];
            $thisText = '';

            if (isset($_ENV['HOST']) && $_ENV['HOST'] == 'grand-cms.ru') {
                foreach ($categories as $category) {
                    if (!in_array($category->id, $dis)) {
                        $id = $category->id;
                        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                            ->whereHas('category', function ($query) use ($id) {
                                $query->where('id', $id)
                                    ->orWhere('parent_id', $id);
                            })->where('quantity', '>', 0)
                            ->select('products.*')
                            ->orderBy('categories.position', 'ASC')
                            ->orderBy('products.memory', 'ASC')
                            ->orderBy('products.position', 'ASC')
                            ->get();

                        $text = '';
                        if ($products->count() > 0) {
                            $text .= $category->name . ":\n";

                            $modelTitle = '';
                            $memory = '';
                            foreach ($products as $product) {
                                $nameFlag = '';
                                if ($product->country && Attribute_value::where('value', $product->country)->first()) {
                                    $nameFlag = Attribute_value::where('value', $product->country)->first()->additional_data;
                                }

                                if ($memory != $product->memory) {
                                    $attributeFlag = "\n";
                                    $memory = $product->memory;
                                } else {
                                    $attributeFlag = "";
                                }

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

                                if ($product->category->parent_id != 0 && $product->category->name != $modelTitle) {
                                    $modelTitle = $product->category->name;
                                    $text .= "\n" . $modelTitle . "\n\n" . $nameFlag . ' ' . $product->name . ' - ' . $price . "\n";
                                } elseif ($product->category->parent_id == 0 && $product->category->name != $modelTitle) {
                                    $modelTitle = $product->category->name;
                                    $text .= "\n" . $nameFlag . ' ' . $product->name . ' - ' . $price . "\n";
                                } else {
                                    $text .= $attributeFlag . $nameFlag . ' ' . $product->name . ' - ' . $price . "\n";
                                }

                            }

                            $messages[] = $text;
                        }
                    }
                }

                for ($i = 0; $i < count($messages); $i++)
                    $client->sendMessage($messages[$i]);
                $thisText = $messages[count($messages) - 1];

            } else {
                $thisText = $client->type ? Bot_setting::getBotText('Apple (крупный опт)') : Bot_setting::getBotText('Apple');
            }
        }
        elseif ($userId) {
            $categories = Category::where('parent_id', 0)
                ->where('id', '!=', config('discount.discount_category_id'))
                ->get();

            $user = User::findOrFail($userId);
            $type = $user->client->type;

            $mailings = [];
            foreach ($categories as $category) {
                $id = $category->id;
                $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                    ->whereHas('category', function ($query) use ($id) {
                        $query->where('id', $id)
                            ->orWhere('parent_id', $id);
                    })->where('quantity', '>', 0)
                    ->select('products.*')
                    ->orderBy('categories.position', 'ASC')
                    ->orderBy('products.memory', 'ASC')
                    ->orderBy('products.position', 'ASC')
                    ->get();

                    $text = '';
                    $textOpt = '';
                    $textMiddleOpt = '';
                    $modelTitle = '';
                    $memory = '';

                if ($products->count() > 0) {
                    $text = $category->name . ":\r\n";
                    $textOpt = $category->name . ":\n";
                    $textMiddleOpt = $category->name . ":\n";
                    foreach ($products as $product) {
                        $nameFlag = '';
                        if ($product->country && Attribute_value::where('value', $product->country)->first()) {
                            $nameFlag = Attribute_value::where('value', $product->country)->first()->additional_data;
                        }

                        if ($memory != $product->memory) {
                            $attributeFlag = "\n";
                            $memory = $product->memory;
                        } else {
                            $attributeFlag = "";
                        }

                        switch ($type) {
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

                        $percent = $user->percents->where('category_id', $product->category_id)->first();
                        $priceSmall = round(($price + (($price * $percent->percentSmall)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10;
                        $priceMiddle = round(($price + (($price * $percent->percentMiddle)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10;
                        $priceLarge = round(($price + (($price * $percent->percentLarge)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10;

                        if ($product->category->parent_id != 0 && $product->category->name != $modelTitle) {
                            $modelTitle = $product->category->name;
                            $text .= "\n" . $modelTitle . "\n\n" . $nameFlag . ' ' . $product->name . ' - ' . $priceSmall . "\n";
                            $textMiddleOpt .= "\n" . $modelTitle . "\n\n" . $nameFlag . ' ' . $product->name . ' - ' . $priceMiddle . "\n";
                            $textOpt .= "\n" . $modelTitle . "\n\n" . $nameFlag . ' ' . $product->name . ' - ' . $priceLarge . "\n";
                        } elseif ($product->category->parent_id == 0 && $product->category->name != $modelTitle) {
                            $modelTitle = $product->category->name;
                            $text .= "\n" . $nameFlag . ' ' . $product->name . ' - ' . $priceSmall . "\n";
                            $textMiddleOpt .= "\n" . $nameFlag . ' ' . $product->name . ' - ' . $priceMiddle . "\n";
                            $textOpt .= "\n" . $nameFlag . ' ' . $product->name . ' - ' . $priceLarge . "\n";
                        } else {
                            $text .= $attributeFlag . $nameFlag . ' ' . $product->name . ' - ' . $priceSmall . "\n";
                            $textMiddleOpt .= $attributeFlag . $nameFlag . ' ' . $product->name . ' - ' . $priceMiddle . "\n";
                            $textOpt .= $attributeFlag . $nameFlag . ' ' . $product->name . ' - ' . $priceLarge . "\n";
                        }
                    }
                }

                $mailing[$category->name] =  $text;
                $mailing[$category->name . " (средний опт)"] = $textMiddleOpt;
                $mailing[$category->name . " (крупный опт)"] = $textOpt;
            }

            return $mailing;
        }
        else {
            foreach ($categories as $category) {
                $id = $category->id;
                $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                    ->whereHas('category', function ($query) use ($id) {
                        $query->where('id', $id)
                            ->orWhere('parent_id', $id);
                    })->where('quantity', '>', 0)
                    ->select('products.*')
                    ->orderBy('categories.position', 'ASC')
                    ->orderBy('products.memory', 'ASC')
                    ->orderBy('products.position', 'ASC')
                    ->get();

                $text = '';
                $textOpt = '';
                $textMiddleOpt = '';

                if ($products->count() > 0) {
                    $text = $category->name . ":\n";
                    $textOpt = $category->name . ":\n";
                    $textMiddleOpt = $category->name . ":\n";

                    $modelTitle = '';
                    $memory = '';

                    foreach ($products as $product) {
                        $nameFlag = '';
                        if ($product->country && Attribute_value::where('value', $product->country)->first()) {
                            $nameFlag = Attribute_value::where('value', $product->country)->first()->additional_data;
                        }

                        if ($memory != $product->memory) {
                            $attributeFlag = "\n";
                            $memory = $product->memory;
                        } else {
                            $attributeFlag = "";
                        }

                        if ($product->category->parent_id != 0 && $product->category->name != $modelTitle) {
                            $modelTitle = $product->category->name;
                            $text .= "\n" . $modelTitle . "\n\n" . $nameFlag . ' ' . $product->name . ' - ' . $product->price . "\n";
                            $textMiddleOpt .= "\n" . $modelTitle . "\n\n" . $nameFlag . ' ' . $product->name . ' - ' . $product->price_middle . "\n";
                            $textOpt .= "\n" . $modelTitle . "\n\n" . $nameFlag . ' ' . $product->name . ' - ' . $product->price_opt . "\n";
                        } elseif ($product->category->parent_id == 0 && $product->category->name != $modelTitle) {
                            $modelTitle = $product->category->name;
                            $text .= "\n" . $nameFlag . ' ' . $product->name . ' - ' . $product->price . "\n";
                            $textMiddleOpt .= "\n" . $nameFlag . ' ' . $product->name . ' - ' . $product->price_middle . "\n";
                            $textOpt .= "\n" . $nameFlag . ' ' . $product->name . ' - ' . $product->price_opt . "\n";
                        } else {
                            $text .= $attributeFlag . $nameFlag . ' ' . $product->name . ' - ' . $product->price . "\n";
                            $textMiddleOpt .= $attributeFlag . $nameFlag . ' ' . $product->name . ' - ' . $product->price_middle . "\n";
                            $textOpt .= $attributeFlag . $nameFlag . ' ' . $product->name . ' - ' . $product->price_opt . "\n";
                        }
                    }

                    self::where('type', $category->name)->update(['text' => $text]);
                    self::where('type', $category->name . " (средний опт)")->update(['text' => $textMiddleOpt]);
                    self::where('type', $category->name . " (крупный опт)")->update(['text' => $textOpt]);
                }
            }
        }
    }
}
