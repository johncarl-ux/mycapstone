<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Smart Recommendations — Browse</title>
  <base href="./">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold">Smart Recommendations</h1>
        <p class="text-sm text-gray-600">Recommendations are tailored for the municipality of Mabini, Batangas and do not reference any specific barangay names.</p>
      </div>
      <div>
        <a href="barangay-dashboard.php" class="px-4 py-2 bg-blue-600 text-white rounded">Back to Dashboard</a>
      </div>
    </div>

    <div class="bg-white p-6 rounded shadow">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Category</label>
          <select id="filterCategory" class="mt-1 rounded border-gray-200 w-full p-2">
            <option value="">(all)</option>
            <option value="CLUP">CLUP</option>
            <option value="CDP">CDP</option>
            <option value="AIP">AIP</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Search</label>
          <input id="filterQuery" class="mt-1 rounded border-gray-200 w-full p-2" placeholder="Free-text search title or summary">
        </div>
        <div class="flex items-end">
          <button id="btnSearch" class="px-4 py-2 bg-blue-600 text-white rounded">Search</button>
        </div>
      </div>

      <div id="results" class="space-y-3">
        <div class="text-sm text-gray-500">Run a search or select CLUP, CDP, or AIP to view recommendations applicable to Mabini, Batangas.</div>
      </div>
    </div>
  </div>

  <script>
    async function fetchRecs(category, q) {
      let url = '../api/get_recommendations.php';
      if (category) url += '?category=' + encodeURIComponent(category);
      else if (q) url += '?q=' + encodeURIComponent(q);
      try {
        const res = await fetch(url);
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        return data.recommendations || [];
      } catch (err) {
        console.error(err);
        return null;
      }
    }

    function renderList(items) {
      const out = document.getElementById('results');
      out.innerHTML = '';
      if (items === null) return out.innerHTML = '<div class="text-sm text-red-600">Failed to load recommendations.</div>';
      if (!items || items.length === 0) return out.innerHTML = '<div class="text-sm text-gray-500">No recommendations found.</div>';
      items.forEach(it => {
        const el = document.createElement('div');
        el.className = 'p-4 border rounded bg-gray-50';
        el.innerHTML = `
          <div class="flex justify-between">
            <div>
              <div class="font-semibold">${escapeHtml(it.title)}</div>
              <div class="text-xs text-gray-600">${escapeHtml(it.category)} • relevance: ${it.relevance ?? ''}</div>
            </div>
            <div class="text-right">
              <button class="use-btn bg-green-600 text-white px-3 py-1 rounded text-sm">Use</button>
            </div>
          </div>
          <div class="mt-2 text-sm text-gray-700">${escapeHtml(it.summary || '')}</div>
          <details class="mt-2"><summary class="text-xs text-blue-600 cursor-pointer">Details</summary><div class="mt-2 text-sm text-gray-700">${escapeHtml(it.details || '')}</div></details>
        `;
        el.querySelector('.use-btn').addEventListener('click', () => {
          navigator.clipboard && navigator.clipboard.writeText((it.title + '\n\n' + (it.summary||'') + '\n\n' + (it.details||''))).then(()=> alert('Copied recommendation to clipboard'));
        });
        out.appendChild(el);
      });
    }

    function escapeHtml(s) { return String(s||'').replace(/[&<>"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }

    document.getElementById('btnSearch').addEventListener('click', async () => {
      const cat = document.getElementById('filterCategory').value;
      const q = document.getElementById('filterQuery').value.trim();
      document.getElementById('results').innerHTML = '<div class="text-sm text-gray-500">Loading…</div>';
      const items = await fetchRecs(cat || null, q || null);
      renderList(items);
    });
  </script>
</body>
</html>
