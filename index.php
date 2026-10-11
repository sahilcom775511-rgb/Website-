<?php
/** index.php - main earning dashboard */
require __DIR__ . '/common/config.php';
$user = require_user();

$types = task_types();
$chips = ['all' => 'All', 'watch' => 'Watch & Earn', 'install' => 'Install & Earn', 'survey' => 'Survey', 'daily' => 'Daily'];
$cat = $_GET['cat'] ?? 'all';
if (!isset($chips[$cat])) { $cat = 'all'; }
$tasks = fetch_tasks_for_user((int)$user['id'], $cat === 'all' ? null : $cat);

$pageTitle = 'Home';
$activeNav = 'home';
require __DIR__ . '/common/header.php';
?>
<h1 class="text-xl font-bold">Hello, <?= e(first_name($user['name'])) ?></h1>
<p class="text-sm text-slate-500">Complete tasks and earn rewards</p>

<div class="mt-4 relative overflow-hidden rounded-2xl bg-indigo-600 text-white p-5 shadow-lg">
  <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-white/10"></div>
  <div class="absolute right-8 -bottom-10 w-24 h-24 rounded-full bg-white/10"></div>
  <p class="text-sm text-indigo-100 flex items-center gap-2"><i class="fa-solid fa-wallet"></i> Wallet Balance</p>
  <p class="text-3xl font-bold mt-1"><?= e(money($user['wallet_balance'])) ?></p>
  <a href="wallet.php" class="relative inline-flex items-center gap-2 mt-4 rounded-xl bg-white text-indigo-600 px-4 py-2.5 text-sm font-semibold">View Wallet <i class="fa-solid fa-arrow-right text-xs"></i></a>
</div>

<h2 class="mt-6 mb-3 font-bold">Task Categories</h2>
<div class="flex gap-2 overflow-x-auto no-scrollbar -mx-4 px-4 pb-1">
  <?php foreach ($chips as $key => $label): $on = ($cat === $key); ?>
    <a href="index.php<?= $key === 'all' ? '' : '?cat=' . e($key) ?>" class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold border <?= $on ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200' ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<div class="mt-4 space-y-4">
<?php if (!$tasks): ?>
  <div class="rounded-2xl bg-white border border-slate-100 p-8 text-center shadow-sm">
    <i class="fa-solid fa-list-check text-3xl text-slate-300"></i>
    <p class="mt-3 font-semibold">No tasks available</p>
    <p class="text-sm text-slate-500">Please check back soon for new ways to earn.</p>
  </div>
<?php endif; ?>

<?php foreach ($tasks as $t):
    $state = task_state($t);
    [$typeLabel, $typeIcon] = $types[$t['type']] ?? ['Task', 'fa-star'];
?>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="relative aspect-video overflow-hidden rounded-t-2xl">
      <?= cover_html($t) ?>
      <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/40 to-transparent"></div>
      <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-indigo-600 shadow-sm"><i class="fa-solid <?= e($typeIcon) ?>"></i><?= e($typeLabel) ?></span>
      <span class="absolute bottom-3 right-3 inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-3 py-1 text-sm font-bold text-white shadow"><i class="fa-solid fa-coins text-xs"></i>+<?= e(money(task_reward($t))) ?></span>
    </div>
    <div class="p-4">
      <h3 class="font-bold leading-snug"><?= e($t['title']) ?></h3>
      <p class="mt-1 text-sm text-slate-500 line-clamp-2"><?= e($t['description']) ?></p>
      <?php if ($t['proof_required']): ?>
        <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-full px-2.5 py-1"><i class="fa-solid fa-camera"></i> Proof required</p>
      <?php endif; ?>
      <div class="mt-4">
        <?php if ($state === 'available'): ?>
          <a href="task-detail.php?id=<?= (int)$t['id'] ?>" class="block text-center rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white py-3 text-sm font-semibold"><?= $t['type'] === 'daily' ? 'Claim Daily Reward' : 'Earn Now' ?></a>
        <?php elseif ($state === 'pending'): ?>
          <div class="text-center rounded-xl bg-amber-50 text-amber-700 py-3 text-sm font-semibold"><i class="fa-solid fa-clock mr-1"></i> Pending Review</div>
        <?php elseif ($state === 'completed'): ?>
          <div class="text-center rounded-xl bg-emerald-50 text-emerald-700 py-3 text-sm font-semibold"><i class="fa-solid fa-circle-check mr-1"></i> Completed</div>
        <?php elseif ($state === 'claimed_today'): ?>
          <div class="text-center rounded-xl bg-emerald-50 text-emerald-700 py-3 text-sm font-semibold"><i class="fa-solid fa-circle-check mr-1"></i> Claimed - come back tomorrow</div>
        <?php else: ?>
          <div class="text-center rounded-xl bg-slate-100 text-slate-500 py-3 text-sm font-semibold"><i class="fa-solid fa-ban mr-1"></i> Limit Reached</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/common/bottom.php'; ?>
