@extends('layouts.app')
@section('title', 'Messages')
@section('content')
<style>
    html{
        overflow: hidden;
    }
</style>
<div>
    <div id="message-friends-list">
        @foreach ($friends['active'] as $friend)
            <div class="message-friend" data-source="{{ $friend['profile_image'] }}" data-name="{{ ucwords($friend['name']) }}" data-id="{{$friend['friend_id']}} ">
                <img src="{{ $friend['profile_image'] }}" alt="{{ $friend['name'] }} profile image">
                {{ ucwords($friend['name']) }}
            </div>
        @endforeach
    </div>
    <div id="message-container" style="display:none">
        <div id="message-header">
            <div id="message-header-image"></div>
            <div id="message-header-name"></div>
        </div>
        <div id="message-body">
            <ul id="message-list">

            </ul>
        </div>
        <div id="message-footer">
            <div id="textbox-container">
                <textarea name="message" id="message" cols="30" rows="1" placeholder="Enter your message and press submit"></textarea>
                <button class="btn btn-success" id="send-message-btn">Send</button>  
            </div>
        </div>
    </div>
</div>
<script>
$(()=>{
    window.currentMessageId = 0;

    $(document).on('click','.message-friend',function(){
        const friend_id = $(this).attr('data-id');
        const name = $(this).attr('data-name');
        const img = $(this).attr('data-source');
        window.currentMessageId = friend_id;
        $('#message-header-name').text(name);
        $('#message-header-image').html(`<img src="${img}" alt="${name} profile image">`);
        $('#send-message-btn').attr('data-id',friend_id);
        $('#message').val('');
        $('#message-container').css('display','block');
        getMessages(friend_id);
    });

    $('#send-message-btn').on('click',function(){
        const message = $('#message').val();
        const friend_id = $(this).attr('data-id');
        $.ajax({
            type: "post",
            url: "{{route('messages.store')}}",
            data: {
                '_token':$('meta[name="csrf-token"]').attr('content'),
                message:message,
                friend_id:friend_id
            },
            dataType: "json",
            success: function (response) {
                if(response.status == 'success' ){
                    $('#message').val('');
                    getMessages(friend_id);
                }else{
                    alert(response.message);
                }
            }
        });
    });

    function getMessages(friend_id) {
        $.ajax({
            type: "get",
            url: "{{route('messages')}}/"+friend_id,
            dataType: "json",
            success: function (response) {
                let messages = response.data;
                messages.reverse();
                let oup = '';
                messages.forEach(e => {
                    oup += `<li class='${e.type}' >${e.message}</li>`;
                });
                if(window.currentMessageId == friend_id){
                    $('#message-list').html(oup);
                    let list = document.getElementById("message-list");
                    list.scrollTop = list.scrollHeight;
                }
            }
        });
    }
});
</script>
@endsection