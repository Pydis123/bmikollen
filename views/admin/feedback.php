<?php $title = $title ?? 'Admin · Feedback'; ?>
<div class="space-y-10">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Feedback (Admin)</h1>
      <a href="<?= url('/feedback') ?>" class="text-sm text-accent">Till användarsidan</a>
    </div>

    <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-3">Buggar</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-left text-zinc-500">
          <tr>
            <th class="py-2 pr-4">Datum</th>
            <th class="py-2 pr-4">Användare</th>
            <th class="py-2 pr-4">Titel</th>
            <th class="py-2 pr-4">Beskrivning</th>
            <th class="py-2 pr-4">Prio</th>
            <th class="py-2 pr-4">Status</th>
            <th class="py-2 pr-4"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
          <?php if (empty($bugs)): ?>
            <tr><td colspan="7" class="py-3 text-zinc-500">Inga buggar.</td></tr>
          <?php else: foreach ($bugs as $b): ?>
            <tr>
              <td class="py-2 pr-4 align-top"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($b['created_at'] ?? 'now'))) ?></td>
              <td class="py-2 pr-4 align-top"><span class="text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars($b['email'] ?? ('#'.$b['user_id'])) ?></span></td>
              <td class="py-2 pr-4 align-top font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($b['title']) ?></td>
              <td class="py-2 pr-4 align-top text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($b['description'])) ?></td>
              <td class="py-2 pr-4 align-top">
                <?php $p = isset($b['prio']) ? (int)$b['prio'] : 3; ?>
                <form method="post" action="<?= url('/admin/feedback/bug/' . (int)$b['id'] . '/status') ?>" class="flex items-center gap-2">
                  <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                  <select name="prio" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950">
                    <option value="1" <?= $p===1?'selected':'' ?>>1 (Hög)</option>
                    <option value="2" <?= $p===2?'selected':'' ?>>2 (Medel)</option>
                    <option value="3" <?= $p===3?'selected':'' ?>>3 (Låg)</option>
                  </select>
              </td>
              <td class="py-2 pr-4 align-top">
                  <?php $s = $b['status'] ?? 'not_started'; ?>
                  <select name="status" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950">
                    <option value="not_started" <?= $s==='not_started'?'selected':'' ?>>ej påbörjad</option>
                    <option value="in_progress" <?= $s==='in_progress'?'selected':'' ?>>påbörjad</option>
                    <option value="done" <?= $s==='done'?'selected':'' ?>>avslutad</option>
                  </select>
                  <button class="px-3 py-1 rounded-lg bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900">Spara</button>
                </form>
              </td>
              <td class="py-2 pr-4 align-top"></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <h2 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-3">Förslag</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-left text-zinc-500">
          <tr>
            <th class="py-2 pr-4">Datum</th>
            <th class="py-2 pr-4">Användare</th>
            <th class="py-2 pr-4">Titel</th>
            <th class="py-2 pr-4">Beskrivning</th>
            <th class="py-2 pr-4">Prio</th>
            <th class="py-2 pr-4">Status</th>
            <th class="py-2 pr-4"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
          <?php if (empty($suggestions)): ?>
            <tr><td colspan="7" class="py-3 text-zinc-500">Inga förslag.</td></tr>
          <?php else: foreach ($suggestions as $s): ?>
            <tr>
              <td class="py-2 pr-4 align-top"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($s['created_at'] ?? 'now'))) ?></td>
              <td class="py-2 pr-4 align-top"><span class="text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars($s['email'] ?? ('#'.$s['user_id'])) ?></span></td>
              <td class="py-2 pr-4 align-top font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($s['title']) ?></td>
              <td class="py-2 pr-4 align-top text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($s['description'])) ?></td>
              <td class="py-2 pr-4 align-top">
                <?php $p = isset($s['prio']) ? (int)$s['prio'] : 3; ?>
                <form method="post" action="<?= url('/admin/feedback/suggestion/' . (int)$s['id'] . '/status') ?>" class="flex items-center gap-2">
                  <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                  <select name="prio" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950">
                    <option value="1" <?= $p===1?'selected':'' ?>>1 (Hög)</option>
                    <option value="2" <?= $p===2?'selected':'' ?>>2 (Medel)</option>
                    <option value="3" <?= $p===3?'selected':'' ?>>3 (Låg)</option>
                  </select>
              </td>
              <td class="py-2 pr-4 align-top">
                  <?php $st = $s['status'] ?? 'not_started'; ?>
                  <select name="status" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950">
                    <option value="not_started" <?= $st==='not_started'?'selected':'' ?>>ej påbörjad</option>
                    <option value="in_progress" <?= $st==='in_progress'?'selected':'' ?>>påbörjad</option>
                    <option value="done" <?= $st==='done'?'selected':'' ?>>avslutad</option>
                  </select>
                  <button class="px-3 py-1 rounded-lg bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900">Spara</button>
                </form>
              </td>
              <td class="py-2 pr-4 align-top"></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
