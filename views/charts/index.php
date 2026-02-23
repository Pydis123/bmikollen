<?php $title = $title ?? 'Grafer'; ?>
<div class="space-y-6">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h1 class="text-2xl font-bold mb-8 text-zinc-900 dark:text-zinc-50 tracking-tight">Dina framsteg</h1>
    
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
  const dates = totals.map(t => {
    const [y, m, d] = t.date.split('-');
    return m + '-' + d;
  });
  const kcal = totals.map(t => parseInt(t.kcal || 0));
  const protein = totals.map(t => parseInt(t.protein || 0));
  const steps = totals.map(t => parseInt(t.steps || 0));
  const wVals = weights.map(w => w.weight);

  function mkChart(id, label, data, color, targetValue = null) {
    const ctx = document.getElementById(id).getContext('2d');
    const datasets = [{ label, data, borderColor: color, tension: .2, fill: false, spanGaps: true }];
    
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
          x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } }
        }
      }
    });
  }

  mkChart('kcalChart', 'Kcal', kcal, '#ef4444', plan ? plan.kcal_target : null);
  mkChart('proteinChart', 'Protein (g)', protein, '#06b6d4', plan ? plan.protein_target : null);
  mkChart('stepsChart', 'Steg', steps, '#10b981', plan ? plan.steps_target : null);

  // Weight chart
  mkChart('weightChart', 'Vikt (kg)', wVals, '#8b5cf6', plan ? plan.weight_goal : null);
</script>
