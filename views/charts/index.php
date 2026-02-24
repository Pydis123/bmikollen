<?php $title = $title ?? 'Grafer'; ?>
<div class="space-y-6">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h1 class="text-2xl font-bold mb-8 text-zinc-900 dark:text-zinc-50 tracking-tight">Dina framsteg</h1>

    <form method="get" class="mb-6 grid grid-cols-1 md:grid-cols-8 lg:grid-cols-12 gap-3 items-end">
      <div class="md:col-span-4 lg:col-span-4">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Intervall</label>
        <?php $r = $filters['range'] ?? '30d'; ?>
        <select name="range" id="rangeSelect" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950">
          <option value="30d" <?= $r==='30d' ? 'selected' : '' ?>>Senaste 30 dagar</option>
          <option value="90d" <?= $r==='90d' ? 'selected' : '' ?>>Senaste 90 dagar</option>
          <option value="1y" <?= $r==='1y' ? 'selected' : '' ?>>Senaste året</option>
          <option value="plan" <?= (!empty($plan) ? '' : 'disabled') ?> <?= $r==='plan' ? 'selected' : '' ?>>Hela planperioden</option>
          <option value="custom" <?= $r==='custom' ? 'selected' : '' ?>>Eget intervall</option>
        </select>
      </div>
      <div class="md:col-span-2 lg:col-span-2">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Start</label>
        <input type="date" name="start" id="startInput" value="<?= htmlspecialchars($filters['start'] ?? '') ?>" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-900" <?= ($r==='custom' ? '' : 'disabled') ?>>
      </div>
      <div class="md:col-span-2 lg:col-span-2">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Slut</label>
        <input type="date" name="end" id="endInput" value="<?= htmlspecialchars($filters['end'] ?? '') ?>" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-900" <?= ($r==='custom' ? '' : 'disabled') ?>>
      </div>
      <div class="md:col-span-3 lg:col-span-2">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Upplösning</label>
        <?php $agg = $filters['agg'] ?? 'daily'; ?>
        <select name="agg" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950">
          <option value="daily" <?= $agg==='daily' ? 'selected' : '' ?>>Dagar</option>
          <option value="weekly" <?= $agg==='weekly' ? 'selected' : '' ?>>Veckor</option>
        </select>
      </div>
      <div class="md:col-span-2 lg:col-span-2">
        <button class="px-4 py-2 h-[42px] rounded-lg bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 font-semibold w-full">Visa</button>
      </div>
    </form>

    <p class="text-xs text-zinc-500 mb-4">Visar: <span class="font-mono text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars(($filters['start'] ?? '') . ' – ' . ($filters['end'] ?? '')) ?></span></p>

    <div class="space-y-12">
      <div>
        <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Kalorier</h2>
        <canvas id="kcalChart" height="120"></canvas>
      </div>
      <div>
        <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Protein</h2>
        <canvas id="proteinChart" height="120"></canvas>
      </div>
      <div>
        <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Steg</h2>
        <canvas id="stepsChart" height="120"></canvas>
      </div>
      <div>
        <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4 text-accent">Vikt (kg)</h2>
        <canvas id="weightChart" height="120"></canvas>
      </div>
    </div>
  </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const totals = <?= json_encode($totals ?? []) ?>;
  const weights = <?= json_encode($weights ?? []) ?>;
  const plan = <?= json_encode($plan ?? null) ?>;
  const filters = <?= json_encode($filters ?? []) ?>;
  const agg = (filters && filters.agg) ? filters.agg : 'daily';
  const dates = totals.map(t => {
    const [y, m, d] = t.date.split('-');
    return m + '-' + d;
  });
  const kcal = totals.map(t => (t.kcal === null || t.kcal === undefined) ? null : parseInt(t.kcal));
  const protein = totals.map(t => (t.protein === null || t.protein === undefined) ? null : parseInt(t.protein));
  const steps = totals.map(t => (t.steps === null || t.steps === undefined) ? null : parseInt(t.steps));
  const wVals = weights.map(w => (w.weight === null || w.weight === undefined) ? null : parseFloat(w.weight));

  function mkChart(id, label, data, color, targetValue = null) {
    const ctx = document.getElementById(id).getContext('2d');
    const datasets = [{ label, data, borderColor: color, tension: .2, fill: false, spanGaps: false }];
    
    if (targetValue) {
      datasets.push({
        label: 'Mål (' + targetValue + ')',
        data: Array(dates.length).fill(targetValue),
        borderColor: color,
        borderDash: [5, 5],
        borderWidth: 1,
        pointRadius: 0,
        fill: false
      });
    }

    new Chart(ctx, {
      type: 'line',
      data: { labels: dates, datasets: datasets },
      options: { 
        responsive: true, 
        scales: { 
          y: { beginAtZero: false },
          x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: (agg === 'weekly' ? 20 : 10) } }
        }
      }
    });
  }

  mkChart('kcalChart', 'Kcal', kcal, '#ef4444', plan ? plan.kcal_target : null);
  mkChart('proteinChart', 'Protein (g)', protein, '#06b6d4', plan ? plan.protein_target : null);
  mkChart('stepsChart', 'Steg', steps, '#10b981', plan ? plan.steps_target : null);

  // Weight chart
  (function () {
    const ctx = document.getElementById('weightChart').getContext('2d');
    const datasets = [{
      label: 'Vikt (kg)',
      data: wVals,
      borderColor: '#8b5cf6',
      tension: .2,
      fill: false,
      spanGaps: true
    }];

    if (plan && plan.intensity_preset) {
      const map = {
        gentle: { pct: 0.0025, min: 0.2, max: 0.4 },
        normal: { pct: 0.0050, min: 0.4, max: 0.8 },
        aggressive: { pct: 0.0100, min: 0.8, max: 1.0 },
      };
      const i = map[plan.intensity_preset] || map.normal;
      const baseIdx = wVals.findIndex(v => v !== null && v !== undefined);
      if (baseIdx !== -1) {
        const base = parseFloat(wVals[baseIdx]);
        const weeklyKg = Math.max(i.min, Math.min(i.max, i.pct * base));
        const wExpected = new Array(dates.length).fill(null);
        for (let j = baseIdx; j < dates.length; j++) {
          const offset = j - baseIdx;
          const decrease = (agg === 'weekly') ? (weeklyKg * offset) : (weeklyKg * (offset / 7));
          wExpected[j] = +(base - decrease).toFixed(1);
        }
        datasets.push({
          label: `Förväntad (−${weeklyKg.toFixed(1)} kg/v)`,
          data: wExpected,
          borderColor: '#f59e0b',
          borderDash: [6, 4],
          borderWidth: 1.5,
          pointRadius: 0,
          tension: .2,
          fill: false,
          spanGaps: true
        });
      }
    }

    if (plan && plan.weight_goal) {
      datasets.push({
        label: 'Mål (' + plan.weight_goal + ')',
        data: Array(dates.length).fill(plan.weight_goal),
        borderColor: '#8b5cf6',
        borderDash: [5, 5],
        borderWidth: 1,
        pointRadius: 0,
        fill: false
      });
    }

    new Chart(ctx, {
      type: 'line',
      data: { labels: dates, datasets },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: false },
          x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: (agg === 'weekly' ? 20 : 10) } }
        }
      }
    });
  })();
  // Toggle custom date inputs and reflect dates for fixed ranges/plan
  (function(){
    const sel = document.getElementById('rangeSelect');
    const s = document.getElementById('startInput');
    const e = document.getElementById('endInput');
    function update() {
      if (!sel) return;
      const val = sel.value;
      const isCustom = val === 'custom';
      if (s) s.disabled = !isCustom;
      if (e) e.disabled = !isCustom;
      if (typeof filters !== 'undefined') {
        if (val === 'plan') {
          if (s && filters.planStart) s.value = filters.planStart;
          if (e && filters.planEnd) e.value = filters.planEnd;
        } else if (val === '30d' || val === '90d' || val === '1y') {
          if (s && filters.start) s.value = filters.start;
          if (e && filters.end) e.value = filters.end;
        }
      }
    }
    if (sel) {
      sel.addEventListener('change', update);
      update();
    }
  })();
</script>
