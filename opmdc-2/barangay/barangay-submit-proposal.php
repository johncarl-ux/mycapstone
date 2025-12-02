<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Project Proposal — Smart Recommendations</title>
  <base href="./">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/ui-animations.css">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .recommendation-card { 
      border-left: 3px solid #2563eb; 
      padding: 1rem; 
      background: #f8fafc; 
      border-radius: 4px; 
      transition: all 0.2s;
    }
    .recommendation-card:hover { background: #eff6ff; }
    .recommendation-list { max-height: 420px; overflow-y: auto; }
    .form-section { margin-bottom: 1.75rem; padding-bottom: 1.75rem; border-bottom: 1px solid #e5e7eb; }
    .form-section:last-of-type { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
  </style>
</head>
<body class="bg-gray-100">

  <div class="flex h-screen">
    <aside class="sidebar w-80 bg-gray-800 text-white flex flex-col" data-reveal="sidebar">
      <div class="p-6 text-center border-b border-gray-700">
        <img src="../assets/image1.png" alt="Logo" class="logo-formal" style="width:72px;height:72px;">
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
        <a href="../login.html" class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-md">
          <i class="fas fa-sign-out-alt mr-3"></i>
          Logout
        </a>
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="w-full bg-white shadow-sm py-5 border-b" data-reveal="header">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
          <div>
            <h1 id="header-title" class="text-2xl font-bold text-gray-900">Submit Project Proposal</h1>
            <p id="current-date" class="text-sm text-gray-600 mt-1">Select plan type to view recommendations</p>
          </div>
          <a href="barangay-dashboard.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
          </a>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-gray-50">
        <div class="mx-auto w-full max-w-7xl">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Form Section -->
            <div class="lg:col-span-8">
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <form id="proposalForm">
                  
                  <!-- Basic Information -->
                  <section class="form-section">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter project title" />
                        <p id="err-title" class="text-red-600 text-xs mt-1.5 hidden">Project title is required</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project Category <span class="text-red-500">*</span></label>
                        <select id="project_category" name="project_type" required class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                          <option value="">Select plan type</option>
                          <option value="CLUP">CLUP</option>
                          <option value="CDP">CDP</option>
                          <option value="AIP">AIP</option>
                        </select>
                        <p id="err-project_category" class="text-red-600 text-xs mt-1.5 hidden">Category is required</p>
                      </div>
                    </div>
                  </section>

                  <!-- Location & Budget -->
                  <section class="form-section">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Location & Budget</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                        <select name="barangay" id="barangay" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                          <option value="">(auto)</option>
                        </select>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proposed Budget</label>
                        <div class="relative">
                          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">₱</span>
                          <input type="text" name="budget" id="budget" inputmode="decimal" class="w-full border border-gray-300 rounded-md pl-9 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00" />
                        </div>
                      </div>
                    </div>
                  </section>

                  <!-- Description -->
                  <section class="form-section">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Description <span class="text-red-500">*</span></h3>
                    <textarea name="description" id="description" rows="7" required class="w-full border border-gray-300 rounded-md px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Provide detailed project description"></textarea>
                    <p id="err-description" class="text-red-600 text-xs mt-1.5 hidden">Description is required</p>
                  </section>

                  <!-- Attachment -->
                  <section class="form-section">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Attachment</h3>
                    <div class="grid grid-cols-12 gap-4">
                      <div class="col-span-3">
                        <div class="w-full aspect-square bg-gray-50 rounded-lg overflow-hidden border-2 border-gray-200">
                          <img id="thumbPreview" src="../assets/image1.png" alt="thumbnail" class="object-cover w-full h-full">
                        </div>
                      </div>
                      <div class="col-span-9">
                        <label for="attachment" class="block w-full p-8 text-center rounded-lg border-2 border-dashed border-gray-300 hover:border-blue-400 hover:bg-blue-50 bg-white cursor-pointer transition">
                          <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                          <div class="text-sm text-gray-600 font-medium">Drag & drop files here or click to browse</div>
                          <input type="file" name="attachment" id="attachment" class="hidden" accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                        </label>
                        <div class="mt-3 space-y-2">
                          <div class="flex items-center gap-2">
                            <input type="checkbox" id="useAsThumb" name="use_as_thumbnail" value="1" checked class="rounded" />
                            <label for="useAsThumb" class="text-xs text-gray-600">Use uploaded image as thumbnail</label>
                          </div>
                          <button type="button" id="removeThumbBtn" class="text-xs text-red-600 hover:text-red-700 font-medium">Remove thumbnail</button>
                        </div>
                      </div>
                    </div>
                  </section>

                  <!-- Actions -->
                  <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cancelBtn" class="px-6 py-2.5 rounded-md border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">Submit Proposal</button>
                  </div>
                  
                  <!-- Progress Bar -->
                  <div id="uploadProgress" class="w-full bg-gray-200 rounded-full h-2 mt-4 hidden">
                    <div id="uploadProgressBar" class="bg-green-600 h-2 rounded-full transition-all" style="width:0%"></div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Recommendations Sidebar -->
            <aside class="lg:col-span-4">
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                <div class="flex items-center gap-2 mb-4">
                  <i class="fas fa-lightbulb text-blue-600"></i>
                  <h3 class="text-base font-bold text-gray-900">Smart Recommendations</h3>
                </div>
                <p class="text-xs text-gray-600 mb-4">Select category to view recommendations</p>

                <div id="recommendationsBox" class="recommendation-list space-y-2">
                  <div class="text-sm text-gray-500 text-center py-8">Choose a category</div>
                </div>

                <div id="rec-footer" class="mt-6 pt-4 border-t border-gray-200 text-xs text-gray-500 leading-relaxed">
                  Tailored for <strong>Mabini, Batangas</strong>
                </div>
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
          recommendationsBox.innerHTML = '<div class="text-sm text-gray-500 text-center py-8">Choose a category</div>';
          return;
        }
        recommendationsBox.innerHTML = '<div class="text-sm text-gray-500 text-center py-8"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</div>';
        try {
          const res = await fetch(`../api/get_recommendations.php?category=${encodeURIComponent(cat)}`);
          if (!res.ok) throw new Error('Network');
          const data = await res.json();
          if (!data.success) throw new Error(data.error || 'Failed to fetch');
          const recs = data.recommendations || [];
          if (recs.length === 0) {
            recommendationsBox.innerHTML = '<div class="text-sm text-gray-500 text-center py-8">No recommendations available</div>';
            return;
          }
          // Build recommendation list
          recommendationsBox.innerHTML = '';
          recs.forEach(r => {
            const div = document.createElement('div');
            div.className = 'recommendation-card';
            div.innerHTML = `
              <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                  <div class="font-semibold text-sm text-gray-900">${escapeHtml(r.title || 'Recommendation')}</div>
                  <div class="text-xs text-gray-600 mt-1 leading-relaxed">${escapeHtml(r.summary || '')}</div>
                </div>
                <button class="use-rec-btn bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition flex-shrink-0" data-id="${r.id}">Apply</button>
              </div>
              <details class="mt-3 text-xs text-gray-700"><summary class="cursor-pointer text-blue-600 font-medium hover:text-blue-700">View details</summary><div class="mt-2 text-gray-600 leading-relaxed">${escapeHtml(r.details || '')}</div></details>
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
          recommendationsBox.innerHTML = '<div class="text-sm text-red-600 text-center py-8"><i class="fas fa-exclamation-circle mr-2"></i>Failed to load</div>';
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
          if (preview) preview.src = '../assets/image1.png';
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
        xhr.open('POST', '../api/submit_project_proposal.php');
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
                const preview = document.getElementById('thumbPreview'); if (preview) preview.src = '../assets/image1.png';

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
