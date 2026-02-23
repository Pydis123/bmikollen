<?php $title = $title ?? 'Vecka'; ?>
<div class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm space-y-4">
  <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-50">Veckoöversikt</h1>
  <div class="text-sm text-zinc-500">Period: <span class="font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars(($monday ?? '') . ' – ' . ($sunday ?? '')) ?></span></div>
  
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
      <div class="text-xs font-medium text-zinc-500 mb-1 uppercase tracking-wider">Kcal</div>
      <div class="flex items-baseline gap-2">
        <span class="text-2xl font-bold"><?= (int)($sum['kcal'] ?? 0) ?></span>
        <?php if ($plan && isset($plan['kcal_target'])): ?>
          <span class="text-xs text-zinc-400">/ <?= $plan['kcal_target'] * 7 ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
      <div class="text-xs font-medium text-zinc-500 mb-1 uppercase tracking-wider">Protein</div>
      <div class="flex items-baseline gap-2">
        <span class="text-2xl font-bold"><?= (int)($sum['protein'] ?? 0) ?>g</span>
        <?php if ($plan && isset($plan['protein_target'])): ?>
          <span class="text-xs text-zinc-400">/ <?= $plan['protein_target'] * 7 ?>g</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
      <div class="text-xs font-medium text-zinc-500 mb-1 uppercase tracking-wider">Steg</div>
      <div class="flex items-baseline gap-2">
        <span class="text-2xl font-bold"><?= (int)($sum['steps'] ?? 0) ?></span>
        <?php if ($plan && isset($plan['steps_target'])): ?>
          <span class="text-xs text-zinc-400">/ <?= $plan['steps_target'] * 7 ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
    <div class="text-sm text-zinc-500">Vikttrend: <span class="font-mono text-zinc-900 dark:text-zinc-100"><?= (isset($weight_start) && $weight_start !== null) ? number_format((float)$weight_start,1,'.','') : '—' ?></span> &rarr; <span class="font-mono text-zinc-900 dark:text-zinc-100"><?= (isset($weight_end) && $weight_end !== null) ? number_format((float)$weight_end,1,'.','') : '—' ?></span> kg</div>
  </div>
</div>
