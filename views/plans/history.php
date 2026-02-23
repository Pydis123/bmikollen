<?php $title = $title ?? 'Planhistorik'; ?>
<div class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm overflow-hidden">
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Planhistorik</h1>
    <p class="text-zinc-500 text-sm mt-1">Här kan du se dina tidigare mål och planer.</p>
  </div>

  <div class="overflow-x-auto -mx-6">
    <table class="min-w-full text-sm text-left border-collapse">
      <thead>
        <tr class="bg-zinc-50 dark:bg-zinc-950 text-zinc-500 uppercase text-[10px] tracking-widest font-bold">
          <th class="px-6 py-4">Startdatum</th>
          <th class="px-6 py-4">Slut/Mål</th>
          <th class="px-6 py-4 text-center">Status</th>
          <th class="px-6 py-4 text-right">Kcal</th>
          <th class="px-6 py-4 text-right">Protein</th>
          <th class="px-6 py-4 text-right">Steg</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
        <?php foreach (($plans ?? []) as $p): ?>
          <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-colors">
            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($p['start_date'] ?? '') ?></td>
            <td class="px-6 py-4 text-zinc-500"><?= htmlspecialchars($p['closed_at'] ?? ($p['target_end_date'] ?? '—')) ?></td>
            <td class="px-6 py-4 text-center">
              <?php if (!empty($p['is_active'])): ?>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-[10px] font-bold rounded-full uppercase">Aktiv</span>
              <?php else: ?>
                <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[10px] font-bold rounded-full uppercase">Avslutad</span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4 text-right font-mono"><?= htmlspecialchars($p['kcal_target'] ?? '') ?></td>
            <td class="px-6 py-4 text-right font-mono"><?= htmlspecialchars($p['protein_target'] ?? '') ?>g</td>
            <td class="px-6 py-4 text-right font-mono"><?= number_format($p['steps_target'] ?? 0, 0, ',', ' ') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($plans)): ?>
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-zinc-400 italic">Ingen historik hittades.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
