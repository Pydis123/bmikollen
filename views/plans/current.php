<?php $title = $title ?? 'Nuvarande plan'; ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Nuvarande Plan</h1>
        <div class="flex gap-2 w-full sm:w-auto">
            <a href="<?= url('/wizard') ?>" class="flex-1 sm:flex-none px-4 py-2 bg-accent text-white rounded-xl font-semibold hover:opacity-90 transition-all text-center">Uppdatera plan</a>
            <a href="<?= url('/plans/history') ?>" class="flex-1 sm:flex-none px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-center">Historik</a>
        </div>
    </div>

    <?php if (!$plan): ?>
        <div class="p-8 border border-yellow-200 dark:border-yellow-900/50 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200">
            <p class="mb-4 font-medium">Du har ingen aktiv plan just nu.</p>
            <a href="<?= url('/wizard') ?>" class="inline-block px-6 py-2.5 bg-yellow-600 text-white rounded-xl font-semibold hover:opacity-90 transition-all">Skapa din första plan</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
                <h2 class="text-lg font-bold mb-6 text-zinc-900 dark:text-zinc-50 border-b border-zinc-100 dark:border-zinc-800 pb-3">Målsättningar</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Kcal per dag</span>
                        <span class="font-bold text-lg text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($plan['kcal_target']) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Protein per dag</span>
                        <span class="font-bold text-lg text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($plan['protein_target']) ?> g</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500">Steg per dag</span>
                        <span class="font-bold text-lg text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars(number_format($plan['steps_target'], 0, ',', ' ')) ?></span>
                    </div>
                    <?php if (!empty($plan['weight_goal'])): ?>
                    <div class="flex justify-between items-center border-t border-zinc-100 dark:border-zinc-800 pt-3">
                        <span class="text-zinc-500">Målvikt</span>
                        <span class="font-bold text-lg text-accent"><?= htmlspecialchars($plan['weight_goal']) ?> kg</span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($plan['min_weight'])): ?>
                    <div class="flex justify-between items-center <?= empty($plan['weight_goal']) ? 'border-t border-zinc-100 dark:border-zinc-800 pt-3' : '' ?>">
                        <span class="text-zinc-500">Min-vikt</span>
                        <span class="font-bold text-lg text-zinc-400"><?= htmlspecialchars($plan['min_weight']) ?> kg</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-6 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm">
                <h2 class="text-lg font-bold mb-6 text-zinc-900 dark:text-zinc-50 border-b border-zinc-100 dark:border-zinc-800 pb-3">Plan-detaljer</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Startdatum</span>
                        <span class="font-medium"><?= htmlspecialchars($plan['start_date']) ?></span>
                    </div>
                    <?php if ($plan['target_end_date']): ?>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Måldatum</span>
                            <span class="font-medium"><?= htmlspecialchars($plan['target_end_date']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Intensitet</span>
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 uppercase">
                            <?= match($plan['intensity_preset'] ?? 'normal') {
                                'gentle' => 'Lugn',
                                'normal' => 'Normal',
                                'aggressive' => 'Hård',
                                default => htmlspecialchars(ucfirst($plan['intensity_preset'] ?? 'Normal'))
                            } ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Aktivitetsnivå</span>
                        <span class="font-medium">
                            <?= match($plan['activity_level'] ?? 'sedentary') {
                                'sedentary' => 'Stillasittande',
                                'light' => 'Lätt aktiv',
                                'moderate' => 'Måttligt aktiv',
                                'active' => 'Mycket aktiv',
                                'very_active' => 'Extremt aktiv',
                                default => htmlspecialchars(ucfirst($plan['activity_level'] ?? 'Normal'))
                            } ?>
                        </span>
                    </div>
                    <div class="flex justify-between border-t border-zinc-100 dark:border-zinc-800 pt-3">
                        <span class="text-zinc-500">Längd</span>
                        <span class="font-medium"><?= (int)round($plan['height_cm']) ?> cm</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
