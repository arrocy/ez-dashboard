@extends('errors.layout')

@section('title', __('Server error'))
@section('code', '500')
@section('heading', __('Something went wrong'))
@section('fallbackMessage', __('An unexpected server error occurred. Please try again later.'))
