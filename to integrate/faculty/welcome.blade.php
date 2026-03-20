@extends('layouts.app')

@section('title', 'Home - Amore Academy')

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
@endif

  <div class="d-flex align-items-center justify-content-center text-center content" id="hero-content" id="home">
    <div class="text-white">
      <h6 class="mb-4 d-inline-block">SY 2025-2026</h6>
      <h1 class="mb-4 heading text-white">We are now accepting<br>applications for admission.</h1>
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

  <section id="about" class="py-5">
    <div class="container text-white">
      <h2 class="mb-3">About Us</h2>
      <p class="opacity-75">Amore Academy is committed to excellence in education and character formation. This section can include your mission, vision, and brief history.</p>
    </div>
  </section>

  <section id="news" class="py-5">
    <div class="container text-white">
      <h2 class="mb-3">News</h2>
      <p class="opacity-75">Latest announcements and updates will appear here.</p>
    </div>
  </section>

  <section id="faculty" class="py-5">
    <div class="container text-white">
      <h2 class="mb-3">Faculty</h2>
      <p class="opacity-75">Meet our dedicated faculty and staff.</p>
    </div>
  </section>

  <section id="gallery" class="py-5">
    <div class="container text-white">
      <h2 class="mb-3">Gallery</h2>
      <p class="opacity-75">Photos and highlights from campus activities.</p>
    </div>
  </section>

  <section id="contacts" class="py-5">
    <div class="container text-white">
      <h2 class="mb-3">Contacts</h2>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const registerBtn = document.getElementById('registerAccessTrigger');
  const modalEl = document.getElementById('facultyAccessModal');
  const accessForm = document.getElementById('facultyAccessForm');
  const codeInput = document.getElementById('facultyAccessCode');
  const errorEl = document.getElementById('facultyAccessError');
  const FACULTY_ACCESS_CODE = @json(config('app.faculty_registration_code', 'FACULTY-ACCESS-2026'));
  let modalInstance = null;

  if (modalEl && window.bootstrap && window.bootstrap.Modal) {
    modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
  }

  if (registerBtn && modalInstance) {
    registerBtn.addEventListener('click', function (event) {
      event.preventDefault();
      codeInput.value = '';
      codeInput.classList.remove('is-invalid');
      errorEl.classList.add('d-none');
      modalInstance.show();
    });
  }

  if (accessForm) {
    accessForm.addEventListener('submit', function (event) {
      event.preventDefault();
      const enteredCode = codeInput.value.trim();
      if (enteredCode && enteredCode.toUpperCase() === FACULTY_ACCESS_CODE.toUpperCase()) {
        codeInput.classList.remove('is-invalid');
        errorEl.classList.add('d-none');
        if (modalInstance) {
          modalInstance.hide();
        }
        window.location.href = @json(route('register'));
      } else {
        codeInput.classList.add('is-invalid');
        errorEl.classList.remove('d-none');
      }
    });
  }
});
</script>
@endpush



