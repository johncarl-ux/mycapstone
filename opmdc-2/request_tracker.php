<?php
// request_tracker.php - standalone tracker page for a single request

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$barangayFilter = isset($_GET['barangay']) ? trim((string)$_GET['barangay']) : '';

/** @var mysqli $mysqli */
$mysqli = require __DIR__ . '/db.php';

if ($id > 0) {
  $stmt = $mysqli->prepare("SELECT id, request_code, barangay, request_type, urgency, location, description, email, notes, attachment, status, history, created_at FROM requests WHERE id = ? LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $request = $res->fetch_assoc();
  if (! $request) {
    http_response_code(404);
    echo "<p>Request not found.</p>";
    exit;
  }
  $requests = [$request];
  $singleView = true;
} else {
  // If barangay filter provided, show all requests for that barangay
  $singleView = false;
  if ($barangayFilter !== '') {
    $stmt = $mysqli->prepare("SELECT id, request_code, barangay, request_type, urgency, location, description, email, notes, attachment, status, history, created_at FROM requests WHERE barangay = ? ORDER BY created_at DESC LIMIT 200");
    $stmt->bind_param('s', $barangayFilter);
    $stmt->execute();
    $res = $stmt->get_result();
    $requests = $res->fetch_all(MYSQLI_ASSOC);
  } else {
    http_response_code(400);
    echo "<p>Invalid request id or barangay not specified.</p>";
    exit;
  }
}

// Normalize fields and prepare stepper data for each request
$steps = ['Submitted', 'For Review', 'Approved', 'Completed'];
foreach ($requests as &$request) {
  $request['history'] = $request['history'] ? json_decode($request['history'], true) : null;
  if (!is_array($request['history'])) {
    $request['history'] = [['status' => $request['status'] ?: 'Pending', 'timestamp' => $request['created_at'] ?: date('c'), 'notes' => 'Submitted']];
  }
  $happened = [];
  foreach ($request['history'] as $h) {
    $s = strtolower(trim((string)($h['status'] ?? $h['notes'] ?? '')));
    if ($s !== '') $happened[] = $s;
  }
  $happened = array_unique($happened);
  $stepStatus = [];
  foreach ($steps as $s) {
    $k = strtolower($s);
    if ($k === 'submitted') $stepStatus[] = true;
    elseif ($k === 'for review') $stepStatus[] = (bool) preg_grep('/pending|for review|processing|submitted/', $happened);
    elseif ($k === 'approved') $stepStatus[] = (bool) preg_grep('/approved|accept|approved by/', $happened) || stripos((string)$request['status'], 'approved') !== false;
    elseif ($k === 'completed') $stepStatus[] = (bool) preg_grep('/completed|allocated|closed|delivered|finished/', $happened);
    else $stepStatus[] = false;
  }
  $request['_stepStatus'] = $stepStatus;
  $lastEvent = end($request['history']);
  $request['_lastTs'] = $lastEvent['timestamp'] ?? $request['created_at'] ?? date('c');
}
unset($request);

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <?php if ($singleView): ?>
    <title>Request Tracker — #<?php echo htmlspecialchars($requests[0]['request_code'] ?: $requests[0]['id']); ?></title>
  <?php else: ?>
    <title>Request Tracker — Barangay <?php echo htmlspecialchars($barangayFilter); ?></title>
  <?php endif; ?>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    .stepper { display:flex; align-items:center; gap:1rem; width:100%; }
    .stepper-line { height:4px; background:#e5e7eb; flex:1; position:relative; }
    .step { display:flex; flex-direction:column; align-items:center; text-align:center; width:140px; }
    .step .circle { width:20px; height:20px; border-radius:9999px; background:#cbd5e0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; }
    .step.active .circle { background:#0ea5e9; }
    .step-label { font-size:13px; color:#6b7280; margin-top:8px; }
    .step.active .step-label { color:#111827; font-weight:600; }
    .alert { padding:0.75rem 1rem; border-radius:6px; display:flex; align-items:center; gap:0.75rem; }
    /* Compact aggregated card styles */
  .compact-card { padding:0.6rem 0.9rem; border-radius:8px; display:block; border:1px solid #e6eef8; box-shadow: 0 6px 14px rgba(14,45,80,0.03); }
  .compact-card:hover { transform: translateY(-3px); transition: all 160ms ease; box-shadow: 0 12px 28px rgba(14,45,80,0.06); }
  .compact-stepper { position:relative; height:48px; }
  .compact-line { position:absolute; left:12px; right:12px; top:22px; height:6px; background:#e5e7eb; border-radius:6px; }
  .compact-fill { position:absolute; left:12px; top:22px; height:6px; background:linear-gradient(90deg,#06b6d4,#0ea5e9 60%); border-radius:6px; transition: width 480ms cubic-bezier(.2,.9,.2,1), background-position 800ms linear; background-size: 200% 100%; }
  .compact-dot { position:absolute; top:15px; width:16px; height:16px; border-radius:9999px; background:#cbd5e0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px; box-shadow:0 6px 14px rgba(2,6,23,0.06); border:2px solid #fff; transition: background-color 360ms ease, transform 360ms cubic-bezier(.2,.9,.2,1); }
  .compact-dot.active { background:#0284c7; transform: translateY(-2px) scale(1.05); }
  .compact-labels { display:flex; justify-content:space-between; margin-top:8px; font-size:12px; color:#6b7280; padding:0 8px; }
  .compact-labels > div { transition: color 360ms ease, transform 360ms cubic-bezier(.2,.9,.2,1); }
  .compact-labels > div.active { color: #111827; font-weight:600; transform: translateY(-2px); }

  /* Toast */
  .rt-toast { position: fixed; right: 20px; bottom: 20px; background: #0ea5e9; color: white; padding: 10px 14px; border-radius: 8px; box-shadow: 0 8px 24px rgba(2,6,23,0.18); transform: translateY(10px); opacity: 0; transition: transform 260ms ease, opacity 260ms ease; z-index:9999; }
  .rt-toast.show { transform: translateY(0); opacity: 1; }
  .rt-toast .rt-icon { display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; margin-right:8px; }
  .rt-toast .rt-text { display:inline-block; vertical-align:middle; }
  .legend-item { gap:8px; }
  .compact-meta { font-size:12px; color:#6b7280; }
  </style>
</head>
<body class="bg-gray-50">
  <div class="max-w-4xl mx-auto py-8">
    <div class="bg-white p-6 rounded-lg shadow">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-800">Request Tracker</h1>
<?php if ($singleView): ?>
          <div class="text-sm text-gray-600">Tracking details for request <strong>#<?php echo htmlspecialchars($requests[0]['request_code'] ?: $requests[0]['id']); ?></strong></div>
<?php else: ?>
              <div class="text-sm text-gray-600">Tracking all requests for <strong><?php echo htmlspecialchars($barangayFilter); ?></strong></div>
              <?php
                // server-side legend mapping (keep in sync with client toast mapping)
                $stepColors = ['#0ea5e9', '#f59e0b', '#10b981', '#7c3aed'];
                $stepIcons = ['fas fa-paper-plane', 'fas fa-hourglass-half', 'fas fa-check-circle', 'fas fa-flag-checkered'];
              ?>
              <div class="mt-3">
                <div class="legend inline-flex items-center gap-3 text-sm text-gray-700">
                  <?php foreach ($steps as $si => $slabel): ?>
                    <div class="legend-item inline-flex items-center gap-2">
                      <span class="legend-icon" style="width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;color:#fff;background:<?php echo htmlspecialchars($stepColors[$si] ?? '#0ea5e9'); ?>;"><i class="<?php echo htmlspecialchars($stepIcons[$si] ?? 'fas fa-info-circle'); ?>" style="font-size:11px;"></i></span>
                      <span><?php echo htmlspecialchars($slabel); ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
<?php endif; ?>
        </div>
        <div class="text-right">
          <a href="barangay-dashboard.php" class="inline-block text-sm text-gray-600 hover:underline">&larr; Back to Dashboard</a>
          <div class="mt-2 text-right">
            <button onclick="location.reload()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Refresh</button>
          </div>
        </div>
      </div>

      <?php if ($singleView):
          $r = $requests[0];
      ?>
        <!-- ALERT: concise current status -->
        <div class="mt-4" id="trackerAlert">
          <div class="alert bg-blue-50 border border-blue-100">
            <div class="text-blue-600 text-xl"><i class="fas fa-info-circle"></i></div>
            <div>
              <div class="text-sm text-blue-800">Current status: <strong><?php echo htmlspecialchars($r['status']); ?></strong></div>
              <div class="text-xs text-blue-600">Last updated: <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($r['_lastTs']))); ?></div>
            </div>
          </div>
        </div>

        <!-- Stepper -->
        <div class="mt-6">
          <div class="flex items-center justify-between mb-3">
            <div class="text-sm text-gray-600">Type: <?php echo htmlspecialchars($r['request_type']); ?></div>
            <div class="text-sm text-gray-600">Barangay: <?php echo htmlspecialchars($r['barangay']); ?></div>
          </div>

          <div class="flex items-center space-x-4">
            <?php foreach ($steps as $idx => $label): ?>
              <div class="step<?php echo $r['_stepStatus'][$idx] ? ' active' : ''; ?>">
                <div class="circle"><?php echo $idx+1; ?></div>
                <div class="step-label"><?php echo htmlspecialchars($label); ?></div>
              </div>
              <?php if ($idx < count($steps)-1): ?>
                <div class="stepper-line">
                  <?php
                    $fill = 0;
                    if ($r['_stepStatus'][$idx] && $r['_stepStatus'][$idx+1]) $fill = 100;
                    elseif ($r['_stepStatus'][$idx]) $fill = 50;
                  ?>
                  <div style="position:absolute;left:0;top:0;height:100%;width:<?php echo $fill; ?>%;background:<?php echo ($fill===100? '#0ea5e9' : ($fill>0? 'linear-gradient(90deg,#0ea5e9,#e5e7eb)' : '#e5e7eb')); ?>;"></div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- History -->
        <div class="mt-6 border-t pt-4">
          <h3 class="text-lg font-medium text-gray-800 mb-3">History</h3>
          <div class="space-y-3">
            <?php foreach (array_reverse($r['history']) as $ev): ?>
              <div class="p-3 bg-gray-50 rounded">
                <div class="text-xs text-gray-500"><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($ev['timestamp'] ?? $r['created_at']))); ?></div>
                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($ev['status'] ?? $ev['notes'] ?? ''); ?></div>
                <div class="text-sm text-gray-700"><?php echo htmlspecialchars($ev['notes'] ?? ''); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      <?php else: ?>
        <!-- Aggregated barangay requests list -->
        <div class="mt-6 space-y-4">
          <?php if (empty($requests)): ?>
            <div class="p-4 bg-white rounded">No requests found for <?php echo htmlspecialchars($barangayFilter); ?>.</div>
          <?php else: ?>
            <?php foreach ($requests as $r): ?>
              <div class="compact-card bg-white" data-request-id="<?php echo htmlspecialchars($r['id']); ?>">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-sm text-gray-700">Request <strong>#<?php echo htmlspecialchars($r['request_code'] ?: $r['id']); ?></strong></div>
                    <div class="compact-meta">Type: <?php echo htmlspecialchars($r['request_type']); ?> • <?php echo htmlspecialchars(date('F j, Y', strtotime($r['created_at']))); ?></div>
                  </div>
                  <div class="text-right">
                    <a href="request_tracker.php?id=<?php echo urlencode($r['id']); ?>" class="text-sm text-blue-600 hover:underline">Details</a>
                  </div>
                </div>

                <div class="compact-stepper mt-3">
                  <div class="compact-line"></div>
                  <?php
                    $len = count($steps) - 1;
                    // compute last completed index
                    $lastCompleted = -1;
                    for ($si = 0; $si < count($steps); $si++) {
                      if (!empty($r['_stepStatus'][$si])) $lastCompleted = $si;
                    }
                    $fillPercent = ($lastCompleted >= 0 && $len > 0) ? round(($lastCompleted / $len) * 100, 2) : 0;
                    foreach ($steps as $idx => $label):
                      $pos = ($len > 0) ? round(($idx / $len) * 100, 2) : 0;
                      $active = $r['_stepStatus'][$idx] ? ' active' : '';
                  ?>
                    <?php if ($idx === 0): ?>
                      <div class="compact-line"></div>
                      <div class="compact-fill" style="width:<?php echo $fillPercent; ?>%;"></div>
                    <?php endif; ?>
                    <div class="compact-dot<?php echo $active; ?>" data-step="<?php echo $idx; ?>" style="left:<?php echo $pos; ?>%;"></div>
                  <?php endforeach; ?>
                  <div class="compact-labels">
                    <?php foreach ($steps as $idx => $label): ?>
                      <div style="width:<?php echo intval(100/count($steps)); ?>%;text-align:center;"><?php echo htmlspecialchars($label); ?></div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <div class="mt-2 text-xs text-gray-500 last-update">Last update: <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($r['_lastTs']))); ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</body>
</html>

<?php if (!isset($singleView) || !$singleView): ?>
<script>
  (function() {
    const barangay = <?php echo json_encode($barangayFilter); ?>;
    if (!barangay) return;
    let prevMap = {};
    let firstFetch = true;

    function showToast(msg, stepIdx) {
      const stepColors = ['#0ea5e9', '#f59e0b', '#10b981', '#7c3aed'];
      const stepIcons = ['fas fa-paper-plane', 'fas fa-hourglass-half', 'fas fa-check-circle', 'fas fa-flag-checkered'];
      let t = document.createElement('div');
      t.className = 'rt-toast';
      // icon + text
      const color = (typeof stepIdx === 'number' && stepColors[stepIdx]) ? stepColors[stepIdx] : '#0ea5e9';
      const iconCls = (typeof stepIdx === 'number' && stepIcons[stepIdx]) ? stepIcons[stepIdx] : 'fas fa-info-circle';
      t.innerHTML = `<span class="rt-icon"><i class="${iconCls}"></i></span><span class="rt-text">${escapeHtml(msg)}</span>`;
      // style color
      t.style.background = color;
      document.body.appendChild(t);
      // force reflow then show
      requestAnimationFrame(() => t.classList.add('show'));
      setTimeout(() => { t.classList.remove('show'); setTimeout(()=> t.remove(), 300); }, 3800);
    }

    // small helper to avoid raw HTML injection inside toast text
    function escapeHtml(str) {
      return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    async function fetchUpdates() {
      try {
        const res = await fetch(`request_status_api.php?barangay=${encodeURIComponent(barangay)}`, { credentials: 'same-origin' });
        if (!res.ok) return;
        const d = await res.json();
        if (!d || !Array.isArray(d.requests)) return;
        d.requests.forEach(r => {
          const card = document.querySelector(`.compact-card[data-request-id="${r.id}"]`);
          if (!card) return;
          const fill = card.querySelector('.compact-fill');
          const dots = card.querySelectorAll('.compact-dot');
          const labels = card.querySelectorAll('.compact-labels > div');
          // compute fill percent by last completed step
          const stepCount = r._stepStatus ? r._stepStatus.length : 0;
          let lastCompleted = -1;
          if (Array.isArray(r._stepStatus)) {
            for (let i=0;i<r._stepStatus.length;i++) if (r._stepStatus[i]) lastCompleted = i;
          }
          const len = Math.max(1, stepCount-1);
          const pct = (lastCompleted >= 0) ? Math.round((lastCompleted/len)*100*100)/100 : 0;
          if (fill) fill.style.width = pct + '%';
          // update dots
          if (dots && dots.length) {
            dots.forEach(dEl => {
              const si = Number(dEl.getAttribute('data-step'));
              const active = (r._stepStatus && r._stepStatus[si]);
              if (active) dEl.classList.add('active'); else dEl.classList.remove('active');
            });
          }
          // update labels active state
          if (labels && labels.length) {
            labels.forEach((lab, idx) => {
              if (r._stepStatus && r._stepStatus[idx]) lab.classList.add('active'); else lab.classList.remove('active');
            });
          }
          // update last update text
          const lu = card.querySelector('.last-update');
          if (lu) lu.textContent = 'Last update: ' + (r._lastTs ? new Date(r._lastTs).toLocaleString() : '');

          // detect newly completed steps and show toast
          if (!firstFetch) {
            const prev = prevMap[r.id] || [];
            if (Array.isArray(r._stepStatus)) {
              for (let si = 0; si < r._stepStatus.length; si++) {
                const now = !!r._stepStatus[si];
                const was = !!prev[si];
                if (now && !was) {
                  // find label text
                  const label = (<?php echo json_encode($steps); ?>)[si] || ('Step ' + (si+1));
                  showToast(`Request #${r.request_code || r.id} advanced to ${label}`, si);
                  break; // one toast per request per poll
                }
              }
            }
          }
          // save state
          prevMap[r.id] = Array.isArray(r._stepStatus) ? r._stepStatus.slice() : [];
        });
        firstFetch = false;
      } catch (err) {
        console.warn('tracker poll failed', err);
      }
    }
    // initial load already rendered server-side; start polling
    setInterval(fetchUpdates, 6000);
  })();
</script>
<?php endif; ?>
