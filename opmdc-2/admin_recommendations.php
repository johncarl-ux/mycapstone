<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Manage Recommendations</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold">Manage Smart Recommendations</h1>
      <div>
        <a href="barangay-dashboard.php" class="px-4 py-2 bg-gray-600 text-white rounded">Back to Dashboard</a>
      </div>
    </div>

    <div class="bg-white p-6 rounded shadow">
      <div class="flex items-center justify-between mb-4">
        <div>
          <button id="btnNew" class="px-4 py-2 bg-blue-600 text-white rounded"><i class="fas fa-plus mr-2"></i>New Recommendation</button>
        </div>
        <div>
          <input id="searchInput" placeholder="Search title or summary" class="border rounded p-2 w-72" />
          <button id="btnSearch" class="px-3 py-2 bg-gray-200 rounded ml-2">Search</button>
        </div>
      </div>

      <div id="list" class="space-y-2">
        <div class="text-sm text-gray-500">Loading recommendations…</div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div id="modal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded p-6 w-full max-w-2xl">
      <div class="flex justify-between items-center mb-4">
        <h2 id="modalTitle" class="text-lg font-semibold">New Recommendation</h2>
        <button id="closeModal" class="text-gray-600">&times;</button>
      </div>
      <form id="recForm">
        <input type="hidden" id="recId" name="id" />
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Plan Type</label>
            <select id="recCategory" name="category" required class="mt-1 border rounded w-full p-2">
              <option value="CLUP">CLUP</option>
              <option value="CDP">CDP</option>
              <option value="AIP">AIP</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input id="recTitle" name="title" required class="mt-1 border rounded w-full p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Summary</label>
            <textarea id="recSummary" name="summary" class="mt-1 border rounded w-full p-2" rows="2"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Details</label>
            <textarea id="recDetails" name="details" class="mt-1 border rounded w-full p-2" rows="4"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Relevance (0.000 - 1.000)</label>
            <input id="recRelevance" name="relevance" type="number" step="0.001" min="0" max="1" class="mt-1 border rounded p-2" value="0.8" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Source</label>
            <input id="recSource" name="source" class="mt-1 border rounded w-full p-2" />
          </div>
        </div>

        <div class="mt-4 flex justify-end space-x-2">
          <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
          <button type="submit" id="saveBtn" class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    async function fetchAll(q) {
      let url = 'get_recommendations.php';
      if (q) url += '?q=' + encodeURIComponent(q);
      const res = await fetch(url);
      if (!res.ok) throw new Error('Network');
      const data = await res.json();
      return data.recommendations || [];
    }

    function escapeHtml(s) { return String(s||'').replace(/[&<>'"]/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":"&#39;",'"':'&quot;'})[c]; }); }

    async function renderList(q) {
      const list = document.getElementById('list');
      list.innerHTML = '<div class="text-sm text-gray-500">Loading recommendations…</div>';
      try {
        const items = await fetchAll(q);
        if (!items || items.length === 0) { list.innerHTML = '<div class="text-sm text-gray-500">No recommendations found.</div>'; return; }
        list.innerHTML = '';
        items.forEach(it => {
          const el = document.createElement('div');
          el.className = 'p-3 border rounded flex justify-between items-start bg-gray-50';
          el.innerHTML = `
            <div>
              <div class="font-semibold">${escapeHtml(it.title)}</div>
              <div class="text-xs text-gray-600">${escapeHtml(it.category)} • ${escapeHtml(it.source||'')} • relevance: ${it.relevance ?? ''}</div>
              <div class="text-sm mt-2">${escapeHtml(it.summary||'')}</div>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <button class="editBtn px-3 py-1 bg-blue-600 text-white rounded text-sm" data-id="${it.id}">Edit</button>
              <button class="deleteBtn px-3 py-1 bg-red-600 text-white rounded text-sm" data-id="${it.id}">Delete</button>
            </div>
          `;
          list.appendChild(el);
        });
        // attach handlers
        document.querySelectorAll('.editBtn').forEach(b => b.addEventListener('click', onEdit));
        document.querySelectorAll('.deleteBtn').forEach(b => b.addEventListener('click', onDelete));
      } catch (err) {
        list.innerHTML = '<div class="text-sm text-red-600">Failed to load.</div>';
        console.error(err);
      }
    }

    function showModal() { document.getElementById('modal').classList.remove('hidden'); document.getElementById('modal').classList.add('flex'); }
    function hideModal() { document.getElementById('modal').classList.add('hidden'); document.getElementById('modal').classList.remove('flex'); }

    document.getElementById('btnNew').addEventListener('click', () => {
      document.getElementById('modalTitle').textContent = 'New Recommendation';
      document.getElementById('recForm').reset();
      document.getElementById('recId').value = '';
      showModal();
    });
    document.getElementById('closeModal').addEventListener('click', hideModal);
    document.getElementById('cancelBtn').addEventListener('click', hideModal);

    document.getElementById('btnSearch').addEventListener('click', () => {
      const q = document.getElementById('searchInput').value.trim();
      renderList(q || null);
    });

    async function onEdit(e) {
      const id = e.currentTarget.getAttribute('data-id');
      try {
        const res = await fetch('get_recommendation.php?id=' + encodeURIComponent(id));
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Not found');
        const r = data.recommendation;
        document.getElementById('modalTitle').textContent = 'Edit Recommendation';
        document.getElementById('recId').value = r.id;
        document.getElementById('recCategory').value = r.category;
        document.getElementById('recTitle').value = r.title;
        document.getElementById('recSummary').value = r.summary || '';
        document.getElementById('recDetails').value = r.details || '';
        document.getElementById('recRelevance').value = r.relevance || 0.8;
        document.getElementById('recSource').value = r.source || '';
        showModal();
      } catch (err) {
        alert('Failed to load recommendation');
        console.error(err);
      }
    }

    async function onDelete(e) {
      const id = e.currentTarget.getAttribute('data-id');
      if (!confirm('Delete recommendation #' + id + '? This cannot be undone.')) return;
      try {
        const res = await fetch('delete_recommendation.php', { method: 'POST', body: new URLSearchParams({ id }) });
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        if (data.success) { renderList(); } else { alert('Delete failed: ' + (data.error || 'Unknown')); }
      } catch (err) { alert('Delete failed'); console.error(err); }
    }

    document.getElementById('recForm').addEventListener('submit', async (ev) => {
      ev.preventDefault();
      const form = ev.target;
      const fd = new FormData(form);
      try {
        const res = await fetch('save_recommendation.php', { method: 'POST', body: fd });
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        if (data.success) { hideModal(); renderList(); }
        else alert('Save failed: ' + (data.error || 'Unknown'));
      } catch (err) {
        alert('Save failed'); console.error(err);
      }
    });

    // initial
    renderList();
  </script>
</body>
</html>
