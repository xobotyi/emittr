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

$awesomeObject = new AwesomeClass();

$awesomeObject
    ->once('testEvent', function (emittr\Event $e) {
        echo $e->getPayload()['message'] . PHP_EOL;
        $e->stopPropagation();
    })
    ->once('testEvent', function () { echo "emittr is awful!" . PHP_EOL; });

$awesomeObject->emit('testEvent', ['message' => "eittr is awesome!"]);

// global event handling
emittr\EventEmitterGlobal::getInstance()
                         ->once("MostAwesomeClass", 'testEvent', 'awesomeCallback')
                         ->once("MostAwesomeClass", 'testEvent', 'nonAwesomeCallback');

class MostAwesomeClass
{
    use emittr\Traits\EventEmitterStatic;
}

function awesomeCallback(emittr\Event $e) {
    echo $e->getPayload()['message'] . PHP_EOL;
    $e->stopPropagation();
}

function nonAwesomeCallback() { echo "emittr is awful!" . PHP_EOL; }

MostAwesomeClass::emit('testEvent', ['message' => "eittr is awesome!"]);