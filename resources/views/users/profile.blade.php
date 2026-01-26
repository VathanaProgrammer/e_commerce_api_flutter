@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <x-widget title="My Profile">

                <div class="row">
                    <!-- Left Column - Avatar & Quick Actions -->
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <div class="profile-card p-4 rounded-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); animation: fadeInLeft 0.5s ease forwards;">
                            <!-- Avatar -->
                            <div class="profile-avatar-wrapper mx-auto mb-4 position-relative" style="width: 140px; height: 140px;">
                                <div class="profile-avatar w-100 h-100 rounded-circle bg-gradient d-flex items-center justify-content-center overflow-hidden shadow-lg"
                                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 4px;">
                                    <div class="inner-circle w-100 h-100 rounded-circle bg-white d-flex align-items-center justify-content-center overflow-hidden">
                                        @if(auth()->user()->profile_image_url)
                                            <img src="{{ auth()->user()->profile_image_url }}" alt="Profile Image" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <i class="bi bi-person-fill text-gray-400" style="font-size: 4rem;"></i>
                                        @endif
                                    </div>
                                </div>
                                <!-- Status indicator -->
                                <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" 
                                      style="width: 24px; height: 24px; border: 3px solid white;"></span>
                            </div>

                            <!-- User Name & Role -->
                            <h4 class="fw-bold mb-1" style="color: #1e293b;">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h4>
                            <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                            
                            <span class="badge rounded-pill px-4 py-2 mb-4" 
                                  style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 0.85rem;">
                                <i class="bi bi-shield-check me-1"></i>{{ ucfirst(auth()->user()->role) }}
                            </span>

                            <!-- Profile Actions -->
                            <div class="d-grid gap-2 mt-4">
                                <a href="{{ route('users.edit', auth()->user()->id) }}" 
                                   class="btn btn-primary rounded-pill py-2 hover-lift">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Profile
                                </a>

                                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger rounded-pill py-2 w-100 hover-lift">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Profile Details -->
                    <div class="col-md-8">
                        <div class="profile-details">
                            <h5 class="fw-bold mb-4 pb-2 border-bottom" style="border-color: #667eea !important;">
                                <i class="bi bi-person-badge me-2 text-primary"></i>Profile Information
                            </h5>

                            <div class="row g-4">
                                <!-- First Name -->
                                <div class="col-md-6 profile-item" style="animation: fadeInUp 0.4s ease 0.1s forwards; opacity: 0;">
                                    <div class="info-card p-3 rounded-3 h-100" style="background: #f8fafc; border-left: 4px solid #667eea;">
                                        <label class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">First Name</label>
                                        <p class="mb-0 fw-semibold" style="color: #1e293b; font-size: 1.1rem;">{{ auth()->user()->first_name }}</p>
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6 profile-item" style="animation: fadeInUp 0.4s ease 0.2s forwards; opacity: 0;">
                                    <div class="info-card p-3 rounded-3 h-100" style="background: #f8fafc; border-left: 4px solid #764ba2;">
                                        <label class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">Last Name</label>
                                        <p class="mb-0 fw-semibold" style="color: #1e293b; font-size: 1.1rem;">{{ auth()->user()->last_name }}</p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-12 profile-item" style="animation: fadeInUp 0.4s ease 0.3s forwards; opacity: 0;">
                                    <div class="info-card p-3 rounded-3" style="background: #f8fafc; border-left: 4px solid #10b981;">
                                        <label class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                            <i class="bi bi-envelope me-1"></i>Email Address
                                        </label>
                                        <p class="mb-0 fw-semibold" style="color: #1e293b; font-size: 1.1rem;">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 profile-item" style="animation: fadeInUp 0.4s ease 0.4s forwards; opacity: 0;">
                                    <div class="info-card p-3 rounded-3 h-100" style="background: #f8fafc; border-left: 4px solid #f59e0b;">
                                        <label class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                            <i class="bi bi-shield me-1"></i>Role
                                        </label>
                                        <p class="mb-0 fw-semibold" style="color: #1e293b; font-size: 1.1rem;">{{ ucfirst(auth()->user()->role) }}</p>
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="col-md-6 profile-item" style="animation: fadeInUp 0.4s ease 0.5s forwards; opacity: 0;">
                                    <div class="info-card p-3 rounded-3 h-100" style="background: #f8fafc; border-left: 4px solid #ec4899;">
                                        <label class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                            <i class="bi bi-gender-ambiguous me-1"></i>Gender
                                        </label>
                                        <p class="mb-0 fw-semibold" style="color: #1e293b; font-size: 1.1rem;">{{ auth()->user()->gender ?? 'Not specified' }}</p>
                                    </div>
                                </div>

                                <!-- Last Login -->
                                <div class="col-md-12 profile-item" style="animation: fadeInUp 0.4s ease 0.6s forwards; opacity: 0;">
                                    <div class="info-card p-3 rounded-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #22c55e;">
                                        <label class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                            <i class="bi bi-clock-history me-1"></i>Last Login
                                        </label>
                                        <p class="mb-0 fw-semibold" style="color: #1e293b; font-size: 1.1rem;">
                                            {{ auth()->user()->last_login?->format('D, M d Y H:i') ?? 'Never' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </x-widget>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
        from {
            opacity: 0;
            transform: translateY(15px);
        }
    }

    @keyframes fadeInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
        from {
            opacity: 0;
            transform: translateX(-15px);
        }
    }

    .info-card {
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .profile-avatar-wrapper {
        transition: all 0.3s ease;
    }

    .profile-avatar-wrapper:hover {
        transform: scale(1.05);
    }

    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>
@endsection
