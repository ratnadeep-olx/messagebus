<?php
namespace messagebus;
abstract class AbstractObserver {
    abstract function update($record);
}