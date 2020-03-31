@extends('admin::errors.minimal')

@section('title', $exception->getMessage())
@section('code', '403')
@section('message', $exception->getMessage())
