@extends('admin::errors.minimal')

@section('title', $exception->getMessage())
@section('code', '500')
@section('message', $exception->getMessage())
