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

ClassA::on('test', function ($e) { var_dump('1'); });
ClassA::on('test', function ($e) { var_dump('2'); });
ClassA::on('test', function ($e) { var_dump('3'); });
EventEmitterGlobal::loadClassesEventListeners(['xobotyi\emittr\ClassA' => ['test' => function ($e) { var_dump(4); }]]);

$a = new ClassA();
$a->on('test', function (Event $e) {
    var_dump('0', $e->getSourceClass());
});
$a->emit('test');