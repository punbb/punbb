
## TODO

### Хелпер для языковых трансляций 

с автоподгрузкой файла трансляций

было
    
    $lang_common['No view']
    $lang_index['Link to']
    ...

стало 
    
    __('No view', 'common') или __('No view')
    __('Link to', 'index')
    ...

    хранение в
    $_PUNBB['lang']['commmon']['No view']
    $_PUNBB['lang']['index']['Link to']
    ...

- новый "домен" 'extension1' регистрируется каким-то образом (путь к файлу для загрузки добавляется в ассоциативный массив) через расширение типа "языковой пакет"
- если есть в массиве - файл подключается по пути оттуда, если нет подключается по умолчанию.
- __fmt('const', [arg1, arg2, arg3], $domain, $language) - работает как sprintf(__(...), arg1, arg2,)

### пространство имен 

    namespace punbb;

### вынос глобальных переменных в $_PUNBB - глобальный "репозитарий"

### шаблоны

стандартный view, helper - всегда выводит данные вместо записи в буфер, потом вывода
возможность переопределить функции (может реализовать на классах с DI)
переопределяющая функция заботится об буферизации контента 
примеры использования - кеширование html-блоков, обработка контента блока

### ORM или функции для работы с сущностями

чтобы можно было модифицировать запрос через расширения

    ~~forum_add() ...~~
    ~~forum_get() ...~~
    ~~topic_add() ...~~
    ~~user_add() ...~~
    ...

### "фабрики" для создания объектов форума:

    db()
    ...

### события

передача по значению, возвращая значение может работать как фильтр

    event_trigger('page_before_render', $data)

    foreach ($_PUNBB['event'][$event] as $handler) {
        if (is_callable($handler)) {
            $result = call_user_func($handler, $data);
            if ($result !== false) {
                $data = $result;
                ???
            }
        }
    }

    event_add(event, handler) добавляет и возвращает индекс
    event_remove(event, index) - удаляет по индексу

хранениие

    $_PUNBB['event']['page_before_render'] = array(handler1, handler2, handler3)

обработчик

    function handler1($data) {
        return false; - останавливает цикл вызова обработчиков
        return $some_modified_data; - или возвращает какойто результат
        или лучше [$some_modified_data, false]
        если надо возвратить результат и остановить цикл
    }


### расширения

основной файл, подключается при загрузке ядра форума
    
    /extensions/myextension-name/init.php

- расширение в виде пакета к композеру, посмотреть - установку и распространение через git репозитарий
- настройка запуска композером команд install.php update.php uninstall.php при composer install, composer update ...
- web-интерфейс для управления расширениями - это будет просто интерфейс для команд композера - нужен ли?
- если возможно, предусмотреть установку без композера - т.е. компируется расширение в нужную папку, запускается install.php или update.php через браузер 

так же сделать как расширения
- языковые файлы
- темы
- сделать возможность изменения урл форума в виде расширения, выпилить текущий из форума

## Удалены некоторые хуки для шаблонов

    fn_redirect_pre_template_loaded
    fn_redirect_template_loaded
    fn_maintenance_message_pre_template_loaded
    fn_maintenance_message_template_loaded
    hd_pre_template_loaded
    hd_template_loaded
    hd_gen_elements
    hd_visit_elements
    hd_main_elements
    hd_end
    pf_change_pass_key_pre_errors
    pf_change_pass_normal_pre_errors
    pf_change_email_pre_errors
    pf_change_details_identity_pre_errors
    pf_change_details_signature_pre_errors
    pf_change_details_avatar_pre_errors
    rg_pre_register_errors
    aex_install_ext_pre_errors

## Структура темы

Блоки основного контента:

    style/some-template1/login/main.php
    style/some-template1/...

Общие макеты:
    
    style/some-template1/layout/main.php
    style/some-template1/layout/admin.php
    style/some-template1/layout/...

Хелперы:

    style/some-template1/helper/main_menu.php    
    style/some-template1/helper/...

Стандартные шаблоны:

    include/view/layout/...
    include/view/helper/...
    include/view/...

## Переопределение шаблонов

Проверяется подключаемый шаблон в папке текущей темы, например login/main
    
    style/template1/login/main.php
    
если такого файла нет, берется стандартный

    include/view/login/main.php
