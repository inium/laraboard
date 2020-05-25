@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    <list-component
        :board='@json($board)'
        :notices='@json($notices)'
        :posts='@json($posts)'
        :paginate='@json($paginate)'
        :search-types='@json($searchTypes)'
        :query='@json($query)'
        {{-- :authUser='@json(Auth::user())' --}}
        ></list-component>

</div>

@endsection
