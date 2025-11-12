<?php
// barangay_status_tracker.php - tracker page embedded in dashboard UI
// This page re-uses the dashboard layout for visual consistency and hosts
// the tracker modal / inline tracker for a barangay or single request.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Request Tracker — Barangay</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    /* Reuse the dashboard visual language (clean, formal) */
    .logo-formal { width:6.5rem; height:6.5rem; border-radius:9999px; background:#fff; display:block; object-fit:cover; margin-left:auto; margin-right:auto; margin-bottom:0.9rem; border:2px solid #e6eef8; box-shadow:0 6px 18px rgba(14,45,80,0.06); padding:4px; }
    .logo-formal:hover { transform: translateY(-3px) scale(1.02); box-shadow:0 12px 28px rgba(14,45,80,0.08); }
    aside.sidebar { width:16rem; }
    header .notif-badge { position:absolute; top:-6px; right:-6px; background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:9999px; border:2px solid white; }

    /* Compact aggregated card + tracker styles (kept tidy & consistent) */
    .compact-card { padding:0.75rem 1rem; border-radius:8px; display:block; border:1px solid #e6eef8; box-shadow:0 6px 14px rgba(14,45,80,0.03); background:#fff; }
    .compact-card + .compact-card { margin-top:0.8rem; }
    .compact-stepper { position:relative; height:48px; }
    .compact-line { position:absolute; left:12px; right:12px; top:22px; height:6px; background:#e5e7eb; border-radius:6px; }
    .compact-fill { position:absolute; left:12px; top:22px; height:6px; background:linear-gradient(90deg,#06b6d4,#0ea5e9 60%); border-radius:6px; transition: width 480ms cubic-bezier(.2,.9,.2,1); }
    .compact-dot { position:absolute; top:15px; width:16px; height:16px; border-radius:9999px; background:#cbd5e0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px; box-shadow:0 6px 14px rgba(2,6,23,0.06); border:2px solid #fff; transition: background-color 360ms ease, transform 360ms cubic-bezier(.2,.9,.2,1); }
    .compact-dot.active { background:#0284c7; transform: translateY(-2px) scale(1.05); }
    .compact-labels { display:flex; justify-content:space-between; margin-top:8px; font-size:12px; color:#6b7280; padding:0 8px; }
    .compact-labels > div { transition: color 360ms ease, transform 360ms cubic-bezier(.2,.9,.2,1); }
    .compact-labels > div.active { color:#111827; font-weight:600; transform: translateY(-2px); }

    /* Modal & toast */
    .rt-modal-backdrop { position:fixed; inset:0; background:rgba(2,6,23,0.45); display:none; align-items:center; justify-content:center; z-index:9998; }
    .rt-modal { background:#fff; border-radius:10px; width:92%; max-width:980px; max-height:86vh; overflow:auto; box-shadow:0 20px 60px rgba(2,6,23,0.35); padding:18px; }
    .rt-toast { position: fixed; right: 20px; bottom: 20px; color: white; padding: 10px 14px; border-radius: 8px; box-shadow: 0 8px 24px rgba(2,6,23,0.18); transform: translateY(10px); opacity: 0; transition: transform 260ms ease, opacity 260ms ease; z-index:9999; display:flex; align-items:center; gap:8px; }
    .rt-toast.show { transform: translateY(0); opacity: 1; }
    .rt-toast .rt-icon { width:20px; height:20px; display:inline-flex; align-items:center; justify-content:center; }
  </style>
</head>
<body class="bg-gray-100">

  <div class="flex h-screen">
    <aside class="sidebar bg-gray-800 text-white flex flex-col p-6">
      <img src="assets/image1.png" alt="Logo" class="logo-formal">
      <h2 id="barangay-name-header" class="text-xl font-semibold">Barangay Name</h2>
      <p class="text-xs text-gray-400">Mabini, Batangas</p>
      <nav class="mt-6 flex-1">
        <a href="barangay-dashboard.php" class="block px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 rounded-md"><i class="fas fa-tachometer-alt mr-3"></i> Dashboard</a>
        <a href="#" class="block px-4 py-2 mt-2 bg-gray-700 text-white rounded-md"><i class="fas fa-spinner mr-3"></i> Status</a>
        <a href="head-dashboard.php" class="block px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 rounded-md"><i class="fas fa-history mr-3"></i> History</a>
      </nav>
      <div class="mt-6">
        <a href="logout.php" class="block px-4 py-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-md"><i class="fas fa-sign-out-alt mr-3"></i> Logout</a>
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white shadow-md p-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Request Tracker</h1>
            <p class="text-sm text-gray-500">Monitor requests and their phase in real-time</p>
          </div>
          <div class="flex items-center gap-4">
            <div class="relative">
              <i class="fas fa-search text-gray-400"></i>
              <input type="text" placeholder="Search..." class="pl-8 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="relative">
              <button id="notifBellBtn" class="p-2 rounded-full hover:bg-gray-100"><i class="fas fa-bell text-gray-600 text-lg"></i><span id="notifBadge" class="hidden">0</span></button>
            </div>
            <div class="flex items-center">
              <img class="w-10 h-10 rounded-full" src="https://placehold.co/100x100/E2E8F0/4A5568?text=User" alt="User Avatar">
              <div class="ml-3">
                <p id="user-name" class="text-sm font-semibold text-gray-800">Barangay Official</p>
                <p id="user-role" class="text-xs text-gray-500">Official</p>
              </div>
            </div>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
          <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-xl font-semibold text-gray-800">Tracking</h2>
                <p class="text-sm text-gray-600">Use the tracker to follow requests for your barangay or a single request.</p>
              </div>
              <div>
                <button id="openTrackerBtn" class="bg-blue-600 text-white px-3 py-1 rounded">Open Tracker</button>
              </div>
            </div>

            <div class="mt-4 text-sm text-gray-600">Open the tracker modal, or pass <code>?barangay=Name</code> or <code>?id=123</code> to auto-open.</div>

            <div id="tracking-results-container" class="mt-6">
              <!-- Inline aggregated cards or single timeline will render here when user opens tracker or clicks details -->
              <p class="text-gray-500">Click "Open Tracker" to view the tracker.</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal markup (same tracker modal used previously) -->
  <div id="rtBackdrop" class="rt-modal-backdrop">
    <div class="rt-modal" role="dialog" aria-modal="true" aria-labelledby="rtTitle">
      <div class="flex items-start justify-between">
        <div>
          <h2 id="rtTitle" class="text-lg font-semibold">Tracker</h2>
          <div id="rtSubtitle" class="text-sm text-gray-600">Loading...</div>
        </div>
        <div>
          <button id="rtClose" class="rt-modal-close text-gray-600"><i class="fas fa-times"></i></button>
        </div>
      </div>

      <div id="rtLegend" class="mt-3 text-sm text-gray-700"></div>

      <div id="rtContent" class="mt-4 space-y-3">
        <!-- dynamic content: either aggregated compact cards or single full stepper -->
      </div>
    </div>
  </div>

  <script>
  (function(){
    // reuse the same tracker logic as earlier but embedded in dashboard UI
    const steps = ['Submitted','For Review','Approved','Completed'];
    const stepColors = ['#0ea5e9','#f59e0b','#10b981','#7c3aed'];
    const stepIcons = ['fas fa-paper-plane','fas fa-hourglass-half','fas fa-check-circle','fas fa-flag-checkered'];

    const url = new URL(location.href);
    const barangay = url.searchParams.get('barangay');
    const rid = url.searchParams.get('id');

    const openBtn = document.getElementById('openTrackerBtn');
    const backdrop = document.getElementById('rtBackdrop');
    const closeBtn = document.getElementById('rtClose');
    const rtContent = document.getElementById('rtContent');
    const rtSubtitle = document.getElementById('rtSubtitle');
    const rtLegend = document.getElementById('rtLegend');
    const resultsContainer = document.getElementById('tracking-results-container');

    function renderLegend(){
      let html = '';
      for(let i=0;i<steps.length;i++){
        html += `<span style="display:inline-flex;align-items:center;gap:8px;margin-right:12px;"><span style="width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:${stepColors[i]};color:#fff;font-size:11px"><i class="${stepIcons[i]}"></i></span><span>${steps[i]}</span></span>`;
      }
      rtLegend.innerHTML = html;
    }

    function openModal(){ backdrop.style.display='flex'; document.body.style.overflow='hidden'; }
    function closeModal(){ backdrop.style.display='none'; document.body.style.overflow='auto'; }
    openBtn.addEventListener('click', ()=>{ openModal(); start(); });
    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', (e)=>{ if (e.target === backdrop) closeModal(); });

    function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function showToast(msg, stepIdx){
      const t = document.createElement('div');
      t.className = 'rt-toast';
      const color = stepColors[stepIdx] || '#0ea5e9';
      t.style.background = color;
      t.innerHTML = `<span class="rt-icon"><i class="${stepIcons[stepIdx] || 'fas fa-info-circle'}"></i></span><span>${escapeHtml(msg)}</span>`;
      document.body.appendChild(t);
      requestAnimationFrame(()=> t.classList.add('show'));
      setTimeout(()=>{ t.classList.remove('show'); setTimeout(()=> t.remove(),300); }, 3600);
    }

    function renderCompactList(requests, target){
      const container = target || rtContent;
      container.innerHTML = '';
      requests.forEach(r => {
        const div = document.createElement('div');
        div.className = 'compact-card';
        div.dataset.requestId = r.id;
        const lastCompleted = (r._stepStatus||[]).map((v,i)=> v? i:-1).filter(x=>x>=0).pop() || -1;
        const len = Math.max(1, (r._stepStatus||[]).length-1);
        const fill = lastCompleted>=0? Math.round((lastCompleted/len)*100):0;
        div.innerHTML = `
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-700">Request <strong>#${escapeHtml(r.request_code||r.id)}</strong></div>
              <div class="compact-meta">Type: ${escapeHtml(r.request_type||'')} • ${new Date(r.created_at||'').toLocaleDateString()}</div>
            </div>
            <div class="text-right"><a href="#" class="text-sm text-blue-600 hover:underline details-link" data-id="${r.id}">Details</a></div>
          </div>
          <div class="compact-stepper mt-3">
            <div class="compact-line"></div>
            <div class="compact-fill" style="width:${fill}%;"></div>
          </div>
          <div class="compact-labels mt-2">${steps.map(s=>`<div style="width:${Math.floor(100/steps.length)}%;text-align:center">${escapeHtml(s)}</div>`).join('')}</div>
          <div class="mt-2 text-xs text-gray-500 last-update">Last update: ${r._lastTs? new Date(r._lastTs).toLocaleString():''}</div>
        `;
        container.appendChild(div);
      });
    }

    function renderSingle(r){
      const html = [];
      html.push(`<div class="text-sm text-gray-700">Request <strong>#${escapeHtml(r.request_code||r.id)}</strong> — ${escapeHtml(r.request_type||'')}</div>`);
      html.push('<div class="mt-4">');
      html.push('<div class="flex items-center space-x-4">');
      for(let i=0;i<steps.length;i++){
        html.push(`<div class="step ${r._stepStatus && r._stepStatus[i]? 'active':''}"><div class="circle" style="width:24px;height:24px;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:${r._stepStatus && r._stepStatus[i]? stepColors[i] : '#cbd5e0'};color:#fff">${i+1}</div><div class="step-label">${escapeHtml(steps[i])}</div></div>`);
        if (i < steps.length-1) html.push('<div style="flex:1;height:6px;background:#e5e7eb;border-radius:6px;margin-top:9px"></div>');
      }
      html.push('</div></div>');
      html.push('<div class="mt-4 border-t pt-4"><h3 class="text-sm font-medium">History</h3>');
      (r.history||[]).slice().reverse().forEach(ev=>{
        html.push(`<div class="p-3 bg-gray-50 rounded mt-2"><div class="text-xs text-gray-500">${new Date(ev.timestamp||'').toLocaleString()}</div><div class="font-medium text-gray-800">${escapeHtml(ev.status||ev.notes||'')}</div><div class="text-sm text-gray-700">${escapeHtml(ev.notes||'')}</div></div>`);
      });
      html.push('</div>');
      rtContent.innerHTML = html.join('');
    }

    // polling and change-detection
    let prevMap = {};
    let first = true;
    async function fetchAndRender(q){
      try{
        const res = await fetch('request_status_api.php'+(q||''), { credentials: 'same-origin' });
        if (!res.ok) return;
        const d = await res.json();
        if (!d) return;
        if (d.request) {
          renderSingle(d.request);
          if (!first){
            const prev = prevMap[d.request.id] || [];
            (d.request._stepStatus||[]).forEach((now,si)=>{ if (now && !prev[si]) showToast(`Request #${d.request.request_code||d.request.id} advanced to ${steps[si]}`, si); });
            prevMap[d.request.id] = (d.request._stepStatus||[]).slice();
          } else { prevMap[d.request.id] = (d.request._stepStatus||[]).slice(); }
        } else if (Array.isArray(d.requests)){
          renderCompactList(d.requests);
          d.requests.forEach(r=>{
            if (!first){
              const prev = prevMap[r.id] || [];
              (r._stepStatus||[]).forEach((now,si)=>{ if (now && !prev[si]) showToast(`Request #${r.request_code||r.id} advanced to ${steps[si]}`, si); });
            }
            prevMap[r.id] = (r._stepStatus||[]).slice();
          });
        }
        first = false;
      }catch(er){ console.warn('poll err',er); }
    }

    function start(){
      renderLegend();
      rtSubtitle.textContent = barangay? `Barangay: ${barangay}` : (rid? `Request: ${rid}` : '');
      fetchAndRender(barangay? `?barangay=${encodeURIComponent(barangay)}` : (rid? `?id=${encodeURIComponent(rid)}` : ''));
      window._rtInterval = setInterval(()=> fetchAndRender(barangay? `?barangay=${encodeURIComponent(barangay)}` : (rid? `?id=${encodeURIComponent(rid)}` : '')), 6000);
    }

    // Inline aggregated rendering helper (render inside main results area)
    async function renderAggregatedInline(b){
      try{
        const res = await fetch(`request_status_api.php?barangay=${encodeURIComponent(b)}`, { credentials: 'same-origin' });
        if (!res.ok) return;
        const d = await res.json();
        if (!d || !Array.isArray(d.requests)) return;
        // create legend + list
        resultsContainer.innerHTML = '<div id="tracker-legend" class="mb-3"></div><div id="tracker-cards"></div>';
        document.getElementById('tracker-legend').innerHTML = rtLegend.innerHTML = (()=>{ let h=''; for(let i=0;i<steps.length;i++){ h+=`<span style="display:inline-flex;align-items:center;gap:8px;margin-right:12px;"><span style="width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:${stepColors[i]};color:#fff;font-size:11px"><i class="${stepIcons[i]}"></i></span><span>${steps[i]}</span></span>`;} return h; })();
        const cardsTarget = document.getElementById('tracker-cards');
        renderCompactList(d.requests, cardsTarget);
      }catch(e){ console.warn('agg render failed', e); }
    }

    // Wire details click from main results area
    document.addEventListener('click', async function(e){
      const a = e.target.closest('.details-link');
      if (a) {
        e.preventDefault();
        const id = a.getAttribute('data-id');
        if (!id) return;
        try{
          const res = await fetch(`request_status_api.php?id=${encodeURIComponent(id)}`, { credentials: 'same-origin' });
          if (!res.ok) throw new Error('Not found');
          const d = await res.json();
          if (d && d.request) {
            // show single view inside modal
            renderSingle(d.request);
            openModal();
            return;
          }
        }catch(err){ console.warn('details fetch failed', err); alert('Request not found'); }
      }
    });

    // Auto open / inline render based on query
    if (barangay) {
      renderAggregatedInline(barangay);
    }
    if (rid) { openModal(); start(); }

  })();
  </script>
</body>
</html>
<?php
// barangay_status_tracker.php - restores the tracker as a modal-style component
// This file renders a small page that demonstrates/hosts the modal tracker.
// It uses the existing `request_status_api.php` endpoint for polling.
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Barangay Status Tracker (Modal)</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<style>
		body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; }
		/* Modal styles */
		.rt-modal-backdrop { position:fixed; inset:0; background:rgba(2,6,23,0.45); display:none; align-items:center; justify-content:center; z-index:9998; }
		.rt-modal { background:#fff; border-radius:10px; width:92%; max-width:980px; max-height:86vh; overflow:auto; box-shadow:0 20px 60px rgba(2,6,23,0.35); padding:18px; }
		.rt-modal.open { transform: translateY(0); }
		.rt-modal-close { cursor:pointer; }
		.compact-card { padding:0.6rem 0.9rem; border-radius:8px; display:block; border:1px solid #e6eef8; box-shadow: 0 6px 14px rgba(14,45,80,0.03); }
		.compact-stepper { position:relative; height:48px; }
		.compact-line { position:absolute; left:12px; right:12px; top:22px; height:6px; background:#e5e7eb; border-radius:6px; }
		.compact-fill { position:absolute; left:12px; top:22px; height:6px; background:linear-gradient(90deg,#06b6d4,#0ea5e9 60%); border-radius:6px; transition: width 480ms cubic-bezier(.2,.9,.2,1); }
		.compact-dot { position:absolute; top:15px; width:16px; height:16px; border-radius:9999px; background:#cbd5e0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px; box-shadow:0 6px 14px rgba(2,6,23,0.06); border:2px solid #fff; transition: background-color 360ms ease, transform 360ms cubic-bezier(.2,.9,.2,1); }
		.compact-dot.active { background:#0284c7; transform: translateY(-2px) scale(1.05); }
		.compact-labels { display:flex; justify-content:space-between; margin-top:8px; font-size:12px; color:#6b7280; padding:0 8px; }
		.compact-labels > div.active { color:#111827; font-weight:600; }
		.rt-toast { position: fixed; right: 20px; bottom: 20px; color: white; padding: 10px 14px; border-radius: 8px; box-shadow: 0 8px 24px rgba(2,6,23,0.18); transform: translateY(10px); opacity: 0; transition: transform 260ms ease, opacity 260ms ease; z-index:9999; display:flex; align-items:center; gap:8px; }
		.rt-toast.show { transform: translateY(0); opacity: 1; }
		.rt-toast .rt-icon { width:20px; height:20px; display:inline-flex; align-items:center; justify-content:center; }
	</style>
</head>
<body class="bg-gray-50">
	<div class="max-w-4xl mx-auto py-8">
		<div class="bg-white p-6 rounded-lg shadow">
			<div class="flex items-center justify-between">
				<h1 class="text-xl font-semibold">Barangay Status Tracker (Modal)</h1>
				<div>
					<button id="openTrackerBtn" class="bg-blue-600 text-white px-3 py-1 rounded">Open Tracker</button>
				</div>
			</div>
			<div class="mt-4 text-sm text-gray-600">Click "Open Tracker" to view the modal tracker. You can pass <code>?barangay=Name</code> or <code>?id=123</code> to auto-open.</div>
		</div>
	</div>

	<div id="rtBackdrop" class="rt-modal-backdrop">
		<div class="rt-modal" role="dialog" aria-modal="true" aria-labelledby="rtTitle">
			<div class="flex items-start justify-between">
				<div>
					<h2 id="rtTitle" class="text-lg font-semibold">Tracker</h2>
					<div id="rtSubtitle" class="text-sm text-gray-600">Loading...</div>
				</div>
				<div>
					<button id="rtClose" class="rt-modal-close text-gray-600"><i class="fas fa-times"></i></button>
				</div>
			</div>

			<div id="rtLegend" class="mt-3 text-sm text-gray-700"></div>

			<div id="rtContent" class="mt-4 space-y-3">
				<!-- dynamic content: either aggregated compact cards or single full stepper -->
			</div>
		</div>
	</div>

	<script>
		(function(){
			const steps = ['Submitted','For Review','Approved','Completed'];
			const stepColors = ['#0ea5e9','#f59e0b','#10b981','#7c3aed'];
			const stepIcons = ['fas fa-paper-plane','fas fa-hourglass-half','fas fa-check-circle','fas fa-flag-checkered'];
			const url = new URL(location.href);
			const barangay = url.searchParams.get('barangay');
			const rid = url.searchParams.get('id');

			const openBtn = document.getElementById('openTrackerBtn');
			const backdrop = document.getElementById('rtBackdrop');
			const closeBtn = document.getElementById('rtClose');
			const rtContent = document.getElementById('rtContent');
			const rtSubtitle = document.getElementById('rtSubtitle');
			const rtLegend = document.getElementById('rtLegend');

			function renderLegend(){
				let html = '';
				for(let i=0;i<steps.length;i++){
					html += `<span style="display:inline-flex;align-items:center;gap:8px;margin-right:12px;"><span style="width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:${stepColors[i]};color:#fff;font-size:11px"><i class="${stepIcons[i]}"></i></span><span>${steps[i]}</span></span>`;
				}
				rtLegend.innerHTML = html;
			}

			function openModal(){ backdrop.style.display='flex'; document.body.style.overflow='hidden'; }
			function closeModal(){ backdrop.style.display='none'; document.body.style.overflow='auto'; }
			openBtn.addEventListener('click', ()=>{ openModal(); start(); });
			closeBtn.addEventListener('click', closeModal);
			backdrop.addEventListener('click', (e)=>{ if (e.target === backdrop) closeModal(); });

			// toast helper
			function showToast(msg, stepIdx){
				const t = document.createElement('div');
				t.className = 'rt-toast';
				const color = stepColors[stepIdx] || '#0ea5e9';
				t.style.background = color;
				t.innerHTML = `<span class="rt-icon"><i class="${stepIcons[stepIdx] || 'fas fa-info-circle'}"></i></span><span>${escapeHtml(msg)}</span>`;
				document.body.appendChild(t);
				requestAnimationFrame(()=> t.classList.add('show'));
				setTimeout(()=>{ t.classList.remove('show'); setTimeout(()=> t.remove(),300); }, 3600);
			}
			function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

			// render helpers
			function renderCompactList(requests){
				rtContent.innerHTML = '';
				requests.forEach(r => {
					const div = document.createElement('div');
					div.className = 'compact-card bg-white';
					div.dataset.requestId = r.id;
					const lastCompleted = (r._stepStatus||[]).map((v,i)=> v? i:-1).filter(x=>x>=0).pop() || -1;
					const len = Math.max(1, (r._stepStatus||[]).length-1);
					const fill = lastCompleted>=0? Math.round((lastCompleted/len)*100):0;
					div.innerHTML = `<div class="flex items-center justify-between"><div><div class="text-sm text-gray-700">Request <strong>#${escapeHtml(r.request_code||r.id)}</strong></div><div class="compact-meta">Type: ${escapeHtml(r.request_type||'')} • ${new Date(r.created_at||'').toLocaleDateString()}</div></div><div class="text-right"><a href="request_tracker.php?id=${encodeURIComponent(r.id)}" class="text-sm text-blue-600 hover:underline">Details</a></div></div><div class="compact-stepper mt-3"><div class="compact-line"></div><div class="compact-fill" style="width:${fill}%;"></div></div><div class="compact-labels mt-2">${steps.map(s=>`<div style="width:${Math.floor(100/steps.length)}%;text-align:center">${escapeHtml(s)}</div>`).join('')}</div><div class="mt-2 text-xs text-gray-500 last-update">Last update: ${r._lastTs? new Date(r._lastTs).toLocaleString():''}</div>`;
					rtContent.appendChild(div);
				});
			}

			function renderSingle(r){
				// simple stepper and history
				const html = [];
				html.push(`<div class="text-sm text-gray-700">Request <strong>#${escapeHtml(r.request_code||r.id)}</strong> — ${escapeHtml(r.request_type||'')}</div>`);
				html.push('<div class="mt-4">');
				html.push('<div class="flex items-center space-x-4">');
				for(let i=0;i<steps.length;i++){
					html.push(`<div class="step ${r._stepStatus && r._stepStatus[i]? 'active':''}"><div class="circle" style="width:24px;height:24px;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:${r._stepStatus && r._stepStatus[i]? stepColors[i] : '#cbd5e0'};color:#fff">${i+1}</div><div class="step-label">${escapeHtml(steps[i])}</div></div>`);
					if (i < steps.length-1) html.push('<div style="flex:1;height:6px;background:#e5e7eb;border-radius:6px;margin-top:9px"></div>');
				}
				html.push('</div></div>');
				// history
				html.push('<div class="mt-4 border-t pt-4"><h3 class="text-sm font-medium">History</h3>');
				(r.history||[]).slice().reverse().forEach(ev=>{
					html.push(`<div class="p-3 bg-gray-50 rounded mt-2"><div class="text-xs text-gray-500">${new Date(ev.timestamp||'').toLocaleString()}</div><div class="font-medium text-gray-800">${escapeHtml(ev.status||ev.notes||'')}</div><div class="text-sm text-gray-700">${escapeHtml(ev.notes||'')}</div></div>`);
				});
				html.push('</div>');
				rtContent.innerHTML = html.join('');
			}

			// polling + change detection
			let prevMap = {};
			let first = true;
			async function fetchAndRender(){
				try{
					const q = rid? `?id=${encodeURIComponent(rid)}` : (barangay? `?barangay=${encodeURIComponent(barangay)}` : '');
					const res = await fetch('request_status_api.php'+q, { credentials: 'same-origin' });
					if (!res.ok) return;
					const d = await res.json();
					if (!d) return;
					if (d.request) {
						renderSingle(d.request);
						// detect
						if (!first){
							const prev = prevMap[d.request.id] || [];
							(d.request._stepStatus||[]).forEach((now,si)=>{ if (now && !prev[si]) showToast(`Request #${d.request.request_code||d.request.id} advanced to ${steps[si]}`, si); });
							prevMap[d.request.id] = (d.request._stepStatus||[]).slice();
						} else { prevMap[d.request.id] = (d.request._stepStatus||[]).slice(); }
					} else if (Array.isArray(d.requests)){
						renderCompactList(d.requests);
						d.requests.forEach(r=>{
							if (!first){
								const prev = prevMap[r.id] || [];
								(r._stepStatus||[]).forEach((now,si)=>{ if (now && !prev[si]) showToast(`Request #${r.request_code||r.id} advanced to ${steps[si]}`, si); });
							}
							prevMap[r.id] = (r._stepStatus||[]).slice();
						});
					}
					first = false;
				}catch(er){ console.warn('poll err',er); }
			}

			function start(){
				renderLegend();
				rtSubtitle.textContent = barangay? `Barangay: ${barangay}` : (rid? `Request: ${rid}` : '');
				fetchAndRender();
				window._rtInterval = setInterval(fetchAndRender, 6000);
			}

			// auto-open if params present
			if (barangay || rid){ openModal(); start(); }
		})();
	</script>
</body>
</html>

