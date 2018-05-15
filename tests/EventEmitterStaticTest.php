<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

use PHPUnit\Framework\TestCase;

class EventEmitterStaticTest extends TestCase
{
    public function testEventEmitterStaticAddRemove() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $eventName = '';

        $onListenerAdded   = function (Event $e) use (&$eventName) {
            $eventName = $e->getEventName();
            $this->assertEquals(EventEmitterStatic::EVENT_LISTENER_REMOVED, $e->getPayload()['eventName']);
        };
        $onListenerRemoved = function (Event $e) use (&$eventName) { $eventName = ''; };

        $ee::removeListener($ee::EVENT_LISTENER_ADDED, $onListenerAdded);
        $ee::removeAllListeners();
        $ee::on($ee::EVENT_LISTENER_ADDED, $onListenerAdded);
        $this->assertEquals([$ee::EVENT_LISTENER_ADDED], $ee::getEventNames());

        $ee::once($ee::EVENT_LISTENER_REMOVED, $onListenerRemoved);
        $this->assertEquals(
            [
                $ee::EVENT_LISTENER_ADDED   => [[false, $onListenerAdded]],
                $ee::EVENT_LISTENER_REMOVED => [[true, $onListenerRemoved]],
            ],
            $ee::getListeners());
        $this->assertEquals([[false, $onListenerAdded]],
                            $ee::getListeners($ee::EVENT_LISTENER_ADDED));

        $this->assertEquals($ee::EVENT_LISTENER_ADDED, $eventName);

        $ee::removeListener($ee::EVENT_LISTENER_ADDED, $onListenerAdded);
        $this->assertEquals('', $eventName);

        $ee::setMaxListeners(5);
        $this->assertEquals(5, $ee::getMaxListeners());

        $ee::on($ee::EVENT_LISTENER_ADDED, $onListenerAdded);
        $ee::removeAllListeners($ee::EVENT_LISTENER_ADDED);
        $ee::removeAllListeners();
    }

    public function testEventEmitterEventPropagationStop() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $res = '';

        $cb1 = function (Event $e) use (&$res) {
            $res .= '1';

            if ($e->getPayload()['stop']) {
                $e->stopPropagation();
            }
        };
        $cb2 = function (Event $e) use (&$res) {
            $res .= '2';
        };

        $ee::on('testEvent', $cb1);
        $ee::on('testEvent', $cb2);

        $res = '';
        $ee::emit('testEvent', ['stop' => false]);
        $this->assertEquals('12', $res);

        $res = '';
        $ee::emit('testEvent', ['stop' => true]);
        $this->assertEquals('1', $res);
    }

    public function testEventEmitterException() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $this->expectException(\Error::class);
        $ee::SomeStuffCall();
    }

    public function testEventEmitterExceptionOn() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $cb1 = function (Event $e) { };

        $ee::setMaxListeners(2);
        $ee::on('test', $cb1);
        $ee::on('test', $cb1);

        $this->expectException(Exception\EventEmitter::class);
        $ee::on('test', $cb1);
    }

    public function testEventEmitterExceptionOnce() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $cb1 = function (Event $e) { };

        $ee::setMaxListeners(2);
        $ee::once('test', $cb1);
        $ee::once('test', $cb1);

        $this->expectException(Exception\EventEmitter::class);
        $ee::once('test', $cb1);
    }

    public function testEventEmitterExceptionPrependListener() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $cb1 = function (Event $e) { };

        $ee::setMaxListeners(2);
        $ee::prependListener('test', $cb1);
        $ee::prependListener('test', $cb1);

        $this->expectException(Exception\EventEmitter::class);
        $ee::prependListener('test', $cb1);
    }

    public function testEventEmitterExceptionPrependOnceListener() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $cb1 = function (Event $e) { };

        $ee::setMaxListeners(2);
        $ee::prependOnceListener('test', $cb1);
        $ee::prependOnceListener('test', $cb1);

        $this->expectException(Exception\EventEmitter::class);
        $ee::prependOnceListener('test', $cb1);
    }

    public function testEventEmitterExceptionNegativeMaxListeners() {
        $ee = new class extends EventEmitterStatic
        {
        };

        $this->expectException(\InvalidArgumentException::class);
        $ee::setMaxListeners(-5);
    }
}