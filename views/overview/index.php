<?php $title = $title ?? 'Översikt'; ?>
<div class="space-y-6">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <form method="get" class="flex flex-col sm:flex-row gap-4 items-end">
      <div class="w-full sm:w-auto">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Välj datum</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      <button class="w-full sm:w-auto px-6 py-2 bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 rounded-xl font-semibold hover:opacity-90 transition-all">
        Visa
      </button>
    </form>
  </section>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
      <h2 class="text-lg font-bold mb-4 text-zinc-900 dark:text-zinc-50">Dagens status</h2>
      <div class="space-y-4">
        <div class="flex justify-between items-center">
          <span class="text-zinc-500">Kalorier</span>
          <div class="text-right">
            <span class="font-bold text-xl"><?= (int)($totals['kcal'] ?? 0) ?></span>
            <?php if ($plan && isset($plan['kcal_target'])): ?>
              <span class="text-xs text-zinc-400 block">Mål: <?= $plan['kcal_target'] ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-zinc-500">Protein</span>
          <div class="text-right">
            <span class="font-bold text-xl"><?= (int)($totals['protein'] ?? 0) ?>g</span>
            <?php if ($plan && isset($plan['protein_target'])): ?>
              <span class="text-xs text-zinc-400 block">Mål: <?= $plan['protein_target'] ?>g</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="flex justify-between items-center border-t border-zinc-100 dark:border-zinc-800 pt-4">
          <span class="text-zinc-500">Steg</span>
          <span class="font-bold text-xl"><?= (int)($totals['steps'] ?? 0) ?></span>
        </div>
      </div>
    </section>

    <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
      <h2 class="text-lg font-bold mb-4 text-zinc-900 dark:text-zinc-50">Vikt & BMI</h2>
      <div class="space-y-4">
        <div class="flex justify-between items-center">
          <span class="text-zinc-500">Vikt</span>
          <span class="font-bold text-xl"><?= isset($weight) && $weight !== null ? htmlspecialchars(number_format((float)$weight,1,'.','')) . ' kg' : '—' ?></span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-zinc-500">BMI</span>
          <span class="font-bold text-xl text-accent"><?= (isset($bmi) && $bmi !== null) ? htmlspecialchars($bmi) : '—' ?></span>
        </div>
        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
           <a href="<?= url('/weight') ?>" class="text-sm text-accent hover:underline font-medium">Uppdatera vikt &rarr;</a>
        </div>
      </div>
    </section>
  </div>
</div>
