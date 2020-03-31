<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link href="{{ mix('css/app.css', 'build') }}" rel="stylesheet" type="text/css">
    </head>
    <body>

    <div class="super_container">
        <!-- Header -->
        <header class="header">

            <!-- Top Bar -->
            <div class="top_bar">
                <div class="top_bar_container">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <div class="top_bar_content d-flex flex-row align-items-center justify-content-start">
                                    <ul class="top_bar_contact_list">
                                        <li>
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                            <div>
                                                <a style="color: white" href="tel:phone">phone</a>
                                            </div>
                                        </li>
                                        <li>
                                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                            <div>
                                                <a style="color: white" href="mailto:">mail@yandex.ru</a>
                                            </div>
                                        </li>
                                        <li><div class="question"> <a style = "color: white; font-weight: bold" href="/contacts">Обратная связь</a></div></li>
                                    </ul>
                                    @php
                                        $isAuth = \Auth::check() ?? false;
                                    @endphp

                                    @if ($isAuth)
                                        @php
                                            $name = \Auth::user()->name;
                                        @endphp
                                        <div class="top_bar_login ml-auto">
                                            <div class="dropdown" style="padding: 10px; color: white">
                                                <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {{ $name }}
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <form action="/logout" method="post">
                                                        @csrf
                                                        <button type='submit' class="dropdown-item" href="">Выйти</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="top_bar_login ml-auto">
                                            <div class="login_button">
                                                <a href="{{ route('login') }}">
                                                    Войти/Регистрация</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Content -->
            <div class="header_container">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="header_content d-flex flex-row align-items-center justify-content-start">
                                <div class="logo_container">
                                    <a href="/">
                                        <div class="logo_text" style="width: 135px;">
{{--                                            АЛЬТ<span>НПО</span>--}}
                                            <img src="/images/logo.svg" />
                                        </div>
                                    </a>
                                </div>
                                <nav class="main_nav_contaner ml-auto">
                                    <ul class="main_nav">
                                        <li><a href="/">Главная</a></li>
                                        <li><a href="/catalog">Каталог</a></li>
                                        <li><a href="/delivery">Доставка</a></li>
                                        <li><a href="/about">О компании</a></li>
                                        <li><a href="/contacts">Контакты</a></li>
{{--                                        <li class="active"><a href="#">Home</a></li>--}}
                                    </ul>
                                    <div class="search_button"><i class="fa fa-search" aria-hidden="true"></i></div>
                                    <!-- Hamburger -->
                                    <div class="shopping_cart">
                                        <a href="/basket"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="hamburger menu_mm">
                                        <i class="fa fa-bars menu_mm" aria-hidden="true"></i>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Search Panel -->
            <div class="header_search_container">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="header_search_content d-flex flex-row align-items-center justify-content-end">
                                <form action="#" class="header_search_form">
                                    <input type="search" class="search_input" placeholder="Поиск" required="required">
                                    <button class="header_search_button d-flex flex-column align-items-center justify-content-center">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Menu -->

        <div class="menu d-flex flex-column align-items-end justify-content-start text-right menu_mm trans_400">
            <div class="menu_close_container">
                <div class="menu_close">
                    <div></div>
                    <div></div>
                </div>
            </div>
            <div class="search">
                <form action="#" class="header_search_form menu_mm">
                    <input type="search" class="search_input menu_mm" placeholder="Поиск" required="required">
                    <button
                        class="header_search_button d-flex flex-column align-items-center justify-content-center menu_mm">
                        <i class="fa fa-search menu_mm" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
            <nav class="menu_nav">
                <ul class="menu_mm">
                    <li class="menu_mm"><a href="/">Главная</a></li>
                    <li class="menu_mm"><a href="/catalog">Каталог</a></li>
                    <li class="menu_mm"><a href="/about">О нас</a></li>
                    <li class="menu_mm"><a href="/contacts">Контакты</a></li>
                    <li class="menu_mm"><a href="/basket">Корзина</a></li>
                    <li class="menu_mm"><a href="/delivery">Доставка</a></li>
                </ul>
            </nav>
        </div>

        <div class="container" style="padding: 20px 0 50px 0;">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer_background" style="background-image:url(images/footer_background.png)"></div>
            <div class="container">
                <div class="row footer_row">
                    <div class="col">
                        <div class="footer_content">
                            <div class="row">

                                <div class="col-lg-3 footer_col">

                                    <!-- Footer About -->
                                    <div class="footer_section footer_about">
                                        <div class="footer_logo_container">
                                            <a href="#">
                                                <div class="footer_logo_text">
{{--                                                    АЛЬТ<span>НПО</span>--}}
                                                    <img src="/images/logo.svg" style="width: 135px;"/>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="footer_about_text">
                                            <p>Lorem ipsum dolor sit ametium, consectetur adipiscing elit.</p>
                                        </div>
                                        <div class="footer_social">
                                            <ul>
                                                <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                                <li><a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
                                                <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                                <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-3 footer_col">

                                    <!-- Footer Contact -->
                                    <div class="footer_section footer_contact">
                                        <div class="footer_title">Контакты</div>
                                        <div class="footer_contact_info">
                                            <ul>
                                                <li>Email: <a style="color: white" href="mailto:email">email</a></li>
                                                <li>Телефон:  <a style="color: white" href="tel:phone">phone</a></li>
                                                <li>Address</li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-3 footer_col">

                                    <!-- Footer links -->
                                    <div class="footer_section footer_links">
                                        <div class="footer_title">Компания</div>
                                        <div class="footer_links_container">
                                            <ul>
                                                <li><a href="/">Главная</a></li>
                                                <li><a href="/about">О компании</a></li>
                                                <li><a href="/contacts">Контакты</a></li>
                                                <li><a href="/catalog">Каталог</a></li>
                                                <li><a href="/basket">Корзина</a></li>
                                                <li><a href="/delivery">Доставка</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-3 footer_col clearfix">

                                    <!-- Footer links -->
                                    <div class="footer_section footer_mobile">
                                        <div class="footer_title">Mobile</div>
                                        <div class="footer_mobile_content">
                                            <div class="footer_image"><a href="#"><img src="images/mobile_1.png" alt=""></a></div>
                                            <div class="footer_image"><a href="#"><img src="images/mobile_2.png" alt=""></a></div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row copyright_row">
                    <div class="col">
                        <div class="copyright d-flex flex-lg-row flex-column align-items-center justify-content-start">
                            <div class="cr_text"><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></div>
                            <div class="ml-lg-auto cr_links">
                                <ul class="cr_list">
                                    <li><a href="#">Copyright notification</a></li>
                                    <li><a href="#">Terms of Use</a></li>
                                    <li><a href="#">Privacy Policy</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
        <script src="{{ mix('js/app.js', 'build') }}" ></script>
    </body>
</html>
