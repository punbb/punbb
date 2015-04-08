<?php

// Language definitions used in install.php
return array(

// Install Form
'Install PunBB'				=> 'Установка PunBB %s',
'Choose language'           => 'Выбрать язык',
'Choose language help'      => 'Выбрать язык, на котором будет происходить установка форума',
'Installer language'        => 'Язык установки',
'Choose language legend'    => 'Язык установки',
'Install intro'				=> 'Для установки PunBB необходимо заполнить форму ниже. Внимательно прочитайте инструкции перед заполнением полей. Если возникают трудности во время установки, обратитесь к документации, входящей в состав установочного пакета PunBB.',
'Part1'						=> 'Часть 1 — Параметры базы данных',
'Part1 intro'				=> 'Для настройки соединения с базой данных необходимо точно узнать и ввести следующие параметры:',
'Database type'				=> 'Тип',
'Database name'				=> 'Имя',
'Database server'			=> 'Сервер',
'Database username'			=> 'Имя пользователя',
'Database password'			=> 'Пароль',
'Database user pass'		=> 'Имя пользователя и пароль пользователя базы данных',
'Table prefix'				=> 'Префикс таблиц',
'Database type info'		=> 'На текущий момент PunBB поддерживает MySQL, PostgreSQL и SQLite. Если тип вашей базы данных отсутствует в выпадающем меню, это означает, что PHP не поддерживает особенности вашей базы данных.',
'Mysql type info'			=> 'PunBB определил, что текущая сборка PHP поддерживает два разных способа связи с MySQL, «<em>Standart</em>» и «<em>Improved</em>». Если вы не уверены, какой из способов выбрать, попробуйте начать с «<em>Improved</em>»; если же он будет работать некорректно, оставьте «<em>Standart</em>».',
'Database server info'		=> 'Введите адрес сервера базы данных (например, <em>localhost</em>, <em>mysql1.example.com</em> или <em>208.77.188.166</em>). Можно назначить свой номер порта, если база недоступна по стандартному (например, <em>localhost:3580</em>). Для SQLite введите что угодно или оставьте <em>localhost</em>.',
'Database name info'		=> 'Введите имя базы данных, в которой будут размещены данные PunBB. Эта база уже должна существовать. Для SQLite это относительный путь до файла базы данных. Если файл базы данных SQLite не существует, PunBB попытается его создать.',
'Database username info'	=> 'Введите имя пользователя и пароль, используемые для подключения к базе. Для SQLite данный параметр не требуется.',
'Table prefix info'			=> 'Необязательный параметр — префикс для таблиц базы данных. Определив префикс (например, <em>foo_</em>), можно использовать несколько копий PunBB с одной базой данных.',
'Part1 legend'				=> 'Информация о базе данных',
'Database type help'		=> 'Выберите тип базы данных.',
'Database server help'		=> 'Адрес сервера базы данных. Для SQLite введите что угодно.',
'Database name help'		=> 'Существующая база данных, в которую будет установлен форум.',
'Database username help'	=> 'Для соединения с базой данных. Для SQLite не требуется.',
'Database password help'	=> 'Для соединения с базой данных. Для SQLite не требуется.',
'Table prefix help'			=> 'Необязательный префикс базы данных, например «foo_».',
'Part2'						=> 'Часть 2 — Настройка администратора форума',
'Part2 legend'				=> 'Параметры администратора',
'Part2 intro'				=> 'Введите необходимую информацию для настройки параметров администратора форума.',
'Admin username'			=> 'Имя администратора',
'Admin password'			=> 'Пароль администратора',
'Admin confirm password'	=> 'Подтвердить пароль',
'Admin e-mail'				=> 'Эл. почта',
'Username help'				=> 'От 2 до 25 символов.',
'Password help'				=> 'Минимум 4 символа. РеГиСтР уЧитыВаетСя.',
'Confirm password help'		=> 'Повторить пароль.',
'E-mail address help'		=> 'Адрес электронной почты администратора.',
'Part3'						=> 'Часть 3 — Настройки форума',
'Part3 legend'				=> 'Информация форума',
'Part3 intro'				=> 'Введите необходимую информацию. Обратите особое внимание на базовый URL и внимательно читайте пояснения ниже.',
'Base URL'					=> 'Базовый URL',
'Base URL info'				=> 'Обратите особое внимание на параметр «базовый URL». Необходимо правильно ввести его, иначе форум будет работать некорректно. Базовый URL — это интернет-адрес форума (например, <em>http://forum.example.com</em> или <em>http://example.com/~myuser</em>). Имейте ввиду, что изначально прописанное в этом поле значение — просто догадка PunBB.',
'Base URL help'				=> 'URL (без закрывающего слеша) форума.',
'Pun repository'			=> 'Репозиторий',
'Pun repository help'		=> 'Установка расширений из репозитория (загрузчик расширений «в один клик») после установки форума.',
'Start install'				=> 'Начать установку', // Label for submit button
'Required'					=> '(Обязательно)',
'Required warn'				=> 'Все поля, выделенные полужирным начертанием, должны быть заполнены.',

// Install errors
'No database support'		=> 'Ваша сборка PHP не поддерживает базу данных, совместимую с PunBB. Для продолжения установки необходима поддержка хотя бы одной — MySQL, PostgreSQL или SQLite.',
'Missing database name'		=> 'Вы должны ввести имя базы данных. Пожалуйста, вернитесь и исправьте ошибку.',
'Username too long'			=> 'Имена пользователей не могут быть длиннее 25 символов. Пожалуйста, вернитесь и исправьте ошибку.',
'Username too short'		=> 'Имена пользователей должны иметь длину не менее 2 символов. Пожалуйста, вернитесь и исправьте ошибку.',
'Pass too short'			=> 'Пароли должны иметь длину не менее 4 символов. Пожалуйста, вернитесь и исправьте ошибку.',
'Username guest'			=> 'Имя пользователя guest (гость) зарезервировано. Пожалуйста, вернитесь и исправьте ошибку.',
'Username BBCode'			=> 'Имена пользователей не могут содержать никаких тегов форматирования текста (BB-кодов), которые используются на форуме. Пожалуйста, вернитесь и исправьте ошибку.',
'Username reserved chars'	=> 'Имена пользователей не могут содержать символы \', " и [ или ] одновременно. Пожалуйста, вернитесь и исправьте ошибку.',
'Username IP'				=> 'Имена пользователей не могут быть записаны в форме IP-адреса. Пожалуйста, вернитесь и исправьте ошибку.',
'Invalid email'			    => 'Введённый адрес электронной почты администратора неверен. Пожалуйста, вернитесь и исправьте ошибку.',
'Missing base url'			=> 'Вы должны указать базовый URL. Пожалуйста, вернитесь и исправьте ошибку.',
'No such database type'		=> '\'%s\' неверный тип базы данных.',
'Invalid MySQL version'		=> 'Ваша версия MySQL — %1$s. Минимальные требования для корректной работы PunBB — MySQL %2$s. Вы должны обновить MySQL, прежде чем продолжать установку.',
'Invalid table prefix'		=>	'Префикс \'%s\' содержит недопустимые символы или слишком длинный. Префикс может содержать буквы от a до z, любые цифры и символ подчёркивания, однако он не должен начинаться с цифры. Максимальная длина — 40 символов. Пожалуйста, укажите другой префикс.',
'SQLite prefix collision'	=> 'Префикс \'sqlite_\' зарезервирован для использования ядром SQLite. Пожалуйста, укажите другой префикс.',
'PunBB already installed'	=> 'Таблица «%1$susers» уже существует в базе данных «%2$s». Это может означать, что PunBB уже установлен или установлено какое-то другое ПО, использующее одну или несколько таблиц, необходимых для работы PunBB. Если вы хотите установить несколько копий PunBB в одну базу данных, то вы должны указать другой префикс.',
'Invalid language'			=>	'Выбранный языковой пакет не существует или повреждён.',
'InnoDB Not Supported'		=> 'Ваша версия базы данных не поддерживает InnoDB таблицы.',

// Used in the install
'Default language'          => 'Язык по умолчанию',
'Default language help'     => 'Выбрать язык форума по умолчанию',
'Default announce heading'	=> 'Пример объявления',
'Default announce message'	=> '<p>Введите текст вашего объявления здесь.</p>',
'Default rules'				=> 'Введите правила здесь.',
'Default category name'		=> 'Тестовая категория',
'Default forum name'		=> 'Тестовый раздел',
'Default forum descrip'		=> 'Это просто тестовый раздел',
'Default topic subject'		=> 'Тестовый пост',
'Default post contents'		=> 'Если вы видите это сообщение (а я думаю, вы его видите), значит, установка PunBB прошла успешно! Теперь авторизируйтесь, зайдите в административную панель и конфигурируйте свой форум.',
'Default rank 1'			=> 'Новый участник',
'Default rank 2'			=> 'Участник',


// Installation completed form
'Success description'		=> 'Поздравляем! PunBB %s успешно установлен.',
'Final instructions'		=> 'Последние инструкции',
'No write info 1'			=> 'Важно! Для завершения установки вам необходимо нажать на кнопку, расположенную ниже, чтобы скачать файл под именем config.php. Затем вам нужно загрузить этот файл в корневую директорию вашего форума PunBB.',
'No write info 2'			=> 'Как только вы загрузите в корневую директорию файл config.php, PunBB будет полностью установлен! Как только файл будет загружен, вы можете перейти %s.',
'Go to index'				=> 'перейти к главной странице форума',
'Warning'					=> 'Внимание!',
'No cache write'			=> '<strong>Каталог cache недоступен для записи!</strong> Для корректной работы PunBB каталог <em>cache</em> должен быть доступен для записи. Используйте chmod, чтобы задать права доступа для каталога. Если сомневаетесь, установите chmod 0777.',
'No avatar write'			=> '<strong>Каталог avatar недоступен для записи!</strong> Если вы хотите, чтобы пользователи могли загружать собственные аватары, то должны убедиться, что каталог <em>img/avatars</em> доступен для записи. Позже вы сможете указать другую папку для хранения аватар (смотрите Администрирование/Настройки/Дополнительные возможности). Используйте chmod, чтобы задать права доступа для каталога. Если сомневаетесь, установите chmod 0777.',
'File upload alert'			=> '<strong>Загрузка файлов не разрешена на этом сервере!</strong> Если вы хотите, чтобы пользователи могли загружать собственные аватары, то должны включить параметр file_uploads в настройках PHP. Как только загрузка файлов будет разрешена, загрузка аватаров может быть включена в настройках Администрирование/Настройки/Дополнительные возможности.',
'Download config'			=> 'Скачать файл config.php', // Label for submit button
'Write info'				=> 'PunBB был полностью установлен! Теперь вы можете %s.',
);
