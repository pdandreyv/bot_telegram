INSERT INTO `categories` (`id`, `name`, `parent_id`, `position`, `visible`, `created_at`, `updated_at`) VALUES 
(7, 'Уцененный товар', '0', '1', '0', '2017-08-08 01:00:00', '2017-08-08 01:00:00');

INSERT INTO `bot_settings` (`text`, `type`, `code`, `shortcodes`, `created_at`, `updated_at`) VALUES
('Уцененный товар', 'Уцененный товар', 'button', '', '2017-08-08 00:00:00', '2017-08-08 00:00:00');

INSERT INTO `products` (`category_id`, `position`, `name`, `country`, `quantity`, `one_hand`, `price`, `price_opt`, `created_at`, `updated_at`) VALUES
(7, '1', 'iPHONE 5S 16GB SILVER', 'USA', '100', '100', '15600', '15600', '2017-08-08 00:00:00', '2017-08-08 00:00:00');