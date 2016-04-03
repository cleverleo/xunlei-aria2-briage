<?php
/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 16/4/2
 * Time: 下午9:28
 */

set_exception_handler(function (Exception $e) {
    die(json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]));
});

require 'vendor/autoload.php';
require 'XunleiLixian.php';

if (!isset($_POST['url'])) {
    throw new Exception('The Url Is Required');
}

$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');

if (!isset($glb_option['result']['dir'])) {
    throw new Exception('The Aria2 Server Not Correct');
}

$gdriveid = XunleiLixian::getInfo()['gdriveid'];
$file_list = XunleiLixian::downloadByUrl($_POST['url']);


foreach ($file_list as $item) {
    $aria2->addUri([$item['download_link']], ['header' => ['Cookie: gdriveid=' . $gdriveid], 'out' => $item['file_path']]);
}

die(json_encode(['success' => true, 'file_count' => count($file_list)]));