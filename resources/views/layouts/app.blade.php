@include('layouts.header')
<div id="app">
    @include('layouts.nav')

    <main class="py-4">
        @yield('content')
    </main>
</div>
@include('layouts.footer')
