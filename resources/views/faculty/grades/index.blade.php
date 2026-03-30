@extends('layouts.app')

@section('title', 'Manage Grades - Faculty Dashboard - Amore Academy')

@section('content')

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@vite(['resources/css/layouts/dashboard-roles/dashboard-faculty.css','resources/css/faculty/grade-management.css'])

<style>
  .grade-cards-wrap {
    display: grid;
    gap: 1rem;
  }

  .grade-card {
    display: block;
    border: 1px solid rgba(22, 101, 52, 0.2);
    border-radius: 14px;
    background: #ffffff;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
  }

  .grade-card:hover {
    transform: translateY(-2px);
    border-color: rgba(22, 101, 52, 0.35);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
  }

  .grade-card:focus-visible {
    outline: 3px solid rgba(22, 163, 74, 0.35);
    outline-offset: 2px;
  }

  .grade-card .grade-card-preview {
    padding: 1rem;
  }

  .grade-card .card-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .grade-card .hierarchy-label {
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.1rem;
  }

  .grade-card .hierarchy-value {
    color: #0f172a;
    font-weight: 600;
  }

  .grade-card .meta-item {
    border: 1px solid #dcfce7;
    background: #f8fafc;
    border-radius: 10px;
    padding: 0.65rem 0.75rem;
    margin-top: 0.75rem;
    color: #14532d;
  }

  .grade-card .open-hint {
    margin-top: 0.75rem;
    color: #15803d;
    font-size: 0.8rem;
    font-weight: 600;
  }
</style>

<div class="dashboard-container">
  <div class="container-fluid px-4">
    <div class="row">

      <main class="col-12">


        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error:</strong>
          <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @include('faculty.grades.partials.index-live-section')
      </main>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var liveSectionEl = document.getElementById('facultyManageGradesLiveSection');
    var pollingTimerId = null;
    var isRefreshing = false;

    function refreshManageGradesSection() {
      if (!liveSectionEl || isRefreshing || document.hidden) {
        return;
      }

      var liveUrl = liveSectionEl.getAttribute('data-live-url');
      if (!liveUrl) {
        return;
      }

      isRefreshing = true;

      fetch(liveUrl, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(function(response) {
          if (!response.ok) {
            throw new Error('Live update request failed');
          }

          return response.json();
        })
        .then(function(payload) {
          if (!payload || !payload.html) {
            return;
          }

          var tempWrapper = document.createElement('div');
          tempWrapper.innerHTML = payload.html.trim();
          var freshSection = tempWrapper.firstElementChild;

          if (!freshSection) {
            return;
          }

          liveSectionEl.replaceWith(freshSection);
          liveSectionEl = freshSection;
        })
        .catch(function() {
          // Silently ignore polling failures to avoid interrupting navigation.
        })
        .finally(function() {
          isRefreshing = false;
        });
    }

    pollingTimerId = window.setInterval(refreshManageGradesSection, 10000);

    document.addEventListener('visibilitychange', function() {
      if (!document.hidden) {
        refreshManageGradesSection();
      }
    });

    window.addEventListener('beforeunload', function() {
      if (pollingTimerId) {
        window.clearInterval(pollingTimerId);
      }
    });
  });
</script>
@endsection
