<?php $title = $title ?? 'Logga in'; ?>
<div class="max-w-md mx-auto p-8 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-xl">
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Logga in</h1>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">Välkommen tillbaka till BMIKollen.</p>
  </div>

  <?php if (!empty($_GET['verified'])): ?>
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm font-medium flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      E-post verifierad. Du kan nu logga in.
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

  <form method="post" class="space-y-5">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <div>
      <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">E-postadress</label>
      <input name="email" type="email" required placeholder="namn@exempel.se" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
    </div>
    <div>
      <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Lösenord</label>
        <a href="<?= url('/auth/forgot') ?>" class="text-xs text-accent hover:underline font-medium">Glömt lösenord?</a>
      </div>
      <input name="password" type="password" required placeholder="••••••••" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
    </div>
    
    <button class="w-full py-3 bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-zinc-200 dark:shadow-none">
      Logga in
    </button>
  </form>

  <p class="mt-8 text-center text-sm text-zinc-500">
    Har du inget konto? 
    <a href="<?= url('/auth/register') ?>" class="text-accent hover:underline font-semibold">Registrera dig nu</a>
  </p>
</div>
