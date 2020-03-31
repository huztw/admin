@extends('admin::errors.minimal')

@section('title', $exception->getMessage())
@section('code', '401')
@section('message', $exception->getMessage())
