<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as BaseRoute;

class Route extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['http_path', 'http_method', 'name', 'visibility'];

    /**
     * @var array
     */
    public static $httpMethods = [
        'DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT',
    ];

    /**
     * @var array
     */
    public static $exceptMethods = [
        'HEAD', 'OPTIONS',
    ];

    /**
     * @var array
     */
    protected static $visibility = [
        'public'    => 'public',
        'protected' => 'protected',
        'private'   => 'private',
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

        $this->setTable(config('admin.database.routes_table'));

        parent::__construct($attributes);
    }

    /**
     * Route belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        $pivotTable = config('admin.database.permission_routes_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'route_id', 'permission_id')->withTimestamps();
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
            $model->permissions()->detach();
        });
    }

    /**
     * Get visibility is Public.
     *
     * @return string
     */
    public static function getPublic()
    {
        return self::$visibility['public'];
    }

    /**
     * Get visibility is protected.
     *
     * @return string
     */
    public static function getProtected()
    {
        return self::$visibility['protected'];
    }

    /**
     * Get visibility is private.
     *
     * @return string
     */
    public static function getPrivate()
    {
        return self::$visibility['private'];
    }

    /**
     * Is Http Path?
     *
     * @param \Illuminate\Http\Request $request
     * @param $http_path
     *
     * @return bool
     */
    public static function isHttpPath(Request $request, $http_path): bool
    {
        $http_path = preg_replace("/\{(.*?)\}/", '*', $http_path);

        return $request->is($http_path);
    }

    /**
     * Determine if the route should pass through.
     *
     * @param Request $request
     *
     * @return true|null
     */
    public function shouldPassThrough(Request $request)
    {
        return static::get()->first(function ($route) use ($request) {
            if ($route::isHttpPath($request, $route->http_path)) {
                if (empty($route->http_method)) {
                    return true;
                }

                foreach ($route->http_method as $http_method) {
                    if ($request->isMethod($http_method)) {
                        return true;
                    }
                }
            }
        });
    }

    /**
     * Get route.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Huztw\Admin\Database\Auth\Route|null
     */
    public static function getProtectedRoute(Request $request)
    {
        $similar = 0;
        $result  = null;

        foreach (self::all() as $route) {
            if (self::isHttpPath($request, $route->http_path)) {
                if (in_array($request->method(), $route->http_method)) {
                    similar_text($request->path(), $route->http_path, $newSimilar);

                    if ($newSimilar > $similar) {
                        $similar = $newSimilar;
                        $result  = $route;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get route.
     *
     * @return \Illuminate\Support\Facades\Route
     */
    public static function getRoutes()
    {
        return BaseRoute::getRoutes();
    }
}
