@extends('errors.layout')

@section('title', __('Authentication required'))
@section('code', '401')
@section('heading', __('Authentication required'))
@section('fallbackMessage', __('Please sign in to access this page.'))
