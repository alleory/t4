<?php

namespace T4\Orm\Extensions;

use T4\Core\Std;
use T4\Orm\Extension;
use T4\Orm\Model;

class Standard
    extends Extension
{

    public static function hasMagicStaticMethod($method)
    {
        switch (true) {
            case preg_match('~^findAllBy(.+)$~', $method):
                return true;
            case preg_match('~^findBy(.+)$~', $method):
                return true;
            case preg_match('~^countAllBy(.+)$~', $method):
                return true;
        }
        return false;
    }

    public static function __callStatic($method, $argv)
    {
        /** @var \T4\Orm\Model $class */
        $class = $argv[0];
        array_shift($argv);
        switch (true) {
            case preg_match('~^findAllBy(.+)$~', $method, $m):
                return $class::findAllByColumn(lcfirst($m[1]), $argv[0], isset($argv[1]) ? $argv[1] : []);
                break;
            case preg_match('~^findBy(.+)$~', $method, $m):
                return $class::findByColumn(lcfirst($m[1]), $argv[0], isset($argv[1]) ? $argv[1] : []);
                break;
            case preg_match('~^countAllBy(.+)$~', $method, $m):
                return $class::countAllByColumn(lcfirst($m[1]), $argv[0], isset($argv[1]) ? $argv[1] : []);
                break;
        }
    }

    public function hasMagicDynamicMethod($method)
    {
        switch (true) {
            case preg_match('~^set(.+)$~', $method):
                return true;
        }
        return false;
    }

    public function __call($method, $argv)
    {
        /** @var \T4\Orm\Model $model */
        $model = $argv[0];
        array_shift($argv);
        switch (true) {
            case preg_match('~^set(.+)$~', $method, $m):
                $column = lcfirst($m[1]);
                $model->{$column} = $argv[0];
                return $model;
                break;
        }
    }

    public function afterFind(Model &$model)
    {
        $class = get_class($model);
        $columns = $class::getColumns();
        foreach ($columns as $name => $column) {
            if ($column['type'] == 'json') {
                $model->$name = new Std(json_decode($model->$name, true));
            }
        }
        return true;
    }

    public function beforeSave(Model &$model)
    {
        $class = get_class($model);
        $columns = $class::getColumns();
        foreach ($columns as $name => $column) {
            if ($column['type'] == 'json') {
                $model->$name = json_encode($model->$name, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
            }
        }
        return true;
    }

}