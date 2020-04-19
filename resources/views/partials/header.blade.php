        <!-- Main Header -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ admin_url('/') }}">{{ config('app.name', 'Laravel') }}</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto"></ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
@if (!Admin::guard()->check())
@if (!request()->is('admin/login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ admin_url('login') }}">{{ trans('admin.login') }}</a>
                        </li>
@endif
@else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Admin::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ admin_url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ trans('admin.logout') }}
                                </a>

                                <form id="logout-form" action="{{ admin_url('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                    
                                </form>
                            </div>
                        </li>
@if (Route::has('admin.register'))
@if(admin_permission()->route()->check('admin/register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ admin_url('register') }}">{{ trans('admin.register') }}</a>
                        </li>
@endif
@endif
@endif
                    </ul>
                </div>
            </div>
        </nav>