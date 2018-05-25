<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use PHPUnit\Framework\TestCase;

class EmitterTest extends EventEmitterOld
{
}

class EventEmitterGlobalTest extends TestCase
{
    public function testEventEmitterGlobal() {
        $ee = new EmitterTest();

        $callback1 = function () { };
        $callback2 = function (Event $e) { $e->stopPropagation(); };

        EventEmitterGlobalOld::loadClassesEventListeners([
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
                            ], EventEmitterGlobalOld::getListeners());

        EventEmitterGlobalOld::setMaxListeners(0);
        $this->assertEquals(0, EventEmitterGlobalOld::getMaxListeners());

        EventEmitterGlobalOld::removeListener(EmitterTest::class, 'boo', ['Bar', 'baz']);
        $this->assertEquals([
                                EmitterTest::class => [
                                    'test0'  => [[false, $callback1]],
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobalOld::getListeners());
        EventEmitterGlobalOld::removeListener(EmitterTest::class, 'boo', ['Bar', 'baz']);
        $this->assertEquals([
                                EmitterTest::class => [
                                    'test0'  => [[false, $callback1]],
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobalOld::getListeners());

        EventEmitterGlobalOld::removeAllListeners(EmitterTest::class, 'test0');
        $this->assertEquals([
                                EmitterTest::class => [
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobalOld::getListeners());

        EventEmitterGlobalOld::removeAllListeners(EmitterTest::class, 'test0');
        $this->assertEquals([
                                EmitterTest::class => [
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, $callback2]],
                                ],
                            ], EventEmitterGlobalOld::getListeners());

        EventEmitterGlobalOld::removeListener(EmitterTest::class, 'boo', $callback2);
        $this->assertEquals([
                                EmitterTest::class => [
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                ],
                            ], EventEmitterGlobalOld::getListeners());
        EventEmitterGlobalOld::removeListener(EmitterTest::class, 'boo', $callback2);

        EventEmitterGlobalOld::removeAllListeners(EmitterTest::class);
        $this->assertEquals([
                                EmitterTest::class => [],
                            ], EventEmitterGlobalOld::getListeners());
        EventEmitterGlobalOld::removeAllListeners(EmitterTest::class);

        EventEmitterGlobalOld::on(EmitterTest::class, 'test', $callback1);
        EventEmitterGlobalOld::removeAllListeners(EmitterTest::class, 'test');
        EventEmitterGlobalOld::once(EmitterTest::class, 'test', $callback1);
        EventEmitterGlobalOld::prependListener(EmitterTest::class, 'test', $callback2);
        EventEmitterGlobalOld::prependOnceListener(EmitterTest::class, 'test', $callback2);
        $this->assertEquals([
                                EmitterTest::class => [
                                    'test' => [[true, $callback2], [false, $callback2], [true, $callback1]],
                                ],
                            ], EventEmitterGlobalOld::getListeners());

        EventEmitterGlobalOld::removeAllListeners(EmitterTest::class, 'test');
        EventEmitterGlobalOld::prependListener(EmitterTest::class, 'test', $callback2);
        EventEmitterGlobalOld::removeAllListeners(EmitterTest::class, 'test');
        EventEmitterGlobalOld::prependOnceListener(EmitterTest::class, 'test', $callback2);

        $ee->emit('test');
        EventEmitterGlobalOld::prependOnceListener(EmitterTest::class, 'test', $callback2);

        $ee = new class extends EventEmitterOld
        {
        };
        $ee->emit('test');//anonimous classes dews not generate global events;
    }

    public function testEventEmitterGlobalExceptionMaxListeners() {
        EventEmitterGlobalOld::setMaxListeners(2);

        $this->expectException(Exception\EventEmitter::class);
        EventEmitterGlobalOld::loadClassesEventListeners([
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
        EventEmitterGlobalOld::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test2' => [
                                                                  null,
                                                              ],
                                                          ],
                                                      ]);
    }

    public function testEventEmitterGlobalExceptionNegativeMaxListeners() {
        $this->expectException(\InvalidArgumentException::class);
        EventEmitterGlobalOld::setMaxListeners(-1);
    }

    public function testEventEmitterGlobalExceptionWrongMethod() {
        $this->expectException(\Error::class);
        EventEmitterGlobalOld::fooBar();
    }
}