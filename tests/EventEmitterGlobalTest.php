<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use PHPUnit\Framework\TestCase;

class EmitterTest extends EventEmitter
{
}

class EventEmitterGlobalTest extends TestCase
{
    public function testEventEmitterGlobal() {
        $globalEmitter = new EventEmitterGlobal();

        $globalEmitter->setMaxListenersCount(5);
        $this->assertEquals(5, $globalEmitter->getMaxListenersCount());
        $globalEmitter->setMaxListenersCount(0);

        $cb1 = function () { };
        $cb2 = function (Event $e) { };
        $cb3 = ['className', 'methodName'];

        $globalEmitter->once(EmitterTest::class, 'test0', $cb1);
        $globalEmitter->on(EmitterTest::class, 'test1', $cb2);

        $this->assertEquals(['test0', 'test1'], $globalEmitter->getEventNames(EmitterTest::class));
    }

    public function testEventEmitterGlobalOff() {
        $globalEmitter = new EventEmitterGlobal();

        $cb1 = function () { };
        $cb2 = function (Event $e) { };
        $cb3 = ['className', 'methodName'];

        $globalEmitter->once(EmitterTest::class, 'test0', $cb3);
        $globalEmitter->on(EmitterTest::class, 'test0', $cb2);
        $globalEmitter->on(EmitterTest::class, 'test0', $cb1);
        $globalEmitter->once(EmitterTest::class, 'test0', $cb1);
        $globalEmitter->on(EmitterTest::class, 'test1', $cb2);
        $globalEmitter->on(EmitterTest::class, 'test1', $cb3);

        $globalEmitter->off(EmitterTest::class, 'test0', $cb1);
        $this->assertEquals([[true, $cb3], [false, $cb2],],
                            $globalEmitter->getListeners(EmitterTest::class, 'test0'));

        $globalEmitter->off(EmitterTest::class, 'test0', $cb3);
        $this->assertEquals([[false, $cb2],], $globalEmitter->getListeners(EmitterTest::class, 'test0'));

        $globalEmitter->off(EmitterTest::class, 'test0', $cb2);
        $this->assertEquals([], $globalEmitter->getListeners(EmitterTest::class, 'test0'));

        $globalEmitter->off(EmitterTest::class, 'test0', $cb2);
        $this->assertEquals([], $globalEmitter->getListeners(EmitterTest::class, 'test0'));

        $globalEmitter->off(EmitterTest::class, 'test1', $cb2);
        $globalEmitter->off(EmitterTest::class, 'test1', $cb3);
        $this->assertEquals([], $globalEmitter->getListeners(EmitterTest::class));
    }

    public function testEventEmitterGlobalPrependEvents() {
        $globalEmitter = new EventEmitterGlobal();

        $cb1 = function () { };
        $cb2 = function (Event $e) { };
        $cb3 = ['className', 'methodName'];

        $globalEmitter->prependListener(EmitterTest::class, 'test0', $cb3);
        $globalEmitter->prependOnceListener(EmitterTest::class, 'test0', $cb2);
        $globalEmitter->prependListener(EmitterTest::class, 'test0', $cb1);

        $this->assertEquals([[false, $cb1], [true, $cb2], [false, $cb3]],
                            $globalEmitter->getListeners(EmitterTest::class, 'test0'));

        $globalEmitter->removeAllListeners();
        $globalEmitter->prependOnceListener(EmitterTest::class, 'test0', $cb2);
        $globalEmitter->prependListener(EmitterTest::class, 'test0', $cb3);

        $this->assertEquals([[false, $cb3], [true, $cb2]],
                            $globalEmitter->getListeners(EmitterTest::class, 'test0'));
    }

    public function testEventEmitterGlobalRemoveAllListeners() {
        $globalEmitter = new EventEmitterGlobal();

        $cb1 = function () { };
        $cb2 = function (Event $e) { };
        $cb3 = ['className', 'methodName'];

        $globalEmitter->on(EmitterTest::class, 'test1', $cb1);
        $globalEmitter->on(EmitterTest::class, 'test2', $cb2);

        $globalEmitter->removeAllListeners(EmitterTest::class, 'test1');
        $this->assertEquals(['test2' => [[false, $cb2]]],
                            $globalEmitter->getListeners(EmitterTest::class));

        $globalEmitter->removeAllListeners(EmitterTest::class, 'test1');
        $this->assertEquals(['test2' => [[false, $cb2]]],
                            $globalEmitter->getListeners(EmitterTest::class));

        $globalEmitter->removeAllListeners(EmitterTest::class, 'test2');
        $this->assertEquals([],
                            $globalEmitter->getListeners(EmitterTest::class));

        $globalEmitter->on(EmitterTest::class, 'test1', $cb1);
        $globalEmitter->on(EmitterTest::class, 'test2', $cb2);

        $globalEmitter->removeAllListeners(EmitterTest::class);
        $this->assertEquals([],
                            $globalEmitter->getListeners(EmitterTest::class));
        $globalEmitter->removeAllListeners(EmitterTest::class);
    }

    public function testEventEmitterGlobalEventEmission() {
        $globalEmitter = new EventEmitterGlobal();
        $ee            = new EmitterTest($globalEmitter);
        $res           = '';

        $ee->once('testEvent', function (Event $e) { $e->getPayload()['res'] .= '1'; });
        $globalEmitter->once(EmitterTest::class, 'testEvent', function (Event $e) { $e->getPayload()['res'] .= '2'; });

        $ee->emit('testEvent', ['res' => &$res]);
        $this->assertEquals('12', $res);

        $res = '';
        $ee->emit('testEvent', ['res' => &$res]);
        $this->assertEquals('', $res);

        $res = '';
        $ee->once('testEvent', function (Event $e) {
            $e->getPayload()['res'] .= '1';
            $e->stopPropagation();
        });
        $globalEmitter->once(EmitterTest::class, 'testEvent', function (Event $e) { $e->getPayload()['res'] .= '2'; });
        $ee->emit('testEvent', ['res' => &$res]);
        $this->assertEquals('1', $res);

        $anonymousEE = new class extends EventEmitter
        {
        };
        $anonymousEE->emit('test'); // anonymous class wont trigger global event;
    }

    public function testEventEmitterGlobalExceptionMaxListeners() {
        $globalEmitter = (new EventEmitterGlobal())->setMaxListenersCount(2);

        $this->expectException(Exception\EventEmitter::class);
        $globalEmitter->on(EmitterTest::class, 'test1', function (Event $e) { });
        $globalEmitter->on(EmitterTest::class, 'test1', function (Event $e) { });
        $globalEmitter->on(EmitterTest::class, 'test1', function (Event $e) { });
    }

    public function testEventEmitterGlobalExceptionNotCallable() {
        $this->expectException(Exception\EventEmitter::class);
        (new EventEmitterGlobal())->on(EmitterTest::class, 'test2', null);
    }

    public function testEventEmitterGlobalExceptionNegativeMaxListeners() {
        $this->expectException(\InvalidArgumentException::class);
        (new EventEmitterGlobal())->setMaxListenersCount(-1);
    }
}