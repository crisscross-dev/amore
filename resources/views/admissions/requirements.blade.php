@extends('layouts.admission')

@section('title', 'Admission Requirements - Amore Academy')

@section('content')

@vite(['resources/css/admissions.css'])

<div class="enrollment-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="enrollment-card">
                    <!-- Success Header -->
                    <div class="text-center mb-4">
                        <div class="success-icon mb-3">
                            <i class="fas fa-check-circle" style="font-size: 5rem; color: #20c997;"></i>
                        </div>
                        <h2 class="mb-2" style="color: #20c997; font-weight: bold; text-align: center;">Application Submitted Successfully!</h2>
                        <p class="mb-4" style="color: #6c757d; font-size: 1.1rem; text-align: center;">
                            Your application has been received and is now pending review.
                        </p>
                        <div class="alert" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white; border: none; border-radius: 10px; padding: 1.5rem;">
                            <strong style="font-size: 1.1rem;">Application ID:</strong> <span style="padding: 0.3rem 0.8rem; border-radius: 5px;">#{{ $admission->id }}</span><br>
                            <strong style="font-size: 1.1rem;">Name:</strong> <span style="padding: 0.3rem 0.8rem; border-radius: 5px;">{{ $admission->full_name }}</span><br>
                            <strong style="font-size: 1.1rem;">Level:</strong> <span style="padding: 0.3rem 0.8rem; border-radius: 5px;">{{ strtoupper($admission->school_level) }}</span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Requirements Section -->
                    <div class="requirements-section">
                        <h4 class="mb-3" style="color: #198754; font-weight: bold; font-size: 1.8rem;">
                            <i class="fas fa-clipboard-list me-2" style="color: #20c997;"></i>
                            Required Documents to Submit
                        </h4>
                        <p class="mb-4" style="color: #495057; font-size: 1.05rem;">
                            Please prepare and submit the following documents to the school office for processing your application:
                        </p>

                        <div class="requirements-list">
                            @php
                                $requirements = [];
                                
                                // Base requirements for all
                                $baseRequirements = [
                                    'PSA Birth Certificate (Original)',
                                    'Form 138 (Report Card) (Original)',
                                    'Certificate of Completion (Photocopy)',
                                    'Certificate of Good Moral'
                                ];

                                if ($admission->school_type === 'Public') {
                                    $requirements = $baseRequirements;
                                } elseif ($admission->school_type === 'Private') {
                                    if ($admission->private_type === 'ESC') {
                                        $requirements = array_merge($baseRequirements, ['ESC Certificate']);
                                    } else {
                                        $requirements = $baseRequirements;
                                    }
                                }
                            @endphp

                            @foreach($requirements as $index => $requirement)
                                <div class="requirement-item">
                                    <div class="requirement-number">{{ $index + 1 }}</div>
                                    <div class="requirement-text">
                                        <strong style="color: #2c3e50; font-size: 1.1rem;">{{ $requirement }}</strong>
                                    </div>
                                    <div class="requirement-icon">
                                        <i class="fas fa-file-alt" style="color: #20c997; font-size: 1.8rem;"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- School Type Badge -->
                        <div class="mt-4 p-3 rounded" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-school me-3" style="font-size: 2rem; color: white;"></i>
                                <div>
                                    <strong style="font-size: 1.2rem;">Previous School Type:</strong><br>
                                    <span class="badge" style="background: rgba(255,255,255,0.3); color: white; font-size: 1rem; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                                        {{ $admission->school_type }}
                                        @if($admission->school_type === 'Private')
                                            - {{ $admission->private_type }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Important Notes -->
                    <div class="notes-section">
                        <h5 class="mb-3" style="color: #198754; font-weight: bold; font-size: 1.5rem;">
                            <i class="fas fa-info-circle me-2" style="color: #20c997;"></i>
                            Important Notes:
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-2" style="color: #2c3e50; font-size: 1.05rem;">
                                <i class="fas fa-check me-2" style="color: #20c997; font-size: 1.2rem;"></i>
                                Please submit all documents within <strong style="color: #dc3545;">7 days</strong> from the date of application.
                            </li>
                            <li class="mb-2" style="color: #2c3e50; font-size: 1.05rem;">
                                <i class="fas fa-check me-2" style="color: #20c997; font-size: 1.2rem;"></i>
                                All original documents will be returned after verification.
                            </li>
                            <li class="mb-2" style="color: #2c3e50; font-size: 1.05rem;">
                                <i class="fas fa-check me-2" style="color: #20c997; font-size: 1.2rem;"></i>
                                Incomplete requirements may delay the processing of your application.
                            </li>
                            <li class="mb-2" style="color: #2c3e50; font-size: 1.05rem;">
                                <i class="fas fa-check me-2" style="color: #20c997; font-size: 1.2rem;"></i>
                                You will be notified via the contact information provided once your application is reviewed.
                            </li>
                            <li class="mb-2" style="color: #2c3e50; font-size: 1.05rem;">
                                <i class="fas fa-check me-2" style="color: #20c997; font-size: 1.2rem;"></i>
                                For inquiries, please contact the school office during office hours.
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admissions.selection') }}" class="btn btn-lg"
                            style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white; border: none; padding: 0.75rem 2rem; font-weight: bold; border-radius: 50px; box-shadow: 0 4px 15px rgba(25, 135, 84, 0.4);">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Admissions
                        </a>

                        <!-- Push this button to the right -->
                        <button onclick="window.print()" class="btn btn-lg ms-auto"
                            style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white; border: none; padding: 0.75rem 2rem; font-weight: bold; border-radius: 50px;">
                            <i class="fas fa-print me-2"></i>
                            Print Requirements List
                        </button>
                    </div>

                    


                </div>
                
            </div>
            
        </div>
        
    </div>
    
</div>

<style>
    .success-icon {
        animation: scaleIn 0.5s ease-out;
    }

    @keyframes scaleIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .requirements-list {
        background: linear-gradient(135deg, #e3f2fd 0%, #c8e6c9 100%);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .requirement-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #198754;
    }

    .requirement-item:hover {
        transform: translateX(10px);
        box-shadow: 0 6px 20px rgba(32, 201, 151, 0.3);
        border-left-color: #20c997;
    }

    .requirement-number {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .requirement-text {
        flex-grow: 1;
    }

    .requirement-icon {
        font-size: 1.5rem;
        margin-left: 1rem;
        flex-shrink: 0;
    }

    .notes-section ul li {
        padding: 0.5rem 0;
        border-bottom: 2px solid #e3f2fd;
    }

    .notes-section ul li:last-child {
        border-bottom: none;
    }

    @media print {
        .btn, .navbar, .top-bar, footer {
            display: none !important;
        }
        
        .enrollment-wrapper {
            background: white !important;
        }
        
        .enrollment-card {
            box-shadow: none !important;
            border: 1px solid #ddd;
        }
    }

    @media (max-width: 768px) {
        .requirement-item {
            flex-wrap: wrap;
        }
        
        .requirement-icon {
            margin-left: 0;
            margin-top: 0.5rem;
        }
    }
</style>

@endsection
