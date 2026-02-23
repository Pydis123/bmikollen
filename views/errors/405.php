<?php $title = $title ?? '405'; ?>
<div class="max-w-md mx-auto py-12 text-center">
  <div class="mb-6 p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 inline-block">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
    </svg>
  </div>
  <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-50 mb-2">405</h1>
  <p class="text-zinc-500 mb-8 italic">Den här metoden är tyvärr inte tillåten här.</p>
  <a href="<?= url('/') ?>" class="inline-block px-6 py-2.5 bg-accent text-white rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-accent/20">
    Gå till startsidan
  </a>
</div>
