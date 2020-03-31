@extends('admin::errors.minimal')

@section('title', $exception->getMessage())
@section('code', '419')
@section('message', $exception->getMessage())
