<?php

namespace Huztw\Admin\Extensions;

use Illuminate\Support\Arr;

class SessionModel
{
    /**
     * @var string
     */
    protected static $migrations = '_model_migrations';

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $attributes = [];

    /**
     * Set model
     *
     * @param  string  $model
     *
     * @return \App\SessionModel
     */
    public function model($model)
    {
        $this->setMigrations($model);

        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    protected function getModel()
    {
        if (!$this->model) {
            $this->model('session_model');
        }

        return $this->model;
    }

    /**
     * Set model datas
     *
     * @param  mixed $datas
     *
     * @return void
     */
    protected function setModelDatas($datas = [])
    {
        session([$this->getModel() => $datas]);
    }

    /**
     * Set model name for migrations
     *
     * @param  string  $model
     *
     * @return void
     */
    protected function setMigrations($model)
    {
        $migrations = session(self::$migrations, []);

        if (!in_array($model, $migrations)) {
            array_push($migrations, $model);
            session([self::$migrations => $migrations]);
        }
    }

    /**
     * Remove model from the session.
     *
     * @param  string|null  $model
     *
     * @return \App\SessionModel
     */
    public function flush($model = null)
    {
        collect($this->models())->each(function ($item, $key) use ($model) {
            if ($model === null || $model == $item) {
                session()->forget($item);
            }

            if (!session()->has($item)) {
                $migrations = collect($this->models())->filter(function ($value, $key) use ($item) {
                    return $value != $item;
                })->all();

                sort($migrations);

                session([self::$migrations => $migrations]);
            }
        });

        if (Arr::has(session()->all(), self::$migrations) && count($this->models()) == 0) {
            session()->forget(self::$migrations);
        }

        return $this;
    }

    /**
     * Determine if a table is exist in model.
     *
     * @param  string  $table
     *
     * @return bool
     */
    public function is($table): bool
    {
        $tables = $this->tables();

        return Arr::has($tables, $table);
    }

    /**
     * Table of model
     *
     * @param  string $table
     * @param  mixed $datas
     *
     * @return \App\SessionModel
     */
    public function table($table)
    {
        $this->table = $table;

        $get = $this->getTableAttributes();

        if (count($get) > 0) {
            $this->attributes = $get;
        } else {
            $this->setTableAttributes();
        }

        return $this;
    }

    /**
     * Get this table from the model.
     *
     * @return string
     */
    protected function getTable()
    {
        if (!$this->table) {
            throw new \InvalidArgumentException("Invalid table with [$this->table].");
        }

        return $this->table;
    }

    /**
     * Set table attributes
     *
     * @param  mixed $datas
     *
     * @return void
     */
    protected function setTableAttributes($datas = [])
    {
        $tables = $this->tables();

        Arr::set($tables, $this->getTable(), $datas);

        $this->setModelDatas($tables);
    }

    /**
     * Get table attributes
     *
     * @return mixed
     */
    protected function getTableAttributes()
    {
        return Arr::get($this->tables(), $this->table, []);
    }

    /**
     * Insert attributes for table
     *
     * @param string $key
     *
     * @return \App\SessionModel
     */
    public function insert($datas = [])
    {
        $attributes = $this->getTableAttributes();

        array_push($attributes, $datas);

        $this->setTableAttributes($attributes);

        return $this;
    }

    /**
     * Clear all table from model
     *
     * @return \App\SessionModel
     */
    public function clearAll()
    {
        if (session()->has($this->getModel())) {
            session()->forget($this->getModel());
            $this->flush($this->getModel());
        }

        return $this;
    }

    /**
     * Clear table from model
     *
     * @return \App\SessionModel
     */
    public function clear()
    {
        if (session()->has($this->getModel())) {
            $table  = $this->getTable();
            $tables = $this->tables();

            if (Arr::has($tables, $table)) {
                Arr::forget($tables, $table);
                $this->setModelDatas($tables);
            }
        }

        return $this;
    }

    /**
     * Get value from attributes.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return collect($this->all())->get($key, $default);
    }

    /**
     * Use key to find the attributes from the table.
     *
     * @return \App\SessionModel
     */
    public function find($key)
    {
        $attributes = $this->getTableAttributes();

        $attribute = $attributes[$key];

        if (isset($attribute)) {
            $this->attributes = $attribute;
        } else {
            throw new \ErrorException("Undefined key in attributes: {$key}");
        }

        return $this;
    }

    /**
     * Filter attributes by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return \App\SessionModel
     */
    public function where($key, $value = null)
    {
        $this->attributes = collect($this->getTableAttributes())->where($key, $value)->all();

        return $this;
    }

    /**
     * Get all models.
     *
     * @return array
     */
    public function models()
    {
        return session(self::$migrations, []);
    }

    /**
     * Get all tables from model.
     *
     * @return array
     */
    public function tables()
    {
        return session($this->getModel(), []);
    }

    /**
     * Get all of the attributes from this attributes.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->attributes ?? [];
    }

    /**
     * Get the first attributes from this attributes.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     *
     * @return \App\SessionModel
     */
    public function first(callable $callback = null, $default = null)
    {
        $this->attributes = collect($this->all())->first($callback, $default);

        return $this;
    }

    /**
     * Get the last attributes from this attributes.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     *
     * @return \App\SessionModel
     */
    public function last(callable $callback = null, $default = null)
    {
        $this->attributes = collect($this->all())->last($callback, $default);

        return $this;
    }
}
