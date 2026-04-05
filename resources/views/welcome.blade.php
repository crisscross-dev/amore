@extends('layouts.app-public')

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
  document.addEventListener('DOMContentLoaded', function() {
    var toastEl = document.getElementById('popupToast');
    var toast = new bootstrap.Toast(toastEl, {
      delay: 3000
    });
    toast.show();
  });
</script>
@endif

<div class="d-flex align-items-center justify-content-center text-center content" id="hero-content">
  <div class="text-white">
    <h6 class="mb-4 d-inline-block">SY 2025-2026</h6>
    <h1 class="mb-4 heading text-white hero-shadow">We are now accepting<br>applications for admission.</h1>
    <div class="d-flex gap-3 justify-content-center">
      <a href="{{ route('admissions.selection') }}" class="content_homepage btn btn-primary rounded-pill button" style="min-width: 220px;">
        <i class="fas fa-file-alt me-2"></i>Apply for Admission
      </a>
      <a href="{{ route('register') }}" id="registerAccessTrigger" class="content_homepage btn btn-outline-light rounded-pill button" style="min-width: 220px;">
        <i class="fas fa-user-plus me-2"></i>Create Account
      </a>
    </div>
  </div>
</div>

<div class="modal fade" id="facultyAccessModal" tabindex="-1" aria-labelledby="facultyAccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-dark border border-success" style="background-color: rgba(255, 255, 255, 0.8);">
      <div class="modal-header border-0">
        <h5 class="modal-title text-success" id="facultyAccessModalLabel">Faculty Verification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger text-start fw-semibold" role="alert">
          <i class="bi bi-exclamation-octagon-fill me-2"></i>
          This registration form is intended for faculty accounts only. Students should apply through the admissions portal instead.
        </div>
        <p class="small text-dark fw-semibold">Enter the faculty access code provided by the registrar to continue with account registration.</p>
        <form id="facultyAccessForm" class="mt-3">
          <div class="mb-3">
            <label for="facultyAccessCode" class="form-label text-dark fw-semibold">Access Code</label>
            <input type="password" class="form-control" id="facultyAccessCode" required>
            <div id="facultyAccessError" class="invalid-feedback d-none">
              The access code does not match our records. Please contact the registrar for assistance.
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="facultyAccessForm" class="btn btn-success">Continue</button>
      </div>
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
    <p class="section-subtitle">Latest announcements and updates from Amore Academy</p>
  </div>
  <div class="container mt-4">
    @if(isset($publicAnnouncements) && $publicAnnouncements->count() > 0)
    <div class="row g-4">
      @foreach($publicAnnouncements as $announcement)
      <div class="col-lg-4 col-md-6">
        <div class="news-card h-100">
          @php
          $imageAttachment = collect($announcement->attachments ?? [])->first(function($att) {
          return in_array(strtolower($att['type'] ?? ''), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
          });
          @endphp
          @if($imageAttachment)
          <img src="{{ asset('storage/' . $imageAttachment['path']) }}"
            class="news-card-img"
            alt="{{ $announcement->title }}">
          @else
          <div class="news-card-placeholder">
            <i class="fas fa-bullhorn fa-3x text-success"></i>
          </div>
          @endif
          <div class="news-card-body">
            @if($announcement->is_pinned)
            <span class="badge bg-warning mb-2"><i class="fas fa-thumbtack me-1"></i>Pinned</span>
            @endif
            <h5 class="news-card-title">{{ $announcement->title }}</h5>
            <p class="news-card-text">{{ Str::limit(strip_tags($announcement->content), 100) }}</p>
            <div class="news-card-meta">
              <small class="text-muted">
                <i class="far fa-calendar me-1"></i>{{ $announcement->created_at->format('M d, Y') }}
              </small>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="text-center py-5">
      <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
      <p class="text-muted">No announcements at the moment. Check back later!</p>
    </div>
    @endif
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
@endsection

@push('scripts')
<div
  id="welcome-runtime-data"
  class="d-none"
  data-register-url="{{ route('register') }}"
  data-verify-url="{{ route('register.access.verify') }}"
  data-csrf-token="{{ csrf_token() }}"></div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const registerBtn = document.getElementById('registerAccessTrigger');
    const modalEl = document.getElementById('facultyAccessModal');
    const accessForm = document.getElementById('facultyAccessForm');
    const codeInput = document.getElementById('facultyAccessCode');
    const errorEl = document.getElementById('facultyAccessError');
    const runtimeData = document.getElementById('welcome-runtime-data');
    const registerUrl = runtimeData ? (runtimeData.dataset.registerUrl || '') : '';
    const verifyUrl = runtimeData ? (runtimeData.dataset.verifyUrl || '') : '';
    const csrfToken = runtimeData ? (runtimeData.dataset.csrfToken || '') : '';
    const defaultErrorText = errorEl ? errorEl.textContent : '';

    if (!registerBtn || !modalEl || !accessForm || !codeInput || !errorEl) {
      return;
    }

    let modalInstance = null;
    const fallbackBackdropClass = 'faculty-access-modal-backdrop';

    function showModalFallback() {
      modalEl.style.display = 'block';
      modalEl.classList.add('show');
      modalEl.removeAttribute('aria-hidden');
      modalEl.setAttribute('aria-modal', 'true');
      document.body.classList.add('modal-open');

      const backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show ' + fallbackBackdropClass;
      document.body.appendChild(backdrop);
    }

    function hideModalFallback() {
      modalEl.classList.remove('show');
      modalEl.style.display = 'none';
      modalEl.setAttribute('aria-hidden', 'true');
      modalEl.removeAttribute('aria-modal');
      document.body.classList.remove('modal-open');

      document.querySelectorAll('.' + fallbackBackdropClass).forEach(function(backdrop) {
        backdrop.remove();
      });
    }

    function showModal() {
      if (modalInstance) {
        modalInstance.show();
        return;
      }

      showModalFallback();
    }

    function hideModal() {
      if (modalInstance) {
        modalInstance.hide();
        return;
      }

      hideModalFallback();
    }

    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
      modalInstance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
    } else {
      modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(dismissBtn) {
        dismissBtn.addEventListener('click', function() {
          hideModalFallback();
        });
      });

      modalEl.addEventListener('click', function(event) {
        if (event.target === modalEl) {
          hideModalFallback();
        }
      });

      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modalEl.classList.contains('show')) {
          hideModalFallback();
        }
      });
    }

    registerBtn.addEventListener('click', function(event) {
      event.preventDefault();
      codeInput.value = '';
      codeInput.classList.remove('is-invalid');
      if (defaultErrorText) {
        errorEl.textContent = defaultErrorText;
      }
      errorEl.classList.add('d-none');
      showModal();
    });

    accessForm.addEventListener('submit', async function(event) {
      event.preventDefault();
      const enteredCode = codeInput.value.trim();

      if (!enteredCode || !verifyUrl || !registerUrl) {
        codeInput.classList.add('is-invalid');
        errorEl.textContent = 'Access code is required.';
        errorEl.classList.remove('d-none');
        return;
      }

      try {
        const response = await fetch(verifyUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({
            access_code: enteredCode
          }),
        });

        if (response.ok) {
          codeInput.classList.remove('is-invalid');
          if (defaultErrorText) {
            errorEl.textContent = defaultErrorText;
          }
          errorEl.classList.add('d-none');
          hideModal();
          window.location.href = registerUrl;
          return;
        }

        const payload = await response.json().catch(() => ({}));
        codeInput.classList.add('is-invalid');
        errorEl.textContent = payload.message || 'Invalid access code. Please contact the registrar.';
        errorEl.classList.remove('d-none');
      } catch (error) {
        codeInput.classList.add('is-invalid');
        errorEl.textContent = 'Unable to verify access code right now. Please try again.';
        errorEl.classList.remove('d-none');
      }

    });
  });
</script>
@endpush