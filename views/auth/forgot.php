<?php $title = $title ?? 'Glömt lösenord'; ?>
<div class="p-6 border rounded bg-slate-50 dark:bg-slate-800">
  <h1 class="text-xl font-semibold mb-4">Glömt lösenord</h1>
  <?php if (!empty($success)): ?>
    <div class="mb-3 text-green-600"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <form method="post" class="space-y-3">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <div>
      <label class="block text-sm mb-1">E-post</label>
      <input name="email" type="email" required class="w-full px-3 py-2 border rounded bg-white dark:bg-slate-900">
    </div>
    <div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Skicka instruktioner</button>
    </div>
  </form>
</div>
