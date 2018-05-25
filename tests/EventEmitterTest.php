<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use PHPUnit\Framework\TestCase;

class EventEmitterTestClass extends EventEmitterOld
{

}

class EventEmitterTest extends TestCase
{
    public function testEventEmitter() {
        $ee = new EventEmitterTestClass();

        $ee->setMaxListeners(5);
        $this->assertEquals(5, $ee->getMaxListeners());

        $callback1 = function () { };
        $callback2 = function (Event $e) { };

        $ee->once('test1', $callback1);
        $this->assertEquals(['test1'], $ee->getEventNames());
        $this->assertEquals([[true, $callback1]], $ee->getListeners('test1'));

        $ee->on('test1', $callback2);
        $this->assertEquals([[true, $callback1], [false, $callback2]], $ee->getListeners('test1'));

        $ee->removeListener('test1', $callback2);
        $this->assertEquals([[true, $callback1]], $ee->getListeners('test1'));

        $ee->removeListener('test1', $callback1);
        $this->assertEquals([], $ee->getListeners('test1'));
        $ee->removeListener('test1', $callback1);

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
    }

    public function testEventEmitterExceptionNegativeMaxListeners() {
        $ee = new EventEmitterTestClass();

        $this->expectException(\InvalidArgumentException::class);
        $ee->setMaxListeners(-1);
    }

    public function testEventEmitterExceptionUndefinedMethod() {
        $ee = new EventEmitterTestClass();

        $this->expectException(\Error::class);
        $ee->fooBar();
    }
}