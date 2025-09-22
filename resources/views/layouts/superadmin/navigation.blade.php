{{-- SPDX-License-Identifier: MIT --}}
<nav class="navbar bg-white w-full flex  lg:flex-row px-4 lg:px-8 py-2 justify-between items-center">
    <div class="nav-brand flex items-center">
        @if(\Auth::user())
            <button class="block lg:hidden md:hidden mr-3" onclick="showsidebar('res_sidebar')">
                <span class="navbar-toggler-icon">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path class="heroicon-ui" d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z"/></svg>
                </span>
            </button>

            @if(Auth::user()->SchoolLogo['meta_value'] != '-')
                <a class="h-10 object-contain" href="{{ route('dashboard') }}">
                    <img src="{{ Auth::user()->SchoolLogoPath }}" class="h-10 w-auto object-cover">
                </a>
            @endif

             <a class="h-10 object-contain" href="{{ route('dashboard') }}">
                    <img src="/uploads/demologo.png" class="h-10 w-auto object-cover mr-3">
                </a>
           
        @else
            @include('layouts.partials.logo')
        @endif
    </div>
    <div class="navbar-menu collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav mr-auto flex">
        </ul>
    </div>
 
    <div class="flex flex-col-reverse lg:flex-row md:flex-row items-center">
       
        <div class="flex items-center">
            {{-- <notification url="{{url('/')}}" mode="admin"></notification> --}}
            <div class="navbar-menu">
                <ul class="navbar-nav ml-auto flex items-center">
                    @guest
                        <li class="nav-item px-2">
                            <a class="nav-link" href="{{ route('login') }}" id="login">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item px-2">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @else
                        <!-- start -->
                        <li>
                            <div class="profile-click" dusk="profile-menu">
                                @if(Auth::user()->userprofile->avatar!= null)
                                    <img src="{{ url(Auth::user()->userprofile->AvatarPath) }}" class="w-8 h-8 rounded-full cursor-pointer">
                                @else
                                    <img src="{{ asset('uploads/user/avatar/default-user.jpg') }}" class="w-8 h-8 rounded-full cursor-pointer">
                                @endif
                                <div class="user-dtl rounded">
                                    <ul class="list-reset bg-white border border-gray-400 -mt-3 shadow-lg z-40">
                                        <div class="flex border-b p-2 items-center">
                                            @if(Auth::user()->userprofile->avatar!= null)
                                                <img src="{{ url(Auth::user()->userprofile->AvatarPath) }}" class="w-10 h-10 rounded-full cursor-pointer">
                                            @else
                                                <img src="{{asset('uploads/user/avatar/default-user.jpg')}}" class="w-10 h-10 rounded-full cursor-pointer">
                                            @endif
                                            <div>
                                                <div>
                                                    <a id="navbarDropdown" class="nav-link dropdown-toggle text-sm  no-underline text-black px-2" href="{{url('/admin/dashboard')}}">
                                                        @if(Auth::user()->userprofile->firstname != null)
                                                            {{ Auth::user()->FullName }} <span class="caret"></span>
                                                        @else
                                                            {{ Auth::user()->name }} <span class="caret"></span>
                                                        @endif
                                                    </a>
                                                </div>
                                                <div>
                                                    <p class="text-sm  no-underline text-black px-2">{{ Auth::user()->email }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="py-2 leading-loose">
                                            <li class="hover:bg-gray-200">
                                                <a href="{{url('/superadmin/changepassword')}}" dusk="password-link" class="text-sm no-underline text-black px-3">
                                                    <span>Change Password</span>
                                                </a>
                                            </li>

                                            <li class="hover:bg-gray-200">
                                                <a href="{{url('/superadmin/changeavatar')}}" dusk="avatar-link" class="text-sm no-underline text-black px-3">
                                                    <span>Change Avatar</span>
                                                </a>
                                            </li>

                                            <li class="hover:bg-gray-200">
                                                <a class="dropdown-item text-sm  no-underline text-black px-3" dusk="logout-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </li>
                                        </div>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <!-- end -->
                    @endguest
                </ul>
            </div>
        </div>
    </div>
</nav>