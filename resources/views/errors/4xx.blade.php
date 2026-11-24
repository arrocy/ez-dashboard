@extends('errors.layout')

@section('title', __('Request error'))
@section('code', $exception->getStatusCode())
@section('heading', __('We could not complete your request'))
@section('fallbackMessage', __('The request could not be completed. Check the address and try again.'))
