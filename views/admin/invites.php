<?php $title = $title ?? 'Invites'; ?>
<div class="space-y-6">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h1 class="text-xl font-bold mb-4 text-zinc-900 dark:text-zinc-50">Skapa inbjudan</h1>
    <form method="post" class="flex flex-col sm:flex-row gap-4 items-end">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
      <div class="w-full sm:flex-1">
        <label class="block text-xs font-medium text-zinc-500 mb-1">E-post (valfritt)</label>
        <input type="email" name="email" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all" placeholder="user@example.com">
      </div>
      <button class="w-full sm:w-auto px-6 py-2 bg-accent text-white rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-accent/20">
        Skapa
      </button>
    </form>
  </section>

  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm overflow-hidden">
    <h2 class="text-lg font-bold mb-4 text-zinc-900 dark:text-zinc-50">Befintliga inbjudningar</h2>
    <div class="overflow-x-auto -mx-6">
      <table class="min-w-full text-sm text-left border-collapse">
        <thead>
          <tr class="bg-zinc-50 dark:bg-zinc-950 text-zinc-500 uppercase text-[10px] tracking-widest font-bold">
            <th class="px-6 py-4">Token</th>
            <th class="px-6 py-4">E-post</th>
            <th class="px-6 py-4">Skapad</th>
            <th class="px-6 py-4">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
          <?php foreach (($invites ?? []) as $inv): ?>
            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-colors">
              <td class="px-6 py-4 font-mono text-xs text-accent"><?= htmlspecialchars($inv['token']) ?></td>
              <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400"><?= htmlspecialchars($inv['email'] ?? '—') ?></td>
              <td class="px-6 py-4 text-zinc-500 text-xs"><?= htmlspecialchars($inv['created_at'] ?? '') ?></td>
              <td class="px-6 py-4">
                <?php if ($inv['consumed_at']): ?>
                   <span class="text-zinc-400 text-xs italic">Förbrukad (<?= htmlspecialchars($inv['consumed_at']) ?>)</span>
                <?php else: ?>
                   <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-[10px] font-bold rounded-full uppercase">Klar att använda</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($invites)): ?>
            <tr>
              <td colspan="4" class="px-6 py-8 text-center text-zinc-400 italic">Inga inbjudningar skapade.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
