
## TODO

### Языковые пакеты как расширения

- новый "домен" 'extension1' регистрируется (путь к файлу для загрузки добавляется в ассоциативный массив) через расширение типа "языковой пакет"
или может заменить путь по умолчанию
- константы для языковых доменов? 'common' == L_COMMON 'index' == L_INDEX 'userlist' == L_USERLIST

### вынос глобальных переменных в PUNBB::get PUNBB::set - глобальный "репозитарий"

### расширения

сделать как расширения
- темы
- сделать возможность изменения урл форума в виде расширения, выпилить текущий из форума

- расширения в виде пакетов composer вида punbb-extensionname
- пример composer.json файла 
{
  "name": "punbb/lang-English",
  "description": "PUNBB English language definitions",
  "authors": [
    {
      "name": "Your Name",
      "email": "your@name.com"
    }
  ],
  "autoload": {
    "files": [
        "init.php" <- тут иницализация расширения (настройка сервисов, добавление информации по расширению и т.п.).
    ]
  }
}

- в init.php файле задется настройка расширинения - тема, языковой пакет, рауты. может сожержать свой namespace, переменные. но настройка punbb только через методы класса PUNBB.
- настройка запуска композером команд install.php update.php uninstall.php при composer install, composer update 
- в простейшем случае - если нет настроек БД и т.п. скрипты типа install.php не нужны - просто как стандартный пакет композера
- web-интерфейс для управления расширениями - это будет просто интерфейс для команд композера - нужен ли?- если возможно, предусмотреть установку без композера - т.е. компируется расширение в нужную папку, запускается install.php или update.php через браузер 

### шаблоны

- view, helper - всегда выводит данные вместо записи в буфер - облегчит отладку и улучшит производительность
- возможность переопределить функции (может реализовать на классах с DI)
- переопределяющее расширение заботится об буферизации контента - если оно нужно
- событие на вывод страницы page_render

примеры использования - кеширование html-блоков, обработка контента блока

- возможность подмены макета через расширения ("layout_apply" ?)

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

## Структура темы

Блоки основного контента:

    /login/main.php
    /...

Общие макеты:
    
    /layout/main.php
    /layout/admin.php
    /layout/...

Хелперы:

    /helper/main_menu.php    
    /helper/...

Стандартные шаблоны:

    include/view/layout/...
    include/view/helper/...
    include/view/...

## Переопределение шаблонов

Проверяется подключаемый шаблон в папке текущей темы, например login/main
    
    /login/main.php
    
если такого файла нет, берется стандартный

    include/view/login/main.php

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
    li_pre_login_errors
    li_forgot_pass_pre_new_password_errors
    mi_pre_email_errors
    mi_pre_report_errors
    po_pre_post_errors
    fn_get_style_packs_end
    fn_get_language_packs_end
