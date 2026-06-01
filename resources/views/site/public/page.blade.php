@extends('site.public.layout')

@section('content')
    @foreach ($page->content ?? [] as $block)
        @includeIf("site.blocks.{$block['type']}", ['block' => $block])
    @endforeach
@endsection