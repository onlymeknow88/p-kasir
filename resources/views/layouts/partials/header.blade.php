<!-- header -->
<header class="header-container">
    <div class="header">
        <div class="header-left">
            <div class="menubar">
                <button class="btn btn-master btn-menu" id="header-toggle">
                    <svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" focusable="false"
                        style="width: 100%; height: 100%">
                        <g>
                            <path d="M21,6H3V5h18V6z M21,11H3v1h18V11z M21,17H3v1h18V17z"></path>
                        </g>
                    </svg>
                </button>
            </div>

            <div class="logo">
                <a href="#">
                    <div class="top-logo">
                        {{-- <img src="/assets/img/logo-adaro-mining.png" alt="" width="100"> --}}
                        <h4>Koperasi</h4>
                    </div>
                </a>
            </div>
        </div>
        <div class="header-right">
            <div class="header-options">
                <div class="avatar-profiles">
                    <div class="dropdown-profile">
                        <a class="dropdown-toggle d-flex align-items-center" href="#" role="button"
                            id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="text-black">Hi, {{ Auth::user()->name }}</span>
                            @if (Auth::user()->avatar != null)
                                <img src="{{ Storage::disk('public')->url('avatars/' . Auth::user()->avatar) }}"
                                    class="rounded-circle" width="40" height="40" alt="">
                            @else
                            {{-- <img src="{{ Helper::getProfilePicture(Auth::user()->name) }}"
                                    class="rounded-circle" width="40" height="40" alt="{{ Auth::user()->name }}"> --}}
                            {{-- <img src="data:image/png;base64,{{ Helper::getProfilePicture(Auth::user()->name) }}" alt="{{ Auth::user()->name }}"> --}}
                                {{-- <img src="{{ Helper::getProfilePicture(Auth::user()->name) }}" alt="Profile Picture"> --}}
                            @endif
                        </a>

                        <ul class="dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#">Account Settings</a>
                            </li>
                            {{-- <li>
                                <a class="dropdown-item" href="#">Log Out</a>
                                <a href="#" onclick="document.querySelector('#form-logout').submit()" class="dropdown-item">Log Out</a>

                                <form action="{{ route('logout') }}" method="post" id="form-logout">
                                    @csrf
                                </form>
                            </li> --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
