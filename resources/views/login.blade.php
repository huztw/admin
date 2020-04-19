@extends('admin::layouts.admin')

@push('title'){{ trans("admin.login") }}@endpush

@push('style')
    <!-- Styles -->
    <link href="{{ admin_asset('vendor/huztw-admin/css/admin.css') }}" rel="stylesheet">
@endpush

@push('script')
    <!-- Scripts -->
    <script src="{{ admin_asset('vendor/huztw-admin/jQuery/jquery-3.4.1.min.js') }} "></script>
    <script src="{{ admin_asset('vendor/huztw-admin/js/admin.js') }}" defer></script>
@endpush

@push('content')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ trans('admin.login') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ admin_url('login') }}">
                                @csrf

                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-3">
                                        <input id="username" class="form-control @error('username') is-invalid @enderror" placeholder="{{ trans('admin.username') }}" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
@error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
@enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-3">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ trans('admin.password') }}" name="password" required autocomplete="password">
@error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
@enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">{{ trans('admin.remember_me') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-3">
                                        <button type="submit" class="btn btn-primary">{{ trans('admin.login') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endpush
