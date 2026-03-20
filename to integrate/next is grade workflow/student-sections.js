document.addEventListener('DOMContentLoaded', () => {
  console.log('Student Sections JS loaded');

  // Dynamic fetch sections by grade (filter UI)
  const gradeFilter = document.getElementById('studentGradeFilter');
  const sectionFilter = document.getElementById('studentSectionFilter');

  const loadSections = async (grade) => {
    if (!sectionFilter) return;
    sectionFilter.innerHTML = '<option value="">All Sections</option>';

    if (!grade) {
      return;
    }

    try {
      const response = await fetch(`/admin/sections/by-grade?grade_level=${encodeURIComponent(grade)}`);
      if (!response.ok) return;
      const sections = await response.json();
      const selected = sectionFilter.getAttribute('data-selected');

      sections.forEach((section) => {
        const option = document.createElement('option');
        option.value = section.id;
        option.textContent = section.name;
        if (selected && String(selected) === String(section.id)) {
          option.selected = true;
        }
        sectionFilter.appendChild(option);
      });
    } catch (error) {
      console.warn('Failed to load sections by grade', error);
    }
  };

  if (gradeFilter && sectionFilter) {
    gradeFilter.addEventListener('change', (e) => {
      sectionFilter.setAttribute('data-selected', '');
      loadSections(e.target.value);
    });

    if (gradeFilter.value) {
      loadSections(gradeFilter.value);
    }
  }

  // Optional: dynamic fetch sections by grade when grade changes (per-student select)
  const selects = document.querySelectorAll('select.section-select');
  selects.forEach(sel => {
    sel.addEventListener('change', (e) => {
      const form = sel.closest('form');
      if (!form) return;
      // Do nothing; submit button exists. If auto-submit is preferred, uncomment:
      // form.submit();
    });
  });
});
