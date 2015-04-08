<?php

// Language definitions used in all admin files
return array(

'Ban advanced'  				=> 'Расширенные настройки блокировок',
'Ban advanced heading'  		=> 'Блокировка по имени пользователя с IP-адресом и адресом электронной почты или просто по IP, по email или по обоим адресам',
'Ban criteria legend'   		=> 'Критерий блокировки',
'Ban settings legend'   		=> 'Настройки блокировок',
'Ban IP warning'				=> '<strong>Внимание!</strong> Будьте осторожны при блокировке по IP-диапазону, поскольку таким образом можно заблокировать много пользователей, имеющих такой же частичный IP.',
'Current ban head'  			=> 'Блокировку добавил %s',
'Username'  					=> 'Имя пользователя:',
'Username to ban label' 		=> 'Блокировать по имени',
'Ban creator'   				=> 'Создатель блокировки',
'IP-addresses to ban label' 	=> 'Блокировать по IP-адресу',
'IP-addresses help'				=>	'IP-адрес или IP-диапазон для блокировки (например, 150.11.110.1 или 150.11.110). Разделяйте адреса пробелами. Если IP-адрес уже введён, это - последний известный IP-адрес этого пользователя в базе данных.',
'IP-addresses help stats'   	=> 'Перейдите по ссылке, чтобы посмотреть IP-статистику по данному пользователю: ',
'IP-addresses help link'		=> 'IP-статистика пользователя',
'E-mail/domain to ban label'	=> 'Блокировать по эл. почте',
'E-mail/domain help'			=> 'Адрес электронной почты или домен для блокировки (например, someone@example.ru или example.ru). Также смотрите пункт «Разрешить регистрацию с заблокированных адресов электронной почты» в Настройках&nbsp;&rarr;&nbsp;Регистрация для более подробной информации.',
'Ban message label' 			=> 'Сообщение заблокированным',
'Ban message help'  			=> 'Показывается заблокированному пользователю, когда он посетит форум',
'Expire date label' 			=> 'Срок блокировки',
'Expire date help'  			=> 'Дата, когда блокировка будет автоматически удалена (формат: ГГГГ-ММ-ДД, например 2008-12-28). Оставьте пустым, если хотите удалить блокировку вручную.',
'Expires'   					=> 'Срок:',
'Message'   					=> 'Сообщение:',
'New ban heading'   			=> 'Заблокировать пользователя по имени',
'New ban legend'				=> 'Новая блокировка',
'Advanced ban info'				=>	'На следующей странице введите IP и адреса электронной почты. Если необходимо блокировать определённый IP-адрес, IP-диапазон или адрес электронной почты, то оставьте поле на этой странице пустым.',
'Existing bans heading' 		=> 'Редактирование или удаление блокировок',
'Add ban'   					=> 'Добавить блокировку',
'Save ban'  					=> 'Сохранить блокировку',
'E-mail'						=> 'Эл. почта:',
'IP-ranges' 					=> 'IP / IP-диапазон:',
'Reason'						=> 'Причина',
'No bans'   					=> 'Список блокировок пуст.',
'Edit ban'  					=> 'Редактировать блокировку',
'Remove ban'					=> 'Удалить блокировку',
'Edit or remove'				=> '%s или %s',
'Ban removed'   				=> 'Блокировка удалена.',
'Ban added' 					=> 'Блокировка добавлена.',
'Ban edited'					=> 'Блокировка отредактирована.',
'No user id message'			=> 'Нет зарегистрированных пользователей с этим ID.',
'No user username message'  	=> 'Нет зарегистрированных пользователей с этим именем. Если нужно добавить блокировку, не привязанную к имени пользователя, оставьте поле пустым.',
'User is admin message' 		=> 'Этот пользователь — администратор и не может быть заблокирован. Если необходимо заблокировать администратора, то сначала нужно перевести его в другую группу.',
'Must enter message'			=> 'Необходимо ввести хотя бы один из параметров блокировки: имя пользователя, IP-адрес или адрес электронной почты.',
'Invalid IP message'			=>	'Введён неправильный IP-адрес или IP-диапазон.',
'Can\'t ban guest user'			=> 'Гость не может быть заблокирован.',
'Invalid e-mail message'		=>	'Введён неправильный адрес электронной почты или домен адреса электронной почты.',
'Invalid expire message'		=>	'Введён неправильный срок блокировки. Формат должен быть «ГГГГ-ММ-ДД» и дата должна быть хотя бы на один день больше сегодняшней.',

);
