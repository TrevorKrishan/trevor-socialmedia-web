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
            <div id="search-container" class="position-relative">
                <div id="search-bar" class="input-group">
                    {{-- <label for="search-input">Enter Email: </label> --}}
                    <input type="text" class="form-control" id="search-input" placeholder="Enter Name">
                </div>
                <div id="search-results" style="z-index:2;" class="position-absolute w-100 d-none">
                    <p class="bg-info p-1 text-white">Enter atleast 3 characters</p>
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
    $("#search-input").on('focus',function(){
        $("#search-results").removeClass('d-none');
        
    });

    $("#search-container").on('focusout',function(){
        // $("#search-results").addClass('d-none');
        // $("#search-input").val('');
    });

    $('#search-input').on('keyup',function(){
        const value = $(this).val();
        $("#search-results").html(`<div class="bg-info p-1"><div style="left:40%;" class="l-40 spinner-border position-relative text-white" role="status"><span class="sr-only">Loading...</span></div></div>`);
        if(value.length > 3){
            $.ajax({
                type: "get",
                url: "{{route('friend.search')}}",
                data: {q: value},
                dataType: "json",
                success: function (response) {
                    const data = response.data;
                    let oup;
                    if(data && data.length > 0){
                        oup = `<ul class="list-group list-unstyled">`;
                        data.forEach(e => {
                            oup += `<li class="list-group-item list-group-item-action"> 
                                        <img style="width:10%;" src="storage/${e.profile_image}" alt="${e.name} profile image" class="img-thumbnail rounded"><span class="ml-1"> ${e.name}</span><button class="align-middle btn btn-info btn-sm text-white float-right add-friend-btn" data-id="${e.id}">Add Friend</button>
                                        <li>`;
                        });
                        oup += `</ul>`;
                    }else{
                        oup = '<p class="bg-info p-1 text-white">No data found.</p>';
                    }
                    $("#search-results").html(oup);
                }
            });
        }else{
            $("#search-results").html('<p class="bg-info p-1 text-white">Enter atleast 3 characters</p>');
        }
    });

    $(document).on('click','.add-friend-btn',function () {
        const id = $(this).attr('data-id');
        let _this = $(this);
        $.ajax({
            type: "post",
            url: "{{route('friend.store')}}",
            data: {
                '_token':$('meta[name="csrf-token"]').attr('content'),
                id:id
            },
            dataType: "json",
            success: function (response) {
               if(response.status == 'success'){
                    _this.parent('li').remove();
               }
               alert(response.message);
            }
        });
    });
});
</script>
@endsection