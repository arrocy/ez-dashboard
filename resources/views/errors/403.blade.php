@extends('errors.layout')

@section('title', __('Access denied'))
@section('code', '403')
@section('heading', __('Access denied'))
@section('fallbackMessage', __('You do not have permission to access this page.'))
