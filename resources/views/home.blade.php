@extends('layouts.landing.app')
@section('content')
    
<style>
    .hero {
    position: relative;
    width: 100%;
    overflow: hidden;
    direction: rtl;
    font-family: 'Cairo', sans-serif;
    padding: 10px 40px 30px 40px;
    background-color: #fff;
    }
    .video-container {
        position: relative;
        width: 100%;
        padding-top: 56.25%; 
        border-radius: 40px;
        overflow: hidden;
    }
    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .hero-content {
        position: absolute;
        bottom: 0;
        width: 94%;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(
            to top,
            rgba(22, 60, 45, 0.95),
            rgba(22, 60, 45, 0.75)
        );
        color: #fff;
        text-align: center;
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 40px;
        z-index: 2;
    }
    .hero-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 8px;
    } 
    .hero-subtitle span { 
        display: block; 
        font-size: 12px; 
        opacity: 0.7; 
    } 
    .hero-desc { 
        font-size: 14px; 
        opacity: 0.85; 
        margin-bottom: 20px; 
    }
    .hero-btn { 
        display: inline-block; 
        background: #fff; 
        color: #1b4d3e; 
        padding: 10px 28px; 
        border-radius: 30px; 
        font-weight: bold; 
        text-decoration: none; 
        transition: 0.3s; 
    } 
    .hero-btn:hover { 
        background: #eee; 
    }

        
        .offers {
        padding: 60px 20px;
        text-align: center;
        font-family: 'Cairo', sans-serif;
    }
    .offers h2 {
        font-size: 22px;
        margin-bottom: 40px;
        color: #5b2c2c;
        font-weight: bold;
    }
    .offers-container {
        display: flex;
        gap: 30px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .offer-card {
        width: 420px;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        display: flex;
    }
    .offer-card img {
        width: 45%;
        object-fit: cover;
    }
    .offer-content {
        padding: 20px;
        text-align: right;
        flex: 1;
    }
    .offer-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #222;
    }
    .offer-desc {
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 15px;
        color: #444;
    }
    .offer-discount {
        font-size: 14px;
        font-weight: bold;
        color: #b91c1c;
    }
    .testimonials {
        padding: 70px 20px;
        background: #ffffffff;
        text-align: center;
        font-family: 'Cairo', sans-serif;
    }
    .testimonials h2 {
        font-size: 22px;
        margin-bottom: 40px;
        color: #5b2c2c;
        font-weight: bold;
    }
    .testimonials-container {
        display: flex;
        justify-content: center;
        gap: 25px;
        flex-wrap: wrap;
    }
    .testimonial-card {
        background: #fff;
        width: 320px;
        padding: 25px 20px;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: right;
    }
    .testimonial-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }
    .testimonial-header img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    .testimonial-name {
        font-weight: bold;
        font-size: 14px;
        color: #222;
    }
    .testimonial-stars {
        font-size: 14px;
        color: #f59e0b;
        margin-top: 2px;
    }
    .testimonial-text {
        font-size: 13px;
        line-height: 1.8;
        color: #555;
    }
    .features-section {
        background-color: #fdf8f2; 
        padding: 60px 20px;
        font-family: 'Cairo', sans-serif; 
        text-align: center;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .section-header {
        margin-bottom: 50px;
    }
    .main-title {
        color: #5c2d25;
        font-size: 2.5rem;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }
    .title-underline {
        width: 80px;
        height: 4px;
        background-color: #ffffffff;
        margin: 0 auto;
        border-radius: 2px;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }
    .featuree-card {
        position: relative;
        background: #ffffff;
        padding: 70px 25px 40px; /* مساحة للأيقونة */
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #f0e6db;
        transition: all 0.3s ease;
        height: 100%;
        text-align: center;
    }
    .featuree-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(92, 45, 37, 0.1);
    }
    .icon-box {
        position: absolute;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: #fff;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        border: 1px solid #f0e6db;
        transition: 0.3s ease;
    }
    .featuree-card:hover .icon-box {
        transform: translateX(-50%) translateY(-6px);
    }
    .icon-box img {
        width: 45px;
        height: 45px;
    }
    .feature-title {
        color: #5c2d25;
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .feature-desc {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.7;
    }
    .gallery {
        max-width: 1000px;
        margin: 60px auto;
        text-align: center;
        font-family: 'Cairo', sans-serif;
    }
    .gallery h2 {
        font-size: 26px;
        margin-bottom: 25px;
    }
    .gallery-container {
        display: flex;
        gap: 15px;
        justify-content: center;
        align-items: stretch;
    }

    .main-image {
        flex: 3;
        border-radius: 18px;
        overflow: hidden;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.3s;
    }
    .thumbs {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .thumbs img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 14px;
        cursor: pointer;
        opacity: 0.7;
        transition: 0.3s;
    }
    .thumbs img:hover,
    .thumbs img.active {
        opacity: 1;
        transform: scale(1.03);
    }
    @media (max-width: 768px) {
        .gallery-container {
            flex-direction: column;
        }

        .thumbs {
            flex-direction: row;
        }

        .thumbs img {
            height: 90px;
        }
        .testimonial-card {
            width: 100%;
            max-width: 360px;
        }
        .offer-card {
            flex-direction: column;
            width: 100%;
        }

        .offer-card img {
            width: 100%;
            height: 200px;
        }
    }
</style>

<div class="hero">
    <div class="video-container"> 
        <video autoplay muted loop>
            <source src="{{ asset('assets/landing/v.mp4') }}" type="video/mp4">
            {{ __('messages.Your browser does not support video.') }}
        </video>
    </div>

    <div class="hero-content">
        <p class="hero-subtitle">
            {{ __('messages.Sham Al-Ezz...the essence of taste and the spirit of Damascus') }}
            <span>{{ __('messages.We offer you the finest types of meat with the highest quality standards.') }}</span>
        </p>

        <p class="hero-desc"> 
            {{ __('messages.Because you deserve the best') }}
        </p>

        <a href="{{route('menu.index')}}" class="hero-btn">
            {{ __('messages.menu') }}
        </a>
    </div>
</div>


    
    <!-- ==== Banner Section Ends Here ==== -->

    <!-- ==== Ecommerce Venture Section Starts Here ==== -->
    <section class="offers">

    <h2>{{ __('messages.Enjoy the most delicious meals') }}</h2>

    <div class="offers-container">

        <!-- كارت 1 -->
        <div class="offer-card">
            <img src="{{ asset('assets/landing/img/mashawy.jpg') }}" alt="مشويات مشكلة">

            <div class="offer-content"> 
                <div class="offer-title">{{ __('messages.Mix grill') }}</div>

                <div class="offer-desc">
                    {{ __('messages.A selection of the finest Levantine grills Served with rice and bread') }}
                </div>

                <div class="offer-discount">
                    {{ __('messages.20% discount for a limited time') }}
                </div>
            </div> 
        </div>

        <!-- كارت 2 -->
        <div class="offer-card">
            <img src="{{ asset('assets/landing/img/mesahb.png') }}" alt="مسحب دجاج">

            <div class="offer-content">
                <div class="offer-title">{{ __('messages.Boneless chicken') }}</div>

                <div class="offer-desc">
                    {{ __('messages.Marinated chicken pieces prepared in the Levantine style Served with a special sauce') }}
                </div>

                <div class="offer-discount">
                    {{ __('messages.20% discount for a limited time') }}
                </div>
            </div>
        </div>

    </div>
    
    </section>
    
    <!-- ==== Ecommerce Venture Section Ends Here ==== -->

    <!-- ==== Main Category Section Starts Here ==== -->
    <section class="testimonials"> 

    <h2>@lang('messages.Our customers opinions')</h2>

    <div class="testimonials-container">

        <!-- كارت 1 -->
        <div class="testimonial-card">
            <div class="testimonial-header">
                <img src="{{ asset('assets/landing/img/u.jpg') }}" alt="عميل">
                <div>
                    <div class="testimonial-name">@lang('messages.mohamed')</div>
                    <div class="testimonial-stars">★★★★★</div>
                </div>
            </div>

            <div class="testimonial-text"> 
                @lang('messages.Honestly, it is s a very respectable place and the food tastes excellent. The grilled meats are perfect and the service is top-notch. It definitely wonot be the last time I order from them.')
            </div>
        </div>

        <!-- كارت 2 -->
        <div class="testimonial-card">
            <div class="testimonial-header">
                <img src="{{ asset('assets/landing/img/u.jpg') }}" alt="عميل">
                <div>
                    <div class="testimonial-name">@lang('messages.Wasam')</div>
                    <div class="testimonial-stars">★★★★★</div>
                </div>
            </div>

            <div class="testimonial-text">
                @lang('messages.Excellent experience from the first try. The quality of the meat is very clear, and the taste is truly authentic.I highly recommend it.')
            </div>
        </div>

        <!-- كارت 3 -->
        <div class="testimonial-card">
            <div class="testimonial-header">
                <img src="{{ asset('assets/landing/img/u.jpg') }}" alt="عميل">
                <div>
                    <div class="testimonial-name">@lang('messages.Lamis')</div>
                    <div class="testimonial-stars">★★★★★</div>
                </div>
            </div>

            <div class="testimonial-text">
                @lang('messages.The food arrived piping hot and fresh,the seasoning was amazing and the service was fast,one of the best restaurants Ihave tried.')
            </div>
        </div>

    </div>

</section>
    <!-- ==== Main Category Section Ends Here ==== -->

    <!-- ==== Learn Feature Section Starts Here ==== -->
    <section class="learn-feature-section"
        style="background: 
        linear-gradient(rgba(255,255,255,0.6), rgba(255,255,255,0.6)),
        url({{ asset('assets/landing/img/food.jpg') }}) 
        no-repeat center center / cover;">
        <div class="container position-relative">
            <div class="row gy-5 gx-0 gx-xl-4 align-items-center">
                <div class=" col-lg-6 pe-lg-5">
                    <div class="learn-feature-content wow fadeInUp">
                        <div class="section-header text-start mb-0">
                            <h2 class="title">
                                @lang('messages.An authentic Syrian experience you won\'t forget!')

                            </h2>
                            <div class="text">
                                @lang('messages.In Sham El Ezz restaurant, every dish tells a story, and every bite takes you on a journey to the authenticity of Syrian cuisine.')
                            </div>
                        </div>
                    </div>
                </div>

                @php($feature = $landing_data['features'])
                <?php
                $array1 = array_slice($feature, 0, ceil(count($feature) / 2));
                $array2 = array_slice($feature, ceil(count($feature) / 2));
                ?>
                @if (isset($feature) && ($x = count($feature) > 0))

                    <div class="col-lg-6">
                        <div class="learn-feature-wrapper py-5">
                            <div class="row g-4 learn-feature-item-group">
                                <div class="col-6">
                                    <div class="row gy-4 gy-sm-5">
                                        <!-- Item -->
                                            <div class="col-12">
                                                <div class="learn-feature-item">
                                                    <div class="learn-feature-icon">
                                                        <img src="{{ asset('assets/landing/img/ar4.jpeg') }}"
                                                            alt="{{ $item['title'] ?? '' }}">
                                                    </div>
                                                    <div class="learn-feature-item-content">
                                                        <h5 class="subttle">@lang('messages.Appetizers')</h5>
                                                        <div class="text">
                                                            @lang('messages.A selection of kibbeh, tabbouleh, and muhammara')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- Item End-->
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="row gy-4 gy-sm-5 mt-5 pt-5">
                                            <div class="col-12">
                                                <div class="learn-feature-item">
                                                    <div class="learn-feature-icon">
                                                        <img src="{{ asset('assets/landing/img/ar2.jpeg') }}"
                                                            alt="{{ $item['title'] ?? '' }}">
                                                    </div>
                                                    <div class="learn-feature-item-content">
                                                        <h5 class="subttle">@lang('messages.Al Ezz Grills')</h5>
                                                        <div class="text">
                                                            @lang('messages.Grilled chicken with Syrian spices')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- ==== Learn Feature Section Ends Here ==== -->

    <!-- ==== Delivery Area Section Starts Here ==== -->
    <section class="features-section" dir="rtl">
    <div class="container">
        
        <div class="section-header">
            <h2 class="main-title">{{ __('messages.Why Sham El Ezz?') }}</h2>
            <div class="title-underline"></div>
        </div>

        <div class="features-grid">
            
            <div class="featuree-card">
                <div class="icon-box">
                    <img src="https://img.icons8.com/fluency/96/star--v1.png" alt="تقييم">
                </div>
                <h3 class="feature-title">{{ __('messages.High customer rating') }}</h3>  
                <p class="feature-desc">{{ __('messages.We earned the satisfaction of our customers thanks to the quality of our dishes and our excellent service that befits your status.') }}</p>
            </div>

            <div class="featuree-card">
                <div class="icon-box"> 
                    <img src="{{ asset('assets/landing/img/c.png') }}" alt="شيف">
                </div>
                <h3 class="feature-title">{{ __('messages.Professional chefs') }}</h3>
                <p class="feature-desc">{{ __('messages.An elite group of the most skilled professional chefs are keen to present the most delicious traditional Levantine dishes.') }}</p>
            </div>

            <div class="featuree-card">
                <div class="icon-box">
                    <img src="{{ asset('assets/landing/img/kn.png') }}" alt="مكونات">
                </div>
                <h3 class="feature-title">{{ __('messages.Fresh ingredients and high quality') }}</h3> 
                <p class="feature-desc">{{ __('messages.We use the finest fresh ingredients selected with great care to ensure the authentic taste.') }}</p>
            </div>

            <div class="featuree-card"> 
                <div class="icon-box">
                    <img src="{{ asset('assets/landing/img/km.png') }}" alt="طعم">
                </div>
                <h3 class="feature-title">{{ __('messages.Authentic Damascus flavor') }}</h3>
                <p class="feature-desc">{{ __('messages.We bring you the authentic taste of Damascus through our traditional recipes passed down through generations.') }}</p>
            </div>

        </div>
    </div>
</section>
    <!-- ==== Delivery Area Section Ends Here ==== -->

    <!-- ==== Refer Section Starts Here ==== -->
    <section class="gallery">

        <h2>{{ __('messages. A Feast for Your Eyes') }}</h2>

        <div class="gallery-container">

            <!-- الصورة الكبيرة -->
            <div class="main-image">
                <img id="currentImage" src="{{ asset('assets/landing/img/z1.jpg') }}">
            </div>

            <!-- الصور الصغيرة --> 
            <div class="thumbs"> 
                <img src="{{ asset('assets/landing/img/z1.jpg') }}" class="active" onclick="changeImage(this)">
                <img src="{{ asset('assets/landing/img/z2.jpg') }}" onclick="changeImage(this)">
                <img src="{{ asset('assets/landing/img/z3.jpg') }}" onclick="changeImage(this)">
                <img src="{{ asset('assets/landing/img/z.jpg') }}" onclick="changeImage(this)">
            </div>

        </div>

    </section>

    <script>
        function changeImage(el) {
            const mainImage = document.getElementById('currentImage');
            mainImage.src = el.src;

            document.querySelectorAll('.thumbs img').forEach(img => {
                img.classList.remove('active');
            });

            el.classList.add('active');
        }
</script>
    <!-- ==== Refer Section Ends Here ==== -->


    @if (isset($new_user) && $new_user == true)
        <!-- Modal -->
        <div class="modal fade show" id="welcome-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header border-0 pt-4 px-4">
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-sm-5 pb-5">
                        <div class="text-center">
                            <img src="{{ asset('assets/landing/img/welcome.svg') }}" class="mw-100 mb-3"
                                alt="">
                            <h5 class="mb-3">{{ translate('Welcome_to') }} {{ $business_name }}!</h5>
                            <p class="m-0 mb-4">
                                {{ translate('Thanks for joining us! Your registration is under review. Hang tight, we’ll notify you once approved!') }}
                            </p>
                            <button type="button" class="border-0 outline-0 shadow-none cmn--btn"
                                data-bs-dismiss="modal">
                                {{ translate('okay') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
    @endif

@endsection
@push('script_2')
    <script>
        $(document).ready(function() {
            $('#welcome-modal').modal('show');
        });
    </script>
    <script>
        "use strict";
        $(document).ready(function() {
            "use strict";
            $('.onerror-image').on('error', function() {
                let img = $(this).data('onerror-image')
                $(this).attr('src', img);
            });
        });
    </script>
    <script>
        var tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))

        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    </script>
@endpush

