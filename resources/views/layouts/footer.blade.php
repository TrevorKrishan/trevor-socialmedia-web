</body>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();

        $("#notification-nav").on('click',function(){
            $("#notifications").html(`<div class="bg-white p-1"><div style="left:45%;" class="l-40 spinner-border position-relative text-success" role="status"><span class="sr-only">Loading...</span></div></div>`);
            $("#notification-container").toggleClass("d-none");
            if(!$("#notification-container").hasClass('d-none')){
                $.ajax({
                    type: "get",
                    url: "{{route('notification')}}",
                    dataType: "json",
                    success: function (response) {
                        const data = response.data;
                        let oup ='';
                        if(data && data.length > 0){
                            data.forEach(e => {
                            oup += `<li class="list-group-item list-group-item-action"> 
                                        <img style="width:10%;" src="storage/${e.profile_image}" alt="${e.name} profile image" class="img-thumbnail rounded"><span class="ml-1"> ${e.name}</span>
                                        <button class="align-middle btn mt-1 ml-1 mr-3 btn-danger btn-sm text-white float-right friend-request-btn" data-value="reject" data-id="${e.id}">Reject</button>
                                        <button class="align-middle mt-1 mr-1 btn btn-success btn-sm text-white float-right friend-request-btn" data-value="accept" data-id="${e.id}">Accept</button>
                                        <li>`;
                            });
                        }else{
                            oup = '<li class="list-group-item text-center text-success list-group-item-action"> No Notification found.</li>';
                        }
                        $("#notifications").html(oup);
                    }
                });
            }
        });

        $(document).on('click','.friend-request-btn',function () {
            const id = $(this).attr('data-id');
            const value = $(this).attr('data-value');
            let _this = $(this);
            $.ajax({
                type: "put",
                url: "{{route('friend.index')}}/"+id,
                data: {
                    '_token':$('meta[name="csrf-token"]').attr('content'),
                    value:value,
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
    })
</script>

</html>