<?php $title = $title ?? 'Plan-wizard'; ?>
<div class="space-y-6">
  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Plan-wizard</h1>
      <p class="text-zinc-500 text-sm">Beräkna din personliga plan baserat på dina mål och förutsättningar.</p>
    </div>

    <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Längd (cm)</label>
        <input type="number" step="1" name="height_cm" value="<?= htmlspecialchars(isset($user['height_cm']) ? (string)(int)round((float)$user['height_cm']) : '') ?>" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Nuvarande vikt (kg)</label>
        <input type="number" step="0.1" name="weight_now" placeholder="t.ex. 72.5" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Kön</label>
        <select name="gender" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          <option value="neutral">Vill ej ange / Annat</option>
          <option value="male">Man</option>
          <option value="female">Kvinna</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Ålder</label>
        <input type="number" name="age" min="10" max="100" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Aktivitetsnivå</label>
        <select name="activity_level" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          <option value="sedentary">Stillasittande (kontorsjobb, ingen motion)</option>
          <option value="light">Lätt aktiv (lätt motion 1-3 dgr/vecka)</option>
          <option value="moderate">Måttligt aktiv (motion 3-5 dgr/vecka)</option>
          <option value="active">Aktiv (hård träning 6-7 dgr/vecka)</option>
          <option value="very_active">Mycket aktiv (hård träning & fysiskt jobb)</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Intensitet (Viktminskning per vecka)</label>
        <select name="intensity" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          <option value="gentle">Långsam (0,25 % – ca 0,2–0,4 kg/v)</option>
          <option value="normal" selected>Normal (0,50 % – ca 0,4–0,8 kg/v)</option>
          <option value="aggressive">Aggressiv (1,0 % – ca 0,8–1,0 kg/v)</option>
        </select>
        <p class="text-xs text-zinc-500 mt-2 italic">Högre intensitet ger snabbare resultat men kräver mer disciplin.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Steg/dag (mål)</label>
        <input type="number" name="steps_target" value="8000" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>

      <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Målvikt (kg, valfritt)</label>
        <input type="number" step="0.1" name="weight_goal" placeholder="ex 70" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
      </div>

      <div class="md:col-span-2 flex flex-col gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
        <div class="text-sm font-bold text-zinc-900 dark:text-zinc-50 uppercase tracking-wider mb-2">Avancerade inställningar</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Slutdatum (valfritt)</label>
            <input type="date" name="target_end_date" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          </div>
          <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Minimivikt (kg, valfritt)</label>
            <input type="number" step="0.1" name="min_weight" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          </div>
          <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Manuellt Kcal-mål</label>
            <input type="number" name="kcal_target" placeholder="Auto" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          </div>
          <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Manuellt Protein-mål (g)</label>
            <input type="number" name="protein_target" placeholder="Föreslås ≈ 2.0 g/kg" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
          </div>
        </div>
      </div>

      <div class="md:col-span-2 pt-4">
        <button class="w-full py-4 bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-xl">
          Spara & aktivera plan
        </button>
      </div>
    </form>
  </section>

  <section class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-zinc-50 dark:bg-zinc-950 text-sm">
    <h2 class="font-bold text-zinc-900 dark:text-zinc-50 mb-3">Viktig information</h2>
    <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
      <li class="flex gap-2">
        <span class="text-accent font-bold">•</span>
        <span>Auto-kcal använder Mifflin–St Jeor-formeln kombinerat med din aktivitetsfaktor och valt underskott.</span>
      </li>
      <li class="flex gap-2">
        <span class="text-accent font-bold">•</span>
        <span>Planen flaggar varningar vid aggressiv viktnedgång (> 1.0% per vecka).</span>
      </li>
      <li class="flex gap-2">
        <span class="text-accent font-bold">•</span>
        <span>BMI under 18.5 markeras tydligt som en varning för din hälsa.</span>
      </li>
      <li class="flex gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-2 mt-2">
        <span class="text-accent font-bold">i</span>
        <div>
          <p class="font-bold mb-1">Aktivitetsnivåer:</p>
          <ul class="space-y-1 text-xs">
            <li><strong>Stillasittande:</strong> Ingen avsiktlig motion, sitter mest hela dagen.</li>
            <li><strong>Lätt aktiv:</strong> Promenader eller lätt träning 1-3 gånger i veckan.</li>
            <li><strong>Måttligt aktiv:</strong> Träning eller sport medelintensivt 3-5 gånger i veckan.</li>
            <li><strong>Aktiv:</strong> Intensiv träning eller fysiskt krävande sport 6-7 gånger i veckan.</li>
            <li><strong>Mycket aktiv:</strong> Mycket tung träning två gånger om dagen eller ett fysiskt krävande jobb.</li>
          </ul>
        </div>
      </li>
    </ul>
  </section>
</div>
