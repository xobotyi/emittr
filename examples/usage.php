<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

include_once __DIR__ . '/../vendor/autoload.php';

use xobotyi\emittr;

$globalEmitter = emittr\EventEmitterGlobal::getInstance();
$cb1           = function () { };
$cb2           = function (emittr\Event $event) { };

$globalEmitter->on('test', 'testEvt', $cb1)
              ->off('test', 'testEvt', $cb1);

var_dump($globalEmitter->getListeners());