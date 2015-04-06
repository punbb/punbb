
## TODO

### хелпер для языковых трансляций 

    $lang_common['No view'] -> __('No view', 'common') = __('No view')
    'common' - по умолчанию - тип "область" для констант
    в хелпере так же подгружает указанный файл констант

### пространство имен 

    namespace punbb;

### вынос глобальных переменных в $_PUNBB - глобальный "репозитарий"

### если получится легко - функции для работы с сущностями

forum_add() ...
forum_get() ...
topic_add() ...
user_add() ...
...

### "фабрики" для создания объектов форума:

    db()
    ...

### расширения

основной файл, подключается при загрузке ядра форума
    
    /extensions/myextension-name/init.php

- расширение в виде пакета к композеру, посмотреть - установку и распространение через git репозитарий
- настройка запуска композером команд install.php update.php uninstall.php при composer install, composer update ...
- web-интерфейс для управления расширениями - это будет просто интерфейс для команд композера - нужен ли?
- если возможно, предусмотреть установку без композера - т.е. компируется расширение в нужную папку, запускается install.php или update.php через браузер 

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
