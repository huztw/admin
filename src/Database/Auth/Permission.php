<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Permission extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'slug', 'http_method', 'http_path'];

    /**
     * @var array
     */
    public static $httpMethods = [
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.permissions_table'));

        parent::__construct($attributes);
    }

    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id')->withTimestamps();
    }

    /**
     * Permission belongs to many routes.
     *
     * @return BelongsToMany
     */
    public function routes()
    {
        $pivotTable = config('admin.database.permission_routes_table');

        $relatedModel = config('admin.database.routes_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'route_id')->withTimestamps();
    }

    /**
     * If request should pass through the current permission.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function shouldPassThrough(Request $request): bool
    {
        if (empty($this->http_method) && empty($this->http_path)) {
            return true;
        }

        $method = $this->http_method;

        $matches = array_map(function ($path) use ($method) {
            if (Str::contains($path, ':')) {
                list($method, $path) = explode(':', $path);
                $method              = explode(',', $method);
            }

            return compact('method', 'path');
        }, explode("\n", $this->http_path));

        foreach ($matches as $match) {
            if ($this->matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * filter \r.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getHttpPathAttribute($path)
    {
        return str_replace("\r\n", "\n", $path);
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function getRoutesAttribute($value)
    {
        $routeModel = $this->routes()->get();
        $routes     = [];

        foreach ($routeModel as $route) {
            array_push($routes, [
                'http_path'   => $route->http_path,
                'http_method' => $route->http_method,
            ]);
        }

        return $routes;
    }

    /**
     * If a request match the specific HTTP method and path.
     *
     * @param array   $match
     * @param Request $request
     *
     * @return bool
     */
    protected function matchRequest(array $match, Request $request): bool
    {
        if ($match['path'] == '/') {
            $path = '/';
        } else {
            $path = trim($match['path'], '/');
        }

        if (!$request->is($path)) {
            return false;
        }

        $method = collect($match['method'])->filter()->map(function ($method) {
            return strtoupper($method);
        });

        return $method->isEmpty() || $method->contains($request->method());
    }

    /**
     * @param $method
     */
    public function setHttpMethodAttribute($method)
    {
        if (is_array($method)) {
            $this->attributes['http_method'] = implode(',', $method);
        }
    }

    /**
     * @param $method
     *
     * @return array
     */
    public function getHttpMethodAttribute($method)
    {
        if (is_string($method)) {
            return array_filter(explode(',', $method));
        }

        return $method;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->roles()->detach();
            $model->routes()->detach();
        });
    }
}
