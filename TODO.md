
## Удалены хуки для шаблонов

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

## Структура шаблонов

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

## Наследование шаблонов 

Cтруктура полностью повторяет заданную структуру шаблонов форума, например:

    style/some-template1/profile/profile.twig
    
или
    
    style/template1/profile/profile.php
    
если такого файла нет, берется стандартный

    include/view/profile/profile.php

## TODO Сделать пример интеграции стороннего шаблонизатора (twig)

? Враппер для шаблонизатора размещается в:
    
    style/some-template1/render.php

Допилить функции чтобы учитывал шаблонизатор
    function view($name)
    function helper($name)
