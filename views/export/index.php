<?php $title = $title ?? 'Exportera data'; ?>
<div class="max-w-2xl mx-auto p-8 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Exportera data</h1>
    <p class="text-zinc-500 text-sm mt-1">Ladda ner din historik som CSV för egen analys.</p>
  </div>

  <form method="post" action="<?= url('/export') ?>" class="space-y-6">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Från</label>
        <input type="date" name="from" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Till</label>
        <input type="date" name="to" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Välj specifika datum</label>
      <div id="datesList" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm max-h-48 overflow-y-auto p-4 border border-zinc-100 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-950/50">
        <div class="text-zinc-400 italic">Välj ett intervall ovan för att se datum.</div>
      </div>
    </div>

    <div class="pt-4">
      <button class="w-full py-3 bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg">
        Generera CSV-fil
      </button>
    </div>
  </form>
</div>

<script>
  const from = document.querySelector('input[name=from]');
  const to = document.querySelector('input[name=to]');
  const list = document.getElementById('datesList');

  function renderDates() {
    list.innerHTML = '';
    if (!from.value || !to.value) return;
    const start = new Date(from.value);
    const end = new Date(to.value);
    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
      const val = d.toISOString().slice(0,10);
      const label = document.createElement('label');
      label.className = 'flex items-center gap-2';
      label.innerHTML = `<input type="checkbox" name="dates[]" value="${val}" checked> <span>${val}</span>`;
      list.appendChild(label);
    }
  }
  from.addEventListener('change', renderDates);
  to.addEventListener('change', renderDates);
</script>
