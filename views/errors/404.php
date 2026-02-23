<?php $title = $title ?? '404'; ?>
<div class="max-w-md mx-auto py-12 text-center">
  <div class="mb-6 p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 inline-block">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  </div>
  <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-50 mb-2">404</h1>
  <p class="text-zinc-500 mb-8 italic">Sidan du letar efter verkar ha sprungit bort.</p>
  <a href="<?= url('/') ?>" class="inline-block px-6 py-2.5 bg-accent text-white rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-accent/20">
    Gå till startsidan
  </a>
</div>
