<div class="block-infos">
    <div>
        <h3>Projets</h3>
        <ul>
        <?php foreach ($retroPlanning->projects as $_project): ?>
            <li>
                <span class="dot" style="color: <?php echo $_project['color']; ?>">•</span>
                <strong><?php echo $_project['name']; ?></strong> :
                ~<?php echo $_project['hours_per_day']; ?>h/j,
                reste <?php echo $_project['total_time']; ?>h.
                <?php if ($_project['start_time'] > $retroPlanning->now): ?>
                    à partir du <?php echo date('d/m/Y', $_project['start_time']); ?>
                <?php endif;?>
                <?php if($_project['end_time'] > $_project['start_time']): ?>
                    jusqu'au <?php echo date('d/m/Y', $_project['end_time']); ?>
                <?php endif; ?>
            </li>
        <?php endforeach;?>
        </ul>
    </div>
    <div>
        <h3>Infos</h3>
        <p>
            <strong>Heures travaillées par jour :</strong> <?php echo $retroPlanning->max_hours_per_day; ?><br />
            <strong>Jours fériés :</strong> <?php echo implode(', ', $retroPlanning->holidays); ?>
        </p>
    </div>
</div>
