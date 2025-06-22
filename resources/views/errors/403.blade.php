@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() == "User is not logged in." ? "ابتدا وارد حساب کاربری شوید": 'Forbidden'))
