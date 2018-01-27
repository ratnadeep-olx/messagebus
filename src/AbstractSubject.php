<?php
namespace messagebus;
abstract class AbstractSubject {
    abstract function attach(AbstractObserver $observer);
    abstract function detach(AbstractObserver $observer);
    abstract protected function notify($record);
}