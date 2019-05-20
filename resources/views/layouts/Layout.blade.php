<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('includes.head')
    </head>
    <body>
        @include('includes.header')

        <div id="container" class="container"  style="margin-right: 100px;" >

                @yield('content')


        </div>
            @include('includes.footer')
       </body>
</html>
