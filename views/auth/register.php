<?php $title = $title ?? 'Registrera'; ?>
<div class="max-w-md mx-auto p-8 border border-zinc-200 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-xl">
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Skapa konto</h1>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">Börja din resa mot en hälsosammare livsstil.</p>
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

  <form method="post" class="space-y-5">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    
    <?php if (!empty($require_invite)): ?>
    <div>
      <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Inbjudningskod</label>
      <input name="invite" required placeholder="Din kod..." class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
    </div>
    <?php endif; ?>

    <div>
      <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">E-postadress</label>
      <input name="email" type="email" required placeholder="namn@exempel.se" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
    </div>

    <div>
      <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Lösenord</label>
      <input name="password" type="password" required placeholder="Minst 8 tecken" class="w-full px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950 focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
    </div>

    <div class="p-4 rounded-xl border border-amber-300/60 dark:border-amber-400/30 bg-amber-50 dark:bg-amber-900/10 text-amber-900 dark:text-amber-100 space-y-3">
      <div class="font-semibold text-sm">Viktigt innan du registrerar dig</div>

      <details class="group">
        <summary class="cursor-pointer text-sm font-medium">Integritet (GDPR) – klicka för att läsa</summary>
        <div class="mt-2 text-xs leading-5 space-y-3">
          <p>När du skapar ett konto i BMIKollen behandlar vi personuppgifter för att kunna tillhandahålla tjänsten.</p>
          
          <div>
            <p class="font-semibold mb-1">Vilka uppgifter vi behandlar</p>
            <ul class="list-disc ml-4 space-y-1">
              <li><strong>Kontouppgifter:</strong> e-postadress, tidszon, temainställningar och kontostatus samt tidsstämplar (skapad/uppdaterad/verifierad e-post).</li>
              <li><strong>Säkerhetsuppgifter:</strong> lösenord lagras endast som hash, samt tokens (hashade) för t.ex. e-postverifiering och lösenordsåterställning.</li>
              <li><strong>Session- och driftdata:</strong> sessionsdata kopplad till ditt konto (t.ex. user_id och säkerhetstokens) samt tidsstämplar.</li>
              <li><strong>Säkerhetsloggar:</strong> IP-adress och uppgifter om inloggningsförsök (för att förebygga missbruk).</li>
              <li><strong>Hälsorelaterade uppgifter som du själv anger:</strong> längd, vikt, viktmål/minvikt (lagras krypterat), samt plan-/livsstilsmål (kcal, protein, steg, aktivitetsnivå, träningsmål, intensitet) och dagliga loggar/anteckningar om kost och aktivitet.</li>
            </ul>
          </div>

          <div>
            <p class="font-semibold mb-1">Varför vi behandlar uppgifterna</p>
            <p>För att driva och leverera tjänstens funktioner, t.ex. beräkningar, grafer, historik, mål/planer, kontoadministration, säker inloggning och missbruks-/bedrägeriskydd.</p>
          </div>

          <div>
            <p class="font-semibold mb-1">Rättslig grund</p>
            <ul class="list-disc ml-4 space-y-1">
              <li><strong>Avtal (GDPR art. 6.1 b):</strong> för kontot och tjänstens grundläggande funktioner.</li>
              <li><strong>Uttryckligt samtycke (GDPR art. 9.2 a):</strong> för behandling av dina hälsodata (t.ex. vikt, viktmål, kost-/aktivitetsloggar och planparametrar).</li>
            </ul>
            <p class="mt-1">Du kan när som helst återkalla samtycke och radera ditt konto via Profil.</p>
          </div>

          <div>
            <p class="font-semibold mb-1">Lagring, mottagare och lagringstid</p>
            <p>Uppgifterna lagras inom EU/EES. Vi säljer inte dina uppgifter och delar dem inte med andra för deras egna ändamål. Vi kan använda personuppgiftsbiträden för drift och säkerhet (t.ex. hosting och e-postutskick) enligt avtal.</p>
            <p class="mt-1">Uppgifter sparas så länge kontot är aktivt eller tills du begär radering. Vid radering tas kontot och kopplade uppgifter bort (de flesta uppgifter raderas via automatiska kopplingar), medan sessionsdata och vissa loggar/backuper kan finnas kvar under begränsad tid innan de rensas.</p>
          </div>

          <div>
            <p class="font-semibold mb-1">Dina rättigheter</p>
            <p>Du har rätt att begära tillgång, rättelse, radering, begränsning, och dataportabilitet (för uppgifter du lämnat). Du har även rätt att klaga hos Integritetsskyddsmyndigheten (IMY).</p>
          </div>
        </div>
      </details>

      <details class="group">
        <summary class="cursor-pointer text-sm font-medium">Om hälsa och tolkning – klicka för att läsa</summary>
        <div class="mt-2 text-xs leading-5 space-y-2">
          <p>BMIKollen är ett hobbyprojekt och tillhandahålls som ett informations- och planeringsverktyg. Jag är inte läkare. Innehåll, beräkningar och funktioner i BMIKollen utgör inte medicinsk rådgivning och är inte avsedda för diagnos, behandling eller förebyggande av sjukdom.</p>
          <p>Använd inte BMIKollen som ersättning för professionell rådgivning. Vid frågor om hälsa, sjukdom, medicinering, graviditet eller om du är osäker: kontakta hälso- och sjukvården (t.ex. 1177). Vid akuta symptom: ring 112.</p>
          <p>BMIKollen kan innehålla fel, förenklingar eller avvikelser (t.ex. på grund av inmatningsfel, avrundningar, tekniska fel eller tredjepartsdata). Du ansvarar själv för hur informationen används. I den utsträckning lagen medger ansvarar utvecklaren inte för skador eller förluster som uppstår genom användning av BMIKollen.</p>
        </div>
      </details>

      <div class="pt-1">
        <label class="inline-flex items-start gap-3 text-sm">
          <input type="checkbox" name="accept_terms" required class="mt-0.5 h-4 w-4 rounded">
          <span>Genom att fortsätta bekräftar jag att jag har läst och förstått integritetstexten och "Om hälsa och tolkning".</span>
        </label>
      </div>
    </div>
    
    <button class="w-full py-3 bg-zinc-900 text-white dark:bg-zinc-50 dark:text-zinc-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-zinc-200 dark:shadow-none">
      Skapa konto
    </button>
  </form>

  <p class="mt-8 text-center text-sm text-zinc-500">
    Har du redan ett konto? 
    <a href="<?= url('/auth/login') ?>" class="text-accent hover:underline font-semibold">Logga in istället</a>
  </p>
</div>
