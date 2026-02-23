<?php 
$title = $title ?? 'Min dag';
$plan = $plan ?? null;
?>
<div class="space-y-6">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h2 class="text-lg font-bold mb-4 text-zinc-900 dark:text-zinc-50">Logga aktivitet</h2>
    <form method="post" action="<?= url('/day/log/add') ?>" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 items-end">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
      
      <div class="col-span-2 md:col-span-2 lg:col-span-2">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Datum</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      
      <div class="col-span-1 md:col-span-1 lg:col-span-1">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Tid</label>
        <input type="time" name="time" value="<?= htmlspecialchars(date('H:i')) ?>" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      
      <div class="col-span-1 md:col-span-1 lg:col-span-4">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Etikett</label>
        <input type="text" name="label" placeholder="Lunch, löpning, etc..." class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      
      <div class="col-span-1 lg:col-span-1">
        <label class="block text-xs font-medium text-zinc-500 mb-1">kcal</label>
        <input type="number" name="kcal" placeholder="0" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      
      <div class="col-span-1 lg:col-span-1">
        <label class="block text-xs font-medium text-zinc-500 mb-1">protein (g)</label>
        <input type="number" name="protein" placeholder="0" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      
      <div class="col-span-1 lg:col-span-1">
        <label class="block text-xs font-medium text-zinc-500 mb-1">steg</label>
        <input type="number" name="steps" placeholder="0" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>

      <div class="col-span-1 lg:col-span-1">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Vikt (kg)</label>
        <?php $hasWeight = isset($weight) && $weight !== null; ?>
        <input type="number" step="0.1" name="weight" 
          <?= $hasWeight ? 'disabled' : '' ?> 
          placeholder="<?= $hasWeight ? htmlspecialchars(number_format((float)$weight, 1, '.', '')) : '0.0' ?>" 
          class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
      </div>
      
      <div class="col-span-2 md:col-span-1 lg:col-span-3">
        <button class="w-full px-2 py-2 bg-accent text-white rounded-xl font-semibold hover:opacity-90 transition-all shadow-lg shadow-accent/20 text-sm whitespace-nowrap">
          Logga aktivitet
        </button>
      </div>
    </form>
    <?php if ($hasWeight): ?>
      <p class="text-[10px] text-zinc-400 mt-2 italic text-right lg:text-left">Vikt har redan loggats för detta datum.</p>
    <?php endif; ?>
  </section>

  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-2">
      <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">Dagens sammanfattning</h2>
      <div class="text-sm text-zinc-500">
        <?= htmlspecialchars($date ?? date('Y-m-d')) ?>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
        <div class="text-xs font-medium text-zinc-500 mb-1 uppercase tracking-wider">Kalorier</div>
        <div class="flex items-baseline gap-2">
          <span class="text-2xl font-bold"><?= (int)($totals['kcal'] ?? 0) ?></span>
          <?php if ($plan && isset($plan['kcal_target'])): ?>
            <span class="text-sm text-zinc-400">/ <?= $plan['kcal_target'] ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
        <div class="text-xs font-medium text-zinc-500 mb-1 uppercase tracking-wider">Protein</div>
        <div class="flex items-baseline gap-2">
          <span class="text-2xl font-bold"><?= (int)($totals['protein'] ?? 0) ?>g</span>
          <?php if ($plan && isset($plan['protein_target'])): ?>
            <span class="text-sm text-zinc-400">/ <?= $plan['protein_target'] ?>g</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
        <div class="text-xs font-medium text-zinc-500 mb-1 uppercase tracking-wider">Steg</div>
        <div class="flex items-baseline gap-2">
          <span class="text-2xl font-bold"><?= (int)($totals['steps'] ?? 0) ?></span>
          <?php if ($plan && isset($plan['steps_target'])): ?>
            <span class="text-sm text-zinc-400">/ <?= $plan['steps_target'] ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-widest mb-4">Loggade poster</h3>
      <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
        <?php foreach (($logs ?? []) as $log): ?>
          <div class="py-4 flex items-center justify-between group">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-medium text-zinc-400"><?= htmlspecialchars(substr($log['time'], 0, 5)) ?></span>
                <span class="font-semibold text-zinc-800 dark:text-zinc-200"><?= htmlspecialchars($log['label'] ?: 'Aktivitet') ?></span>
              </div>
              <div class="text-sm font-mono text-zinc-500">
                <?php if ($log['kcal_delta']): ?>+<?= (int)$log['kcal_delta'] ?> kcal <?php endif; ?>
                <?php if ($log['protein_delta']): ?>· +<?= (int)$log['protein_delta'] ?>g p <?php endif; ?>
                <?php if ($log['steps_delta']): ?>· +<?= (int)$log['steps_delta'] ?> steg <?php endif; ?>
              </div>
            </div>
            <form method="post" action="<?= url('/day/log/delete/' . (int)$log['id']) ?>" onsubmit="return confirm('Ta bort post?')">
              <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
              <button class="p-2 text-zinc-300 hover:text-red-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </form>
          </div>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
          <div class="py-8 text-center text-zinc-400 italic text-sm">Inga poster loggade ännu idag.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>
