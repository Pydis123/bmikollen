<?php
/** @var string $content */
/** @var string $title */
$csrf = $csrf_token ?? '';
$auth = $auth_user_id ?? null;
?>
<!doctype html>
<html lang="sv" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'BMIKollen') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  
  <!-- Favicons -->
  <link rel="icon" type="image/svg+xml" href="<?= url('/favicon.svg') ?>">
  <link rel="apple-touch-icon" href="<?= url('/favicon.svg') ?>">
  <link rel="manifest" href="<?= url('/site.webmanifest') ?>">
  <meta name="theme-color" content="#8b5cf6">
  <script>
    // Dark/Light mode via localStorage or system
    (function() {
      const pref = localStorage.getItem('theme');
      const phpPref = '<?= $_SESSION['theme_pref'] ?? 'system' ?>';
      const mql = window.matchMedia('(prefers-color-scheme: dark)');
      
      let dark = false;
      if (pref === 'dark') dark = true;
      else if (pref === 'light') dark = false;
      else if (phpPref === 'dark') dark = true;
      else if (phpPref === 'light') dark = false;
      else if (mql.matches) dark = true;

      if (dark) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace'],
          },
          colors: {
            accent: {
              DEFAULT: '#8b5cf6', // A nice violet
              dark: '#a78bfa',
            },
            blue: {
                600: '#8b5cf6', // Override blue to use accent instead to avoid code sweeps
                700: '#7c3aed',
                50: '#f5f3ff',
                900: '#4c1d95',
            }
          }
        }
      }
    }
  </script>
  <style>
    [v-cloak] { display: none; }
    .nav-link-active {
        @apply border-b-2 border-accent text-zinc-900 dark:text-zinc-50 font-medium;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
  </style>
</head>
<body class="h-full bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans transition-colors duration-200">
  <div class="max-w-4xl mx-auto px-6 py-6">
    <header class="flex items-center justify-between py-4 mb-4 md:mb-8 border-b border-zinc-200 dark:border-zinc-800 gap-4">
      <div class="flex items-center gap-2">
        <a href="<?= \App\Core\Config::get('app.url') ?>/" class="flex items-center">
          <!-- Mobila loggor -->
          <img src="<?= url('/assets/img/logo-light-mobile.png') ?>" alt="BMIKollen" class="h-6 w-auto dark:hidden md:hidden">
          <img src="<?= url('/assets/img/logo-dark-mobile.png') ?>" alt="BMIKollen" class="h-6 w-auto hidden dark:block md:dark:hidden md:hidden">
          
          <!-- Desktop-loggor -->
          <img src="<?= url('/assets/img/logo-light-desktop.png') ?>" alt="BMIKollen" class="h-8 w-auto hidden md:block dark:hidden">
          <img src="<?= url('/assets/img/logo-dark-desktop.png') ?>" alt="BMIKollen" class="h-8 w-auto hidden md:dark:block">
        </a>
      </div>
      
      <div class="flex items-center gap-2 md:gap-4">
        <nav class="hidden md:flex items-center gap-x-6 text-base">
          <?php 
          $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
          $scriptName = $_SERVER['SCRIPT_NAME'];
          $basePath = str_replace('\\', '/', dirname($scriptName));
          if ($basePath !== '/' && str_starts_with($currentPath, $basePath)) {
              $currentPath = substr($currentPath, strlen($basePath));
          }
          if ($currentPath === '') $currentPath = '/';

          $navItems = [
              ['path' => '/', 'label' => 'Idag'],
              ['path' => '/overview', 'label' => 'Framsteg', 'active_paths' => ['/overview', '/plan', '/week', '/charts']],
              ['path' => '/feedback', 'label' => 'Feedback'],
              ['path' => '/profile', 'label' => 'Profil'],
          ];

          if ($auth): 
              foreach($navItems as $item):
                  $isActive = isset($item['active_paths']) 
                      ? in_array($currentPath, $item['active_paths']) 
                      : ($currentPath === $item['path']);
                  $activeClass = $isActive ? 'text-zinc-950 dark:text-white font-semibold border-b-2 border-accent' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors';
          ?>
            <a href="<?= \App\Core\Config::get('app.url') . $item['path'] ?>" class="py-1 <?= $activeClass ?>">
              <?= $item['label'] ?>
            </a>
          <?php endforeach; ?>
          <?php if (!empty($is_admin)): 
                $isActive = in_array($currentPath, ['/admin/feedback', '/admin/invites']);
                $activeClass = $isActive ? 'text-zinc-950 dark:text-white font-semibold border-b-2 border-accent' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors';
          ?>
            <a href="<?= \App\Core\Config::get('app.url') ?>/admin/feedback" class="py-1 <?= $activeClass ?>">Admin</a>
          <?php endif; ?>
            <form action="<?= \App\Core\Config::get('app.url') ?>/auth/logout" method="post" class="inline ml-2">
              <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
              <button class="text-zinc-400 hover:text-red-500 transition-colors" title="Logga ut">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
              </button>
            </form>
          <?php else: ?>
            <a href="<?= \App\Core\Config::get('app.url') ?>/auth/login" class="px-4 py-2 rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-medium hover:opacity-90 transition-opacity">Logga in</a>
            <a href="<?= \App\Core\Config::get('app.url') ?>/auth/register" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">Registrera</a>
          <?php endif; ?>
        </nav>


        <?php if ($auth): ?>
        <form action="<?= \App\Core\Config::get('app.url') ?>/auth/logout" method="post" class="md:hidden">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
          <button class="p-2 text-zinc-400 hover:text-red-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </form>
        <?php endif; ?>
      </div>
    </header>

    <main class="min-h-[60vh] pb-24 md:pb-0">
      <?php 
      $tabs = [
          ['path' => '/overview', 'label' => 'Översikt'],
          ['path' => '/plan', 'label' => 'Plan'],
          ['path' => '/week', 'label' => 'Vecka'],
          ['path' => '/charts', 'label' => 'Grafer'],
      ];
      $currentGroupPaths = array_column($tabs, 'path');
      if ($auth && in_array($currentPath, $currentGroupPaths)): 
      ?>
        <div class="mb-6 border-b border-zinc-200 dark:border-zinc-800">
          <nav class="flex -mb-px overflow-x-auto no-scrollbar gap-x-8" aria-label="Tabs">
            <?php foreach($tabs as $tab): 
              $isTabActive = ($currentPath === $tab['path']);
              $tabClass = $isTabActive 
                ? 'border-accent text-accent' 
                : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-700';
            ?>
              <a href="<?= \App\Core\Config::get('app.url') . $tab['path'] ?>" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors <?= $tabClass ?>">
                <?= $tab['label'] ?>
              </a>
            <?php endforeach; ?>
          </nav>
        </div>
      <?php endif; ?>

      <?php 
      $adminTabs = [
          ['path' => '/admin/feedback', 'label' => 'Feedback'],
          ['path' => '/admin/invites', 'label' => 'Inbjudningar'],
      ];
      $adminPaths = array_column($adminTabs, 'path');
      if ($auth && !empty($is_admin) && in_array($currentPath, $adminPaths)): 
      ?>
        <div class="mb-6 border-b border-zinc-200 dark:border-zinc-800">
          <nav class="flex -mb-px overflow-x-auto no-scrollbar gap-x-8" aria-label="Tabs">
            <?php foreach($adminTabs as $tab): 
              $isTabActive = ($currentPath === $tab['path']);
              $tabClass = $isTabActive 
                ? 'border-accent text-accent' 
                : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-700';
            ?>
              <a href="<?= \App\Core\Config::get('app.url') . $tab['path'] ?>" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors <?= $tabClass ?>">
                <?= $tab['label'] ?>
              </a>
            <?php endforeach; ?>
          </nav>
        </div>
      <?php endif; ?>
      <?= $content ?>
    </main>

    <?php if ($auth): ?>
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-lg border-t border-zinc-200 dark:border-zinc-800 px-6 py-3 flex justify-between items-center z-50">
      <?php 
      $mobileNav = [
        ['path' => '/', 'label' => 'Idag', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'],
        ['path' => '/overview', 'label' => 'Framsteg', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />', 'active_paths' => ['/overview', '/plan', '/week', '/charts']],
        ['path' => '/feedback', 'label' => 'Feedback', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-6 4h8M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H9l-4 4v-4H5a2 2 0 01-2-2V8a2 2 0 012-2z" />'],
        ['path' => '/profile', 'label' => 'Profil', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'],
      ];

      if (!empty($is_admin)) {
        $mobileNav[] = ['path' => '/admin/feedback', 'label' => 'Admin', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a10 10 0 11-18 0 10 10 0 0118 0z" />', 'active_paths' => ['/admin/feedback', '/admin/invites']];
      }

      foreach($mobileNav as $item):
        $isActive = isset($item['active_paths']) 
            ? in_array($currentPath, $item['active_paths']) 
            : ($currentPath === $item['path']);
        $colorClass = $isActive ? 'text-accent' : 'text-zinc-400 dark:text-zinc-500';
      ?>
      <a href="<?= \App\Core\Config::get('app.url') . $item['path'] ?>" class="flex flex-col items-center gap-1 <?= $colorClass ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <?= $item['icon'] ?>
        </svg>
        <span class="text-[10px] font-medium"><?= $item['label'] ?></span>
      </a>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <footer class="mt-20 py-8 border-t border-zinc-200 dark:border-zinc-800 text-sm text-zinc-500 hidden md:flex justify-between items-center">
      <span>&copy; <?= date('Y') ?> BMIKollen</span>
      <div class="flex gap-4">
        <!-- Eventuella länkar här -->
      </div>
    </footer>
  </div>

</body>
</html>
