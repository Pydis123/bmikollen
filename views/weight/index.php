<?php $title = $title ?? 'Dagens vikt'; ?>
<div class="max-w-xl mx-auto p-8 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Dagens vikt</h1>
    <p class="text-zinc-500 text-sm">Logga din vikt för att följa din utveckling mot dina mål.</p>
  </div>
  
  <form method="post" class="space-y-6">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Datum</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Vikt (kg)</label>
        <input type="number" step="0.01" name="weight" value="<?= htmlspecialchars((isset($weight) && $weight !== null) ? number_format((float)$weight,1,'.','') : '') ?>" placeholder="0.0" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
    </div>
    
    <button class="w-full py-3 bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg">
      Spara vikt
    </button>
  </form>
</div>
