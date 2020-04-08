@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email"
                                class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password"
                                class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm"
                                class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="profile_image"
                                class="col-md-4 col-form-label text-md-right">{{ __('Profile Image') }}</label>

                            <div class="col-md-6">
                                <input id="profile_image" type="file"
                                    class=" @error('profile_image') is-invalid @enderror" name="profile_image" required>

                                @error('profile_image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" id="submit-btn" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('submit-btn').addEventListener('click',function(){
        const token = document.getElementsByName('_token')[0].value;
        
        const nameElement = document.getElementById('name');
        const emailElement = document.getElementById('email');
        const passwordElement = document.getElementById('password');
        const cpasswordElement = document.getElementById('password-confirm');
        const imageElement = document.getElementById('profile_image');


        const name = nameElement.value;
        const email = emailElement.value;
        const password = passwordElement.value;
        const cpassword = cpasswordElement.value;
        const image = imageElement.files[0];

        let formElements = document.getElementsByClassName('form-control');
        let formErrors = document.getElementsByClassName('invalid-feedback');

        for (let i = 0; i < formElements.length; i++) {
            formElements[i].classList.remove('is-invalid');
        }
        
        for (let i = 0; i < formErrors.length; i++) {
            formErrors[i].remove();
        }

        if(name.length < 1){
            nameElement.classList.add('is-invalid');
            appendError(nameElement.parentElement, 'Please enter a name');
            return false;
        }else if(email.length < 1 || email.indexOf('@') === -1 || email.indexOf('.') === -1){
            emailElement.classList.add('is-invalid');
            appendError(emailElement.parentElement, 'Please enter a valid email');
            return false;
        }else if(password.length < 8){
            passwordElement.classList.add('is-invalid');
            appendError(passwordElement.parentElement, 'Please enter a password that is minimum 8 character long');
            return false;
        }else if(password !== cpassword){
            cpasswordElement.classList.add('is-invalid');
            appendError(cpasswordElement.parentElement, 'Password and Confirm Password did not match');
            return false;
        }else if(image === undefined){
            imageElement.classList.add('is-invalid');
            appendError(imageElement.parentElement, 'Please upload an image');
            return false;
        }

        
        this.disabled = true;
        let button = this;
        let data = new FormData;
        data.append('name',name);
        data.append('email',email);
        data.append('password',password);
        data.append('profile_image',image);
        data.append('_token',token);

        let xhr = new XMLHttpRequest;
        xhr.open('POST','{{action("UserController@store")}}');
        xhr.setRequestHeader('Accept', 'Application/json')
        xhr.onreadystatechange = function () {
            if(this.readyState === XMLHttpRequest.DONE && this.status === 200 ){
                let resp = JSON.parse(this.responseText);
                alert(resp.message);
                if(resp.status == 'success'){
                    location.href = 'login';
                }
                button.disabled = false;
            }else if(this.readyState === XMLHttpRequest.DONE && this.status === 422){
                let resp = JSON.parse(this.responseText);
                if(resp.errors.email !== undefined){
                    emailElement.classList.add('is-invalid');
                    appendError(emailElement.parentElement,email+' - '+resp.errors.email[0]);
                }
                
                button.disabled = false;
                alert('Validation Error.Please check your inputs and try again.');
            }
        }

        xhr.onerror = function () { 
            alert('Request Failed Try Again Later');
            button.disabled = false;
        }
        
        xhr.send(data);
    });

    function appendError(element,message){
        const output = `<span class="invalid-feedback" role="alert">
                            <strong>${message}</strong>
                        </span>`;
        element.innerHTML += output;
    }
</script>
@endsection