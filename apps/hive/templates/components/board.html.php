<?php
/**
 * @var array $boardData
 */
?>

<div class="board">
    <?php foreach ($boardData as $tile): ?>
        <div class="tile <?= $tile['class'] ?>" style="<?= $tile['style'] ?>">
            <?= $tile['label'] ?><span><?= $tile['type'] ?></span>
        </div>
    <?php endforeach; ?>
</div>
