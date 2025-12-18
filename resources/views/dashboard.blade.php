@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php $role = Auth::user()->activeRole(); @endphp

@includeIf("dashboard._{$role}")
@endsection