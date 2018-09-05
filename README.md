# Заготовка для создания приложения для InSlaes на PHP+MySql
Заготовка позволяет быстро поднимать приложение для InSlaes на PHP+MySql

## В заготовку входит
1. Класс для работы с БД MySql
2. Простой шаблонизатор, для удобного и быстрого создание шаблона для приложение. Шаблон полностью отделен от бэк-энд
3. Класс для работы с API InSales
4. Установка и удаление приложение из админки InSales
5. Авторизация

## Установка и настройка
1. Заливаем содержимое папки *apps* на свой сервер
2. В файле *data/database.php* указываем логин, пароль и название самой БД MySql
3. Импортируем файл импорта таблиц БД *db.sql*
4. В бэк-офисе InSales, в разделе *"Приложение" -> "Разработчикам"*, добавляем новое приложение. Заполняем все необходимые поля, в особенности:
  + Идентификатор (Будет использоваться далее)
  + URL установки - путь к файлу *install.php*
  + URL входа - путь к файлу *index.php*
  + URL деинсталяции - путь к файлу *uninstall.php*
5. В файле *data/config.php* указываем **Идентификатор приложения** из предыдущего пункта и **Секретный ключ**, который был сгенерирован при добавлении приложения в предыдущем пункте.
6. Запускаем установку приложение из бэк-офиса InSales

## Работа с шаблонизатором
```
  $tpl->load_template('mytpl.tpl'); // Подключаем шаблон mytpl.tpl в папке template
  $tpl->set('{helloworld}', 'Привет Мир!'); // Подключаем переменную {helloworld} в шаблоне mytpl.tpl и задаем ей значение
  $tpl->compile('mytpl'); // Компилируем шаблон в переменную $tpl->result['mytpl']
  $tpl->clear();
```

Далее содержимое шаблона можно вывести на странице с помощью:
```
  echo $tpl->result['mytpl'];
```

или передать в другой шаблон:
```
  $content = $tpl->result['mytpl'];
  $tpl->load_template('_main.tpl');
  $tpl->set('{content}', $content);
  $tpl->compile('content');
  echo $tpl->result['content'];
  $tpl->clear();
```

Можно использовать конструкцию:
```
$tpl->set_block ( '#\[block\](.+?)\[\/block\]#is', '\\1' );
```
для вывода или скрытия той или иной информации в шаблоне. Например:
```
  $show_block = true;
  $tpl->load_template('_main.tpl');
  $tpl->set('{mytpl}', $content);
  if($show_block == true){ // Отображаем или скрываем информацию вмежду тегом [block]Информация[/block]
    $tpl->set('[block]', '');
    $tpl->set('[/block]', '');
  }else{
    $tpl->set_block("'\\[block\\].*?\\[/block\\]'si", '');
  }
  $tpl->compile('content');
  echo $tpl->result['content'];
  $tpl->clear();
```

**Боевой пример**
Шаблонизатор можно использовать вместе с циклом, например, для вывода списка товаров. 
```
// Компилируем шаблон списка товаров
  $tpl->load_template('products_item.tpl');
  $sql_result = $db->query('SELECT * FROM products');
  while($products = $db->get_row($sql_result)){
    if($products['show'] == 1){ // Отображаем или скрываем товар
      $tpl->set('[product_show]', '');
      $tpl->set('[/product_show]', '');
    }else{
      $tpl->set_block("'\\[product_show\\].*?\\[/product_show\\]'si", '');
    }
    $tpl->set('{title}', $products['title']);
    $tpl->set('{description}', $products['description']);
    $tpl->compile('products_items');
  }
  $products_items = $tpl->result['products_items'];
  $tpl->clear();
  $db->free();

// Компилируем шаблон страницы товаров
  $tpl->load_template('products.tpl');
  $tpl->set('{products_items}', $products_items);
  $tpl->compile('products');
  $tpl->clear();

// Компилируем и выводим шаблон
  $tpl->load_template('_main.tpl');
  $tpl->set('{info}', 'Информация');
  $tpl->set('{content}', $tpl->result['products']);
  $tpl->compile('content');
  echo $tpl->result['content'];
  $tpl->clear();
  $tpl->global_clear();
```

И итоге у нас есть три шаблона: 
+ *products_item.tpl* - Список товаров
+ *products.tpl* - Шаблон товаров
+ *_main.tpl* - Основной шаблон, в котором мы собираем все остальные

Иметь ини будут следующий вид:
```
// Шаблон products_item.tpl
  [product_show]
    {title}
    {description}
  [/product_show]
  
// Шаблон products.tpl
  {products_items}
    
// Шаблон _main.tpl
  {info}
  {content}
```

## Работа с базой данных
В заготовке предусмотрен класс для работы с БД. Несколько примеров:
```
// Пример #1 - Выборка нескольких позиций
  $sql_result = $db->query('SELECT * FROM products');
  while($products = $db->get_row($sql_result)){
    print_r($products);
  }
  $db->free();
  
// Пример #2 - Выборка одной позиции
  $product = $db->super_query('SELECT * FROM products id="1"');
  print_r($product);
  
// Пример #3 - Добавление, редактирование, удаление
  $db->query('INSERT INTO products (title, description, token) values ("Заголовок", "Описание")');
  $db->query('UPDATE products SET title="Новый заголовок" WHERE id="1"');
  $db->query('DELETE FROM products WHERE id="1"');
```

## Сам скрипт и пример на GitHub
https://github.com/eZ4hUNt/insales-php-api-apps/
