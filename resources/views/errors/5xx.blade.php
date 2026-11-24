@extends('errors.layout')

@section('title', __('Server error'))
@section('code', $exception->getStatusCode())
@section('heading', __('Something went wrong'))
@section('fallbackMessage', __('An unexpected server error occurred. Please try again later.'))
