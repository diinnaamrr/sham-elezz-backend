@extends('layouts.landing.app') 

@section('title', 'المنيو - شام العز')

@section('content')
<!-- CSS Styles Moved Inside Content to Ensure Rendering -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

    :root {
        --primary-color: #800519;
        --primary-dark: #600412;
        --secondary-color: #f8f9fa;
        --text-dark: #2f3934;
        --text-muted: #717c77;
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .menu-page-wrapper {
        font-family: 'Cairo', 'Lato', sans-serif;
        background-color: #fff;
        color: var(--text-dark);
        overflow-x: hidden;
    }
    .menu-hero {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                    url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
        background-size: cover;
        background-position: center;
        padding: 80px 0;
        color: #fff;
        margin-top: 0;
    }
    .hero-content h1 {
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        font-weight: 700;
        font-size: 3rem;
    }
    .category-nav-wrapper {
        background: #fff;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        position: sticky;
        top: 0;
        z-index: 999;
        border-bottom: 1px solid #eee;
    }
    .category-nav-scroll {
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .category-nav-scroll::-webkit-scrollbar {
        display: none;
    }
    .category-tabs {
        padding: 12px 0;
        display: flex;
        flex-wrap: nowrap;
        gap: 8px;
        list-style: none;
        margin: 0;
    }

    .category-tabs .nav-link {
        color: var(--text-dark);
        font-weight: 600;
        padding: 8px 24px;
        border-radius: 50px;
        transition: var(--transition);
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        border: 1px solid #eee;
    }

    .category-tabs .nav-link:hover {
        background-color: rgba(128, 5, 25, 0.05);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .category-tabs .nav-link.active {
        background-color: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }
    .category-section {
        scroll-margin-top: 80px;
    }
    .category-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
    }
    .category-title {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 2rem;
    }
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }
    .item-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        border: 1px solid #f0f0f0;
    }
    .item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .item-img-wrapper {
        position: relative;
        height: 200px;
        width: 100%;
        overflow: hidden;
    }
    .item-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .item-info {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .item-title {
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: #1a1a1a;
    }
    .item-desc {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-bottom: 20px;
        line-height: 1.6;
    }
    .item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .item-price {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary-color);
        display: flex;
        align-items: baseline;
    }
    .item-price small {
        font-size: 0.9rem;
        margin-right: 4px;
        font-weight: 600;
    }
    .add-to-cart-btn {
        background: var(--primary-color);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: var(--transition);
        cursor: pointer;
    }
    .add-to-cart-btn:hover {
        background: var(--primary-dark);
        transform: scale(1.05);
    }
    @media (max-width: 576px) {
        .items-grid {
            grid-template-columns: 1fr;
        }
        .hero-content h1 {
            font-size: 2.2rem;
        }
        .item-img-wrapper {
            height: 250px;
        }
    }
</style>

<div class="menu-page-wrapper">
    <!-- Hero Section -->
    <div class="menu-hero">
        <div class="container text-center">
            <div class="hero-content">
                <h1 class="mb-3">{{ __('messages.meenu') }}</h1>
                <p class="lead">{{ __('messages.Discover the authentic taste and exceptional service at Sham Al Ezz') }}</p>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="category-nav-wrapper">
        <div class="container">
            <div class="category-nav-scroll">
                <div class="category-tabs">
                    @foreach ($categories as $category)
                        <a class="nav-link" href="#category-{{ $category->id }}">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        @foreach ($categories as $category)
            <div id="category-{{ $category->id }}" class="category-section mb-5">
                <div class="category-header mb-4">
                    <h2 class="category-title">{{ $category->name }}</h2>
                </div>

                <div class="items-grid">
                    @foreach ($category->items as $item)
                        <div class="item-card">
                            @if($item->image)
                                <div class="item-img-wrapper">
                                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" class="item-img">
                                </div>
                            @endif
                            <div class="item-info">
                                <h5 class="item-title">{{ $item->name }}</h5>
                                <p class="item-desc">{{ Str::limit($item->description, 100) }}</p>
                                <div class="item-footer">
                                    <div class="item-price">
                                        <span>{{ $item->price }}</span> 
                                        <small>ج.م</small>
                                    </div>
                                    <button class="add-to-cart-btn">أطلب المنتج</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const sections = document.querySelectorAll(".category-section");
        const navLinks = document.querySelectorAll(".category-tabs .nav-link");

        $(window).scroll(function() {
            let current = "";
            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute("id");
                }
            });

            navLinks.forEach((link) => {
                link.classList.remove("active");
                if (link.getAttribute("href").includes(current)) {
                    link.classList.add("active");
                }
            });
        });
        $('.category-tabs .nav-link').on('click', function(event) {
            if (this.hash !== "") {
                event.preventDefault();
                var hash = this.hash;
                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 120
                }, 600);
            }
        });
    });
</script>
@endsection
