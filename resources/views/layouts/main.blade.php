@props([])

@extends('layouts.app')

@section('title')
{{ $title ?? 'Dashboard' }}
@endsection

@section('content')
{{ $slot }}
@endsection