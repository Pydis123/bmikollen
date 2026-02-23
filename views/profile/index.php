<?php $title = $title ?? 'Profil'; ?>
<div class="p-8 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
  <div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Profilinställningar</h1>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm">Hantera dina personliga inställningar och applikationspreferenser.</p>
  </div>

  <?php if (!empty($success)): ?>
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm font-medium flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm font-medium flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="post" class="space-y-6">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">E-postadress</label>
        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 text-zinc-500 cursor-not-allowed">
      </div>

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Längd (cm)</label>
        <input type="number" step="1" name="height_cm" value="<?= htmlspecialchars(isset($user['height_cm']) ? (string)(int)round((float)$user['height_cm']) : '') ?>" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Tema</label>
        <select name="theme_pref" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          <option value="system" <?= (($user['theme_pref'] ?? '') === 'system') ? 'selected' : '' ?>>Följ systemet</option>
          <option value="light" <?= (($user['theme_pref'] ?? '') === 'light') ? 'selected' : '' ?>>Ljust tema</option>
          <option value="dark" <?= (($user['theme_pref'] ?? '') === 'dark') ? 'selected' : '' ?>>Mörkt tema</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Tidszon</label>
        <input type="text" name="timezone" value="<?= htmlspecialchars($user['timezone'] ?? 'Europe/Stockholm') ?>" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all" placeholder="t.ex. Europe/Stockholm">
      </div>
    </div>

    <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800">
      <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 mb-4">Byt lösenord</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Nuvarande lösenord</label>
          <input type="password" name="current_password" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
        </div>
        <div>
          <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Nytt lösenord</label>
          <input type="password" name="new_password" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
        </div>
        <div>
          <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Bekräfta nytt lösenord</label>
          <input type="password" name="confirm_password" class="w-full px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
        </div>
      </div>
      <p class="text-[10px] text-zinc-500 mt-2 italic">Lämna fälten tomma om du inte vill ändra ditt lösenord.</p>
    </div>

    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
      <button class="px-6 py-2.5 bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 rounded-xl font-semibold hover:opacity-90 transition-opacity shadow-lg shadow-zinc-200 dark:shadow-none">
        Spara inställningar
      </button>
    </div>
  </form>

  <div class="mt-12 pt-8 border-t border-zinc-100 dark:border-zinc-800">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">Nuvarande Plan</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">Visa dina mål för kalorier, protein, steg och vikt.</p>
      </div>
      <a href="<?= url('/plan') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors font-medium">
        Visa min plan
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const themeSelect = document.querySelector('select[name="theme_pref"]');
    if (themeSelect) {
      themeSelect.addEventListener('change', function() {
        if (this.value === 'system') {
          localStorage.removeItem('theme');
        } else {
          localStorage.setItem('theme', this.value);
        }
        
        // Uppdatera live
        if (this.value === 'dark' || (this.value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
        if (window.updateIcons) window.updateIcons();
      });
    }
  });
</script>
