<h1 align="center">emittr</h1>
<p align="center">
    <a href="https://packagist.org/packages/xobotyi/emittr">
        <img alt="License" src="https://poser.pugx.org/xobotyi/emittr/license" />
    </a>
    <a href="https://packagist.org/packages/xobotyi/emittr">
        <img alt="PHP 7 ready" src="http://php7ready.timesplinter.ch/xobotyi/emittr/badge.svg" />
    </a>
    <a href="https://travis-ci.org/xobotyi/emittr">
        <img alt="Build Status" src="https://travis-ci.org/xobotyi/emittr.svg?branch=master" />
    </a>
    <a href="https://www.codacy.com/app/xobotyi/emittr">
        <img alt="Codacy Grade" src="https://api.codacy.com/project/badge/Grade/dc9745b910be457fa3f7e803abbc5208" />
    </a>
    <a href="https://scrutinizer-ci.com/g/xobotyi/emittr/">
        <img alt="Scrutinizer Code Quality" src="https://scrutinizer-ci.com/g/xobotyi/emittr/badges/quality-score.png?b=master" />
    </a>
    <a href="https://www.codacy.com/app/xobotyi/emittr">
        <img alt="Codacy Coverage" src="https://api.codacy.com/project/badge/Coverage/dc9745b910be457fa3f7e803abbc5208" />
    </a>
    <a href="https://packagist.org/packages/xobotyi/emittr">
        <img alt="Latest Stable Version" src="https://poser.pugx.org/xobotyi/emittr/v/stable" />
    </a>
    <a href="https://packagist.org/packages/xobotyi/emittr">
        <img alt="Total Downloads" src="https://poser.pugx.org/xobotyi/emittr/downloads" />
    </a>
</p>

## About
emittr is a small dependency free 7.1+ library that gives you events functionality. It wont bring asyncronious execution, but will let you the best way to separate your code.    
The main feature of emittr is global emitter with which you will be able to assign event callbacks even if class weren't autoloaded yet.  
Library uses PSR-4 autoloader standard and always has 100% tests coverage.

## Why emittr
1. It is well documented
2. I'm eating my own sweet pie=)
3. Assign event callbacks even if class wasn't yet autoloaded or even declared
4. Ability to stop the event propagation
5. Static and non-static variations of event emitter  

Emittr's key feature is GlobalEventHandler which allow you to assign event listeners event if nor event emitter nor it's listener wasn't declared yet.  
During lifecycle, event firstly propagates through all listeners assigned over it's emitter and then through listeners assigned over global handler, if event propagation wasn't stopped by any listener. 

## Requirements
* [PHP](https://php.net/) 7.1+

## Installation
Install it with composer
```bash
composer require xobotyi/emittr
```

## Usage
```php
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
```
or you can use global emitter to assign handler
```php
use xobotyi\emittr;

// note that neiter MostAwesomeClass class nor awesomeCallback function was not defined yet!
emittr\GlobalEventHandler::getInstance()
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
```

## Index
#### class \xobotyi\emittr\Event  
- _function_ **stopPropagation**() :self;
- _function_ **startPropagation**() :self;
- _function_ **isPropagatable**() :bool;
- _function_ **getEventName**() :string;
- _function_ **getPayload**() :mixed;
- _function_ **getSourceObject**() :mixed;
- _function_ **getSourceClass**() :?string
    
#### class \xobotyi\emittr\EventEmitter  
- _function_ **emit**($event, $payload = null) :self;
- _function_ **on**(string $eventName, $callback) :self;
- _function_ **once**(string $eventName, $callback) :self;
- _function_ **prependListener**(string $eventName, $callback) :self;
- _function_ **prependOnceListener**(string $eventName, $callback) :self;
- _function_ **off**(string $eventName, $callback) :self;
- _function_ **removeAllListeners**(?string $eventName = null) :self;
- _function_ **getEventNames**() :array;
- _function_ **getListeners**(?string $eventName = null) :array;
- _function_ **getMaxListenersCount**() :int;
- _function_ **setMaxListenersCount**(int $maxListenersCount) :self;
- _function_ **getGlobalEmitter**() :Interfaces\GlobalEventHandler;
- _function_ **setGlobalEmitter**(Interfaces\GlobalEventHandler $emitterGlobal) :self;
    
#### trait \xobotyi\emittr\Traits\EventEmitterStatic  
- _static function_ **getEventEmitter**() :EventEmitter;
- _static function_ **setEventEmitter**(EventEmitter $eventEmitter) :EventEmitter;
- _static function_ **emit**(string $eventName, $payload = null) :void;
- _static function_ **on**(string $eventName, $callback) :void;
- _static function_ **once**(string $eventName, $callback) :void;
- _static function_ **prependListener**(string $eventName, $callback) :void;
- _static function_ **prependOnceListener**(string $eventName, $callback) :void;
- _static function_ **off**(string $eventName, $callback);
- _static function_ **removeAllListeners**(?string $eventName = null) :void;
- _static function_ **getListeners**(?string $eventName = null) :array;
- _static function_ **getMaxListenersCount**() :int;
- _static function_ **setMaxListenersCount**(int $maxListenersCount) :void;
- _static function_ **getGlobalEmitter**() :GlobalEventHandler;
- _static function_ **setGlobalEmitter**(GlobalEventHandler $emitterGlobal) :void;
    
#### class \xobotyi\emittr\GlobalEventHandler  
- _static function_ **getInstance**() :self;
- _static function_ **propagateEvent**(Event $event, array &$eventsListeners) :bool;
- _static function_ **isValidCallback**($callback) :bool;
- _static function_ **storeCallback**(array &$arrayToStore, string $eventName, $callback, int $maxListeners = 10, bool $once = false, bool $prepend = false) :void;
- _function_ **loadListeners**(array $listeners) :self;
- _function_ **on**(string $className, string $eventName, $callback) :self;
- _function_ **once**(string $className, string $eventName, $callback) :self;
- _function_ **prependListener**(string $className, string $eventName, $callback) :self;
- _function_ **prependOnceListener**(string $className, string $eventName, $callback) :self;
- _function_ **off**(string $className, string $eventName, $callback) :self;
- _function_ **removeAllListeners**(?string $className = null, ?string $eventName = null) :self;
- _function_ **getEventNames**(string $className) :array;
- _function_ **getListeners**(?string $className = null, ?string $eventName = null) :array;
- _function_ **getMaxListenersCount**() :int;
- _function_ **setMaxListenersCount**(int $maxListenersCount) :self;
- _function_ **propagateEventGlobal**(Event $event) :bool;