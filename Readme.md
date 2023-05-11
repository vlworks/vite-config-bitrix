# Vite + Bitrix (PHP, Bitrix template)

Данные проект демонстрирует структуру и настройки Vite для разработки шаблонов на Bitrix (возможно будет дополняться и исправляться)

### **Для верстки использовать правила указанные в разделе Статичные файлы, .env и остальные настройки используются по умолчанию**

```json
{
   "scripts": {
      "dev": "vite",
      "build": "vite build"
   }
}
```

### **Для работы с PHP и локальным сервером ознакомится со всеми разделами**
```json
{
   "scripts": {
      "dev:bitrix": " vite --config vite.bitrix.config.js",
      "build:bitrix": "vite build --config vite.bitrix.config.js"
   }
}
```

### Require dependence
> package.json // Смотри тут

## Структура
```
/local
   /templates
      /your-template
         /components
         /layout
            /dist
            /public
               /fonts
               /logo.svg
            /src
            ...
```

## Статичные файлы

Статичные файлы лежат в папке /public. Все пути для подключения должны быть абсолютными. 

Для файлов /public/fonts/font.wott или /public/logo.svg
```scss
html {
  background: url(/logo.svg); // -> /local/templates/vite/layout/public/logo.svg
}

@font-face {
   font-family: 'YourFont';
   src: url('/fonts/font.wott'); // -> /local/templates/vite/layout/public/fonts/font.wott

}
```
.env
> TEMPLATE_PUBLIC_PATH="/local/templates/vite/layout/public/"

vite.bitrix.config.js
> base: process.env.TEMPLATE_PUBLIC_PATH,
 
## Версия для продакшн

В продакшене на выходе получаем папку /dist с файлами main.js и main.css. Их и требуется подключать в header.php.

```PHP
<? const TEMPLATE_LAYOUT = '/local/templates/your-template/layout' // или использовать глобальную переменную в php_interface ?>
<head>
    <?php Asset::getInstance()->addCss(TEMPLATE_LAYOUT .'/dist/main.css')?>
    <?php Asset::getInstance()->addJs(TEMPLATE_LAYOUT . '/dist/main.js')?>
</head>
```

Имена файлов на выходе можно изменить или добавить manifest.json из которого брать по ключам нужные файлы с хешом (я не использую, битрикс сам добавляет версионность от кэша)
```js
output: {
    entryFileNames: 'main.js',
    assetFileNames: 'main.css',
},
```

Другие настройки
```js
assetsDir: '.', // результаты сборки и файлы ассетов в корневую папку - dist
copyPublicDir: false, // не копируем файлы из папки /public, пути сформируются из переменной base. см.выше Статичные файлы
```

## Версия для разработки

Тут для работы HMR требуется подключить клиент vite и файл main.js в header.php

Так же важно чтобы адрес хоста и порт не менялись при запуске dev билда в vite. Для этого используем в конфиге Vite:
```js
server: {
    strictPort: true
}
```

header.php
```html
<head>
    <script defer type="module" src="http://localhost:5173/@vite/client"></script>
    <script defer type="module" src="http://localhost:5173/src/main.js"></script>
</head>
```

## ЗАКЛЮЧЕНИЕ

По факту остается проблема с файлом header.php, в одном случае мы подключаем клиент Vite, в другом бандлы из папки /dist.

### Первое решение 

Конечный файл header.php

```php
<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

/**
 * @var object $APPLICATION
 */
const TEMPLATE_LAYOUT = '/local/templates/vite/layout';
require_once __DIR__ . '/vite-mode.php';
?>

<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if(isDev):?>
        <script defer type="module" src="http://localhost:5173/@vite/client"></script>
        <script defer type="module" src="http://localhost:5173/src/main.js"></script>
    <?php else:?>
        <?php Asset::getInstance()->addCss(TEMPLATE_LAYOUT .'/dist/main.css')?>
        <?php Asset::getInstance()->addJs(TEMPLATE_LAYOUT . '/dist/main.js')?>
    <?php endif;?>
    <title><?php $APPLICATION->ShowTitle()?></title>
    <?php $APPLICATION->ShowHead();?>
</head>
<body>
<div id="bxpanel" style="position: fixed; z-index: 9999; bottom: 0;right: 0;">
    <?php $APPLICATION->ShowPanel();?>
</div>
```

1. Константа из подключаемого файла ``vite-mode.php`` по умолчанию установлена в true ``isDev = true``
2. По умолчанию сборка происходит для дев разработки и выполняется условие с подключением клиента Vite
3. На продакшене я использую pipeline, в конфигурации которого указано, при деплое копировать на хост файл ``vite-mode-production.php`` переименовывая его в ``vite-mode.php``
4. В ``vite-mode-production.php`` установлена константа в false ``isDev = false``


p.s:

В интернете еще есть решения связанной с проверкой с помощью curl, поднят ли сервер Vite и подключением его клиента...
