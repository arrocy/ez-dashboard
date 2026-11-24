@extends('errors.layout')

@section('title', __('Too many requests'))
@section('code', '429')
@section('heading', __('Please slow down'))
@section('fallbackMessage', __('Too many requests were sent. Wait a moment and try again.'))
