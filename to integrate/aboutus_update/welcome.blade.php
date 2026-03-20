@extends('layouts.app')

@section('title', 'Home - Amore Academy')

@push('styles')
@vite(['resources/css/home/news-faculty.css'])
@endpush

@push('scripts')
@vite(['resources/js/home/news-faculty.js'])
@endpush

@section('content')
@if(session('popup'))
<div class="position-fixed bottom-0 end-0 p-3 popup-toast-container">
    <div id="popupToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('popup') }}
            </div>
            <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('popupToast');
        var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    });
</script>
@endif

  <div class="d-flex align-items-center justify-content-center text-center content" id="hero-content" id="home">
    <div class="text-white">
      <h6 class="mb-4 d-inline-block">SY 2025-2026</h6>
      <h1 class="mb-4 heading text-white hero-shadow">We are now accepting<br>applications for admission.</h1>
      <div class="d-flex gap-3 justify-content-center">
        <a href="{{ route('admissions.selection') }}" class="content_homepage btn btn-primary rounded-pill button">
          <i class="fas fa-file-alt me-2"></i>Apply for Admission
        </a>
        <a href="/register" class="content_homepage btn btn-outline-light rounded-pill button">
          <i class="fas fa-user-plus me-2"></i>Register Account
        </a>
      </div>
    </div>
  </div>

  <section id="about" class="about-section py-5">
    <div class="container">
      <h2 class="about-title text-center mb-4">ABOUT US</h2>

      <div class="about-card about-card-wide mb-4">
        <h3 class="about-card-title">Philosophy</h3>
        <p>Education is an essential tool that can be utilized to overcome challenges of a changing world.</p>
      </div>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="about-card h-100">
            <h3 class="about-card-title">Vision</h3>
            <p>Empower students to obtain, exemplify, be coherent and value knowledge and skills that will enable them to overcome lifelong learners contributing and participating to globalization as they adhere to school's core values; love, piety, and knowledge.</p>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="about-card h-100">
            <h3 class="about-card-title">Mission</h3>
            <p>To allow students to experience success while promoting high quality education in an inclusive community that builds a foundation for lifelong learning.</p>
          </div>
        </div>
      </div>

      <div class="about-card about-card-wide my-4">
        <h3 class="about-card-title">Goals and Objectives</h3>
        <ul class="about-list">
          <li>To empower student, embrace learning in order to fulfill their own goals;</li>
          <li>To build learners emotional, social, mental and physical well-being in preparation for future endeavors;</li>
          <li>To provide a safe and supportive community for learners to have the opportunity to explore, take risks and think for themselves;</li>
          <li>In partnership with parents and the community, prepare all learners to be responsible citizens ready to meet the challenges of a technologically advanced society.</li>
        </ul>
      </div>

      <div class="about-card about-card-wide">
        <h3 class="about-card-title">School History</h3>
        <p>This section is reserved for the school history timeline and narrative. Content to be added.</p>
      </div>
    </div>
  </section>

  <section id="news" class="news-section py-5">
    <div class="container text-center">
      <h2 class="about-title text-center mb-4">NEWS</h2>
      <p class="section-subtitle">Latest announcements and updates will appear here.</p>
    </div>
    <div class="container mt-4">
      <div class="news-card mx-auto"></div>
    </div>
  </section>

  <section id="faculty" class="faculty-section py-5">
    <div class="container">
      <div class="faculty-banner text-center mb-5">
        <h2 class="faculty-banner-title">MEET OUR TEAM</h2>
        <p class="faculty-banner-subtitle">Dedicated educators committed to inspiring excellence and nurturing the potential in every student.</p>
      </div>

      <div class="text-center mb-4">
        <h2 class="about-title text-center mb-4">LEADERSHIP TEAM</h2>
      </div>

      <div class="row g-4 justify-content-center">
        <div class="col-lg-5">
          <div class="team-card team-card-featured">
            <div class="team-avatar"></div>
            <h4>Leticia L. Villanueva</h4>
            <p>School President</p>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-2 justify-content-center">
        <div class="col-lg-4 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Mitca Laurence J. Manuel</h4>
            <p>Vice President for Finance and Registration</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Maria Amalia F. Calvo</h4>
            <p>School Principal</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Princess S. Jamon</h4>
            <p>Assistant Principal</p>
          </div>
        </div>
      </div>

      <div class="text-center mt-5 mb-4">
        <h2 class="about-title text-center mb-4">FACULTY</h2>
      </div>

      <div class="row g-4">
        <div class="col-lg-3 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Joner Antonio L. Santos</h4>
            <p>Guidance Designate</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>John Iriez A. Rogador</h4>
            <p>Guidance Staff</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Fritzie S. Tapia</h4>
            <p>JHS Coordinator</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Rowena L. De Leon</h4>
            <p>Clinic-in-Charge</p>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-1 justify-content-center">
        <div class="col-lg-3 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Loran Camille V. Sara</h4>
            <p>School Librarian</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Violeta U. Creus</h4>
            <p>Teacher-Librarian</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="team-card">
            <div class="team-avatar"></div>
            <h4>Krizza Jill S. Tagomata</h4>
            <p>LIS-ICT Coordinator</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="gallery" class="py-5">
    <div class="container text-white text-center">
      <h2 class="about-title text-center mb-4">Gallery</h2>
      <p class="opacity-75">Photos and highlights from campus activities.</p>
    </div>
    <div class="container mt-4">
      <div class="row g-4">
        <div class="col-lg-3 col-md-6">
          <div class="gallery-card"></div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="gallery-card"></div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="gallery-card"></div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="gallery-card"></div>
        </div>
      </div>
    </div>
  </section>

  <section id="contacts" class="py-5">
    <div class="container text-white text-center">
      <h2 class="about-title text-center mb-4">Contacts</h2>
      <p class="opacity-75">Reach us via phone, email, or visit our address.</p>
    </div>
  </section>

  <footer class="contacts-footer">
    <div class="container text-white py-3">
      <div class="row text-center g-3">
        <div class="col-12 col-md-4">
          <small>Have a questions?</small>
        </div>
        <div class="col-12 col-md-4">
          <small>+63 9123456786</small>
        </div>
        <div class="col-12 col-md-4">
          <small>amore@gmail.com</small>
        </div>
      </div>
    </div>
  </footer>
@endsection



