<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use PHPUnit\Framework\TestCase;

class EventEmitterTest extends TestCase
{
    public function testEventEmitter() {
        $ee = new EventEmitter();

        $ee->setMaxListenersCount(5);
        $this->assertEquals(5, $ee->getMaxListenersCount());

        $callback1 = function () { };
        $callback2 = function (Event $e) { };

        $ee->once('test1', $callback1);
        $this->assertEquals(['test1'], $ee->getEventNames());
        $this->assertEquals([[true, $callback1]], $ee->getListeners('test1'));

        $ee->on('test1', $callback2);
        $this->assertEquals([[true, $callback1], [false, $callback2]], $ee->getListeners('test1'));

        $ee->off('test1', $callback2);
        $this->assertEquals([[true, $callback1]], $ee->getListeners('test1'));

        $ee->off('test1', $callback1);
        $this->assertEquals([], $ee->getListeners('test1'));
        $ee->off('test1', $callback1);

        $ee->on('test1', $callback2);
        $ee->prependOnceListener('test1', $callback1);
        $this->assertEquals([[true, $callback1], [false, $callback2]], $ee->getListeners('test1'));

        $ee->removeAllListeners('test1');
        $this->assertEquals([], $ee->getListeners('test1'));

        $ee->on('test1', $callback2);
        $ee->prependListener('test1', $callback1);
        $this->assertEquals([[false, $callback1], [false, $callback2]], $ee->getListeners('test1'));

        $ee->removeAllListeners('test0');
        $ee->removeAllListeners();
        $this->assertEquals([], $ee->getListeners('test1'));

        $ee->removeAllListeners();
        $this->assertEquals(EventEmitterGlobal::getInstance(), $ee->getGlobalEmitter());

        $ee->emit('test1');
    }

    public function testEventEmitterExceptionNegativeMaxListeners() {
        $ee = new EventEmitter();

        $this->expectException(\InvalidArgumentException::class);
        $ee->setMaxListenersCount(-1);
    }

    public function testEventEmitterExceptionMaxListeners() {
        $ee = new EventEmitter();
        $ee->setMaxListenersCount(2);

        $this->expectException(Exception\EventEmitter::class);
        $ee->on('test1', function (Event $e) { });
        $ee->on('test1', function (Event $e) { });
        $ee->on('test1', function (Event $e) { });
    }

    public function testEventEmitterExceptionNotCallable() {
        $this->expectException(Exception\EventEmitter::class);

        (new EventEmitter())->on('test2', null);
    }
}