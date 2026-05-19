<!-- application/views/emails/anaya_bd_daily.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Anaya � Your Day Plan & Funnel</title>
    <style type="text/css">
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        h1 { font-size: 20px; }
        h2 { font-size: 16px; margin-top: 18px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
        p { margin: 4px 0; }
        .small { font-size: 12px; color: #777; }
        .metric-box { border: 1px solid #eee; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .tag { display: inline-block; padding: 2px 6px; border-radius: 3px; background: #f3f3f3; font-size: 11px; margin-right: 5px; }
        .pill-green { background: #e5f8e5; color: #206030; }
        .pill-red { background: #fdecec; color: #9b1c1c; }
        .pill-yellow { background: #fff8e1; color: #9b6b00; }
        ul { margin: 4px 0 4px 18px; padding: 0; }
        li { margin: 2px 0; }
        .section-number { font-weight: bold; margin-right: 4px; }
        .ai-note { font-style: italic; color: #555; margin-top: 10px; }
        .table { border-collapse: collapse; width: 100%; margin-top: 6px; }
        .table th, .table td { border: 1px solid #eee; padding: 4px 6px; text-align: left; font-size: 12px; }
        .table th { background: #fafafa; }
    </style>
</head>
<body>

<p class="small">
    To: <?= html_escape('deepak.kumar@stemlearning.in'); ?><br>
    Date: <?= html_escape($pack['date']); ?>
</p>

<h1>?? Anaya � Your Day Plan & Funnel</h1>
<p>Hi <strong><?= html_escape($bd['name']); ?></strong>,</p>
<p>Here�s your <strong>daily summary</strong> based on yesterday�s work and today�s funnel.</p>

<!-- 1. Quick snapshot -->
<h2><span class="section-number">1.</span> Quick snapshot</h2>
<div class="metric-box">
    <p>?? Date: <strong><?= html_escape($pack['date']); ?></strong></p>
    <p>?? Planned start: <strong><?= html_escape($metrics['discipline']['planned_start'] ?? 'NA'); ?></strong>
       | Actual start: <strong><?= html_escape($metrics['discipline']['actual_start'] ?? 'NA'); ?></strong></p>
    <p>? Tasks completed yesterday:
       <strong><?= (int)($metrics['snapshot']['tasks_completed'] ?? 0); ?>
       / <?= (int)($metrics['snapshot']['tasks_planned'] ?? 0); ?></strong>
       (<?= (int)($metrics['snapshot']['task_completion_pct'] ?? 0); ?>%)</p>
    <p>?? Active companies in funnel:
       <strong><?= (int)($metrics['funnel']['active_companies'] ?? 0); ?></strong></p>
    <p>?? Total pipeline (Positive + Tentative + Closure):
       <strong><?= html_escape($metrics['funnel']['pipeline_value_display'] ?? '?0'); ?></strong></p>
    <p>?? Expected closures this month:
       <strong><?= html_escape($metrics['target']['expected_closure_value_display'] ?? '?0'); ?></strong></p>
</div>

<!-- 2. Day discipline -->
<h2><span class="section-number">2.</span> Day discipline</h2>
<div class="metric-box">
    <p><strong>Planner status:</strong>
        <?= html_escape($metrics['discipline']['planner_status'] ?? 'Not found'); ?></p>

    <p><strong>Day start / close:</strong><br>
        Day started at
        <strong><?= html_escape($metrics['discipline']['actual_start'] ?? 'NA'); ?></strong>
        (planned:
        <strong><?= html_escape($metrics['discipline']['planned_start'] ?? 'NA'); ?></strong>)<br>
        Day closed at
        <strong><?= html_escape($metrics['discipline']['actual_close'] ?? 'NA'); ?></strong>
    </p>

    <p>?? Discipline score:
        <?php $discScore = (int)($metrics['discipline']['score'] ?? 0); ?>
        <strong><?= $discScore; ?> / 100</strong>
        <?php if ($discScore >= 80): ?>
            <span class="tag pill-green">Strong</span>
        <?php elseif ($discScore >= 60): ?>
            <span class="tag pill-yellow">Moderate</span>
        <?php else: ?>
            <span class="tag pill-red">Needs improvement</span>
        <?php endif; ?>
    </p>

    <p class="small">
        Last 5 days � late starts:
        <strong><?= (int)($metrics['discipline']['late_start_days_5'] ?? 0); ?></strong>
    </p>
</div>

<!-- 3. Funnel & pipeline -->
<h2><span class="section-number">3.</span> Funnel & pipeline (your leads)</h2>
<div class="metric-box">
    <p><strong>By stage (no. of companies):</strong></p>
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
            <tr><td colspan="2">No funnel data for this period.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <p><strong>Pipeline value summary:</strong></p>
    <ul>
        <li>Positive + Very positive:
            <strong><?= html_escape($metrics['funnel']['positive_pipeline_display'] ?? '?0'); ?></strong></li>
        <li>Tentative:
            <strong><?= html_escape($metrics['funnel']['tentative_pipeline_display'] ?? '?0'); ?></strong></li>
        <li>Closure pipeline:
            <strong><?= html_escape($metrics['funnel']['closure_pipeline_display'] ?? '?0'); ?></strong></li>
        <li>Total funnel for FY:
            <strong><?= html_escape($metrics['funnel']['total_fy_pipeline_display'] ?? '?0'); ?></strong></li>
    </ul>
</div>

<!-- 4. Risk & stuck leads -->
<h2><span class="section-number">4.</span> Risk & stuck leads</h2>
<div class="metric-box">
    <?php $stuck = $metrics['risk']['stuck_leads'] ?? []; ?>
    <p>?? <strong>Stuck leads (no task in last <?= (int)($metrics['risk']['stuck_days'] ?? 15); ?> days):</strong>
       <?= count($stuck); ?></p>
    <?php if (!empty($stuck)): ?>
        <ul>
            <?php foreach ($stuck as $lead): ?>
                <li>
                    <strong><?= html_escape($lead['company_name']); ?></strong> �
                    <?= html_escape($lead['status']); ?>,
                    <?= html_escape($lead['value_display']); ?>,
                    last update <?= html_escape($lead['last_update_human']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php $compulsive = $metrics['risk']['compulsive_leads'] ?? []; ?>
    <p>?? <strong>Compulsive log leads:</strong> <?= count($compulsive); ?></p>
</div>

<!-- 5. Closures vs target -->
<h2><span class="section-number">5.</span> Closures vs target (this month)</h2>
<div class="metric-box">
    <p>?? Closure target:
        <strong><?= html_escape($metrics['target']['target_closure_value_display'] ?? '?0'); ?>
        / <?= (int)($metrics['target']['target_closure_count'] ?? 0); ?> closures</strong></p>
    <p>? Achieved:
        <strong><?= html_escape($metrics['target']['achieved_closure_value_display'] ?? '?0'); ?>
        / <?= (int)($metrics['target']['achieved_closure_count'] ?? 0); ?> closures</strong></p>
    <p>?? Balance:
        <strong><?= html_escape($metrics['target']['pending_closure_value_display'] ?? '?0'); ?>
        / <?= (int)($metrics['target']['pending_closure_count'] ?? 0); ?> closures</strong></p>

    <?php $topClosures = $metrics['target']['top_closure_opps'] ?? []; ?>
    <?php if (!empty($topClosures)): ?>
        <p><strong>Most promising closure opportunities:</strong></p>
        <ul>
            <?php foreach ($topClosures as $opp): ?>
                <li>
                    <strong><?= html_escape($opp['company_name']); ?></strong> �
                    <?= html_escape($opp['value_display']); ?> �
                    <?= html_escape($opp['stage']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- 6. Top actions -->
<h2><span class="section-number">6.</span> Top actions for today</h2>
<div class="metric-box">
    <?php $actions = $metrics['actions']['top_actions'] ?? []; ?>
    <?php if (!empty($actions)): ?>
        <ol>
            <?php foreach ($actions as $a): ?>
                <li><?= html_escape($a); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <p>No specific actions generated. Please check your tasks and funnel.</p>
    <?php endif; ?>
</div>

<!-- Optional AI note -->
<?php if (!empty($pack['ai_text'])): ?>
    <div class="ai-note">
        <?= nl2br(html_escape($pack['ai_text'])); ?>
    </div>
<?php endif; ?>

<p>Warmly,<br><strong>Anaya</strong> � Your CRM Agent ??</p>

<p class="small">
    (Data source: your planner, tasks, leads & funnel inside STEM CRM.)
</p>

</body>
</html>
