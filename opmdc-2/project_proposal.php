<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Project Proposal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/ui-animations.css">
  <style>
    body { font-family: 'Inter', sans-serif; }  
    /* Larger typography for a formal feel */
  .form-label { font-size: 1.1rem; font-weight: 700; color: #25313a; }
  .form-input { font-size: 1.12rem; padding-top: 1rem; padding-bottom: 1rem; }
  .form-textarea { font-size: 1.08rem; min-height: 220px; padding-top: 1rem; padding-bottom: 1rem; }
  .btn-primary { background: #0ea5e9; color: #fff; padding: .7rem 1.25rem; border-radius: .5rem; font-weight:700; box-shadow: 0 6px 18px rgba(14,45,80,0.08); }
    .hero-icon { width: 64px; height: 64px; }
    .widget { min-width: 180px; }
    .dropzone { border-style: dashed; }
    /* Dashboard shared styles (logo, sidebar, timeline, notifications) */
  .logo-formal { width: 8rem; height: 8rem; border-radius: 9999px; background: #fff; display:block; object-fit:cover; margin-left:auto; margin-right:auto; margin-bottom:0.9rem; border:2px solid #e6eef8; box-shadow:0 8px 28px rgba(14,45,80,0.08); padding:6px; transition: transform 0.18s ease, box-shadow 0.18s ease; }
    .logo-formal:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 28px rgba(14,45,80,0.08); }
    .timeline-item { position: relative; padding-bottom: 2rem; padding-left: 2.5rem; border-left: 2px solid #e2e8f0; }
    .timeline-item:last-child { border-left: 2px solid transparent; padding-bottom: 0; }
    .timeline-dot { position: absolute; left: -0.6rem; top: 0.1rem; width: 1.2rem; height: 1.2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .timeline-dot-pending { background-color: #f6e05e; }
    .timeline-dot-approved { background-color: #68d391; }
    .timeline-dot-declined { background-color: #fc8181; }
    .timeline-dot-default { background-color: #cbd5e0; }
    .stepper { display:flex; align-items:center; gap:1rem; width:100%; }
    .stepper-line { height:4px; background:#e5e7eb; flex:1; position:relative; }
    .step { display:flex; flex-direction:column; align-items:center; width:120px; text-align:center; }
    .step .circle { width:18px; height:18px; border-radius:9999px; background:#cbd5e0; display:flex; align-items:center; justify-content:center; color:#fff; }
    .step.active .circle { background:#0ea5e9; }
    .step-label { font-size:13px; color:#6b7280; margin-top:8px; }
    .step.active .step-label { color:#111827; font-weight:600; }
    .nav-active { background-color: #1f2937; color: #f9fafb; }
    .notif-badge { position: absolute; top: -6px; right: -6px; background: #ef4444; color: white; font-size: 11px; padding: 2px 6px; border-radius: 9999px; border: 2px solid white; }
    .notif-dropdown { min-width: 300px; max-width: 360px; }
    .notif-item.unread { background: rgba(59,130,246,0.06); }
    .notif-item { display: flex; gap: 0.75rem; padding: 0.5rem; align-items: start; }
    .notif-item .time { font-size: 11px; color: #6b7280; }
    .notif-empty { padding: 1rem; color: #6b7280; }
  </style>
</head>
<body class="bg-gray-100">

  <div class="flex h-screen">
  <aside class="sidebar w-80 bg-gray-800 text-white flex flex-col" data-reveal="sidebar">
      <div class="p-6 text-center border-b border-gray-700">
        <img src="assets/image1.png" alt="Logo" class="logo-formal">
        <h2 id="barangay-name-header" class="text-xl font-semibold">Barangay Name</h2>
        <p class="text-xs text-gray-400">Mabini, Batangas</p>
      </div>
      <nav class="flex-grow px-4 py-6">
        <a href="barangay-dashboard.php" id="nav-dashboard" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-tachometer-alt mr-3"></i>
          Dashboard
        </a>
        <a href="#" id="nav-status" class="flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-spinner mr-3"></i>
          Status
        </a>
        <a href="#" id="nav-history" class="flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-history mr-3"></i>
          History
        </a>
        <a href="#" class="flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-user-circle mr-3"></i>
          Profile
        </a>
      </nav>
      <div class="p-4 border-t border-gray-700">
        <a href="login.html" class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-md">
          <i class="fas fa-sign-out-alt mr-3"></i>
          Logout
        </a>
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="w-full bg-white shadow-sm py-8" data-reveal="header">
        <div class="max-w-7xl mx-auto px-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <img src="assets/image1.png" alt="logo" class="w-16 h-16 rounded-full object-cover shadow-sm">
              <div>
                <h1 id="header-title" class="text-3xl font-semibold text-gray-800">Submit Project Proposal</h1>
                <p id="current-date" class="text-sm text-gray-500 mt-1">Please provide accurate details for routing to the appropriate office.</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <a href="barangay-dashboard.php" class="px-4 py-2 bg-blue-600 text-white rounded-md">Back to Dashboard</a>
            </div>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-gray-100">
  <div class="mx-auto w-full max-w-screen-2xl px-4">
          <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-12 py-12 max-w-7xl mx-auto">
              <form id="proposalForm">
                <!-- Header subtitle removed per request -->
                <div class="space-y-6">
                  <section>
                    <h3 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-3">
                      <img src="assets/image1.png" alt="icon" class="w-8 h-8 rounded-full object-cover">
                      <span>Project Details</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                      <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Project Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required class="w-full rounded border-gray-200 form-input px-3 py-2" />
                        <p id="err-title" class="text-red-600 text-xs mt-1 hidden">Project Title is required.</p>
                      </div>

                      <div class="grid grid-cols-2 gap-12">
                        <div>
                          <label class="block text-sm font-semibold text-gray-600 mb-1">Project Type <span class="text-red-500">*</span></label>
                          <select name="project_type" id="project_type" required class="w-full rounded border-gray-200 form-input px-3 py-2">
                            <option value="">Select type</option>
                            <option value="CLUP">CLUP</option>
                            <option value="CDP">CDP</option>
                            <option value="AIP">AIP</option>
                          </select>
                          <p id="err-project_type" class="text-red-600 text-xs mt-1 hidden">Please select a project type.</p>
                        </div>
                        <div>
                          <label class="block text-sm font-semibold text-gray-600 mb-1">Request Type</label>
                          <select id="requestType" name="requestType" class="w-full rounded border-gray-200 form-input px-3 py-2">
                              <option value="">Select a type</option>
                              <option value="Infrastructure">Infrastructure</option>
                              <option value="Health">Health</option>
                              <option value="Livelihood">Livelihood</option>
                              <option value="Security">Security</option>
                              <option value="Disaster">Disaster</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </section>

                  <section>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Location & Budget</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                      <div class="grid grid-cols-2 gap-10">
                        <div>
                          <label class="block text-sm font-semibold text-gray-600 mb-1">Barangay</label>
                          <select name="barangay" id="barangay" class="w-full rounded border-gray-200 form-input px-3 py-2 bg-gray-50">
                            <option value="">(auto)</option>
                          </select>
                        </div>
                        <div>
                          <label class="block text-sm font-semibold text-gray-600 mb-1">Urgency</label>
                          <select id="urgency" name="urgency" class="w-full rounded border-gray-200 form-input px-3 py-2">
                              <option value="Low">Low</option>
                              <option value="Medium" selected>Medium</option>
                              <option value="High">High</option>
                              <option value="Urgent">Urgent</option>
                          </select>
                        </div>
                      </div>

                      <div class="grid grid-cols-2 gap-10">
                        <div>
                          <label class="block text-sm font-semibold text-gray-600 mb-1">Location / Address</label>
                          <input type="text" id="location" name="location" placeholder="e.g., Purok 1, Barangay Hall" class="w-full rounded border-gray-200 form-input px-3 py-2">
                        </div>
                        <div>
                          <label class="block text-sm font-semibold text-gray-600 mb-1">Proposed Budget (PHP)</label>
                          <div class="relative">
                            <span class="absolute left-0 top-0 h-full flex items-center pl-3 text-gray-500">₱</span>
                            <input type="text" name="budget" id="budget" inputmode="decimal" class="w-full rounded border-gray-200 form-input pl-8 pr-3 py-2" placeholder="0.00" />
                          </div>
                          <p id="err-budget" class="text-red-600 text-xs mt-1 hidden">Budget must be a non-negative number.</p>
                        </div>
                      </div>
                    </div>
                  </section>

                  <section>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Additional Information</h3>
                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="6" required class="w-full rounded border-gray-200 form-textarea px-3 py-2"></textarea>
                        <p id="err-description" class="text-red-600 text-xs mt-1 hidden">Description is required.</p>
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Attachment</label>
                        <div class="flex items-start gap-4">
                          <div class="w-28 h-28 bg-gray-50 rounded overflow-hidden flex items-center justify-center border border-gray-200">
                            <img id="thumbPreview" src="assets/image1.png" alt="thumbnail" class="object-cover w-full h-full">
                          </div>
                          <div class="flex-1">
                            <label for="attachment" class="block w-full p-6 text-center rounded border-2 border-dashed border-gray-200 hover:border-blue-300 bg-white cursor-pointer">
                              <div class="flex items-center justify-center space-x-3">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                <div class="text-sm text-gray-600">Drag &amp; drop files here or click to browse</div>
                              </div>
                              <input type="file" name="attachment" id="attachment" class="hidden" accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                            </label>
                            <div class="mt-2 flex items-center gap-3">
                              <input type="checkbox" id="useAsThumb" name="useAsThumb" checked />
                              <label for="useAsThumb" class="text-sm text-gray-600">Use uploaded image as proposal thumbnail (if image)</label>
                              <button type="button" id="removeThumbBtn" class="ml-4 text-sm text-red-600 hover:underline">Remove thumbnail</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </section>

                  <div class="flex justify-end">
                    <button type="button" id="cancelBtn" class="px-5 py-3 rounded border border-gray-200 text-base mr-3 bg-white">Cancel</button>
                    <button type="submit" id="submitBtn" class="btn-primary text-base">Submit Proposal</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Fill barangay from localStorage if available
      let logged = {};
      try { logged = JSON.parse(localStorage.getItem('loggedInUser')) || {}; } catch (e) { logged = {}; }
      document.getElementById('barangay').value = logged.barangayName || logged.barangay || '';

      // Enhance dropzone: allow drag and drop preview of filename
      const dropzone = document.querySelector('label[for="attachment"]');
      const attachmentInput = document.getElementById('attachment');
      if (dropzone) {
        ['dragenter','dragover'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.add('border-indigo-300','bg-indigo-50'); }));
        ['dragleave','drop'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.remove('border-indigo-300','bg-indigo-50'); }));
        dropzone.addEventListener('drop', (e) => {
          e.preventDefault();
          if (e.dataTransfer.files && e.dataTransfer.files.length) {
            attachmentInput.files = e.dataTransfer.files;
            // show filename
            const hintEl = dropzone.querySelector('.text-sm') || dropzone.querySelector('div');
            if (hintEl) hintEl.textContent = e.dataTransfer.files[0].name;
            // preview image if user wants thumbnail
            const preview = document.getElementById('thumbPreview');
            const useAsThumb = document.getElementById('useAsThumb');
            if (preview && useAsThumb && useAsThumb.checked) {
              const file = e.dataTransfer.files[0];
              if (file && file.type && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => { preview.src = ev.target.result; };
                reader.readAsDataURL(file);
              }
            }
          }
        });
        attachmentInput.addEventListener('change', () => {
          const name = attachmentInput.files && attachmentInput.files[0] ? attachmentInput.files[0].name : 'Click to upload or drag and drop a file';
          const hint = dropzone.querySelector('.text-sm') || dropzone.querySelector('div');
          if (hint) hint.textContent = name;
          // preview image
          const preview = document.getElementById('thumbPreview');
          const useAsThumb = document.getElementById('useAsThumb');
          if (preview && useAsThumb && useAsThumb.checked) {
            const file = attachmentInput.files && attachmentInput.files[0] ? attachmentInput.files[0] : null;
            if (file && file.type && file.type.startsWith('image/')) {
              const reader = new FileReader();
              reader.onload = (ev) => { preview.src = ev.target.result; };
              reader.readAsDataURL(file);
            }
          }
        });
      }
      // Inline validation helpers
      const errTitle = document.getElementById('err-title');
      const errProjectType = document.getElementById('err-project_type');
      const errBudget = document.getElementById('err-budget');
      const errDescription = document.getElementById('err-description');

      // Remove thumbnail button behavior
      const removeThumbBtn = document.getElementById('removeThumbBtn');
      if (removeThumbBtn) {
        removeThumbBtn.addEventListener('click', (ev) => {
          ev.preventDefault();
          // clear file input
          if (attachmentInput) {
            try { attachmentInput.value = ''; } catch (e) { /* ignore */ }
          }
          // reset preview to default image
          const preview = document.getElementById('thumbPreview');
          if (preview) preview.src = 'assets/image1.png';
          // reset dropzone hint
          if (dropzone) {
            const hint = dropzone.querySelector('.text-sm') || dropzone.querySelector('div');
            if (hint) hint.textContent = 'Click to upload or drag and drop a file';
          }
          // uncheck useAsThumb
          const useAsThumb = document.getElementById('useAsThumb');
          if (useAsThumb) useAsThumb.checked = false;
        });
      }

      function clearErrors() {
        if (errTitle) errTitle.classList.add('hidden');
        if (errProjectType) errProjectType.classList.add('hidden');
        if (errBudget) errBudget.classList.add('hidden');
        if (errDescription) errDescription.classList.add('hidden');
      }

      function validateForm() {
        let ok = true;
        const title = document.getElementById('title');
        const projectType = document.getElementById('project_type');
        const budget = document.getElementById('budget');
        const description = document.getElementById('description');

        clearErrors();

        if (!title || !title.value.trim()) {
          if (errTitle) errTitle.classList.remove('hidden');
          ok = false;
        }
        if (!projectType || !projectType.value) {
          if (errProjectType) errProjectType.classList.remove('hidden');
          ok = false;
        }
        if (budget && budget.value) {
          const v = parseFloat(budget.value);
          if (isNaN(v) || v < 0) {
            if (errBudget) errBudget.classList.remove('hidden');
            ok = false;
          }
        }
        if (!description || !description.value.trim()) {
          if (errDescription) errDescription.classList.remove('hidden');
          ok = false;
        }
        return ok;
      }

      // clear specific error on input
      ['title','project_type','budget','description'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', () => { const e = document.getElementById('err-' + id); if (e) e.classList.add('hidden'); });
      });

      document.getElementById('cancelBtn').addEventListener('click', () => window.location.href = 'barangay-dashboard.php');

      document.getElementById('proposalForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        // run inline validation
        if (!validateForm()) {
          // focus first visible error field
          const firstErr = document.querySelector('.text-red-600:not(.hidden)');
          if (firstErr) {
            const input = firstErr.previousElementSibling || firstErr.parentElement.querySelector('input, select, textarea');
            if (input) input.focus();
          }
          return;
        }

        const form = e.target;
  const fd = new FormData(form);
  // ensure barangay present
  if (!fd.get('barangay')) fd.set('barangay', logged.barangayName || logged.barangay || '');
  // include checkbox value for server: use_as_thumbnail
  const useAsThumb = document.getElementById('useAsThumb');
  fd.set('use_as_thumbnail', useAsThumb && useAsThumb.checked ? '1' : '0');

        try {
          const res = await fetch('submit_project_proposal.php', { method: 'POST', body: fd });
          if (!res.ok) throw new Error('Network error');
          const data = await res.json();
          if (data && data.success) {
            // simple success toast inline instead of alert
            const btn = document.getElementById('submitBtn');
            if (btn) btn.textContent = 'Submitted';
            window.location.href = 'barangay-dashboard.php';
          } else {
            alert('Submission failed: ' + (data.error || 'Unknown'));
          }
        } catch (err) {
          console.error(err);
          alert('Failed to submit proposal. Please try again.');
        }
      });
    });
  </script>
</body>
</html>
