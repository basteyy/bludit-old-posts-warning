<?php
declare(strict_types=1);

defined('BLUDIT') or die('Bludit CMS.');

?>

<div class="alert alert-primary" role="alert">
    <?= $this->description(); ?>
</div>


<nav class="mb-3">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="nav-general"
           aria-selected="false"><?= $L->g('General') ?></a>
        <a class="nav-item nav-link" id="nav-advanced-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="nav-advanced"
           aria-selected="false"><?= $L->p('Advanced') ?></a>
    </div>
</nav>


<!-- General tab -->
<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">

    <?php

    echo Bootstrap::formTitle([
        'title' => $L->g('general-settings')
    ]);

    echo Bootstrap::formInputText([
        'name'        => self::MESSAGE,
        'label'       => $L->g('Displayed message'),
        'value'       => $this->getMessage(),
        'class'       => '',
        'placeholder' => '',
        'tip'         => $L->g('old-post-warning-leave-blank')
    ]);

    echo Bootstrap::formSelect([
        'name'     => self::HOOK_POSITION,
        'label'    => $L->g('old-post-warning-position'),
        'options'  => $this->translateArray(self::HOOK_POSITIONS),
        'selected' => $this->getHookPosition(),
        'class'    => '',
        'tip'         => $L->g('old-post-warning-position-tip')
    ]);

    echo Bootstrap::formInputText(array(
        'name'        => self::INTERVAL,
        'label'       => $L->g('old-post-warning-interval'),
        'value'       => $this->getInterval(),
        'class'       => '',
        'placeholder' => '',
        'type'        => 'number',
        'min'         => '1',
        'typ'         => $L->get('Minimum is 1')
    ));

    echo Bootstrap::formSelect([
        'name'     => self::INTERVAL_TYPE,
        'label'    => $L->g('Interval Type'),
        'options'  => $this->translateArray(self::INTERVAL_TYPES),
        'selected' => $this->getIntervalType(),
        'class'    => '',
        'tip'      => $L->g('old-post-warning-leave-blank')
    ]);

    ?>

</div>


<!-- Advanced tab -->
<div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
    <?php
    echo Bootstrap::formTitle([
        'title' => $L->g('advanced-settings')
    ]);

    echo Bootstrap::formTextarea(array(
        'name'        => self::TEMPLATE,
        'label'       => $L->get('Template'),
        'value'       => $this->getTemplate(),
        'class'       => '',
        'placeholder' => '',
        'tip'         => $L->g('old-post-warning-tpl-tip'),
        'rows'        => 4
    ));
    ?>

</div>

<script type="text/javascript">
    document.querySelector('form.plugin-form').classList.add('tab-content');
</script>