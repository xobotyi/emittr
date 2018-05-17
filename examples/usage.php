<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

include_once __DIR__ . '/../vendor/autoload.php';

use xobotyi\emittr;

class AwesomeClass extends emittr\EventEmitter
{
}

AwesomeClass::on('testEvent', function (emittr\Event $e) { echo $e->getPayload()['message'] . PHP_EOL; });

$awesomeObject = new AwesomeClass();
$awesomeObject->on('testEvent', function () { echo "Hello world!" . PHP_EOL; });

$awesomeObject->emit('testEvent', ['message' => "eittr is awesome!"]);

emittr\EventEmitterGlobal::on("MostAwesomeClass", "testEvent", 'awesomeCallback');

function awesomeCallback() { echo "eittr is awesome for sure!" . PHP_EOL; }

class MostAwesomeClass extends emittr\EventEmitterStatic
{
}

MostAwesomeClass::on('testEvent', function () { echo PHP_EOL . "Hello world!" . PHP_EOL; });
MostAwesomeClass::emit('testEvent');