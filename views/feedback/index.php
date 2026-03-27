<?php $title = $title ?? 'Feedback'; ?>
<div class="space-y-6">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h1 class="text-2xl font-bold mb-2 text-zinc-900 dark:text-zinc-50 tracking-tight">Feedback</h1>
    <p class="text-sm text-zinc-500 mb-4">Lämna en bugg eller ett förbättringsförslag. Endast du ser dina egna poster.</p>

    <?php if (!empty($success)): ?>
      <div class="mb-4 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="grid grid-cols-1 md:grid-cols-6 gap-3">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
      <div class="md:col-span-2">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Typ</label>
        <select name="type" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950">
          <option value="bug">Bugg</option>
          <option value="suggestion">Förslag</option>
        </select>
      </div>
      <div class="md:col-span-4">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Titel</label>
        <input type="text" name="title" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-900" placeholder="Kort titel...">
      </div>
      <div class="md:col-span-6">
        <label class="block text-xs font-medium text-zinc-500 mb-1">Beskrivning</label>
        <textarea name="description" rows="4" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-900" placeholder="Beskriv buggen/förslaget så tydligt som möjligt..."></textarea>
      </div>
      <div class="md:col-span-6 flex justify-end pt-1">
        <button class="px-4 py-2 rounded-lg bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 font-semibold">Skicka</button>
      </div>
    </form>
  </section>

  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Mina buggar</h2>
    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
      <?php if (empty($myBugs)): ?>
        <p class="text-sm text-zinc-500">Inga buggar rapporterade ännu.</p>
      <?php else: foreach ($myBugs as $b): ?>
        <div class="py-3">
          <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($b['title']) ?></div>
          <div class="text-xs text-zinc-500 mb-1"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($b['created_at'] ?? 'now'))) ?></div>
          <div class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($b['description'])) ?></div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </section>

  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Mina förslag</h2>
    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
      <?php if (empty($mySuggestions)): ?>
        <p class="text-sm text-zinc-500">Inga förslag inskickade ännu.</p>
      <?php else: foreach ($mySuggestions as $s): ?>
        <div class="py-3">
          <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($s['title']) ?></div>
          <div class="text-xs text-zinc-500 mb-1"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($s['created_at'] ?? 'now'))) ?></div>
          <div class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($s['description'])) ?></div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </section>
</div>
