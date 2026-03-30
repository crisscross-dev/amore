@extends('layouts.app-public')

@section('title', 'Login - Amore Academy')

@vite(['resources/css/auth.css', 'resources/js/auth.js'])

@section('content')
<div class="container d-flex justify-content-center align-items-center login-center-container">
  <div class="col-md-5 ">
    <div class="card shadow-lg p-4 animate__animated animate__fadeIn login-form" id="hero-content">
      <div class="text-center mb-4">
        <h2 class="heading mb-2">Login to Your Account</h2>
        <p class="fw-bold fs-6" style="color: #495057;">Welcome back to Amore Academy</p>
      </div>
      <form method="post" action="{{ route('loginAuth') }}">
        @csrf
        
        <!-- Error Messages -->
        @if($errors->any())
            <x-ui.alert type="danger" :dismissible="true">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <!-- Success Message -->

        <div class="form-group mb-3">
          <x-form.label for="email">Email address</x-form.label>
          <x-form.input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Enter email" 
            required 
          />
        </div>
        
        <div class="form-group mb-3 position-relative">
          <x-form.label for="password">Password</x-form.label>
          <div class="input-group">
            <input 
              type="password" 
              class="form-control @error('password') is-invalid @enderror" 
              id="password" 
              name="password" 
              placeholder="Password"
              required
            >
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
              <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
          </div>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
        
        <x-ui.button type="submit" variant="secondary" class="login-button" :fullWidth="true">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </x-ui.button>
        
        <div class="mt-2 mb-0 text-center">
          <a href="{{ route('password.request') }}" class="small forgot-password-link">Forgot password?</a>
        </div>
      </form>
      <div class="mt-2 text-center">
        <a
          href="{{ route('register') }}"
          id="registerAccessTrigger"
          class="btn create-account-button w-100"
        >
          <i class="bi bi-person-plus me-2"></i>Create Account
        </a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="facultyAccessModal" tabindex="-1" aria-labelledby="facultyAccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title text-success" id="facultyAccessModalLabel">Faculty Verification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2">
        <p class="mb-2 text-muted small">Creating an account requires a faculty access code from the registrar.</p>
        <form id="facultyAccessForm" class="mt-2">
          <label for="facultyAccessCode" class="form-label">Access Code</label>
          <input type="password" class="form-control" id="facultyAccessCode" autocomplete="off" required>
          <div id="facultyAccessError" class="invalid-feedback d-none">
            Invalid access code. Please contact the registrar.
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="facultyAccessForm" class="btn btn-success">Continue</button>
      </div>
    </div>
  </div>
</div>

<div
  id="login-runtime-data"
  class="d-none"
  data-register-url="{{ route('register') }}"
  data-verify-url="{{ route('register.access.verify') }}"
  data-csrf-token="{{ csrf_token() }}"
></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const registerBtn = document.getElementById('registerAccessTrigger');
  const modalEl = document.getElementById('facultyAccessModal');
  const accessForm = document.getElementById('facultyAccessForm');
  const codeInput = document.getElementById('facultyAccessCode');
  const errorEl = document.getElementById('facultyAccessError');
  const runtimeData = document.getElementById('login-runtime-data');
  const registerUrl = runtimeData ? (runtimeData.dataset.registerUrl || '') : '';
  const verifyUrl = runtimeData ? (runtimeData.dataset.verifyUrl || '') : '';
  const csrfToken = runtimeData ? (runtimeData.dataset.csrfToken || '') : '';
  const defaultErrorText = errorEl.textContent;

  if (!registerBtn || !modalEl || !window.bootstrap || !window.bootstrap.Modal || !accessForm || !codeInput || !errorEl) {
    return;
  }

  const modalInstance = window.bootstrap.Modal.getOrCreateInstance(modalEl);

  registerBtn.addEventListener('click', function (event) {
    event.preventDefault();
    codeInput.value = '';
    codeInput.classList.remove('is-invalid');
    errorEl.classList.add('d-none');
    modalInstance.show();
  });

  accessForm.addEventListener('submit', async function (event) {
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
        body: JSON.stringify({ access_code: enteredCode }),
      });

      if (response.ok) {
        codeInput.classList.remove('is-invalid');
        errorEl.textContent = defaultErrorText;
        errorEl.classList.add('d-none');
        modalInstance.hide();
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
