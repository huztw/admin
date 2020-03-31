@extends('admin::errors.minimal')

@section('title', $exception->getMessage())
@section('code', '429')
@section('message', $exception->getMessage())
