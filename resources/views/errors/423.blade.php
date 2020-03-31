@extends('admin::errors.minimal')

@section('title', $exception->getMessage())
@section('code', '423')
@section('message', $exception->getMessage())
