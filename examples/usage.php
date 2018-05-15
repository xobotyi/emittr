<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

include_once __DIR__ . '/../vendor/autoload.php';

class ClassA extends EventEmitter
{
}

class ClassB extends EventEmitterStatic
{
}

var_dump(ClassA::getListeners('test'));
ClassB::on('test', function () { });
var_dump(ClassA::getListeners('test'));


ClassA::on('test', function ($e) { var_dump('1'); });
ClassA::on('test', function ($e) { var_dump('2'); });
ClassA::on('test', function ($e) { var_dump('3'); });
EventEmitterGlobal::loadClassesEventListeners(['xobotyi\emittr\ClassA' => ['test' => function ($e) { var_dump(4); }]]);

$a = new ClassA();
$a->on('test', function (Event $e) {
    var_dump($e->getSourceClass());
});
$a->emit('test');