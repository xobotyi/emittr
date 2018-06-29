<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use PHPUnit\Framework\TestCase;
use xobotyi\emittr\Traits\EventEmitterStatic;

class StaticEmitter
{
    use EventEmitterStatic;
}

class EventEmitterStaticTest extends TestCase
{
    public function testTrait() {
        // just call methods for coverage percent,
        // because because trait just proxy static call to non-static.

        $cb = function () { };

        StaticEmitter::prependListener('testEvent', $cb);
        StaticEmitter::prependOnceListener('testEvent', $cb);
        StaticEmitter::on('testEvent', $cb);
        StaticEmitter::once('testEvent', $cb);
        StaticEmitter::off('testEvent', $cb);
        StaticEmitter::removeAllListeners();
        StaticEmitter::getListeners();
        StaticEmitter::getMaxListenersCount();
        StaticEmitter::setMaxListenersCount(0);
        StaticEmitter::setEventEmitter(new EventEmitter());
        StaticEmitter::setGlobalEmitter(new GlobalEventHandler());
        StaticEmitter::getGlobalEmitter();

        $res = '';
        StaticEmitter::on('testEvent', function (Event $e) use (&$res) { $res = $e->getSourceClass(); });
        StaticEmitter::emit('testEvent');

        $this->assertEquals(StaticEmitter::class, $res);
    }
}