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
        $ee = new EmitterTest();

        $callback1 = function () { };
        $callback2 = function (Event $e) { };

        EventEmitterGlobal::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test0'  => $callback1,
                                                              'fooBar' => ['Bar', 'baz'],
                                                              'boo'    => [
                                                                  $callback2,
                                                                  ['Bar', 'baz'],
                                                              ],
                                                          ],
                                                      ]);

        $this->assertEquals([
                                EmitterTest::class => [
                                    'test0'  => [[false, $callback1]],
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2], [false, ['Bar', 'baz']]],
                                ],
                            ], EventEmitterGlobal::getListeners());

        EventEmitterGlobal::setMaxListeners(0);
        $this->assertEquals(0, EventEmitterGlobal::getMaxListeners());

        EventEmitterGlobal::removeListener(EmitterTest::class, 'boo', ['Bar', 'baz']);
        $this->assertEquals([
                                EmitterTest::class => [
                                    'test0'  => [[false, $callback1]],
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobal::getListeners());
        EventEmitterGlobal::removeListener(EmitterTest::class, 'boo', ['Bar', 'baz']);
        $this->assertEquals([
                                EmitterTest::class => [
                                    'test0'  => [[false, $callback1]],
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobal::getListeners());

        EventEmitterGlobal::removeAllListeners(EmitterTest::class, 'test0');
        $this->assertEquals([
                                EmitterTest::class => [
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobal::getListeners());

        EventEmitterGlobal::removeAllListeners(EmitterTest::class, 'test0');
        $this->assertEquals([
                                EmitterTest::class => [
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobal::getListeners());

        EventEmitterGlobal::removeListener(EmitterTest::class, 'boo', $callback2);
        $this->assertEquals([
                                EmitterTest::class => [
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                ],
                            ], EventEmitterGlobal::getListeners());
        EventEmitterGlobal::removeListener(EmitterTest::class, 'boo', $callback2);

        EventEmitterGlobal::removeAllListeners(EmitterTest::class);
        $this->assertEquals([
                                EmitterTest::class => [],
                            ], EventEmitterGlobal::getListeners());
        EventEmitterGlobal::removeAllListeners(EmitterTest::class);

        $ee->emit('test');
    }

    public function testEventEmitterGlobalExceptionMaxListeners() {
        EventEmitterGlobal::setMaxListeners(2);

        $this->expectException(Exception\EventEmitter::class);
        EventEmitterGlobal::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test1' => [
                                                                  function (Event $e) { },
                                                                  function (Event $e) { },
                                                                  function (Event $e) { },
                                                              ],
                                                          ],
                                                      ]);
    }

    public function testEventEmitterGlobalExceptionNotCallable() {
        $this->expectException(Exception\EventEmitter::class);
        EventEmitterGlobal::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test2' => [
                                                                  null,
                                                              ],
                                                          ],
                                                      ]);
    }

    public function testEventEmitterGlobalExceptionNegativeMaxListeners() {
        $this->expectException(\InvalidArgumentException::class);
        EventEmitterGlobal::setMaxListeners(-1);
    }

    public function testEventEmitterGlobalExceptionWrongMethod() {
        $this->expectException(\Error::class);
        EventEmitterGlobal::fooBar();
    }
}