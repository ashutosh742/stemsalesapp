<!-- application/views/emails/anaya_owner_daily.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Anaya � Daily Sales & Discipline Snapshot</title>
    <style type="text/css">
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        h1 { font-size: 20px; }
        h2 { font-size: 16px; margin-top: 18px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        p { margin: 4px 0; }
        .small { font-size: 12px; color: #777; }
        ul { margin: 4px 0 4px 18px; padding: 0; }
        li { margin: 2px 0; }
        .metric-box { border: 1px solid #eee; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .tag { display: inline-block; padding: 2px 6px; border-radius: 3px; background: #f3f3f3; font-size: 11px; margin-right: 5px; }
        .pill-green { background: #e5f8e5; color: #206030; }
        .pill-red { background: #fdecec; color: #9b1c1c; }
        .pill-yellow { background: #fff8e1; color: #9b6b00; }
        .table { border-collapse: collapse; width: 100%; margin-top: 6px; }
        .table th, .table td { border: 1px solid #eee; padding: 4px 6px; text-align: left; font-size: 12px; }
        .table th { background: #fafafa; }
    </style>
</head>
<body>

<p class="small">
    To: <?= html_escape($owner['email']); ?><br>
    Date: <?= html_escape($pack['date']); ?>
</p>

<h1>?? Anaya � Daily Sales & Discipline Snapshot | <?= html_escape($pack['scope'] ?? 'All India'); ?></h1>
<p>Hi <strong><?= html_escape($owner['name']); ?></strong>,</p>
<p>Here is your <strong>sales & discipline view</strong> for <strong><?= html_escape($pack['date']); ?></strong>.</p>

<!-- 1. Company snapshot -->
<h2>1. Company snapshot</h2>
<div class="metric-box">
    <p>?? Active BDs: <strong><?= (int)($metrics['company']['active_bds'] ?? 0); ?></strong></p>
    <p>?? Companies in current funnel:
       <strong><?= (int)($metrics['company']['funnel_companies'] ?? 0); ?></strong></p>
    <p>?? Total pipeline (Positive + Tentative + Closure):
       <strong><?= html_escape($metrics['company']['pipeline_value_display'] ?? '?0'); ?></strong></p>
    <p>? Closures this month:
       <strong><?= html_escape($metrics['company']['closure_value_display'] ?? '?0'); ?>
       / <?= (int)($metrics['company']['closure_count'] ?? 0); ?> closures</strong></p>
    <p>?? Closure target this month:
       <strong><?= html_escape($metrics['company']['target_closure_value_display'] ?? '?0'); ?></strong></p>
    <p>?? Likely outcome (current trend):
       <strong><?= html_escape($metrics['company']['likely_outcome_display'] ?? '?0'); ?></strong></p>
</div>

<!-- 2. Funnel health -->
<h2>2. Funnel health by stage</h2>
<div class="metric-box">
    <table class="table">
        <thead>
            <tr>
                <th>Stage</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($metrics['funnel']['by_stage'])): ?>
            <?php foreach ($metrics['funnel']['by_stage'] as $stage => $cnt): ?>
                <tr>
                    <td><?= html_escape($stage); ?></td>
                    <td><?= (int)$cnt; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No funnel data.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <p class="small">
        Positive/Tentative leads with no recent task (stuck):
        <strong><?= (int)($metrics['funnel']['stuck_critical_count'] ?? 0); ?></strong>
    </p>
</div>

<!-- 3. Discipline & execution -->
<h2>3. Discipline & execution</h2>
<div class="metric-box">
    <p>?? Planner created (yesterday):
       <strong><?= (int)($metrics['discipline']['planners_created'] ?? 0); ?>
       / <?= (int)($metrics['discipline']['bds_total'] ?? 0); ?> BDs</strong></p>
    <p>?? On-time day start:
       <strong><?= (int)($metrics['discipline']['on_time_start'] ?? 0); ?>
       / <?= (int)($metrics['discipline']['bds_total'] ?? 0); ?> BDs</strong></p>
    <p>? Average task completion:
       <strong><?= (int)($metrics['discipline']['avg_task_completion_pct'] ?? 0); ?>%</strong></p>
    <p>?? BDs with poor discipline (last week):
       <strong><?= (int)($metrics['discipline']['poor_discipline_bds'] ?? 0); ?></strong></p>

    <?php if (!empty($metrics['discipline']['top_bds'])): ?>
        <p><strong>Top disciplined BDs:</strong></p>
        <ul>
            <?php foreach ($metrics['discipline']['top_bds'] as $bdRow): ?>
                <li><strong><?= html_escape($bdRow['name']); ?></strong>
                    � Discipline: <?= (int)$bdRow['score']; ?>,
                    Completion: <?= (int)$bdRow['completion_pct']; ?>%</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($metrics['discipline']['bottom_bds'])): ?>
        <p><strong>Bottom BDs (weak discipline):</strong></p>
        <ul>
            <?php foreach ($metrics['discipline']['bottom_bds'] as $bdRow): ?>
                <li><strong><?= html_escape($bdRow['name']); ?></strong>
                    � Discipline: <?= (int)$bdRow['score']; ?>,
                    Completion: <?= (int)$bdRow['completion_pct']; ?>%</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- 4. Zone / cluster highlights -->
<h2>4. Zone / cluster highlights</h2>
<div class="metric-box">
    <?php if (!empty($metrics['zones'])): ?>
        <?php foreach ($metrics['zones'] as $zoneName => $z): ?>
            <p><strong><?= html_escape($zoneName); ?></strong><br>
                Pipeline:
                <strong><?= html_escape($z['pipeline_display']); ?></strong> |
                Target:
                <strong><?= html_escape($z['target_display']); ?></strong><br>
                Strong clusters:
                <?= html_escape(implode(', ', $z['strong_clusters'] ?? [])); ?><br>
                Weak clusters:
                <?= html_escape(implode(', ', $z['weak_clusters'] ?? [])); ?>
            </p>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No zone-level aggregation found.</p>
    <?php endif; ?>
</div>

<!-- 5. Critical clients & risk -->
<h2>5. Critical clients & risk</h2>
<div class="metric-box">
    <?php if (!empty($metrics['risk']['anchor_clients'])): ?>
        <p><strong>Anchor / Topspender clients at risk (sample):</strong></p>
        <ul>
            <?php foreach ($metrics['risk']['anchor_clients'] as $c): ?>
                <li>
                    <strong><?= html_escape($c['company_name']); ?></strong>
                    (<?= html_escape($c['region']); ?>) �
                    <?= html_escape($c['issue']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p>System flags (compulsive & stuck):</p>
    <ul>
        <li>Compulsive leads:
            <strong><?= (int)($metrics['risk']['compulsive_count'] ?? 0); ?></strong></li>
        <li>Closures pipeline with no next meeting:
            <strong><?= (int)($metrics['risk']['closure_no_next_meet'] ?? 0); ?></strong></li>
    </ul>
</div>

<!-- 6. Suggested actions -->
<h2>6. Anaya�s suggested actions for you</h2>
<div class="metric-box">
    <?php if (!empty($metrics['actions'])): ?>
        <ol>
            <?php foreach ($metrics['actions'] as $a): ?>
                <li><?= html_escape($a); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <p>No specific suggestions generated. Please check with admin if owner pack is configured.</p>
    <?php endif; ?>
</div>

<?php if (!empty($pack['ai_text'])): ?>
    <p style="font-style: italic; color:#555;">
        <?= nl2br(html_escape($pack['ai_text'])); ?>
    </p>
<?php endif; ?>

<p>Warm regards,<br><strong>Anaya</strong> � Your CRM Agent ??</p>

<p class="small">
    (Data built from: leads & funnel in <code>init_call</code>, companies in
    <code>company_master</code>, tasks in <code>tblcallevents</code>, day management in
    <code>daily_planner</code> &amp; <code>user_day</code>, compulsive leads in
    <code>compulsive_log</code>, targets in <code>target_vs_achievement</code>, users & hierarchy
    in <code>user_details</code> &amp; <code>user_type</code>.) :contentReference[oaicite:1]{index=1}
</p>

</body>
</html>
