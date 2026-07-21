@extends('layouts.app')

@section('title', config('ppid.name').' — Layanan Informasi Publik')
@section('meta_description', 'Layanan informasi publik yang cepat, tepat, transparan, akuntabel, dan mudah diakses.')

@section('content')
    <x-home.hero />
    <x-home.categories :categories="$categories" />
    <x-home.services :services="$services" />
    <x-home.maklumat />
    <x-home.service-shortcuts :services="$services" />
    <x-home.posts :posts="$posts" />
    <x-home.announcements :announcements="$announcements" />
    <x-home.statistics :statistics="$statistics" />
    <x-home.units :units="$units" />
    <x-home.map-contact />
@endsection
