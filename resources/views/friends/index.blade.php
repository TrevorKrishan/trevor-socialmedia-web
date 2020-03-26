@extends('layouts.app')
@section('title', 'Friends')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <blockquote class="blockquote">
                <p class="mb-0">Add Friend:</p>
            </blockquote>
        </div>
        <div class="col-md-8">
            <div id="search-container">
                <div id="search-bar" class="input-group">
                    {{-- <label for="search-input">Enter Email: </label> --}}
                    <input type="text" class="form-control" id="search-input" placeholder="Enter Email (Atleast 3 characters) ">
                </div>
                <div id="search-results">

                </div>
            </div>
        </div>
    </div>
    <div id="active-friends-list" class="row justify-content-center mt-5">
        <div class="col-md-12">
            <blockquote class="blockquote">
                <p class="mb-0">Friends List:</p>
              </blockquote>
        </div>
        @foreach ($friends['active'] as $friend)
        <div class="col-md-4 col-sm-2">
            <div class="card" style="max-width: 18rem;">
                <img class="card-img-top" style="height:10rem;" src="{{ $friend['profile_image'] }}" alt="{{ $friend['name'] }} profile image">
                <div class="card-body">
                    <h5 class="card-title">{{ ucwords($friend['name']) }}</h5>
                    <p class="card-text">{{ $friend['email'] }}</p>
                    {{-- <p>{{ $friend['accepted_on'] }}</p> --}}
                    <p class="text-success font-weight-bold p-1" >{{ ucwords($friend['status']) }}</p>
                    <a href="#" class="btn btn-danger">Block</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script>
    $(()=>{
        $('#search-input').on('keyup',function(){
            const value = $(this).val();
            if(value.length > 3){
                $.ajax({
                    type: "get",
                    url: "{{route('friend.search')}}",
                    data: {q: value},
                    dataType: "json",
                    success: function (response) {
                        
                    }
                });
            }
        });
    });
</script>
@endsection