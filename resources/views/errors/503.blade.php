@extends('admin::errors.minimal')

@section('title', $exception->getMessage())
@section('code', '503')
@section('message', $exception->getMessage())
