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
    <?php if(!isDev):?>
        <?php Asset::getInstance()->addCss(TEMPLATE_LAYOUT .'/dist/main.css')?>
        <?php Asset::getInstance()->addJs(TEMPLATE_LAYOUT . '/dist/main.js')?>
    <?php else:?>
        <script defer type="module" src="http://localhost:5173/@vite/client"></script>
        <script defer type="module" src="http://localhost:5173/src/main.js"></script>
    <?php endif;?>
    <title><?php $APPLICATION->ShowTitle()?></title>
    <?php $APPLICATION->ShowHead();?>
</head>
<body>
<div id="bxpanel" style="position: fixed; z-index: 9999; bottom: 0;right: 0;">
    <?php $APPLICATION->ShowPanel();?>
</div>