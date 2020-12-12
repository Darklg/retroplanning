<div class="block-infos">
    <div>
        <h3>Projets</h3>
        <ul>
        <?php foreach ($retroPlanning->projects as $_project):

            $_color = $_project['color'];
            $_style = 'color: ' . $_color . ';';
            $_style_color = $_style;
            if (isset($_GET['filter_client']) && $_GET['filter_client'] != $_project['client_id']) {
                $_style .= 'background:' . $_color . ';';
            }

            ?>
            <li>
                <span class="dot" style="<?php echo $_style_color; ?>">•</span>
                <strong style="<?php echo $_style; ?>"><?php echo $_project['name']; ?></strong> :
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
        <h3>Clients</h3>
        <ul>
            <?php foreach ($retroPlanning->clients as $_idClient => $_client): ?>
            <li><strong><a style="text-decoration:<?php echo(isset($_GET['filter_client']) && $_GET['filter_client'] == $_idClient ? 'underline' : 'none'); ?>;text-transform: uppercase;color:<?php echo $_client['color']; ?>" href="?filter_client=<?php echo $_idClient; ?>"><?php echo $_idClient; ?></a></strong></li>
            <?php endforeach; ?>
            <li><strong><a href="?">-> Tous</a></strong></li>
        </ul>
    </div>
    <div>
        <h3>Infos</h3>
        <p>
            <strong>Heures travaillées par jour :</strong> <?php echo $retroPlanning->max_hours_per_day; ?><br />
            <strong>Jours fériés :</strong> <?php echo implode(', ', $retroPlanning->holidays); ?><br />
            <strong>Vacances :</strong> <?php echo implode(', ', $retroPlanning->vacations); ?>
        </p>
    </div>
</div>
