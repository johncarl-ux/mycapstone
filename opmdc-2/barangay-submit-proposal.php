<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Project Proposal — Smart Recommendations</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/ui-animations.css">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .recommendation-card { border-left: 4px solid #0ea5e9; padding: 0.75rem; background: #f8fafc; border-radius: 6px; }
    .recommendation-list { max-height: 360px; overflow:auto; }
  </style>
</head>
<body class="bg-gray-100">

  <div class="flex h-screen">
    <aside class="sidebar w-80 bg-gray-800 text-white flex flex-col" data-reveal="sidebar">
      <div class="p-6 text-center border-b border-gray-700">
        <img src="assets/image1.png" alt="Logo" class="logo-formal" style="width:72px;height:72px;">
        <h2 id="barangay-name-header" class="text-xl font-semibold">Barangay Name</h2>
        <p class="text-xs text-gray-400">Mabini, Batangas</p>
      </div>
      <nav class="flex-grow px-4 py-6">
        <a href="barangay-dashboard.php" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-tachometer-alt mr-3"></i>
          Dashboard
        </a>
        <a href="#" class="flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-file-alt mr-3"></i>
          Proposals
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
      <header class="w-full bg-white shadow-sm py-6" data-reveal="header">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <div>
            <h1 id="header-title" class="text-2xl font-semibold text-gray-800">Submit Project Proposal</h1>
            <p id="current-date" class="text-sm text-gray-500 mt-1">Choose a plan type (CLUP, CDP or AIP) to load Smart Recommendations tailored for Mabini, Batangas.</p>
          </div>
          <div>
            <a href="barangay-dashboard.php" class="px-4 py-2 bg-blue-600 text-white rounded-md">Back to Dashboard</a>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-gray-100">
        <div class="mx-auto w-full max-w-screen-2xl px-4">
          <div class="bg-white rounded-lg shadow-sm border border-gray-100 grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            <div class="lg:col-span-2">
              <form id="proposalForm">
                <div class="space-y-6">
                  <section>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                      <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Project Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required class="w-full rounded border-gray-200 form-input px-3 py-2" />
                        <p id="err-title" class="text-red-600 text-xs mt-1 hidden">Project Title is required.</p>
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Project Category <span class="text-red-500">*</span></label>
                        <select id="project_category" name="project_type" required class="w-full rounded border-gray-200 form-input px-3 py-2">
                          <option value="">Select plan type</option>
                          <option value="CLUP">CLUP</option>
                          <option value="CDP">CDP</option>
                          <option value="AIP">AIP</option>
                        </select>
                        <p id="err-project_category" class="text-red-600 text-xs mt-1 hidden">Please select a project category.</p>
                      </div>
                    </div>
                  </section>

                  <section>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Location & Budget</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                      <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Barangay</label>
                        <select name="barangay" id="barangay" class="w-full rounded border-gray-200 form-input px-3 py-2 bg-gray-50">
                          <option value="">(auto)</option>
                        </select>
                      </div>
                      <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Proposed Budget (PHP)</label>
                        <div class="relative">
                          <span class="absolute left-0 top-0 h-full flex items-center pl-3 text-gray-500">₱</span>
                          <input type="text" name="budget" id="budget" inputmode="decimal" class="w-full rounded border-gray-200 form-input pl-8 pr-3 py-2" placeholder="0.00" />
                        </div>
                      </div>
                    </div>
                  </section>

                  <section>
                    <div>
                      <label class="block text-sm font-semibold text-gray-600 mb-1">Description <span class="text-red-500">*</span></label>
                      <textarea name="description" id="description" rows="8" required class="w-full rounded border-gray-200 form-textarea px-3 py-2"></textarea>
                      <p id="err-description" class="text-red-600 text-xs mt-1 hidden">Description is required.</p>
                    </div>
                  </section>

                  <section>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Attachment</h3>
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
                          <input type="checkbox" id="useAsThumb" name="use_as_thumbnail" value="1" checked />
                          <label for="useAsThumb" class="text-sm text-gray-600">Use uploaded image as proposal thumbnail (if image)</label>
                          <button type="button" id="removeThumbBtn" class="ml-4 text-sm text-red-600 hover:underline">Remove thumbnail</button>
                        </div>
                      </div>
                    </div>
                  </section>

                  <div class="flex justify-end">
                    <button type="button" id="cancelBtn" class="px-5 py-3 rounded border border-gray-200 text-base mr-3 bg-white">Cancel</button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 bg-green-600 text-white font-bold rounded">Submit Proposal</button>
                  </div>
                  <!-- upload progress bar (hidden until upload starts) -->
                  <div id="uploadProgress" class="w-full bg-gray-200 rounded h-2 mt-3 hidden">
                    <div id="uploadProgressBar" class="bg-green-500 h-2 rounded" style="width:0%"></div>
                  </div>
                </div>
              </form>
            </div>

            <aside class="p-4 bg-gray-50 rounded-md">
              <h3 class="text-lg font-semibold mb-3">Smart Recommendations</h3>
              <p class="text-sm text-gray-600 mb-4">Select a Project Category to load local plans and sample recommendations to help you write the proposal.</p>

              <div id="recommendationsBox" class="recommendation-list space-y-3">
                <div class="text-sm text-gray-500">Choose a category to see suggestions.</div>
              </div>

              <div id="rec-footer" class="mt-4 text-xs text-gray-500">
                Recommendations are tailored for the municipality of <strong>Mabini, Batangas</strong> and do not reference any specific barangay names. They are drawn from the <strong>plan_recommendations</strong> database table — populate the table via the SQL migration for best results.
              </div>
            </aside>

          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Prefill barangay from localStorage
      let logged = {};
      try { logged = JSON.parse(localStorage.getItem('loggedInUser')) || {}; } catch (e) { logged = {}; }
      document.getElementById('barangay').value = logged.barangayName || logged.barangay || '';

      document.getElementById('cancelBtn').addEventListener('click', () => window.location.href = 'barangay-dashboard.php');

      const projectCategoryEl = document.getElementById('project_category');
      const recommendationsBox = document.getElementById('recommendationsBox');

      projectCategoryEl.addEventListener('change', async (ev) => {
        const cat = projectCategoryEl.value;
        if (!cat) {
          recommendationsBox.innerHTML = '<div class="text-sm text-gray-500">Choose a category to see suggestions.</div>';
          return;
        }
        recommendationsBox.innerHTML = '<div class="text-sm text-gray-500">Loading recommendations…</div>';
        try {
          const res = await fetch(`get_recommendations.php?category=${encodeURIComponent(cat)}`);
          if (!res.ok) throw new Error('Network');
          const data = await res.json();
          if (!data.success) throw new Error(data.error || 'Failed to fetch');
          const recs = data.recommendations || [];
          if (recs.length === 0) {
            recommendationsBox.innerHTML = '<div class="text-sm text-gray-500">No recommendations found for this category.</div>';
            return;
          }
          // Build recommendation list
          recommendationsBox.innerHTML = '';
          recs.forEach(r => {
            const div = document.createElement('div');
            div.className = 'recommendation-card';
            div.innerHTML = `
              <div class="flex items-start justify-between">
                <div>
                  <div class="font-semibold text-sm">${escapeHtml(r.title || 'Recommendation')}</div>
                  <div class="text-xs text-gray-600 mt-1">${escapeHtml(r.summary || '')}</div>
                </div>
                <div class="ml-3 text-right">
                  <button class="use-rec-btn bg-blue-600 text-white text-xs px-3 py-1 rounded" data-id="${r.id}">Use</button>
                </div>
              </div>
              <details class="mt-2 text-xs text-gray-700"><summary class="cursor-pointer text-blue-600">Details</summary><div class="mt-2">${escapeHtml(r.details || '')}</div></details>
            `;
            recommendationsBox.appendChild(div);
          });

          // attach handlers for Use buttons
          document.querySelectorAll('.use-rec-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
              const card = e.target.closest('.recommendation-card');
              if (!card) return;
              const detailsEl = card.querySelector('details');
              const title = card.querySelector('.font-semibold').textContent.trim();
              const summary = card.querySelector('.text-xs') ? card.querySelector('.text-xs').textContent.trim() : '';
              const details = detailsEl ? detailsEl.textContent.trim() : '';
              // insert into description field to help user
              const desc = document.getElementById('description');
              let insertText = `Suggested: ${title}. ${summary}\n\n${details}`;
              if (desc && desc.value) desc.value = desc.value + '\n\n' + insertText; else if (desc) desc.value = insertText;
              // focus description so user can edit
              if (desc) desc.focus();
            });
          });

        } catch (err) {
          console.error(err);
          recommendationsBox.innerHTML = '<div class="text-sm text-red-600">Failed to load recommendations.</div>';
        }
      });

      // Simple escaper
      function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function (s) {
          return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[s];
        });
      }

      // Attachment preview / dropzone handlers
      const attachmentInput = document.getElementById('attachment');
      const dropzone = document.querySelector('label[for="attachment"]');
      const preview = document.getElementById('thumbPreview');
      const useAsThumb = document.getElementById('useAsThumb');
      const removeThumbBtn = document.getElementById('removeThumbBtn');
      if (dropzone) {
        ['dragenter','dragover'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.add('border-indigo-300','bg-indigo-50'); }));
        ['dragleave','drop'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.remove('border-indigo-300','bg-indigo-50'); }));
        dropzone.addEventListener('drop', (e) => {
          e.preventDefault();
          if (e.dataTransfer.files && e.dataTransfer.files.length) {
            attachmentInput.files = e.dataTransfer.files;
            const hintEl = dropzone.querySelector('.text-sm') || dropzone.querySelector('div');
            if (hintEl) hintEl.textContent = e.dataTransfer.files[0].name;
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
      }
      if (attachmentInput) {
        attachmentInput.addEventListener('change', () => {
          const name = attachmentInput.files && attachmentInput.files[0] ? attachmentInput.files[0].name : 'Click to upload or drag and drop a file';
          const hint = dropzone.querySelector('.text-sm') || dropzone.querySelector('div');
          if (hint) hint.textContent = name;
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
      if (removeThumbBtn) {
        removeThumbBtn.addEventListener('click', (ev) => {
          ev.preventDefault();
          if (attachmentInput) {
            try { attachmentInput.value = ''; } catch (e) { /* ignore */ }
          }
          if (preview) preview.src = 'assets/image1.png';
          if (dropzone) {
            const hint = dropzone.querySelector('.text-sm') || dropzone.querySelector('div');
            if (hint) hint.textContent = 'Click to upload or drag and drop a file';
          }
          if (useAsThumb) useAsThumb.checked = false;
        });
      }

      // Toast helper (small, non-blocking messages) with optional action button
      // opts: { timeout: number(ms), actionLabel: string, action: function }
      function showToast(type, message, opts = {}) {
        const timeout = typeof opts.timeout === 'number' ? opts.timeout : 5000;
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const el = document.createElement('div');
        el.className = `flex items-center justify-between gap-3 px-4 py-2 rounded shadow-sm text-sm ${type === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : (type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-white text-gray-800 border')}`;
        const text = document.createElement('div'); text.textContent = message; el.appendChild(text);
        if (opts && opts.actionLabel && typeof opts.action === 'function') {
          const btn = document.createElement('button');
          btn.className = 'ml-3 px-3 py-1 bg-white border rounded text-sm text-blue-600 hover:bg-blue-50';
          btn.textContent = opts.actionLabel;
          btn.addEventListener('click', (ev) => { ev.stopPropagation(); try { opts.action(); } catch (e) { console.error(e); } });
          el.appendChild(btn);
        }
        container.appendChild(el);
        setTimeout(() => {
          el.style.transition = 'opacity 300ms ease';
          el.style.opacity = '0';
          setTimeout(() => el.remove(), 300);
        }, timeout);
      }

      // Form submit with XHR to enable upload progress
      document.getElementById('proposalForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const title = document.getElementById('title');
        const projCat = document.getElementById('project_category');
        const desc = document.getElementById('description');
        let ok = true;
        if (!title.value.trim()) { document.getElementById('err-title').classList.remove('hidden'); ok = false; }
        if (!projCat.value) { document.getElementById('err-project_category').classList.remove('hidden'); ok = false; }
        if (!desc.value.trim()) { document.getElementById('err-description').classList.remove('hidden'); ok = false; }
        if (!ok) return;

        const form = e.target;
        const fd = new FormData(form);
        if (!fd.get('barangay')) fd.set('barangay', logged.barangayName || logged.barangay || '');

        const submitBtn = document.getElementById('submitBtn');
        const progressWrap = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('uploadProgressBar');
        submitBtn.disabled = true; submitBtn.classList.add('opacity-60','cursor-not-allowed');
        if (progressWrap) { progressWrap.classList.remove('hidden'); progressBar.style.width = '0%'; }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'submit_project_proposal.php');
        xhr.withCredentials = true;
        xhr.upload.addEventListener('progress', (ev) => {
          if (ev.lengthComputable && progressBar) {
            const pct = Math.round((ev.loaded / ev.total) * 100);
            progressBar.style.width = pct + '%';
          }
        });
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            submitBtn.disabled = false; submitBtn.classList.remove('opacity-60','cursor-not-allowed');
            if (progressWrap) { setTimeout(()=>progressWrap.classList.add('hidden'), 500); }
            try {
              const data = JSON.parse(xhr.responseText || '{}');
              if (xhr.status >= 200 && xhr.status < 300 && data.success) {
                // refresh barangay/staff caches so dashboards update live
                try { if (typeof fetchAndCacheRequests === 'function') fetchAndCacheRequests(); } catch (e) {}
                try { if (typeof fetchServerData === 'function') fetchServerData(); } catch (e) {}

                // keep user on page, clear form and preview
                form.reset();
                const preview = document.getElementById('thumbPreview'); if (preview) preview.src = 'assets/image1.png';

                // show toast with action to view submissions — action navigates to barangay dashboard
                const msg = data.meets_condition ? 'Submitted — meets review condition. Sent to OPMDC staff.' : 'Submitted — will be reviewed by staff.';
                showToast('success', msg, { timeout: 7000, actionLabel: 'View my submissions', action: () => { window.location.href = 'barangay-dashboard.php'; } });
              } else {
                const msg = data && data.error ? data.error : 'Submission failed';
                showToast('error', msg, { timeout: 8000 });
              }
            } catch (err) { showToast('error', 'Server error'); }
          }
        };
        xhr.onerror = function () { submitBtn.disabled = false; submitBtn.classList.remove('opacity-60','cursor-not-allowed'); showToast('error','Upload failed'); };
        xhr.send(fd);
      });
    });
  </script>

  <!-- Toast container -->
  <div id="toastContainer" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>
</body>
</html>
